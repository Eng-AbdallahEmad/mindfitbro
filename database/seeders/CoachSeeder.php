<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class CoachSeeder extends Seeder
{
    public function run(): void
    {
        $coaches = [
            [
                'name'     => 'سليم الطبوبي',
                'email'    => 'salim@mindfitbro.com',
                'username' => 'c_salim',
                'phone'    => '+966536906293',
                'password' => 'coach@salim',
            ],
            [
                'name'     => 'أحمد مصطفي',
                'email'    => 'ahmed@mindfitbro.com',
                'username' => 'c_ahmed',
                'phone'    => '+966500000004',
                'password' => 'coach@ahmed',
            ],
            [
                'name'     => 'محمود اهاب',
                'email'    => 'mahmoud@mindfitbro.com',
                'username' => 'c_mahmoud',
                'phone'    => '+966555555555',
                'password' => 'coach@mahmoud',
            ],
        ];

        foreach ($coaches as $coach) {
            User::updateOrCreate(
                ['email' => $coach['email']],
                [
                    'name'              => $coach['name'],
                    'username'          => $coach['username'],
                    'phone'             => $coach['phone'],
                    'gender'            => 'male',
                    'password'          => Hash::make($coach['password']),
                    'role'              => 'coach',
                    'status'            => 'active',
                    'email_verified_at' => now(),
                ]
            );
        }
    }
}