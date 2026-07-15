<?php

namespace App\Console\Commands;

use App\Mail\SubscriptionStartedMail;
use App\Models\Subscription;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class NotifySubscriptionStart extends Command
{
    protected $signature   = 'subscriptions:notify-start';
    protected $description = 'Send a "subscription started" email to users whose subscription start_date is today';

    public function handle(): int
    {
        $today = Carbon::today();
        $total = 0;

        $this->components->info("Checking subscriptions starting on: {$today->toDateString()}");

        Subscription::query()
            ->where('status', Subscription::STATUS_ACTIVE)
            ->whereDate('start_date', $today)
            ->with(['user', 'plan'])
            ->chunkById(200, function ($chunk) use (&$total) {

                foreach ($chunk as $sub) {
                    $email = $sub->user?->email ?? $sub->guest_email;
                    $name  = $sub->user?->name  ?? $sub->guest_name ?? 'العميل';

                    if ($email) {
                        try {
                            Mail::to($email)->send(new SubscriptionStartedMail($sub, $name));

                            $this->components->twoColumnDetail(
                                "  Subscription #{$sub->id} — {$email}",
                                'start notification sent'
                            );
                        } catch (\Throwable $e) {
                            Log::error('SubscriptionStartedMail failed', [
                                'sub' => $sub->id,
                                'err' => $e->getMessage(),
                            ]);

                            $this->components->error("Failed for sub #{$sub->id}: {$e->getMessage()}");
                        }
                    }

                    $total++;
                }
            });

        if ($total > 0) {
            $this->components->info("{$total} subscription(s) start notification(s) sent.");
        } else {
            $this->components->info('No subscriptions starting today.');
        }

        return Command::SUCCESS;
    }
}
