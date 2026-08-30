# Paymob Migration Audit — Phase 1 (Discovery Only)

**Scope:** Read-only audit of the current manual bank-transfer/InstaPay payment flow, in preparation for replacing it with the Paymob gateway. No code was changed to produce this report. Every claim below is grounded in the actual codebase as of this audit (commit `e4a325c`, 2026-08-30); where something was not found, it's stated explicitly.

**Stack confirmed:** Laravel `^12.0`, PHP `^8.2`, `mpdf/mpdf ^8.2`. No `routes/api.php` exists — only `web.php` and `console.php` are registered (`bootstrap/app.php:8-12`). No existing payment/webhook package in `composer.json`. Guzzle 7.10.0 is available transitively via the framework (used today through Laravel's `Http` facade).

---

## A. Current Flow Map

```
1. GUEST/USER browses homepage → picks a plan (no dedicated "plans" page;
   /purchase redirects to home#programs — routes/web.php:89)

2. GET /purchase/{plan:key}  →  PurchaseController@showForm
   (routes/web.php:95; app/Http/Controllers/Web/PurchaseController.php:33-59)
   - 404s if !$plan->is_active
   - Currency already resolved earlier in the request lifecycle by the global
     DetectCurrency middleware (app/Http/Middleware/DetectCurrency.php,
     registered bootstrap/app.php:17) — IP → country → currency, session-cached
   - Renders resources/views/app/web/purchase/form.blade.php, which embeds
     <x-web.payment-instructions> (resources/views/components/web/payment-instructions.blade.php)
     showing bank/InstaPay details from config/payment.php, keyed by
     currency_to_method (config/payment.php:43-48)

3. (AJAX) POST /purchase/check-coupon → PurchaseController@checkCoupon (:83-125)
   (AJAX) POST /purchase/check-email  → PurchaseController@checkEmail (:128-135)
   - Live-preview endpoints; no rate limiting (see Risk D)

4. POST /purchase/{plan:key} → PurchaseController@submit
   (routes/web.php:96; PurchaseController.php:138-301)
   a. Validates: full_name, email, duration_months (in:3,6), coupon_code
      (nullable), receipt (file|mimes:jpg,jpeg,png,gif,pdf|max:5120) — :170-187
   b. Guard: blocks resubmission if an authenticated user already has a
      subscription in pending_review/approved/active (:not for guests — Risk D)
   c. Stores receipt to PRIVATE disk, BEFORE the DB transaction:
        storeAs('receipts/'.now()->format('Y/m'), Str::uuid().'.'.ext, 'local')
        → storage/app/private/receipts/{Y}/{m}/{uuid}.{ext}   (:222-226)
   d. Server-side price computation, INSIDE the transaction (:190-219):
        base   = $plan->priceFor($currency, $durationMonths)  (fallback SAR, then $plan->price)
        afterSeason = SeasonService::applyToPrice(base)  — round(base*(1-pct/100))
        couponDiscount = Coupon::findActive($code)->calculateDiscount(afterSeason)
        total = round(max(0, afterSeason - couponDiscount))
      NO client-submitted amount is ever read (no 'total'/'amount'/'price' field
      even exists in the validation rules) — pricing is fully server-authoritative.
   e. DB::transaction (:228-276): creates Subscription with
        status = Subscription::STATUS_PENDING_REVIEW, receipt_path, subtotal,
        season_*, coupon_*, total, currency, payment_method_key,
        plans_snapshot (json), guest_name/guest_email/guest_token (if guest)
      If coupon came from a FamilyInvitation, that invitation is looked up
      (not yet marked used at this point per one agent's trace — consumption
      timing is marked at approval, see below) — PurchaseController.php:267-273
   f. Outside the transaction: OrderReceivedMail → customer (:284-286, try/catch
      logged only); OrderPendingReviewMail → all admin/coach users (:290-297)

5. GET /purchase/success/{id} → PurchaseController@success (routes/web.php:92)
   - Renders resources/views/app/web/purchase/success.blade.php — this IS the
     "pending admin review" state shown to the user (3-step panel: "review
     receipt" → "activate subscription" → "start journey", lines ~194-219)

   >>> Order now sits in status = pending_review until an admin acts <<<

6. Admin logs in (admin.auth middleware) → GET admin/subscriptions →
   resources/views/app/admin/subscriptions/index.blade.php (list + filters)
   → GET admin/subscriptions/{id} → show.blade.php
   - Receipt displayed inline via GET admin/subscriptions/{id}/receipt
     → SubscriptionsController@viewReceipt (routes/web.php:187;
       app/Http/Controllers/Admin/SubscriptionsController.php:284-296)
       streams the private-disk file directly (Storage::disk('local'))

7. POST admin/subscriptions/{id}/approve → SubscriptionsController@approve
   (app/Http/Controllers/Admin/SubscriptionsController.php:117-232)
   Guard: abort_if(status !== pending_review, 422)
   DB::transaction (:127-216):
     - status → STATUS_APPROVED (NOT 'active'!), reviewed_by, reviewed_at (:128-132)
     - if coupon tied to a FamilyInvitation → invitation->markRedeemed() (:136-144)
     - guest/account resolution (3 branches, :146-215): link existing user,
       or create new User (role=user, status=active, random password) +
       generate setup-account URL via guest_token
   Outside transaction:
     - Cache::forget('popular_plan_id') (:218)
     - Mail OrderApprovedMail → customer (:220-228, try/catch logged only)
     - redirect()->with('success', ...) — HTTP-only

   *** CRITICAL: approval does NOT activate the subscription, does NOT set
   start_date/end_date, does NOT touch coach/slot booking. See Section C. ***

8. Customer (now with an activated user account, but subscription still
   status=approved) completes a TraineeAssessment
   (BookingController::show, app/Http/Controllers/Web/BookingController.php:20-29)

9. Customer self-books a meeting slot:
   POST .../booking → BookingController@store (:53-154)
   - slot_lock = "{date} {time}" string, unique-indexed at the DB level
     (meeting_bookings.slot_lock, migration
      2026_07_19_200000_add_slot_lock_to_meeting_bookings.php:49-51)
   - MySQL duplicate-key error 1062 is caught and turned into a 422
     "slot already taken" response (:134-141) — the app-level pre-check
     (:110-114) is just a UX nicety; the DB unique index is the real guard.
   - NOTE: meeting_bookings has NO coach_id column — this is a single,
     GLOBAL shared calendar slot lock, not a per-coach lock.

10. Coach confirms the booking → DashboardController@confirmBooking
    (app/Http/Controllers/Web/DashboardController.php:283-397)
    DB::transaction (:301-394):
      - THIS is where real activation happens:
          status = 'active'
          start_date = $request->start_date
          end_date   = start_date->copy()->addMonths($subscription->duration_months ?? 3)
        (:306-314)
      - Also creates Program/ProgramDay rows, a UserProfile, a first WeightLog
        (:330-393)

11. Lifecycle cron jobs (routes/console.php:11-39, all dailyAt + withoutOverlapping
    + runInBackground — requires a real `php artisan schedule:run` cron entry
    on the host; NOT CONFIRMED to exist in production, see Risk D):
      - subscriptions:expire   → sets status=expired when end_date passed
        (app/Console/Commands/ExpireSubscriptions.php:32) + SubscriptionExpiryMail
      - subscriptions:remind   → SubscriptionReminderMail 5 days before expiry
      - subscriptions:notify-start → SubscriptionStartedMail on start_date
      - carts:cleanup (every 5 min)
      - family-invitations:expire (daily)
```

**Rejection path (symmetry):** `SubscriptionsController@reject` (`:235-281`) — same guard, `DB::transaction` sets `status = STATUS_REJECTED` + `rejection_reason`, reverts any `FamilyInvitation` from `used` back to `pending` (`:254-264`), sends `OrderRejectedMail` outside the transaction.

---

## B. Inventory Table

| File | Action | Reason |
|---|---|---|
| `app/Http/Controllers/Web/PurchaseController.php` | **MODIFY** | Keep plan/coupon/season pricing logic (reusable), replace receipt-upload-and-wait with Paymob order creation + redirect to payment intention |
| `resources/views/app/web/purchase/form.blade.php` | **MODIFY** | Remove receipt upload field + `<x-web.payment-instructions>` embed; add "pay with card" CTA |
| `resources/views/components/web/payment-instructions.blade.php` | **REMOVE** (or gate behind a fallback flag) | Bank-transfer/InstaPay instructions become obsolete once Paymob is live for a currency — see Risk D on partial-currency support |
| `resources/views/app/web/purchase/success.blade.php` | **MODIFY** | "Pending admin review" 3-step panel replaced by payment-result state (success/pending/failed) |
| `config/payment.php` | **KEEP (as fallback) / EXTEND** | Manual bank details may still be needed for unsupported currencies; add a new `config/services.php` `'paymob' => [...]` block (see Section E) |
| `app/Models/Subscription.php` | **MODIFY** | Add Paymob-related columns (see Section E); status enum needs new values |
| `database/migrations/2026_07_09_300001_extend_subscriptions_for_new_flow.php` (pattern) | **NEW MIGRATION** | Add `paymob_order_id`, `paymob_transaction_id`, new status values, `payment_intended_at`, etc. |
| `app/Http/Controllers/Admin/SubscriptionsController.php` (`approve`/`reject`) | **MODIFY / EXTRACT** | Core transaction body (`:127-216`) must be extracted into a reusable service (see Section C) so both the admin button and the Paymob webhook can call it |
| `resources/views/app/admin/subscriptions/show.blade.php` | **MODIFY** | Approve/reject buttons become "manual override" only, secondary to automatic Paymob activation; keep for the fallback/manual-currency path and for refund/dispute handling |
| `resources/views/app/admin/subscriptions/index.blade.php` | **KEEP (modify filters)** | Add payment-status columns/filters (paid, failed, refunded) |
| `app/Http/Controllers/Admin/SubscriptionsController.php::viewReceipt` | **KEEP** | Still needed for the manual-fallback path and for historical orders |
| **New: `app/Http/Controllers/Web/PaymobWebhookController.php`** | **NEW** | Server-to-server callback handler — HMAC-verified, CSRF-exempt |
| **New: `app/Services/PaymobService.php`** (or similar) | **NEW** | Wraps Paymob REST API calls (auth token, order registration, payment key), following the `Http::` facade pattern already used in `DetectCurrency.php:57` |
| **New: `app/Services/SubscriptionActivationService.php`** (proposed name) | **NEW** | Extraction target for the reusable "activate" logic — see Section C |
| `bootstrap/app.php` | **MODIFY** | Add CSRF exemption for the webhook route (`validateCsrfTokens(except: [...])`) — none exists today |
| `routes/web.php` | **MODIFY** | Add Paymob callback/webhook routes, order-creation route |
| `app/Mail/OrderApprovedMail.php`, `OrderRejectedMail.php`, `OrderReceivedMail.php`, `OrderPendingReviewMail.php` | **KEEP, retarget dispatch site** | Same templates, now triggered from webhook/service instead of only from admin controller |
| `app/Models/Coupon.php` | **MODIFY** | `findActive()`'s live-COUNT usage check (`:31-46`) has a TOCTOU race under concurrent webhook-driven checkout load — needs a real usage counter or DB-level lock (Risk D) |
| `app/Models/Cart.php`, `app/Models/CartItem.php` | **FIX (unrelated pre-existing bug, flag separately)** | Both models reference DB columns dropped by later migrations (`is_yearly`, `yearly_discount`, `monthly_price`) and omit current columns (`duration_months`, `currency`, `price`) — schema drift discovered during this audit, not caused by it |
| `app/Http/Controllers/Web/BookingController.php`, `DashboardController.php::confirmBooking` | **KEEP, decide scope** | Real activation/expiry logic lives here today, decoupled from payment approval — decide whether Paymob should short-circuit straight to `active` or preserve the assessment→booking→coach-confirms ceremony (Section C/F) |
| `app/Http/Controllers/Web/JourneyController.php` (mPDF usage) | **KEEP, reuse pattern** | Only existing mPDF code in the app; its Arabic-RTL `Mpdf` config (`:85-97`) is a ready-made pattern for a future payment-invoice PDF, which does not exist today |

---

## C. Reusable Core — Can "Activate Subscription" Be Extracted?

**Short answer: not cleanly as one function today, because "approval" and "activation" are two different, temporally separated things in the current code — and only the first one is what admin approval actually does.**

1. **`SubscriptionsController::approve()`** (`app/Http/Controllers/Admin/SubscriptionsController.php:117-232`) only:
   - flips `status: pending_review → approved`
   - resolves/creates the user account
   - redeems a family-invitation coupon
   - sends `OrderApprovedMail`

   It does **not** set `start_date`/`end_date`, does **not** touch `meeting_bookings`, and does **not** compute an expiry date.

2. **Real activation** (`status → active`, expiry-date math) happens much later in `DashboardController::confirmBooking()` (`app/Http/Controllers/Web/DashboardController.php:283-397`), gated behind a manual, human-in-the-loop ceremony: trainee assessment → self-service slot booking → **coach** confirms the booking. This is coupled to coach scheduling, not payment.

**Extraction shape proposed:**

- Extract the `approve()` transaction body (`:127-216`) into a plain class, e.g. `App\Services\OrderApprovalService::approve(Subscription $subscription, ?int $reviewedBy = null): ApprovalResult`, returning a value object (`accountAutoCreated`, `passwordSetUrl`, `customerName`, `customerEmail`, `isGuest`) instead of mutating by-reference closure variables (`&$accountAutoCreated` etc., current lines `121-127`) — this is the one HTTP-coupling problem in that method (`Auth::guard('admin')->id()` becomes an optional nullable param; `abort_if()` becomes a thrown domain exception the caller decides how to handle).
- The controller action becomes a thin wrapper: call the service, then do the HTTP-specific bits (flash message, redirect).
- A Paymob webhook handler calls the **same** service method, passing `reviewedBy: null` (or a sentinel "system/paymob" reviewer id) to mark it as an automated approval.
- **Decide as part of Phase 2 design** (see Section F) whether "payment succeeded" should map to `status = approved` (matching today's admin-approve semantics, leaving assessment/booking/coach-confirm untouched) or should also collapse straight to `active` with immediate expiry calculation, which is a materially bigger behavior change since it removes the coach-in-the-loop scheduling step from the picture — the code today treats those as unrelated concerns and there is no single existing method that does both.

---

## D. Risk List

**1. Multi-currency mismatch (SAR, EGP, TND, USD currently charged).**
`config/payment.php:43-48` and `Plan::priceFor()`/`PlanPrice` confirm the app actively prices and stores orders in **four** currencies: SAR, EGP, TND, USD (mapped 1:1 from IP-detected country via `CurrencyService::COUNTRY_CURRENCY`, `app/Services/Web/CurrencyService.php:17-21`, defaulting anything unmapped to USD). Paymob is fundamentally an Egyptian-market gateway; its merchant accounts and integration IDs are typically issued **per currency/country** and EGP is the only currency guaranteed to work out of the box — SAR/TND/USD support depends entirely on the specific Paymob merchant contract (Paymob does have GCC/KSA and other regional entities, but that's a business/contracting question, not a code one). **If Paymob only ends up covering EGP:** SAR/TND/USD customers need either a Paymob multi-currency integration ID (if the merchant account supports it) or the existing manual bank-transfer/InstaPay flow (`config/payment.php`, `payment-instructions.blade.php`) kept alive **specifically for those currencies** — meaning the manual flow likely cannot be fully removed, only made conditional on currency. This must be settled before writing code (see Section F).

**2. Idempotency — webhook firing more than once.**
Traced concretely in `SubscriptionsController::approve()`: the guard `abort_if($subscription->status !== STATUS_PENDING_REVIEW, 422)` (`:119`) is a plain read-then-write with **no row lock** (`Subscription::where(...)->lockForUpdate()` is not used anywhere in the codebase). Two near-simultaneous calls (double webhook delivery is standard Paymob behavior — they explicitly recommend handling duplicate callbacks) can both read `pending_review` before either commits, and both execute the full transaction body. Concretely today, that race can: create **two duplicate `User` accounts** for the same guest email (`:186`, no unique-email pre-lock/pessimistic lock), send `OrderApprovedMail` twice, and call `FamilyInvitation::markRedeemed()` twice (harmless since it's idempotent by nature). The Paymob webhook handler MUST add either a `lockForUpdate()` transaction or a dedicated `paymob_transaction_id` unique-constraint check before calling into the approval logic.

**3. Webhook route must be CSRF-exempt and HMAC-verified — neither exists today.**
`bootstrap/app.php:13-27` shows the entire app runs through the default `web` middleware group with **no CSRF exceptions configured anywhere** (confirmed: no `validateCsrfTokens(except:)` call, no custom `VerifyCsrfToken` override found in `app/Http/Middleware/`). There is also no `routes/api.php`/`api` middleware group registered (`withRouting()` at `bootstrap/app.php:8-12` only wires `web` and `commands`). A Paymob webhook route added naively to `routes/web.php` **will be rejected by CSRF** on Paymob's server-to-server POST. It needs an explicit CSRF exemption in `bootstrap/app.php`, and the controller must independently verify Paymob's HMAC signature over the callback payload (no existing HMAC-verification code exists anywhere in this codebase — this is 100% new code).

**4. Race between browser redirect and server-to-server webhook.**
Nothing in the current codebase handles this today (there is no redirect-callback concept at all yet — that's new for Paymob). Standard practice, and the recommendation here: **the webhook is the sole source of truth for activation**; the browser redirect (`response_callback`) should only ever show a "processing/checking payment status" UI and poll or redirect to the order-status page — it must never itself trigger activation, exactly because it's the less trustworthy, client-influenced path (a user can hit the redirect URL with fabricated query params).

**5. Coupon/discount amounts must be locked at order creation, not recalculated after payment.**
Good news: this is **already true** for the season/coupon calculation itself — `PurchaseController::submit()` computes `subtotal`/`season_discount`/`coupon_discount`/`total` once, server-side, at order-creation time (`:190-219`), and persists them onto the `Subscription` row (plus a `plans_snapshot` JSON audit trail, `:252-264`). As long as the Paymob order/payment-intention amount is read from that already-persisted `total` column (not recomputed at webhook time, when the season might have ended or the coupon might have expired), this requirement is satisfied by reusing existing code. **Risk to watch:** `Coupon::findActive()`'s usage-limit check (`app/Models/Coupon.php:31-46`) is a live `COUNT()` query against `Subscription` rows excluding `rejected`/`cancelled` — under Paymob-driven traffic (more concurrent checkouts since there's no more "wait for bank transfer" friction), two concurrent submissions can both pass the `$used >= $coupon->max_uses` check before either's `Subscription` row is committed, over-issuing a limited coupon. No usage-counter column exists (`Coupon` model has no `used_count`); a `SELECT ... FOR UPDATE` or an atomic counter increment should be added when this is rebuilt for Paymob-scale traffic.

**6. Refund/failed-payment/timeout states that don't exist in the schema.**
Confirmed: `subscriptions.status` enum is `pending_review, approved, active, expired, rejected, cancelled, waiting` (`database/migrations/2026_07_09_300001_extend_subscriptions_for_new_flow.php:12-15`, mirrored in `Subscription` model constants `:14-20`, minus `waiting` which has no PHP constant and no live write path found anywhere — a dead legacy value). There is **no** `failed`, `refunded`, `expired_unpaid`, or `awaiting_payment` state. A Paymob integration needs new states for: payment initiated but not completed (cart/order abandoned mid-checkout), payment declined by the card issuer, payment timed out, and refund/chargeback — none of which map onto today's admin-review-centric enum. This is a schema change, not just new code.

**7. Will background jobs (mail, PDF) actually run, given the queue/host reality?**
- `QUEUE_CONNECTION=database` is the default (`.env.example:38`, confirmed in the checked-in `.env` too) — but **zero** of the 10 `App\Mail\*` classes implement `ShouldQueue` (confirmed via repo-wide grep), so every mail today sends synchronously inline in the request. This means mail delivery does not currently depend on a queue worker running at all — but if new Paymob-related mail is added as `ShouldQueue` for performance (reasonable, since webhook handlers should respond fast), it WILL depend on a worker.
- **No evidence a queue worker or the Laravel scheduler cron is configured on the production cPanel host**: no `.cpanel.yml`, no Supervisor config, no deployment doc mentions `queue:work` or `schedule:run` anywhere in the repo. The `composer.json` `"dev"` script's `queue:listen` is explicitly local-dev-only (via `concurrently`, alongside `php artisan serve`). Yet `routes/console.php:11-39` already schedules 5 commands (`carts:cleanup`, `subscriptions:expire`, `subscriptions:remind`, `subscriptions:notify-start`, `family-invitations:expire`) that require a real `* * * * * php artisan schedule:run` cron entry to fire at all — **this needs to be confirmed with the hosting setup independent of this codebase**, since it affects both the existing lifecycle commands and any new Paymob reconciliation job. There's no PDF generation in the payment flow today (see below), so that specific sub-risk is moot until a Paymob invoice PDF is built — at which point the same synchronous-by-default pattern should be followed unless the cron/worker situation is verified first.
- No PDF invoice/receipt exists in the current flow at all (mPDF is used only for an unrelated post-program "journey report" in `JourneyController.php:71-119`) — this needs to be built from scratch if Paymob-era receipts should include a PDF.

**8. Currently in-flight `pending_review` records.**
Not queried (per instructions — destructive/production-data queries were out of scope for this audit), but here is the exact query an operator should run before removing the manual flow:
```sql
SELECT id, guest_email, currency, total, payment_method_key, created_at
FROM subscriptions
WHERE status = 'pending_review'
ORDER BY created_at ASC;
```
And to see how old the oldest one is (useful for deciding a cutover date):
```sql
SELECT MIN(created_at) AS oldest_pending, COUNT(*) AS total_pending
FROM subscriptions WHERE status = 'pending_review';
```
**These must be resolved (approved/rejected via the existing manual flow, or explicitly migrated) before the manual UI is removed**, since once `payment-instructions.blade.php` and the receipt-upload field are gone, there's no way for those specific customers to complete their original bank-transfer intent through the UI — the admin screens (`SubscriptionsController::approve/reject`, kept per Section B) remain the only way to resolve them.

**9. Guest-checkout duplicate-order risk is currency-blind today, will get worse under card payments.**
`PurchaseController::submit()`'s "already has a pending/approved/active subscription" guard (`:` around the auth-check block) only applies to **authenticated** users — an unauthenticated guest can submit unlimited `pending_review` orders under different emails today with no real cost (they just don't send a receipt). Once Paymob makes checkout instant (no bank-transfer friction), this becomes more relevant for abandoned-cart-style order pollution — worth deciding whether to add a guest-email-based duplicate-order check.

---

## E. Proposed Target Architecture

**Order lifecycle (new):**
```
pending_review = pending_review   (kept — reused for "order created, awaiting payment result", OR
                                    consider renaming semantics to "awaiting_payment" to avoid
                                    confusion with the old human-review meaning)
  ↓ (Paymob payment intention created, user redirected to iframe/redirection URL)
  ↓ (Paymob webhook: transaction success)
approved            → same as today: account resolved, family coupon redeemed, OrderApprovedMail sent
  ↓ (existing, unchanged for now — assessment → self-booking → coach confirms)
active
  ↓ (existing cron: subscriptions:expire)
expired

NEW terminal/side states needed:
payment_failed      → Paymob webhook: transaction failed/declined
payment_timeout     → order created but no webhook within N minutes (housekeeping job)
refunded            → manual admin action after a Paymob refund API call or Paymob refund webhook
```

**New/changed tables:**
- `subscriptions` — add columns: `paymob_order_id` (bigint, nullable, indexed), `paymob_transaction_id` (bigint, nullable, unique — this unique constraint is the idempotency backstop for risk D-2), `payment_intended_at` (timestamp), `paid_at` (timestamp, nullable), `payment_failure_reason` (text, nullable). Extend the `status` enum with `payment_failed`, `refunded` (and decide the fate of dead legacy values `waiting`/`cancelled` while touching this enum anyway).
- Keep `receipt_path`, `payment_method_key`, `reviewed_by/at`, `rejection_reason` for the **manual fallback path** (unsupported currencies, or Paymob outage).

**New service classes** (matching the project's existing flat `App\Services\{Web\}` convention, e.g. `app/Services/Web/CurrencyService.php`, `SeasonService.php`):
- `App\Services\Paymob\PaymobClient.php` — thin REST wrapper (auth token → order registration → payment key), using `Http::` facade exactly like `DetectCurrency.php:57`'s pattern (timeout, try/catch, credentials from `config('services.paymob.*')` per the existing `config/services.php` convention).
- `App\Services\OrderApprovalService.php` — extraction of `SubscriptionsController::approve()`'s transaction body (Section C), callable from both the admin controller and the new webhook controller.
- `App\Http\Controllers\Web\PaymobWebhookController.php` — verifies HMAC, looks up `Subscription` by `paymob_order_id`, uses `lockForUpdate()` inside a transaction, checks `paymob_transaction_id` hasn't been processed before, then calls `OrderApprovalService`.
- `App\Http\Controllers\Web\PaymobCallbackController.php` (browser redirect target, `response_callback`) — read-only status display, never mutates state (Risk D-4).

**New routes** (`routes/web.php`, with an explicit CSRF exemption added in `bootstrap/app.php` for the webhook path only):
```
POST /paymob/webhook      → PaymobWebhookController@handle   (CSRF-exempt, HMAC-verified)
GET  /paymob/callback     → PaymobCallbackController@show    (browser redirect target)
POST /purchase/{plan:key}/pay → PurchaseController@initiatePayment (replaces today's receipt-upload submit for Paymob-supported currencies)
```

**Sequence for the Paymob flow:**
```
1. PurchaseController computes total exactly as today (:190-219, reused as-is)
2. Subscription row created with status=pending_review, paymob fields null,
   payment_intended_at=now() — same DB::transaction as today, minus receipt_path
3. PaymobClient: auth token → order registration (amount_cents = total*100,
   currency) → payment key request → build iframe/redirection URL
4. User redirected to Paymob-hosted payment page
5. Paymob POSTs to /paymob/webhook (server-to-server, HMAC-signed)
   → verify signature → lockForUpdate the Subscription by paymob_order_id
   → if already processed (paymob_transaction_id set), return 200 and no-op (idempotency)
   → else: set paid_at, paymob_transaction_id, call OrderApprovalService::approve()
6. Browser redirect to /paymob/callback shows "checking payment status" →
   polls/reads current Subscription status (set by the webhook, not by itself)
7. OrderApprovedMail sent (existing mailable, existing template) —
   optionally attach a PDF receipt here (new — no existing invoice PDF code,
   would reuse JourneyController's Arabic-RTL mPDF config as a starting pattern)
8. Existing downstream flow unchanged: assessment → self-booking →
   coach confirms → DashboardController::confirmBooking() activates + sets expiry
```

Currency fallback: keep `payment-instructions.blade.php` + the receipt-upload path alive, conditionally rendered only for currencies Paymob doesn't cover for this merchant account (config-driven, e.g. `config('payment.paymob_currencies')`), rather than deleting it outright — see Section F for the decision this depends on.

---

## F. Open Questions For You

1. **Which currencies will Paymob actually process for this merchant account** — EGP only, or does the contracted Paymob account also cover SAR/TND/USD? This single answer determines whether the manual bank-transfer/InstaPay flow gets fully removed or kept as a permanent per-currency fallback (Risk D-1).
2. **Paymob account type/integration IDs**: online-card integration only, or also wallet/Fawry/installments? Each Paymob "integration ID" maps to a different payment method and may need separate UI options.
3. Do you want to **keep the manual flow as a permanent fallback** for unsupported currencies/outages, or is the goal to fully retire it once Paymob is live (accepting that SAR/TND/USD customers would then need a different solution)?
4. **Scope of "auto-activation"**: should a successful Paymob payment auto-set `status = approved` only (matching today's admin-approve semantics, leaving the existing assessment → self-service-booking → coach-confirms ceremony untouched), or should it also collapse straight to `active` with immediate expiry-date calculation (a bigger behavioral change, removing the coach-in-the-loop step)? Section C flags that these are currently two unrelated code paths.
5. Should the current **manual admin approve/reject UI remain visible for Paymob-paid orders** (as a read-only audit trail / manual-override-in-emergencies tool), or should Paymob-paid orders skip the admin subscriptions list entirely?
6. What should happen to the 4 hardcoded seed coupons and the family-invitation-reward coupons under Paymob — do they still apply the same way against the Paymob charge amount, or does anything change about season/coupon interaction once cards are live?
7. **Production hosting confirmation needed independent of code**: does a `queue:work` daemon or `php artisan schedule:run` cron actually run on the cPanel host today? This affects whether new Paymob-related jobs (reconciliation, payment-timeout sweeps, queued receipt emails) can safely rely on the queue, or must run synchronously inside the webhook request like everything else in the app does today.
8. Do you want a **PDF payment receipt/invoice** attached to `OrderApprovedMail` (there is currently no such PDF anywhere in the payment flow — it would be new code, though the Arabic-RTL mPDF configuration pattern already exists for the unrelated "journey report")?
9. What's the intended handling for **payment_failed / timeout / refund** — is refund handling needed at launch, or can it be a manual admin action (mark subscription cancelled + a note) for a first version, with a real Paymob refund-API integration deferred?
10. How should the currently in-flight `pending_review` records (Risk D-8) be handled at cutover — finish them through the existing manual flow before disabling it, or grandfather them in permanently?

---

## Summary — Top 5 Findings

1. **Admin "approval" ≠ subscription "activation."** `SubscriptionsController::approve()` only flips status to `approved` and resolves the user account — the actual `status → active` + expiry-date calculation happens much later in `DashboardController::confirmBooking()`, after a separate assessment → self-booking → coach-confirms ceremony. A Paymob webhook can only cleanly replicate the *first* half unless you deliberately decide to collapse the two (Section C/F-4).
2. **Pricing is already fully server-authoritative and reusable as-is.** `PurchaseController::submit()` computes plan price → season discount → coupon discount → total entirely server-side (`:190-219`), with no client-submitted amount field anywhere — this logic can be lifted directly into the Paymob order-creation step with no rework needed for trust reasons.
3. **No idempotency protection exists for double-approval**, and the current guard is a plain read-then-write with no row lock — a duplicated Paymob webhook delivery (which Paymob explicitly warns can happen) would risk creating duplicate guest user accounts and duplicate emails; a `lockForUpdate()` plus a unique `paymob_transaction_id` constraint is mandatory, not optional.
4. **CSRF and webhook infrastructure don't exist yet.** There's no `routes/api.php`, no CSRF exemption anywhere in `bootstrap/app.php`, and no HMAC-verification code anywhere in the codebase — a webhook route needs deliberate new plumbing, not just a new controller method.
5. **Multi-currency is the single biggest open business risk.** The app actively charges in 4 currencies (SAR, EGP, TND, USD) today; Paymob is EGP/Egypt-centric by default, so unless the merchant account explicitly covers all four, the manual bank-transfer/InstaPay flow likely cannot be fully retired — it becomes a permanent per-currency fallback, which changes both the UI (conditional rendering) and the schema (new payment-failed/refunded states must coexist with the existing pending_review/approved manual-review states).
