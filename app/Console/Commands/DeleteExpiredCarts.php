<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Cart;
use Carbon\Carbon;

class DeleteExpiredCarts extends Command
{
    protected $signature = 'carts:cleanup';
    protected $description = 'Delete expired carts (inactive sessions)';

    public function handle()
    {
        // نحسب الوقت قبل 30 دقيقة
        $threshold = Carbon::now()->subMinutes(30);

        // حذف الكارت اللي session_id موجودة و قديمة
        $deleted = Cart::whereNotNull('session_id')
            ->where('updated_at', '<', $threshold)
            ->delete();

        $this->info("Deleted {$deleted} expired carts.");

        return Command::SUCCESS;
    }
}