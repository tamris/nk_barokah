<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\BookingAddon;
use App\Models\Package;
use App\Models\Addon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class BookingSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Reset data booking & booking_addons saja
        if (DB::getDriverName() === 'mysql') {
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            BookingAddon::truncate();
            Booking::truncate();
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        } else {
            BookingAddon::query()->delete();
            Booking::query()->delete();
        }

        $packages = Package::all();
        $addons = Addon::all();

        if ($packages->isEmpty() || $addons->isEmpty()) {
            $this->call(DatabaseSeeder::class);
            $packages = Package::all();
            $addons = Addon::all();
        }

        // 50 Pasangan Pengantin Realistis Indonesia
        $couples = [
            // November 2026 (Selesai & Berjalan Hari Ini)
            ['date' => '2026-11-01', 'name' => 'Dimas & Anindya Putri',       'phone' => '081223344551', 'email' => 'dimas.anindya@gmail.com',      'pkg' => 0, 'status' => 'completed',            'pay' => 'lunas'],
            ['date' => '2026-11-05', 'name' => 'Fajar & Nabila Maharani',     'phone' => '081334455662', 'email' => 'fajar.nabila@yahoo.com',       'pkg' => 1, 'status' => 'completed',            'pay' => 'lunas'],
            ['date' => '2026-11-08', 'name' => 'Ilham & Annisa Rahmawati',    'phone' => '082145566773', 'email' => 'ilham.annisa@gmail.com',       'pkg' => 2, 'status' => 'completed',            'pay' => 'lunas'],
            ['date' => '2026-11-12', 'name' => 'Rizky & Amanda Permatasari',  'phone' => '085756677884', 'email' => 'rizky.amanda@outlook.com',     'pkg' => 1, 'status' => 'completed',            'pay' => 'lunas'],
            ['date' => '2026-11-15', 'name' => 'Bima & Citra Kirana',         'phone' => '081267788995', 'email' => 'bima.citra@gmail.com',         'pkg' => 0, 'status' => 'completed',            'pay' => 'lunas'],
            ['date' => '2026-11-20', 'name' => 'Muhammad Reza & Dian Sastro', 'phone' => '081378899006', 'email' => 'reza.dian@gmail.com',          'pkg' => 1, 'status' => 'confirmed',            'pay' => 'lunas'], // Hari ini!
            ['date' => '2026-11-20', 'name' => 'Aditya Pratama & Bella Safira','phone' => '082289900117', 'email' => 'aditya.bella@gmail.com',       'pkg' => 2, 'status' => 'pending_verification', 'pay' => 'dp'],   // Hari ini (Conflict demo)
            ['date' => '2026-11-22', 'name' => 'Bagus Wicaksono & Maya Indah','phone' => '085890011228', 'email' => 'bagus.maya@yahoo.co.id',     'pkg' => 0, 'status' => 'confirmed',            'pay' => 'dp'],
            ['date' => '2026-11-25', 'name' => 'Andi & Tiara Andini',         'phone' => '081201122339', 'email' => 'andi.tiara@gmail.com',         'pkg' => 1, 'status' => 'confirmed',            'pay' => 'lunas'],
            ['date' => '2026-11-28', 'name' => 'Bayu Kusuma & Rina Marlina',  'phone' => '081312233440', 'email' => 'bayu.rina@gmail.com',          'pkg' => 2, 'status' => 'confirmed',            'pay' => 'dp'],
            ['date' => '2026-11-29', 'name' => 'Doni & Siska Febriana',       'phone' => '082123344551', 'email' => 'doni.siska@outlook.com',       'pkg' => 0, 'status' => 'pending_verification', 'pay' => 'dp'],
            ['date' => '2026-11-30', 'name' => 'Eko Prasetyo & Winda Amalia',  'phone' => '085734455662', 'email' => 'eko.winda@gmail.com',          'pkg' => 1, 'status' => 'confirmed',            'pay' => 'dp'],

            // Desember 2026 (Musim Liburan & Tanggal Cantik 12-12)
            ['date' => '2026-12-05', 'name' => 'Gilang & Hesti Purwadinata',   'phone' => '081245566773', 'email' => 'gilang.hesti@gmail.com',       'pkg' => 1, 'status' => 'confirmed',            'pay' => 'dp'],
            ['date' => '2026-12-06', 'name' => 'Hendra & Jessica Mila',       'phone' => '081356677884', 'email' => 'hendra.jessica@gmail.com',     'pkg' => 2, 'status' => 'confirmed',            'pay' => 'lunas'],
            ['date' => '2026-12-10', 'name' => 'Joko & Kartika Sari',         'phone' => '082267788995', 'email' => 'joko.kartika@yahoo.com',       'pkg' => 0, 'status' => 'rejected',             'pay' => 'dp'],
            ['date' => '2026-12-12', 'name' => 'Kevin & Larasati Nugroho',    'phone' => '085878899006', 'email' => 'kevin.laras@gmail.com',        'pkg' => 2, 'status' => 'confirmed',            'pay' => 'lunas'],
            ['date' => '2026-12-12', 'name' => 'Lukman & Mega Utami',         'phone' => '081289900117', 'email' => 'lukman.mega@outlook.com',      'pkg' => 1, 'status' => 'confirmed',            'pay' => 'dp'], // Tanggal cantik 12-12
            ['date' => '2026-12-18', 'name' => 'Mario & Nadya Arina',         'phone' => '081390011228', 'email' => 'mario.nadya@gmail.com',        'pkg' => 0, 'status' => 'confirmed',            'pay' => 'dp'],
            ['date' => '2026-12-20', 'name' => 'Niko & Olivia Jensen',        'phone' => '082101122339', 'email' => 'niko.olivia@gmail.com',       'pkg' => 1, 'status' => 'pending_verification', 'pay' => 'dp'],
            ['date' => '2026-12-24', 'name' => 'Panji & Prilly Latuconsina',  'phone' => '085712233440', 'email' => 'panji.prilly@gmail.com',      'pkg' => 2, 'status' => 'confirmed',            'pay' => 'lunas'],
            ['date' => '2026-12-27', 'name' => 'Rangga & Qory Sandioriva',    'phone' => '081223344552', 'email' => 'rangga.qory@gmail.com',        'pkg' => 1, 'status' => 'confirmed',            'pay' => 'dp'],
            ['date' => '2026-12-31', 'name' => 'Satria & Raisa Andriana',     'phone' => '081334455663', 'email' => 'satria.raisa@gmail.com',       'pkg' => 2, 'status' => 'confirmed',            'pay' => 'lunas'],

            // Januari 2027 (Awal Tahun)
            ['date' => '2027-01-08', 'name' => 'Taufik & Syifa Hadju',        'phone' => '082145566774', 'email' => 'taufik.syifa@gmail.com',       'pkg' => 0, 'status' => 'confirmed',            'pay' => 'dp'],
            ['date' => '2027-01-10', 'name' => 'Umar & Tasya Kamila',         'phone' => '085756677885', 'email' => 'umar.tasya@gmail.com',         'pkg' => 1, 'status' => 'confirmed',            'pay' => 'lunas'],
            ['date' => '2027-01-15', 'name' => 'Muhammad Reza Bakhtiar',      'phone' => '081267788996', 'email' => 'reza.bakhtiar@gmail.com',     'pkg' => 1, 'status' => 'confirmed',            'pay' => 'dp'],
            ['date' => '2027-01-16', 'name' => 'Vino & Vanesha Prescilla',    'phone' => '081378899007', 'email' => 'vino.vanesha@gmail.com',       'pkg' => 2, 'status' => 'confirmed',            'pay' => 'lunas'],
            ['date' => '2027-01-22', 'name' => 'Wahyu & Wulan Guritno',       'phone' => '082289900118', 'email' => 'wahyu.wulan@gmail.com',        'pkg' => 0, 'status' => 'pending_verification', 'pay' => 'dp'],
            ['date' => '2027-01-24', 'name' => 'Yusuf & Yasmine Wildblood',   'phone' => '085890011229', 'email' => 'yusuf.yasmine@gmail.com',     'pkg' => 1, 'status' => 'confirmed',            'pay' => 'dp'],
            ['date' => '2027-01-30', 'name' => 'Zaky & Zaskia Sungkar',       'phone' => '081201122330', 'email' => 'zaky.zaskia@gmail.com',        'pkg' => 2, 'status' => 'confirmed',            'pay' => 'lunas'],

            // Februari 2027 (Valentine & Pra-Ramadhan)
            ['date' => '2027-02-06', 'name' => 'Agung & Anggika Bolsterli',   'phone' => '081312233441', 'email' => 'agung.anggika@gmail.com',      'pkg' => 1, 'status' => 'confirmed',            'pay' => 'dp'],
            ['date' => '2027-02-14', 'name' => 'Bobby & Bunga Citra Lestari', 'phone' => '082123344552', 'email' => 'bobby.bcl@gmail.com',           'pkg' => 2, 'status' => 'confirmed',            'pay' => 'lunas'],
            ['date' => '2027-02-14', 'name' => 'Chandra & Chelsea Islan',     'phone' => '085734455663', 'email' => 'chandra.chelsea@gmail.com',    'pkg' => 0, 'status' => 'pending_verification', 'pay' => 'dp'],
            ['date' => '2027-02-20', 'name' => 'Dicky & Dinda Kirana',        'phone' => '081245566774', 'email' => 'dicky.dinda@gmail.com',        'pkg' => 1, 'status' => 'confirmed',            'pay' => 'dp'],
            ['date' => '2027-02-27', 'name' => 'Erwin & Enzy Storia',         'phone' => '081356677885', 'email' => 'erwin.enzy@gmail.com',         'pkg' => 2, 'status' => 'confirmed',            'pay' => 'lunas'],

            // Maret & April 2027 (Pasca Idul Fitri / Musim Nikah Ramai)
            ['date' => '2027-03-20', 'name' => 'Ferry & Febby Rastanty',      'phone' => '082267788996', 'email' => 'ferry.febby@gmail.com',        'pkg' => 0, 'status' => 'confirmed',            'pay' => 'dp'],
            ['date' => '2027-03-27', 'name' => 'Gading & Gisella Anastasia',  'phone' => '085878899007', 'email' => 'gading.gisel@gmail.com',       'pkg' => 1, 'status' => 'pending_verification', 'pay' => 'dp'],
            ['date' => '2027-04-04', 'name' => 'Haris & Hanggini Purinda',    'phone' => '081289900118', 'email' => 'haris.jeha@gmail.com',         'pkg' => 2, 'status' => 'confirmed',            'pay' => 'lunas'],
            ['date' => '2027-04-10', 'name' => 'Indra & Isyana Sarasvati',    'phone' => '081390011229', 'email' => 'indra.isyana@gmail.com',       'pkg' => 1, 'status' => 'confirmed',            'pay' => 'dp'],
            ['date' => '2027-04-17', 'name' => 'Jordy & Jessica Iskandar',    'phone' => '082101122331', 'email' => 'jordy.jessica@gmail.com',      'pkg' => 0, 'status' => 'confirmed',            'pay' => 'lunas'],
            ['date' => '2027-04-24', 'name' => 'Kresna & Kimberly Ryder',     'phone' => '085712233441', 'email' => 'kresna.kim@gmail.com',          'pkg' => 2, 'status' => 'confirmed',            'pay' => 'dp'],

            // Mei & Juni 2027 (Mid-Year Wedding Season)
            ['date' => '2027-05-08', 'name' => 'Lucky & Luna Maya',           'phone' => '081223344553', 'email' => 'lucky.luna@gmail.com',         'pkg' => 1, 'status' => 'confirmed',            'pay' => 'dp'],
            ['date' => '2027-05-15', 'name' => 'Mahendra & Maudy Ayunda',     'phone' => '081334455664', 'email' => 'mahendra.maudy@gmail.com',     'pkg' => 2, 'status' => 'confirmed',            'pay' => 'lunas'],
            ['date' => '2027-05-29', 'name' => 'Naufal & Natasha Wilona',     'phone' => '082145566775', 'email' => 'naufal.natasha@gmail.com',     'pkg' => 0, 'status' => 'pending_verification', 'pay' => 'dp'],
            ['date' => '2027-06-12', 'name' => 'Oka & Pamela Bowie',          'phone' => '085756677886', 'email' => 'oka.pamela@gmail.com',         'pkg' => 1, 'status' => 'confirmed',            'pay' => 'dp'],
            ['date' => '2027-06-20', 'name' => 'Pasha & Paula Verhoeven',     'phone' => '081267788997', 'email' => 'pasha.paula@gmail.com',        'pkg' => 2, 'status' => 'confirmed',            'pay' => 'lunas'],

            // Juli - Desember 2027 (Second Half 2027)
            ['date' => '2027-07-17', 'name' => 'Raffi & Nagita Slavina',      'phone' => '081378899008', 'email' => 'raffi.nagita@gmail.com',       'pkg' => 2, 'status' => 'confirmed',            'pay' => 'lunas'],
            ['date' => '2027-08-21', 'name' => 'Sule & Nathalie Holscher',    'phone' => '082289900119', 'email' => 'sule.nathalie@gmail.com',      'pkg' => 1, 'status' => 'confirmed',            'pay' => 'dp'],
            ['date' => '2027-09-18', 'name' => 'Tarra & Gya Sadiqah',         'phone' => '085890011230', 'email' => 'tarra.gya@gmail.com',          'pkg' => 0, 'status' => 'rejected',             'pay' => 'dp'],
            ['date' => '2027-10-10', 'name' => 'Uya & Astrid Kuya',           'phone' => '081201122332', 'email' => 'uya.astrid@gmail.com',         'pkg' => 1, 'status' => 'pending_verification', 'pay' => 'dp'],
            ['date' => '2027-12-12', 'name' => 'Vidi & Sheila Dara Aisha',    'phone' => '081312233442', 'email' => 'vidi.sheila@gmail.com',        'pkg' => 2, 'status' => 'confirmed',            'pay' => 'lunas'],
        ];

        $invoiceCounterByMonth = [];

        foreach ($couples as $idx => $item) {
            $eventDate = Carbon::parse($item['date']);
            $monthKey = $eventDate->format('m');
            $invoiceCounterByMonth[$monthKey] = ($invoiceCounterByMonth[$monthKey] ?? 0) + 1;
            $invoiceNumber = sprintf('NKB/%s/%02d', $monthKey, $invoiceCounterByMonth[$monthKey]);

            $pkg = $packages[$item['pkg'] % $packages->count()];
            $packagePrice = (float) $pkg->base_price;

            // Tambahkan variasi add-on realistis untuk setiap pesanan
            $selectedAddons = [];
            $addonTotal = 0;

            // Setiap pesanan mengambil 1-3 add-on acak
            $addonCount = ($idx % 3) + 1;
            for ($a = 0; $a < $addonCount; $a++) {
                $targetAddon = $addons[($idx + $a) % $addons->count()];
                $qty = match ($targetAddon->unit) {
                    'pcs'   => 10 + (($idx * 5) % 40),
                    'meter' => 10 + (($idx * 6) % 30),
                    'unit'  => 1 + ($idx % 3),
                    'hari'  => 1 + ($idx % 2),
                    'set'   => 1,
                    default => 1,
                };
                $subtotal = $targetAddon->unit_price * $qty;
                $addonTotal += $subtotal;

                $selectedAddons[] = [
                    'addon_id'   => $targetAddon->id,
                    'addon_name' => $targetAddon->name,
                    'unit_price' => $targetAddon->unit_price,
                    'quantity'   => $qty,
                    'subtotal'   => $subtotal,
                ];
            }

            $grandTotal = $packagePrice + $addonTotal;

            if ($item['pay'] === 'lunas' || $item['status'] === 'completed') {
                $amountPaid = $grandTotal;
                $remaining = 0;
                $payType = 'lunas';
            } else {
                // DP minimal 1jt (default rules) atau sekitar 35% grand total
                $minDp = (float) config('wo.min_dp_amount', 1000000);
                $calculatedDp = round($grandTotal * 0.35, -5);
                $amountPaid = max($minDp, min($calculatedDp, $grandTotal));
                $remaining = max(0, $grandTotal - $amountPaid);
                $payType = 'dp';
            }

            // Tanggal dibuat reservasi (1-3 bulan sebelum hari H)
            $createdAt = $eventDate->copy()->subDays(rand(20, 90));
            if ($createdAt->isFuture()) {
                $createdAt = Carbon::today()->subDays(rand(1, 15));
            }

            $booking = Booking::create([
                'invoice_number'     => $invoiceNumber,
                'customer_name'      => $item['name'],
                'customer_phone'     => $item['phone'],
                'customer_email'     => $item['email'],
                'event_date'         => $eventDate->toDateString(),
                'package_id'         => $pkg->id,
                'package_name'       => $pkg->name,
                'package_price'      => $packagePrice,
                'addon_total_price'  => $addonTotal,
                'grand_total'        => $grandTotal,
                'amount_paid'        => $amountPaid,
                'remaining_balance'  => $remaining,
                'payment_type'       => $payType,
                'payment_proof_path' => 'proofs/sample-receipt.jpg',
                'status'             => $item['status'],
                'synced_to_sheets'   => $item['status'] === 'confirmed',
                'created_at'         => $createdAt,
                'updated_at'         => $createdAt,
            ]);

            foreach ($selectedAddons as $adPayload) {
                BookingAddon::create([
                    'booking_id' => $booking->id,
                    'addon_id'   => $adPayload['addon_id'],
                    'addon_name' => $adPayload['addon_name'],
                    'unit_price' => $adPayload['unit_price'],
                    'quantity'   => $adPayload['quantity'],
                    'subtotal'   => $adPayload['subtotal'],
                    'created_at' => $createdAt,
                    'updated_at' => $createdAt,
                ]);
            }
        }
    }
}
