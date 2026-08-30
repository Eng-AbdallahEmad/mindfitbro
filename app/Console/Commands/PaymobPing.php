<?php

namespace App\Console\Commands;

use App\Models\Subscription;
use App\Services\Paymob\PaymobClient;
use App\Services\Paymob\PaymobRequestException;
use Illuminate\Console\Command;

class PaymobPing extends Command
{
    protected $signature = 'paymob:ping';

    protected $description = 'Create a real test Paymob intention with the configured credentials and print the resulting checkout URL (or the error). Refuses to run if PAYMOB_ENABLED is false or credentials are missing.';

    public function handle(PaymobClient $client): int
    {
        if (!config('services.paymob.enabled')) {
            $this->error('PAYMOB_ENABLED is false — refusing to run. Set it to true once you have real dashboard credentials to test.');

            return self::FAILURE;
        }

        $missing = collect([
            'PAYMOB_BASE_URL' => config('services.paymob.base_url'),
            'PAYMOB_SECRET_KEY' => config('services.paymob.secret_key'),
            'PAYMOB_PUBLIC_KEY' => config('services.paymob.public_key'),
            'PAYMOB_HMAC_SECRET' => config('services.paymob.hmac_secret'),
            'PAYMOB_INTEGRATION_ID_CARD' => config('services.paymob.integrations.card'),
        ])->filter(fn ($value) => empty($value))->keys();

        if ($missing->isNotEmpty()) {
            $this->error('Missing Paymob configuration: ' . $missing->implode(', '));

            return self::FAILURE;
        }

        $this->info('Creating a test intention against ' . config('services.paymob.base_url') . ' ...');

        // Never writes a real order row — an unsaved, throwaway
        // Subscription-shaped object with a 1.00 EGP test amount. id is set
        // to 0 (never a real subscription id) so the resulting
        // special_reference and logs are unmistakably a ping, not a real order.
        $subscription = new Subscription([
            'charged_amount_cents' => 100,
            'charged_currency' => 'EGP',
        ]);
        $subscription->id = 0;

        try {
            $intention = $client->createIntention($subscription, [
                'full_name' => 'Paymob Ping',
                'email' => 'paymob-ping@mindfitbro.com',
                // Paymob's own published test number (test-credentials page),
                // not a fabricated one — billing_data.phone_number no longer
                // has a placeholder fallback since Batch 5 C1 made it required.
                'phone_number' => '01010101010',
            ]);
        } catch (PaymobRequestException $e) {
            $this->error("Paymob request failed (HTTP " . ($e->httpStatus ?? 'none, connection error') . "): {$e->getMessage()}");

            if ($e->sanitizedResponseBody) {
                $this->line(json_encode($e->sanitizedResponseBody, JSON_PRETTY_PRINT));
            }

            return self::FAILURE;
        } catch (\Throwable $e) {
            $this->error('Unexpected error: ' . $e->getMessage());

            return self::FAILURE;
        }

        $this->info('Success.');
        $this->line("Intention id:   {$intention->intentionId}");
        $this->line("Paymob order id: {$intention->paymobOrderId}");
        $this->line('Checkout URL:');
        $this->line($intention->checkoutUrl);

        return self::SUCCESS;
    }
}
