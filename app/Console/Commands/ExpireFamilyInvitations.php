<?php

namespace App\Console\Commands;

use App\Models\FamilyInvitation;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ExpireFamilyInvitations extends Command
{
    protected $signature   = 'family-invitations:expire';
    protected $description = 'Mark pending family invitations as expired when their coupon has passed expires_at';

    public function handle(): int
    {
        $expired = FamilyInvitation::where('status', 'pending')
            ->whereHas('coupon', fn ($q) => $q->where('expires_at', '<', now()))
            ->with('coupon')
            ->get();

        if ($expired->isEmpty()) {
            $this->components->info('No pending invitations to expire.');
            return self::SUCCESS;
        }

        foreach ($expired as $invitation) {
            $invitation->markExpired();
        }

        $count = $expired->count();
        $this->components->info("Expired {$count} invitation(s).");
        Log::info("family-invitations:expire — expired {$count} record(s)");

        return self::SUCCESS;
    }
}
