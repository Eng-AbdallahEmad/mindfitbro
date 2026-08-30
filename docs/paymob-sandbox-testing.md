# Paymob sandbox testing notes

## Integration IDs (test mode, both EGP)

| Integration | ID | Channel | Env key |
|---|---|---|---|
| Card (VPC) | 5735562 | online | `PAYMOB_INTEGRATION_ID_CARD` |
| Wallet (UIG) | 5886426 | online_new | `PAYMOB_INTEGRATION_ID_WALLET` |

These were confirmed backwards once already — if a comment, docblock, or future doc note claims 5886426 is the card integration, that's wrong; fix it.

## Checkout URL template — CONFIRMED

```
{base}/unifiedcheckout/?publicKey={public_key}&clientSecret={client_secret}
```

Confirmed by loading the URL for a real test intention: renders Paymob's Unified Checkout with the correct EGP amount in test-mode context.

## Test credentials (Paymob's official test-credentials page)

- Mastercard: `5123456789012346` / `5123450000000008`
- Visa: `4111111111111111`
- Cardholder name: `Test Account`, expiry `01/39`, CVV `123`
- Wallet number: `01010101010`, MPin `123456`, OTP `123456`

## Important: phone numbers in sandbox mode

**A real phone number will always fail in Paymob's test mode** — the sandbox never reaches real wallet/OTP providers. Only Paymob's own published test wallet number (`01010101010`) works for wallet-flow testing.

This matters for the checkout phone field (Batch 5 C1, `billing_data.phone_number`): if a sandbox wallet payment is rejected when a real customer phone number is entered, that is expected sandbox behavior, **not** a validation bug in the checkout form or in `PaymobClient`. Don't "fix" this by loosening validation or changing the billing_data mapping — it will work correctly in live mode with real numbers.
