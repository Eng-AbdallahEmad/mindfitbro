<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('plans', function (Blueprint $table) {
            $table->string('style_variant', 20)->default('outline')->after('popular');
        });

        // Backfill: map existing btn_class values to variants
        // accent: contains 'bg-accent'
        DB::table('plans')
            ->where('btn_class', 'like', '%bg-accent%')
            ->update(['style_variant' => 'accent']);

        // solid: contains 'bg-primary' but NOT 'text-primary' (to avoid outline confusion)
        DB::table('plans')
            ->where('btn_class', 'like', '%bg-primary%')
            ->where('btn_class', 'not like', '%text-primary%')
            ->update(['style_variant' => 'solid']);

        // Everything else (including NULL / empty) stays as default 'outline' — no further action needed.

        Schema::table('plans', function (Blueprint $table) {
            $table->dropColumn('btn_class');
        });
    }

    public function down(): void
    {
        Schema::table('plans', function (Blueprint $table) {
            $table->string('btn_class')->nullable()->after('popular');
        });

        DB::table('plans')->where('style_variant', 'outline')->update(['btn_class' => 'border-2 border-primary text-primary hover:bg-blue-50']);
        DB::table('plans')->where('style_variant', 'solid')->update(['btn_class'   => 'bg-primary text-white hover:bg-primaryDark']);
        DB::table('plans')->where('style_variant', 'accent')->update(['btn_class'  => 'bg-accent text-darkBg hover:bg-yellow-300']);

        Schema::table('plans', function (Blueprint $table) {
            $table->dropColumn('style_variant');
        });
    }
};
