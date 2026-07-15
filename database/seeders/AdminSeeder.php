<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'admin@mindfitbro.com'],
            [
                'name'     => 'Admin',
                'username' => 'admin',
                'phone'    => '0000000000',
                'gender'   => 'male',
                'password' => Hash::make('Admin@123456'),
                'role'     => 'admin',
                'status'   => 'active',
            ]
        );
    }
}
