<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50)->unique();
            $table->enum('type', ['percentage', 'fixed'])->default('percentage');
            $table->decimal('value', 8, 2); // 10.00 = 10%
            $table->boolean('is_active')->default(true);
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
        });

        // Seed the existing hardcoded coupons
        $now = now();
        DB::table('coupons')->insert([
            ['code' => 'MFB10',      'type' => 'percentage', 'value' => 10, 'is_active' => true, 'expires_at' => null, 'created_at' => $now, 'updated_at' => $now],
            ['code' => 'MINDFITBRO', 'type' => 'percentage', 'value' => 10, 'is_active' => true, 'expires_at' => null, 'created_at' => $now, 'updated_at' => $now],
            ['code' => 'WELCOME',    'type' => 'percentage', 'value' => 10, 'is_active' => true, 'expires_at' => null, 'created_at' => $now, 'updated_at' => $now],
            ['code' => 'EID2025',    'type' => 'percentage', 'value' => 10, 'is_active' => true, 'expires_at' => null, 'created_at' => $now, 'updated_at' => $now],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('coupons');
    }
};
