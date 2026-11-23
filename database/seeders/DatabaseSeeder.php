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
                'pin'      => '123456',
            ]
        );

        // 5 Master Packages Resmi Sesuai Pricelist 2026 NK Barokah
        $packages = [
            [
                'name'        => 'Tenda Set',
                'slug'        => 'tenda-set',
                'base_price'  => 1750000,
                'description' => 'Tenda 4x6, 20 kursi plastik + cover, 3 meja kotak, dan 1 kipas tempel.',
                'is_active'   => true,
            ],
            [
                'name'        => 'Paket 1',
                'slug'        => 'paket-1',
                'base_price'  => 4500000,
                'description' => 'Tenda 60m² (4x15 atau 6x10), 50 kursi Futura + cover, 2 kipas tempel, 4 meja kotak, dan lighting standard. (Tambahan tenda: Rp 1.750.000/set atau Rp 55.000/m).',
                'is_active'   => true,
            ],
            [
                'name'        => 'Paket 2',
                'slug'        => 'paket-2',
                'base_price'  => 7000000,
                'description' => 'Tenda 96m² (6x16 atau 8x12), 70 kursi Futura + cover, 1 kipas tempel, 1 blower air, 4 meja kotak, dan lighting standard. (Tambahan tenda: Rp 1.750.000/set atau Rp 55.000/m).',
                'is_active'   => true,
            ],
            [
                'name'        => 'Tenda + Decor 6M',
                'slug'        => 'tenda-decor-6m',
                'base_price'  => 13000000,
                'description' => 'Tenda Max 72m² (6x12 / 4x18), 70 kursi Futura, 2 kipas tempel, 1 blower air, 4 meja kotak, panggung 3x6, full karpet, set prasmanan, 2 pondokan. Dekorasi: pelaminan max 6m, melamin floor, mini garden, artificial mix fresh flower, welcome gate, welcome sign, hand bouquet, lighting standard, 2 kotak angpau, 2 standing frame.',
                'is_active'   => true,
            ],
            [
                'name'        => 'Tenda + Decor 8M',
                'slug'        => 'tenda-decor-8m',
                'base_price'  => 16000000,
                'description' => 'Tenda Max 96m² (6x16 / 8x12), 70 kursi Futura, 2 kipas tempel, 1 blower air, 4 meja kotak, panggung 3x8, full karpet, set prasmanan, 2 pondokan. Dekorasi: pelaminan max 8m, melamin floor, mini garden, artificial mix fresh flower, welcome gate, welcome sign, hand bouquet, lighting standard, 2 kotak angpau, 2 standing frame.',
                'is_active'   => true,
            ],
        ];

        foreach ($packages as $pkg) {
            Package::updateOrCreate(['slug' => $pkg['slug']], $pkg);
        }

        // 20 Master Add-ons Resmi Sesuai Pricelist 2026 NK Barokah
        $addons = [
            // Kursi
            [
                'name'       => 'Kursi Plastik + Cover',
                'unit'       => 'pcs',
                'unit_price' => 10000,
                'is_active'  => true,
            ],
            [
                'name'       => 'Kursi Banquet / Futura + Cover',
                'unit'       => 'pcs',
                'unit_price' => 15000,
                'is_active'  => true,
            ],
            // Kipas & Blower
            [
                'name'       => 'Kipas Tempel',
                'unit'       => 'unit',
                'unit_price' => 150000,
                'is_active'  => true,
            ],
            [
                'name'       => 'Kipas Stand',
                'unit'       => 'unit',
                'unit_price' => 250000,
                'is_active'  => true,
            ],
            [
                'name'       => 'Kipas Blower Air',
                'unit'       => 'unit',
                'unit_price' => 400000,
                'is_active'  => true,
            ],
            // Panggung & Flooring
            [
                'name'       => 'Panggung 4x3',
                'unit'       => 'unit',
                'unit_price' => 450000,
                'is_active'  => true,
            ],
            [
                'name'       => 'Panggung 6x3',
                'unit'       => 'unit',
                'unit_price' => 650000,
                'is_active'  => true,
            ],
            [
                'name'       => 'Panggung 8x3',
                'unit'       => 'unit',
                'unit_price' => 850000,
                'is_active'  => true,
            ],
            [
                'name'       => 'Flooring (15cm & 20cm)',
                'unit'       => 'meter',
                'unit_price' => 35000,
                'is_active'  => true,
            ],
            // Meja
            [
                'name'       => 'Meja Kotak + Taplak',
                'unit'       => 'unit',
                'unit_price' => 35000,
                'is_active'  => true,
            ],
            [
                'name'       => 'Meja Bulat + Taplak',
                'unit'       => 'unit',
                'unit_price' => 60000,
                'is_active'  => true,
            ],
            // Perlengkapan & Dekoratif
            [
                'name'       => 'Karpet Jalan / Venue',
                'unit'       => 'meter',
                'unit_price' => 10000,
                'is_active'  => true,
            ],
            [
                'name'       => 'Lampu Tambahan',
                'unit'       => 'pcs',
                'unit_price' => 50000,
                'is_active'  => true,
            ],
            [
                'name'       => 'Cover Dinding Tenda',
                'unit'       => 'pcs',
                'unit_price' => 30000,
                'is_active'  => true,
            ],
            // Sound System & Genset
            [
                'name'       => 'Sound Biasa + Genset',
                'unit'       => 'hari',
                'unit_price' => 1200000,
                'is_active'  => true,
            ],
            [
                'name'       => 'Sound Band + Genset',
                'unit'       => 'hari',
                'unit_price' => 1700000,
                'is_active'  => true,
            ],
            // Prasmanan & Pondokan
            [
                'name'       => 'Set Prasmanan Lengkap (200 Porsi)',
                'unit'       => 'set',
                'unit_price' => 1300000,
                'is_active'  => true,
            ],
            [
                'name'       => 'Pondokan Gubukan',
                'unit'       => 'pcs',
                'unit_price' => 100000,
                'is_active'  => true,
            ],
            // Tambahan Tenda
            [
                'name'       => 'Tambahan Tenda per Set (Inc. Meja & Kursi)',
                'unit'       => 'set',
                'unit_price' => 1750000,
                'is_active'  => true,
            ],
            [
                'name'       => 'Tambahan Tenda Polos',
                'unit'       => 'meter',
                'unit_price' => 55000,
                'is_active'  => true,
            ],
        ];

        foreach ($addons as $addon) {
            Addon::updateOrCreate(['name' => $addon['name']], $addon);
        }

        $this->call(BookingSeeder::class);
    }
}
