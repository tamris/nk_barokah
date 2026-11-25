<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Seed the application's users table.
     */
    public function run(): void
    {
        $users = [
            // Akun Owner Utama
            [
                'email'             => 'owner@nkbarokah.com',
                'name'              => 'Owner NK Barokah',
                'role'              => User::ROLE_OWNER,
                'password'          => Hash::make('password'),
                'pin'               => '123456',
                'is_active'         => true,
                'email_verified_at' => now(),
            ],
            // Akun Admin / Co-Owner
            [
                'email'             => 'admin@nkbarokah.com',
                'name'              => 'Admin NK Barokah',
                'role'              => User::ROLE_OWNER,
                'password'          => Hash::make('adminpassword'),
                'pin'               => '246810',
                'is_active'         => true,
                'email_verified_at' => now(),
            ],
            // Akun Field Coordinator / Staff Lapangan (Shared Account)
            [
                'email'             => 'staff@nkbarokah.com',
                'name'              => 'Field Coordinator',
                'role'              => User::ROLE_STAFF,
                'password'          => Hash::make('staffpassword'),
                'pin'               => '112233',
                'is_active'         => true,
                'email_verified_at' => now(),
            ],
            // Akun Staff Nonaktif (untuk simulasi akun yang dinonaktifkan owner)
            [
                'email'             => 'staff.inactive@nkbarokah.com',
                'name'              => 'Staff Lapangan (Nonaktif)',
                'role'              => User::ROLE_STAFF,
                'password'          => Hash::make('staffpassword'),
                'pin'               => '445566',
                'is_active'         => false,
                'email_verified_at' => now(),
            ],
        ];

        foreach ($users as $data) {
            User::updateOrCreate(
                ['email' => $data['email']],
                $data
            );
        }
    }
}
