<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Package;
use App\Models\Addon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Default Owner Account
        User::updateOrCreate(
            ['email' => 'owner@nkbarokah.com'],
            [
                'name'     => 'Owner NK Barokah',
                'password' => Hash::make('password'),
            ]
        );

        // Sample Master Packages
        $packages = [
            [
                'name'        => 'Silver Package',
                'slug'        => 'silver-package',
                'base_price'  => 25000000,
                'description' => 'Paket intimate wedding meliputi dekorasi pelaminan standar 6m, rias & busana akad + resepsi, dan dokumentasi foto 1 hari.',
                'is_active'   => true,
            ],
            [
                'name'        => 'Gold Royal Package',
                'slug'        => 'gold-royal-package',
                'base_price'  => 45000000,
                'description' => 'Paket favorit lengkap: dekorasi modern 8m, tim WO hari H (6 crew), sound system 5000 watt, serta foto & video cinematic.',
                'is_active'   => true,
            ],
            [
                'name'        => 'Platinum Luxury Package',
                'slug'        => 'platinum-luxury-package',
                'base_price'  => 75000000,
                'description' => 'Layanan VIP eksklusif: Full planner dari nol, dekorasi bunga segar 12m, live acoustic band, master of ceremony, serta drone 4K.',
                'is_active'   => true,
            ],
        ];

        foreach ($packages as $pkg) {
            Package::updateOrCreate(['slug' => $pkg['slug']], $pkg);
        }

        // Sample Master Addons
        $addons = [
            [
                'name'       => 'Kursi Futura + Cover Ketat',
                'unit'       => 'pcs',
                'unit_price' => 15000,
                'is_active'  => true,
            ],
            [
                'name'       => 'Kursi Tiffany Gold VIP',
                'unit'       => 'pcs',
                'unit_price' => 35000,
                'is_active'  => true,
            ],
            [
                'name'       => 'Extra Porsi Buffet Katering',
                'unit'       => 'porsi',
                'unit_price' => 50000,
                'is_active'  => true,
            ],
            [
                'name'       => 'Dokumentasi Drone Aerial 4K',
                'unit'       => 'sesi',
                'unit_price' => 1500000,
                'is_active'  => true,
            ],
        ];

        foreach ($addons as $addon) {
            Addon::updateOrCreate(['name' => $addon['name']], $addon);
        }
    }
}
