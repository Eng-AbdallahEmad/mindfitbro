# Dual Payment Plan — Reintroducing Manual Bank Transfer Alongside Paymob

**Scope:** Part A only — discovery and design report, no implementation. Reverses decision D3 from `docs/paymob-migration-audit.md`: manual bank transfer/InstaPay comes back as a customer-facing SECOND payment method, alongside Paymob, not instead of it.

**A6 answered (2026-09-01) — implementation proceeding on these decisions:**
1. Egypt keeps InstaPay/bank transfer alongside card (limited card penetration there; card stays the default, presented first).
2. SAR/EGP/TND get manual transfer; USD/rest-of-world is Paymob-only. The old `sa_world` bucket's Saudi bank details, previously shown to literally every non-EG/TN visitor, are removed rather than kept on file for a currency that no longer has manual transfer.
3. Manual is always additive — an eligible customer sees both options, card presented first as default, never a replacement for Paymob.
4. Eligibility toggle stays in `config/payment.php` for now (one `'enabled'` boolean per currency) — migrating to a DB-backed, admin-editable setting is a known, deliberately deferred future step, not needed before the first real manual transaction.
5. Eligibility is gated on `session('detected_country')` (IP-derived, not customer-changeable), never on `session('currency')` (freely switchable via `/currency/switch` with zero verification, kept working for **display** only). Fails closed to Paymob-only when detection is unavailable. Every mismatch between detected country and a manual order actually created gets logged for review.
6. Mid-flight method switching is being built (`awaiting_payment`/`payment_failed` → either method, same row, same locked price, blocked once `pending_review`/`approved`/`active`) — tracked as a later step in this same implementation, after the core dual-path is confirmed working.

**Headline finding, before anything else:** this is a much smaller job than a "rebuild." The whole Paymob migration — internally staged as steps "Batch 1" through "Batch 7" in code comments — landed as a single commit, `301e09d` (2026-08-31), on top of `e4a325c` (the exact commit the original audit doc was written against). "Batch 5" specifically refers to that commit's rewrite of `PurchaseController::initiatePayment()` (then named `submit()`), which removed the manual flow from the customer-facing form and stopped dispatching two emails — it did **not** touch the schema, the model, the config, the mail classes, the admin approval screens, the reusable blade component, or the approval service. All of that was deliberately kept "for the fallback path" (see `docs/paymob-migration-audit.md` Section B) and is still sitting in the repo, intact, unused. Restoring manual transfer is primarily a matter of **reconnecting a form field and a submit branch to infrastructure that already exists and is already tested-compatible.**

---

## A1 — What Batch 5 removed, and what's still there

