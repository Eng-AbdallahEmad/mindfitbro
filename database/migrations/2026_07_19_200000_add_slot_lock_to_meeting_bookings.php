<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Pre-flight: abort if duplicate active bookings already exist
        $duplicates = DB::select("
            SELECT meeting_date, meeting_time, COUNT(*) AS cnt
            FROM meeting_bookings
            WHERE status IN ('pending', 'confirmed')
            GROUP BY meeting_date, meeting_time
            HAVING COUNT(*) > 1
        ");

        if (! empty($duplicates)) {
            $details = collect($duplicates)
                ->map(fn ($d) => "{$d->meeting_date} {$d->meeting_time} ({$d->cnt}×)")
                ->implode(', ');

            throw new \RuntimeException(
                "Migration aborted: duplicate active bookings found — {$details}. " .
                'Resolve them manually before running this migration.'
            );
        }

        // 2. Add column (nullable, no index yet)
        Schema::table('meeting_bookings', function (Blueprint $table) {
            $table->string('slot_lock', 30)->nullable()->after('meeting_time');
        });

        // 3. Backfill slot_lock for all active bookings in "Y-m-d H:i" format
        DB::statement("
            UPDATE meeting_bookings
            SET slot_lock = CONCAT(
                DATE_FORMAT(meeting_date, '%Y-%m-%d'),
                ' ',
                TIME_FORMAT(meeting_time, '%H:%i')
            )
            WHERE status IN ('pending', 'confirmed')
        ");

        // 4. Add unique index (NULLs don't conflict, so cancelled/completed rows are fine)
        Schema::table('meeting_bookings', function (Blueprint $table) {
            $table->unique('slot_lock');
        });
    }

    public function down(): void
    {
        Schema::table('meeting_bookings', function (Blueprint $table) {
            $table->dropUnique(['slot_lock']);
            $table->dropColumn('slot_lock');
        });
    }
};
