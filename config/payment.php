<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Manual bank transfer / InstaPay — reintroduced alongside Paymob
    |--------------------------------------------------------------------------
    | Decision (docs/dual-payment-plan.md, A1-A6, dated 2026-09-01): manual
    | transfer is a SECOND payment method, additive to Paymob, never a
    | replacement. Eligibility is read from App\Services\Web\
    | PaymentEligibilityService, which gates on the visitor's IP-DETECTED
    | country (session('detected_country'), set once by DetectCurrency and
    | NOT customer-changeable) — never on the freely-switchable display
    | currency (session('currency'), changeable via POST /currency/switch
    | with zero verification). One row per currency, 'enabled' is the single
    | on/off switch — flip it to add/remove a currency from manual transfer
    | without touching any other code. A DB-backed (admin-editable) version
    | of this toggle is a known future step, not needed before the first
    | real manual transaction goes through this config-file version.
    |
    | 'USD' has NO bank details on purpose: it's the "rest of world" catch-
    | all currency (CurrencyService::fromCountryCode() defaults any country
    | NOT in COUNTRY_CURRENCY to USD) and never had a real local payment
    | rail — the old config pointed this entire bucket at a Saudi bank
    | account, which was a bug (a Kuwaiti or American customer has no reason
    | to wire riyals to Saudi Arabia), not a decision. Confirmed nothing else
    | in the app reads a 'USD' entry here.
    */

    'manual' => [

        'SAR' => [
            'enabled'        => true,
            'country_label'  => 'للعملاء في السعودية',
            'type'           => 'bank_transfer',
            'bank_name'      => 'STC Bank',
            'account_name'   => 'محمود عبدالله',
            'account_number' => '1028992404',
            'iban'           => 'SA7178000000001028992404',
        ],

        'EGP' => [
            'enabled'      => true,
            'country_label'=> 'للعملاء في مصر',
            'type'         => 'instapay',
            'link'         => 'https://ipn.eg/S/mindfitbro/instapay/4s2ZPS',
            'instapay_id'  => 'mindfitbro@instapay',
            'phone'        => '01098630291',
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

        'USD' => [
            'enabled' => false,
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Manual review staleness thresholds (step 6, admin surfacing only)
    |--------------------------------------------------------------------------
    | Purely for the admin UI to flag an unreviewed manual (pending_review)
    | order visually — never auto-rejects, auto-expires, or otherwise mutates
    | anything on its own. A human always decides. Measured from
    | payment_intended_at (set at manual creation AND refreshed on a
    | mid-flight switch-to-manual — see PurchaseController::switchToManual()),
    | not created_at, so the "waiting" clock reflects when THIS review
    | actually started.
    */

    'manual_review_thresholds' => [
        'warning_hours' => (int) env('MANUAL_REVIEW_WARNING_HOURS', 48),
        'urgent_hours'  => (int) env('MANUAL_REVIEW_URGENT_HOURS', 168),
    ],

    /*
    |--------------------------------------------------------------------------
    | FX: converting a displayed price into the EGP amount actually charged
    |--------------------------------------------------------------------------
    | The visitor's price is shown in whatever currency DetectCurrency resolved
    | (SAR/EGP/TND/USD), but Paymob only charges in EGP. At order-creation time
    | the persisted `total` is converted to EGP by App\Services\FxConverter.
    |
    | Since Batch 5.5, the BASE rate itself is no longer read from here — it's
    | fetched on a schedule (`php artisan fx:refresh`, App\Services\Fx\*) into
    | the `fx_rates` DB table, which FxConverter reads and applies a 3-tier
    | staleness policy to (fresh / stale-but-usable / expired). `egp_rates`
    | below is now purely a LAST-RESORT FALLBACK: used only when a currency's
    | stored rate is missing or has gone fully expired (>= max_age_hours) —
    | i.e. exactly when the scheduled fetch has been broken for a long time.
    | It is manually maintained (not fetched); update it by hand if you want a
    | safety net for a prolonged fetch outage. A NULL entry means "no
    | fallback" — FxConverter fails loudly for that currency in that case
    | rather than silently defaulting to 1.0 or treating the source amount as
    | already being EGP.
    |
    | Effective rate = (stored or fallback rate) * (1 + markup_percent / 100).
    | The result is then rounded per `rounding` before being sent to Paymob.
    */

    'fx' => [

        'egp_rates' => [
            'EGP' => 1.0,
            'SAR' => null, // TODO: set a manual fallback rate if you want one
            'TND' => null, // TODO: set a manual fallback rate if you want one
            'USD' => null, // TODO: set a manual fallback rate if you want one
        ],

        // Safety margin on top of the base rate, so a stale rate table doesn't
        // make us undercharge and eat the difference on every order.
        'markup_percent' => (float) env('PAYMENT_FX_MARKUP_PERCENT', 0),

        // Rounding strategy for the final EGP amount. One of:
        // 'none' | 'up_to_nearest_5' | 'up_to_nearest_10'.
        'rounding' => env('PAYMENT_FX_ROUNDING', 'up_to_nearest_5'),

        // Below this age, a stored rate is used silently.
        'stale_after_hours' => (int) env('FX_STALE_AFTER_HOURS', 48),
        // Between stale_after_hours and this, a stored rate is still used but
        // logs a warning on every conversion — the visible symptom of a
        // scheduler that stopped running (audit Risk D-7: the cPanel cron is
        // unverified). At or beyond this age, the rate is treated as expired.
        'max_age_hours' => (int) env('FX_MAX_AGE_HOURS', 168),

        // fx:refresh rejects a freshly-fetched rate that deviates more than
        // this percent from the currently stored one, logs CRITICAL, and
        // keeps the old value — a bad/mis-parsed fetch silently mispricing
        // every order is worse than one more day of a stale-but-sane rate.
        'sanity_deviation_percent' => (float) env('FX_SANITY_DEVIATION_PERCENT', 15),

        // Metadata for whoever hand-edits egp_rates above — not read by code.
        'fx_rate_source'     => 'manual-config',
        'fx_rate_updated_at' => null,

    ],

];