| Piece | Status today | Evidence |
|---|---|---|
| Receipt upload **form field** in `form.blade.php` | **Removed.** No file input, no `<x-web.payment-instructions>` embed. Single Paymob-only submit button. | Confirmed by direct read of `resources/views/app/web/purchase/form.blade.php` — no `type="file"`, no `x-web.payment` tag anywhere. |
| Receipt **validation rule** (`receipt \| file \| mimes:jpg,jpeg,png,gif,pdf \| max:5120`) in the submit controller | **Removed** from `PurchaseController::initiatePayment()`'s validate() call (`app/Http/Controllers/Web/PurchaseController.php:249-264`) — only `full_name/email/phone/duration_months/coupon_code` remain. | Direct read of current `PurchaseController.php`. |
| Receipt **storage** (`storeAs('receipts/{Y}/{m}/{uuid}.{ext}', 'local')`) | **Removed** — no code path writes `receipt_path` anymore. | No `storeAs`/`receipts/` string found anywhere in `PurchaseController.php` today. |
| `receipt_path` **column** on `subscriptions` | **Still exists**, nullable, in `$fillable` (`app/Models/Subscription.php:51`). | Added by `database/migrations/2026_07_09_300001_extend_subscriptions_for_new_flow.php:16`, never dropped. |
| `SubscriptionsController::viewReceipt()` (admin, streams the file) | **Still exists, fully functional**, just currently has nothing to stream for any subscription created since Batch 5. | `app/Http/Controllers/Admin/SubscriptionsController.php` (`viewReceipt`, unchanged since the audit). |
| `OrderReceivedMail` / `OrderPendingReviewMail` | **Both class files still exist** in `app/Mail/`, complete with their Blade templates. Just not dispatched — `PurchaseController::initiatePayment()` has an explicit comment explaining why: *"No OrderReceivedMail / OrderPendingReviewMail here (Batch 5 recommendation): under Paymob, 'order created but unpaid' isn't a customer-facing event worth emailing about, and there's no admin review step left to notify staff of."* | `ls app/Mail/` — both present. Comment at `PurchaseController.php:394-399`. |
| `<x-web.payment-instructions>` component | **Still exists, fully intact**, self-contained (reads `$subscription->payment_method_key` or falls back to `config('payment.currency_to_method')`), renders InstaPay or bank-transfer layout depending on `type`. **Currently invoked nowhere** — orphaned, not deleted. | Full read of `resources/views/components/web/payment-instructions.blade.php`; repo-wide search for `x-web.payment-instructions` usage found zero call sites. |
| `config/payment.php`'s `methods` / `currency_to_method` | **Still exists, with real (not placeholder) bank/InstaPay data** for SAR ("sa_world"), EGP, TND. Read today only by `CurrencyService::paymentMethodKey()`/`paymentInstructions()` (`app/Services/Web/CurrencyService.php:61-69`) and the dev-only `/currency/debug` route (`routes/web.php:75-76`) — not by any customer-facing controller. | Full read of `config/payment.php`. |
| Translation keys (`upload_receipt`, `upload_hint`, `upload_formats`, `file_selected`, `payment_instructions`, `pay_via`, `bank`, `account_name`, `account_number`) | **Still present** in both `resources/lang/ar/messages.php` and `en/messages.php`, unused. | Direct read, lines 881-898. |
| The **manual-flow 3-step "review → activate → start journey" panel** | **Still exists, fully built**, in `resources/views/app/web/purchase/success.blade.php`'s `@else` branch (`payment_gateway !== 'paymob'`), explicitly commented `{{-- Legacy manual flow (payment_gateway = 'manual') --}}`. Currently unreachable because nothing creates a `payment_gateway = manual` subscription anymore. | Full read of `success.blade.php:193-264`. |
| `payment_gateway` column + `Subscription::GATEWAY_MANUAL` / `GATEWAY_PAYMOB` constants, `scopeViaManual()` | **Fully wired**, not a stub. Every pre-Batch-5 row was explicitly backfilled to `'manual'` in the migration itself. | `app/Models/Subscription.php:27-28,143-145`; `database/migrations/2026_08_30_100000_add_paymob_fields_to_subscriptions.php:30,44`. |
| `OrderApprovalService::approve()` | **Already accepts a manual-flow order as valid input**, unchanged, no modification needed. See A4. | `app/Services/OrderApprovalService.php:43`. |
| `SubscriptionFactory` (tests) | **Defaults to `GATEWAY_MANUAL` + `STATUS_PENDING_REVIEW`** — the "manual, pending review" state is the factory's *default*, not a special case that needs adding. | `database/factories/SubscriptionFactory.php:18-32`. |
| Admin approve/reject screens, `SubscriptionsController::approve()/reject()`, rejection-reason field | **Untouched, still the live fallback/manual-review UI** exactly as the audit intended it to remain (Section B: "keep for the fallback/manual-currency path"). | Confirmed via this session's own earlier edits to this controller/views — approve/reject actions were not touched. |

