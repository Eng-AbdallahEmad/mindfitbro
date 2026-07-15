<?php

namespace App\Console\Commands;

use App\Mail\SubscriptionReminderMail;
use App\Models\Subscription;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class RemindExpiringSubscriptions extends Command
{
    protected $signature   = 'subscriptions:remind';
    protected $description = 'Send renewal reminder emails for subscriptions expiring in 5 days';

    public function handle(): int
    {
        $targetDate = Carbon::today()->addDays(5);
        $total      = 0;

        $this->components->info("Checking for subscriptions expiring on: {$targetDate->toDateString()}");

        Subscription::query()
            ->whereIn('status', [Subscription::STATUS_ACTIVE, Subscription::STATUS_APPROVED])
            ->whereDate('end_date', $targetDate)
            ->with(['user', 'plan'])
            ->chunkById(200, function ($chunk) use (&$total) {
                foreach ($chunk as $sub) {
                    $email = $sub->user?->email ?? $sub->guest_email;
                    $name  = $sub->user?->name  ?? $sub->guest_name  ?? 'العميل';

                    if ($email) {
                        try {
                            Mail::to($email)->send(new SubscriptionReminderMail($sub, $name));
                        } catch (\Throwable $e) {
                            Log::error('SubscriptionReminderMail failed', [
                                'sub' => $sub->id,
                                'err' => $e->getMessage(),
                            ]);
                        }
                    }

                    $owner = $email ?? "sub#{$sub->id}";
                    $this->components->twoColumnDetail(
                        "  <fg=green>✓</> Subscription #{$sub->id} — {$owner}",
                        "ends: {$sub->end_date}"
                    );
                    $total++;
                }
            });

        if ($total > 0) {
            $this->components->info("{$total} reminder(s) sent.");
            Log::channel('daily')->info('subscriptions:remind completed', [
                'reminded_count' => $total,
                'target_date'    => $targetDate->toDateString(),
            ]);
        } else {
            $this->components->info('No subscriptions expiring in 5 days.');
        }

        return Command::SUCCESS;
    }
}
