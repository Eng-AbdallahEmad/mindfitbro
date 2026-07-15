<?php

namespace App\Console\Commands;

use App\Mail\SubscriptionExpiryMail;
use App\Models\Subscription;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ExpireSubscriptions extends Command
{
    protected $signature   = 'subscriptions:expire';
    protected $description = 'Mark active subscriptions as expired when their end_date has passed';

    public function handle(): int
    {
        $today     = Carbon::today();
        $expiredAt = now()->toDateTimeString();
        $total     = 0;

        $this->components->info("Running expiry check for date: {$today->toDateString()}");

        Subscription::query()
            ->whereIn('status', [Subscription::STATUS_ACTIVE, Subscription::STATUS_APPROVED])
            ->whereDate('end_date', '<', $today)
            ->with(['user', 'plan'])
            ->chunkById(200, function ($chunk) use (&$total) {

                $ids = $chunk->pluck('id')->all();
                Subscription::whereIn('id', $ids)->update(['status' => Subscription::STATUS_EXPIRED]);

                foreach ($chunk as $sub) {
                    $email = $sub->user?->email ?? $sub->guest_email;
                    $name  = $sub->user?->name  ?? $sub->guest_name  ?? 'العميل';

                    if ($email) {
                        try {
                            Mail::to($email)->send(new SubscriptionExpiryMail($sub, $name));
                        } catch (\Throwable $e) {
                            Log::error('SubscriptionExpiryMail failed', [
                                'sub' => $sub->id,
                                'err' => $e->getMessage(),
                            ]);
                        }
                    }

                    $owner = $email ?? "sub#{$sub->id}";
                    $this->components->twoColumnDetail(
                        "  <fg=green>✓</> Subscription #{$sub->id} — {$owner}",
                        "end_date: {$sub->end_date}"
                    );
                }

                $total += count($ids);
            });

        if ($total > 0) {
            $this->components->info("{$total} subscription(s) marked as expired.");

            Log::channel('daily')->info('subscriptions:expire completed', [
                'expired_count' => $total,
                'run_at'        => $expiredAt,
                'checked_date'  => $today->toDateString(),
            ]);
        } else {
            $this->components->info('No subscriptions to expire today.');
        }

        return Command::SUCCESS;
    }
}