**Bottom line for A1:** nothing needs to be rebuilt from scratch. What needs to be **built new**:
1. A payment-method choice in the customer-facing form (doesn't exist today in any form).
2. Re-wiring the receipt field + validation + storage + the two dormant mail dispatches back into a submit branch.
3. A config-driven eligibility layer (A3) — this genuinely doesn't exist; `currency_to_method` today assumes *everyone* gets *some* manual method, which is no longer the rule.
4. Restoring the pre-submission `<x-web.payment-instructions>` embed on the form (see A2's note — it currently expects a persisted `$subscription`, which doesn't exist pre-submission; needs a small prop rework, not a rebuild).

**Confirmed via git history** (`git show 301e09d^`, the commit just before the removal) — the exact old validation and storage code, for reference when rebuilding:
```php
// old validate() — PurchaseController::submit(), pre-301e09d
'receipt' => 'required|file|mimes:jpg,jpeg,png,gif,pdf|max:5120',
// ... 'receipt.required' => 'إيصال الدفع مطلوب', 'receipt.mimes' => 'يُقبل فقط: صور (JPG, PNG) أو PDF', 'receipt.max' => 'حجم الملف يجب ألا يتجاوز 5 ميجابايت',

// old storage, right after validation, before the DB transaction
$receiptPath = $request->file('receipt')->storeAs(
    'receipts/' . now()->format('Y/m'),
    Str::uuid() . '.' . $request->file('receipt')->extension(),
    'local'
);
```
Two more small findings from that history dig, both harmless but worth knowing about: the old upload dropzone's CSS (`.upload-zone` rules, `form.blade.php:169-188`) is still sitting in the file as dead CSS — no element uses that class anymore, so it'll just be reused, not conflict, once the field comes back; and `resources/views/mail/purchase_confirmation.blade.php` is a **separate, unrelated** orphan (its Mailable, `PurchaseConfirmationMail`, was deleted years earlier in an unconnected cleanup commit `991be53`) — don't confuse it with `OrderReceivedMail`/`OrderPendingReviewMail`, it's not part of this feature.

---

## A2 — Method selection: proposal, and where I'm challenging your wording

**Where the choice happens:** I recommend the SAME step, not a new one — plan is already chosen via `/purchase/{plan}`; duration and coupon are already chosen on `form.blade.php`. I'd place the method choice as the **last decision on that same page**, directly above the submit button, because:
- The manual-instructions block needs the final priced total (after duration + season + coupon) to show "amount to transfer" — that's only known once duration/coupon are picked, which already happens on this page today.
- This matches the *original* pre-Batch-5 structure (form + receipt upload were one page, one submission) — I'm not introducing an extra step, I'm restoring the one that existed.

**What it looks like:**
- **Currency ineligible for manual** (per A3): form renders exactly as it does today — one Paymob CTA, no picker at all. The customer must never see a method they can't use — a picker with a greyed-out/disabled option is not acceptable per your own framing; it must not render at all.
- **Currency eligible for both**: a simple two-tab/radio choice ("ادفع الآن بالبطاقة" / "تحويل بنكي محلي") right above the CTA. Selecting "بطاقة" shows today's flow unchanged. Selecting "تحويل بنكي" reveals `<x-web.payment-instructions>` (bank/InstaPay details) + the receipt-upload field, and swaps the button to "إرسال الطلب" (no card redirect).
- Both options **post to the same route** (`purchase.pay`, i.e. `initiatePayment()`), carrying a `payment_method` field (`card` default / `manual`). One controller branches near the top — pricing/coupon/season code stays fully shared, only the tail (Paymob intention vs. receipt storage + subscription creation with `GATEWAY_MANUAL`) differs. This is deliberately the smallest change to `PurchaseController`, not a parallel controller.

**Challenge to your wording — "certain countries pay by local bank transfer":** the code today doesn't know "countries" for this purpose, it knows **currency** (`session('currency')`, one of `SAR/EGP/TND/USD` — `CurrencyService::COUNTRY_CURRENCY` only maps `SA→SAR, EG→EGP, TN→TND`; every other country on Earth defaults to `USD`). And there's a live, unauthenticated, no-confirmation **currency switcher** (`POST /currency/switch`, `CurrencyController.php`) — any visitor can set their session currency to anything in one click. So "certain countries" in practice must mean "certain **currencies**," and eligibility has to be evaluated **each time**, off whatever currency is currently in the session, not off a one-time IP-derived country. I've written A3/A6 around currency, not country — flag if you actually meant something stricter (e.g., re-verify against the IP-derived `detected_country` session value, which the middleware also stores and which is *not* directly changeable by the customer).

**Second challenge — the existing `sa_world` bucket doesn't match your rule.** Today, `USD` (i.e., literally every non-SA/EG/TN country, the entire "rest of world") is mapped to the *same* manual method as Saudi Arabia — an STC Bank account, in what is implicitly SAR terms. That is not "local currency for local country," it's "the whole rest of the world gets shown a Saudi bank account." Your new rule as stated ("every other country pays through Paymob, in EGP") reads to me as: only genuinely local pairs (SA, EG, TN — the three actually mapped today) keep manual transfer; the `USD`/rest-of-world bucket loses its manual option and becomes Paymob-only. A3 is written around that reading — confirm or correct it in A6.

---

## A3 — Eligibility rules: proposed config structure

Restructuring `config/payment.php` to be **one row per currency**, with an explicit boolean and no indirection through a separate `currency_to_method` map (fewer moving parts, matches your ask for "data, not conditionals"). Using the **real, existing** bank/InstaPay data already sitting in the current config — not placeholders, since real ones already exist and there's no reason to fake them:

```php
// config/payment.php

'manual' => [

    'SAR' => [
        'enabled'       => true,               // toggle per-currency, no deploy needed
        'country_label' => 'للعملاء في السعودية',
        'type'          => 'bank_transfer',
        'bank_name'     => 'STC Bank',
        'account_name'  => 'محمود عبدالله',
        'account_number'=> '1028992404',
        'iban'          => 'SA7178000000001028992404',
    ],

    'EGP' => [
        'enabled'       => true,
        'country_label' => 'للعملاء في مصر',
        'type'          => 'instapay',
        'link'          => 'https://ipn.eg/S/mindfitbro/instapay/4s2ZPS',
        'instapay_id'   => 'mindfitbro@instapay',
        'phone'         => '01098630291',
    ],

    'TND' => [
        'enabled'       => true,
        'country_label' => 'للعملاء في تونس',
        'type'          => 'bank_transfer',
        'bank_name'     => 'الشركة التونسية للبنك (STB)',
        'account_name'  => 'Salim Taboubi',
        'rib'           => '10404100144006978896',
        'swift'         => 'STBKTNTT',
    ],

    // "Rest of world" — no local rail exists to gate on. Paymob/EGP-only,
    // per your stated rule. Flip 'enabled' to true + fill in details only
    // if you decide otherwise (A6, question 2).
    'USD' => [
        'enabled' => false,
    ],

],
```

A helper on `CurrencyService` (or a small new `PaymentEligibilityService` if you'd rather keep `CurrencyService` presentation-only) becomes the single call site every layer uses:

```php
public function manualAllowed(?string $currency = null): bool
{
    return (bool) config('payment.manual.' . ($currency ?? $this->current()) . '.enabled', false);
}
```

`showForm()` computes this once and passes it to the view; `initiatePayment()` re-checks it server-side before honoring `payment_method=manual` in the POST (never trust the client's claim of which method they picked — same principle already applied to price). Paymob eligibility doesn't need a matching per-currency flag — it's effectively "always," gated only by the existing global `services.paymob.enabled` kill switch, per D1 (Paymob is EGP-billed but accepts *display* in all four currencies via `FxConverter`).

**On the IP/VPN hole you asked me to flag — concrete, not theoretical:** eligibility gated on `session('currency')` is not a security control and shouldn't be treated as one. `POST /currency/switch` already lets any visitor set their session currency to anything, no verification, today, unrelated to this feature. So a US-based visitor can already, in one click, make themselves "eligible" for Egyptian InstaPay instructions. **This barely matters in practice**: exploiting it doesn't get anyone anything for free — they'd still need to actually control an Egyptian InstaPay handle or a Saudi/Tunisian bank account to send real money through that rail, and every manual order still lands in `pending_review` for a **human** to check before `OrderApprovalService::approve()` ever runs (unchanged, A4). That human-review backstop is what actually protects this today, exactly as it did before Batch 5 — this reintroduction doesn't weaken it or need to add anything new to it. I'd treat currency-based gating as a **UX/cost-reduction measure** (stop non-local visitors from being confused/tempted into a foreign bank transfer with no reason to use it), not fraud prevention, and not spend engineering effort trying to harden it beyond that.

---

## A4 — Status model: side by side

| Step | Manual path | Paymob path |
|---|---|---|
| Order created | `status = pending_review`, `payment_gateway = manual`, `receipt_path` set | `status = awaiting_payment`, `payment_gateway = paymob`, `charged_amount_cents`/`fx_rate` set |
| Customer-facing "what's next" page | `success.blade.php`'s manual branch (3-step panel) — **already built, dormant** | `/paymob/callback` (Part B) |
| Resolution trigger | Admin clicks Approve/Reject in `admin/subscriptions` | Paymob webhook (HMAC-verified, server-to-server) |
| Convergence point | `OrderApprovalService::approve()` — **already accepts `pending_review` as valid input, unmodified, since it was written with this path in mind from day one** (`app/Services/OrderApprovalService.php:43`: `in_array($locked->status, [STATUS_PENDING_REVIEW, STATUS_AWAITING_PAYMENT])`) | Same method, same line, `STATUS_AWAITING_PAYMENT` branch |
| Rejection | `OrderRejectionService`/`SubscriptionsController::reject()` — unchanged, was never Paymob-specific | N/A today (Paymob failures go to `payment_failed`, not `rejected`) |
| Post-approval | Identical for both — assessment → self-booking → coach confirms → `active` (`DashboardController::confirmBooking()`) | Identical |

**Is `payment_gateway` (already `paymob`/`manual`) enough to distinguish the two?** Yes, for everything that currently needs distinguishing (which mail template fires, which "what's next" panel renders, which admin action resolves it, whether FX fields apply). I found no code path that needs a third value or a finer-grained distinction. The one place it must be **read** that doesn't today: Part B's redesigned `/paymob/callback` page needs a manual-review state, and `success.blade.php` already branches on this exact column — the pattern to copy already exists in the same codebase.

---

## A5 — Impact on Batch 5/6 decisions

- **Duplicate-order rule (C4).** Today's guard (`PurchaseController::initiatePayment():170-237`) blocks a new order whenever `pending_review/approved/active/awaiting_payment` exists, **regardless of gateway** — a `pending_review` manual order blocks a new attempt exactly like an `awaiting_payment` Paymob one does. **This still holds as correct** — one open order at a time is the right rule independent of method. **Can a customer switch methods after starting?** Concretely, **no, not today, and this reintroduction doesn't change that**: for an authenticated user with an existing `awaiting_payment` (Paymob) row, the guard *auto-resumes* it with a fresh Paymob intention (`:200-207`) — it doesn't ask which method they now want. For a `pending_review` (manual) row, the guard just blocks outright with no resume path at all. So once either path is started, the customer is locked into it until it resolves (paid/failed/rejected/approved) or they contact support. I'd keep this — letting someone abandon a manual order mid-review to switch to instant card payment is a reasonable thing to *want*, but it's a distinct, separate feature (a cancel-my-pending-order self-service action) that doesn't exist for Paymob orders either today. Flagging it as an A6 question rather than assuming you want it built now.

- **Coupon consumption (C3).** `Coupon::CONSUMED_STATUSES` already includes `STATUS_PENDING_REVIEW`, with a comment explaining exactly why: *"kept for legacy manual-flow rows still moving through that path"* (`app/Models/Coupon.php:31-36`). This was written anticipating exactly this reintroduction — **no code change needed.** Whether an un-reviewed manual order holding coupon capacity indefinitely is *acceptable* is an ops/SLA question, not a code one: the real mitigation is admins reviewing manual orders promptly (same as before Batch 5 — this isn't a new problem), not a code timeout. A code-level expiry (e.g., auto-reject after N days untouched) is a real option but is new scope beyond "bring back what existed" — flagged in A6, not assumed.

- **FX conversion.** For a manual order, `charged_currency`/`charged_amount_cents`/`fx_rate`/`fx_rate_source` should simply stay `NULL` — those columns exist specifically to describe the Paymob-side EGP conversion (D4) and have no meaning when the customer is transferring their own local currency directly to a local account. `total`/`currency`/`subtotal` (already-existing, gateway-agnostic columns) carry the manual order's real amount, exactly as they did pre-Batch-5. No FX code runs for this path at all — confirmed by re-reading `PurchaseController.php:298-306`, the `FxConverter::toEgpCents()` call sits inside the Paymob-only tail, not the shared prefix.

- **Abandoned-payment sweeper.** Doesn't exist yet — I found no command referencing `payment_intended_at` anywhere in `app/Console/` (grep came back empty), despite a composite DB index (`['status', 'payment_intended_at']`) already built for it with a comment naming it *"the Batch 7 abandoned-payment sweeper"* (`database/migrations/2026_08_30_100000_add_paymob_fields_to_subscriptions.php:37`). So there's nothing currently at risk of touching manual orders — but **whenever that sweeper is built**, it must filter `payment_gateway = 'paymob'` explicitly (not just `status = 'awaiting_payment'`, which is Paymob-only anyway by construction — manual orders never enter that status) so this is naturally safe by the existing status vocabulary alone, not something requiring a new guard.

- **The admin "emergency manual activation" button.** I don't find a *separate* button by that name anywhere — I believe you mean the existing Approve/Reject buttons on `admin/subscriptions/{id}` (the audit's Section B calls these "manual override" — same concept, different label). Those were never removed, are exactly `OrderApprovalService::approve()`/the reject equivalent, and remain necessary regardless of this feature (Paymob outages, disputes, refund handling still need a manual path). Once manual-transfer orders exist again, this button stops being purely an "emergency/fallback" tool and becomes the **primary, everyday** way those specific orders get resolved — same code, no change needed, just a shift in how often it's used.

---

## A6 — Open questions for you

1. **Does Egypt keep a bank-transfer option too, or is Egypt card-only?** Config today has EGP → InstaPay (`'eg'` method key) fully filled in and ready. Since Egypt is also Paymob's home market, EGP customers arguably have the *smoothest* card experience of any currency (no FX conversion at all) — is InstaPay still wanted alongside that, or was EGP meant to be Paymob-only precisely because it needs no conversion?
2. **Exactly which currencies/countries get manual transfer, and does anything change about the account details?** I've proposed keeping exactly SAR/EGP/TND as-is (real data already in config, presumably still valid) and dropping the `USD`/rest-of-world "sa_world" bucket per my reading of your rule (A2) — confirm, or tell me which countries you actually mean and what account/handle belongs to each if different from what's already configured.
3. **If eligible for local transfer, can they choose card instead?** I'm assuming **yes, always** — Paymob stays available to everyone, all the time; manual is the *additional* option, never a replacement. Confirm.
4. **Per-currency manual toggle, config only, no deploy?** Proposed structure in A3 already gives you exactly this (`'enabled' => true/false` per currency) — confirming this is the shape you want, or whether you'd rather this live in an admin-panel-editable DB row instead of a config file (bigger scope: a settings UI, migration, admin form — tell me if that's actually what you want instead of a `.php` config edit).
5. *(New question this research surfaced, not in your original list)* **Should the choice be gated on `session('currency')` (customer-changeable, A2/A3) or on `session('detected_country')` (IP-derived, set once by `DetectCurrency` middleware, not directly changeable by the customer)?** I've written A2/A3 around currency because that's what every existing piece of payment code already keys off of, and country isn't consistently available (only 3 codes are ever detected — `SA/EG/TN` — everything else is `null`, per `CurrencyService::COUNTRY_CURRENCY`). But if you want a tighter gate than "whatever currency the visitor last clicked," country is the only other signal that exists, and it would need new plumbing (nothing reads `detected_country` for a business decision today, only for the debug endpoint).

---

## Not part of this report — deferred to your review

Per your instructions, no manual-flow implementation code has been written. Once you've reviewed and adjusted A1-A6, the actual work is (in the order I'd tackle it, for your reference, not started):
1. `config/payment.php` restructure (A3).
2. `PurchaseController::initiatePayment()` branch on `payment_method` + eligibility re-check.
3. `form.blade.php`: method picker (conditional render) + restored receipt field + rework `<x-web.payment-instructions>`'s `$subscription`-shaped prop so it works pre-submission (it currently expects a persisted subscription to read `total`/`currency`/`payment_method_key` from — pre-submission there isn't one yet; needs to accept raw `currency`+`total` instead, or a lightweight computed-but-unsaved object).
4. Re-enable `OrderReceivedMail`/`OrderPendingReviewMail` dispatch.
5. Tests: eligibility gating, the branch in `initiatePayment()`, receipt validation/storage, both mails, and confirming `OrderApprovalService`/rejection still behave identically for a manual order (they should, unmodified — a regression test locking that in is still worth having).
