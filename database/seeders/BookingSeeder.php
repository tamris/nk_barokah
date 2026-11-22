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

        // 100 Data Reservasi Pasangan Pengantin Realistis Indonesia
        // Wilayah: DIY (Sleman, Bantul, Kota Jogja, Kulon Progo, Gunungkidul) & Jateng (Klaten, Magelang)
        $venues = [
            ['address' => 'Jl. Kaliurang KM 12, RT 03/RW 15, Sardonoharjo, Ngaglik, Sleman', 'landmark' => 'Dekat Masjid Al-Ikhlas, pagar hitam'],
            ['address' => 'Jl. Palagan Tentara Pelajar KM 8.5, Sariharjo, Ngaglik, Sleman', 'landmark' => 'Gang barat Hotel Hyatt, depan pohon beringin'],
            ['address' => 'Jl. Wates KM 5, Ambarketawang, Gamping, Sleman', 'landmark' => 'Samping Alfamart Ambarketawang'],
            ['address' => 'Jl. Bantul KM 7, Pendowoharjo, Sewon, Bantul', 'landmark' => 'Depan gapura Dusun Cepit'],
            ['address' => 'Jl. Imogiri Timur KM 11, Wukirsari, Imogiri, Bantul', 'landmark' => 'Dekat jembatan gantung Karangtalun'],
            ['address' => 'Jl. Solo KM 14, Tamanmartani, Kalasan, Sleman', 'landmark' => 'Belakang RS Bhayangkara Kalasan'],
            ['address' => 'Jl. Wonosari KM 8, Potorono, Banguntapan, Bantul', 'landmark' => 'Sebelah barat lapangan desa Potorono'],
            ['address' => 'Jl. Magelang KM 11, Tridadi, Sleman', 'landmark' => 'Dekat kantor Pemkab Sleman'],
            ['address' => 'Jl. Godean KM 7, Sidoaryo, Godean, Sleman', 'landmark' => 'Masuk gang selatan Polsek Godean'],
            ['address' => 'Jl. Parangtritis KM 9, Timbulharjo, Sewon, Bantul', 'landmark' => 'Depan warung sate klathak Pak Pong'],
            ['address' => 'Jl. Damai No. 45, Sinduharjo, Ngaglik, Sleman', 'landmark' => 'Pagar hijau tosca, seberang kafe kaktus'],
            ['address' => 'Jl. Tajem KM 3, Maguwoharjo, Depok, Sleman', 'landmark' => 'Dekat kampus Sanata Dharma Paingan'],
            ['address' => 'Jl. Bibis Raya, Bangunjiwo, Kasihan, Bantul', 'landmark' => 'Dekat SD Kasihan, gapura warna biru'],
            ['address' => 'Jl. Affandi No. 28, Santren, Caturtunggal, Depok, Sleman', 'landmark' => 'Masuk gang samping resto bebek'],
            ['address' => 'Jl. Raya Prambanan - Piyungan KM 4, Bokoharjo, Sleman', 'landmark' => 'Depan candi Banyunibo'],
            ['address' => 'Jl. Veteran No. 12, Pandeyan, Umbulharjo, Kota Yogyakarta', 'landmark' => 'Dekat XT Square Jogja'],
            ['address' => 'Jl. Sugeng Jeroni No. 55, Gedongkiwo, Mantrijeron, Kota Yogyakarta', 'landmark' => 'Depan mushola Nurul Huda'],
            ['address' => 'Jl. Bugisan No. 34, Patangpuluhan, Wirobrajan, Kota Yogyakarta', 'landmark' => 'Gang sebelah barat SMKI Jogja'],
            ['address' => 'Jl. Raya Solo - Yogya KM 20, Delanggu, Klaten', 'landmark' => 'Sebelah selatan pabrik gula Delanggu'],
            ['address' => 'Jl. Pemuda No. 78, Tonggalan, Klaten Tengah, Klaten', 'landmark' => 'Dekat alun-alun Klaten'],
        ];

        $parentsPool = [
            'Bpk. H. Rahmat & Ibu Hj. Siti Aminah',
            'Bpk. Bambang Sutrisno & Ibu Sri Wahyuni',
            'Bpk. Hendra Gunawan & Ibu Ratna Dewi',
            'Bpk. Slamet Riyadi & Ibu Endang Lestari',
            'Bpk. Joko Purnomo & Ibu Suhartini',
            'Bpk. Drs. Agus Susanto & Ibu Dra. Nurul Hidayah',
            'Bpk. Ir. Budi Santoso & Ibu Maria Ulfah',
            'Bpk. Eko Prasetyo & Ibu Tri Astuti',
            'Bpk. H. Wahyudi & Ibu Hj. Marwiyah',
            'Bpk. Danang Wicaksono & Ibu Kartini',
        ];

        $muaList = ['Sarah MUA Gallery', 'Lestari Wedding MakeUp', 'Ayu Glamour MUA', 'Diva Paes Jogja', 'Griya Pengantin Citra', 'Kendedes MUA Solo', 'Cantika Wedding Studio'];
        $decorList = ['Barokah Floral Decor', 'Jogja Rustic Wedding', 'Asri Pelaminan Modern', 'Gardenia Floral Art', 'Nirmala Wedding Decor', 'Maharani Decor Jogja'];
        $photoList = ['Lensa Cerita Fotografi', 'Kisah Kita Studio', 'Visi Visual Wedding', 'Cahaya Asmara Photo', 'Momentum Cinematic', 'Abadi Story Photography'];
        $mcList = ['Mas Dimas MC', 'MC Rama & Partners', 'Pratama Wedding Host', 'Bagus MC Jogja', 'Aditya Master Ceremony', 'WO Berkah Bersama'];

        // 100 Pasangan Pengantin Terkurasi
        $couples = [
            // 2026: November (1-22 Past/Today, 23-30 Up Next)
            ['date' => '2026-11-01', 'name' => 'Dimas Pratama & Anindya Putri',        'phone' => '081223344551', 'email' => 'dimas.anindya@gmail.com',      'pkg' => 0, 'status' => 'completed',            'pay' => 'lunas', 'dur' => 2, 'disc' => 0],
            ['date' => '2026-11-04', 'name' => 'Fajar Nugroho & Nabila Maharani',      'phone' => '081334455662', 'email' => 'fajar.nabila@yahoo.com',       'pkg' => 1, 'status' => 'completed',            'pay' => 'lunas', 'dur' => 2, 'disc' => 5],
            ['date' => '2026-11-07', 'name' => 'Ilham Maulana & Annisa Rahmawati',     'phone' => '082145566773', 'email' => 'ilham.annisa@gmail.com',       'pkg' => 2, 'status' => 'completed',            'pay' => 'lunas', 'dur' => 2, 'disc' => 0],
            ['date' => '2026-11-10', 'name' => 'Rizky Kurniawan & Amanda Permatasari', 'phone' => '085756677884', 'email' => 'rizky.amanda@outlook.com',     'pkg' => 3, 'status' => 'completed',            'pay' => 'lunas', 'dur' => 3, 'disc' => 10],
            ['date' => '2026-11-14', 'name' => 'Bima Sakti & Citra Kirana Dewi',       'phone' => '081267788995', 'email' => 'bima.citra@gmail.com',         'pkg' => 0, 'status' => 'completed',            'pay' => 'lunas', 'dur' => 2, 'disc' => 0],
            ['date' => '2026-11-18', 'name' => 'Muhammad Reza & Dian Sastrowardoyo',   'phone' => '081378899006', 'email' => 'reza.dian@gmail.com',          'pkg' => 4, 'status' => 'confirmed',            'pay' => 'lunas', 'dur' => 2, 'disc' => 15],
            ['date' => '2026-11-20', 'name' => 'Aditya Pratama & Bella Safira',        'phone' => '082289900117', 'email' => 'aditya.bella@gmail.com',       'pkg' => 1, 'status' => 'confirmed',            'pay' => 'dp',    'dur' => 2, 'disc' => 0],
            ['date' => '2026-11-22', 'name' => 'Bagus Wicaksono & Maya Indah Sari',    'phone' => '085890011228', 'email' => 'bagus.maya@yahoo.co.id',     'pkg' => 2, 'status' => 'confirmed',            'pay' => 'dp',    'dur' => 2, 'disc' => 5], // Hari ini
            ['date' => '2026-11-24', 'name' => 'Andi Wijaya & Tiara Andini',           'phone' => '081201122339', 'email' => 'andi.tiara@gmail.com',         'pkg' => 0, 'status' => 'confirmed',            'pay' => 'lunas', 'dur' => 2, 'disc' => 0],
            ['date' => '2026-11-27', 'name' => 'Bayu Kusuma & Rina Marlina',          'phone' => '081312233440', 'email' => 'bayu.rina@gmail.com',          'pkg' => 1, 'status' => 'confirmed',            'pay' => 'dp',    'dur' => 2, 'disc' => 10],
            ['date' => '2026-11-29', 'name' => 'Doni Saputra & Siska Febriana',        'phone' => '082123344551', 'email' => 'doni.siska@outlook.com',       'pkg' => 2, 'status' => 'pending_verification', 'pay' => 'dp',    'dur' => 2, 'disc' => 0],
            ['date' => '2026-11-30', 'name' => 'Eko Prasetyo & Winda Amalia',          'phone' => '085734455662', 'email' => 'eko.winda@gmail.com',          'pkg' => 3, 'status' => 'confirmed',            'pay' => 'dp',    'dur' => 2, 'disc' => 0],

            // 2026: Desember (Musim Nikah Akhir Tahun & Tanggal Cantik)
            ['date' => '2026-12-04', 'name' => 'Gilang Ramadhan & Hesti Purwadinata',  'phone' => '081245566773', 'email' => 'gilang.hesti@gmail.com',       'pkg' => 1, 'status' => 'confirmed',            'pay' => 'dp',    'dur' => 2, 'disc' => 5],
            ['date' => '2026-12-06', 'name' => 'Hendra Setiawan & Jessica Mila',       'phone' => '081356677884', 'email' => 'hendra.jessica@gmail.com',     'pkg' => 2, 'status' => 'confirmed',            'pay' => 'lunas', 'dur' => 2, 'disc' => 0],
            ['date' => '2026-12-09', 'name' => 'Joko Widodo & Kartika Sari Dewi',      'phone' => '082267788995', 'email' => 'joko.kartika@yahoo.com',       'pkg' => 0, 'status' => 'rejected',             'pay' => 'dp',    'dur' => 2, 'disc' => 0],
            ['date' => '2026-12-12', 'name' => 'Kevin Sanjaya & Larasati Nugroho',     'phone' => '085878899006', 'email' => 'kevin.laras@gmail.com',        'pkg' => 4, 'status' => 'confirmed',            'pay' => 'lunas', 'dur' => 3, 'disc' => 10], // 12-12
            ['date' => '2026-12-12', 'name' => 'Lukman Hakim & Mega Utami Putri',      'phone' => '081289900117', 'email' => 'lukman.mega@outlook.com',      'pkg' => 1, 'status' => 'confirmed',            'pay' => 'dp',    'dur' => 2, 'disc' => 0],
            ['date' => '2026-12-16', 'name' => 'Mario Teguh & Nadya Arina',           'phone' => '081390011228', 'email' => 'mario.nadya@gmail.com',        'pkg' => 0, 'status' => 'confirmed',            'pay' => 'dp',    'dur' => 2, 'disc' => 0],
            ['date' => '2026-12-19', 'name' => 'Niko Al-Hakim & Olivia Jensen',       'phone' => '082101122339', 'email' => 'niko.olivia@gmail.com',       'pkg' => 2, 'status' => 'pending_verification', 'pay' => 'dp',    'dur' => 2, 'disc' => 5],
            ['date' => '2026-12-23', 'name' => 'Panji Trihatmodjo & Prilly Maharani', 'phone' => '085712233440', 'email' => 'panji.prilly@gmail.com',      'pkg' => 3, 'status' => 'confirmed',            'pay' => 'lunas', 'dur' => 2, 'disc' => 0],
            ['date' => '2026-12-26', 'name' => 'Rangga Azof & Qory Sandioriva',        'phone' => '081223344552', 'email' => 'rangga.qory@gmail.com',        'pkg' => 1, 'status' => 'confirmed',            'pay' => 'dp',    'dur' => 2, 'disc' => 0],
            ['date' => '2026-12-28', 'name' => 'Satria Tama & Raisa Andriana',         'phone' => '081334455663', 'email' => 'satria.raisa@gmail.com',       'pkg' => 4, 'status' => 'confirmed',            'pay' => 'lunas', 'dur' => 2, 'disc' => 15],
            ['date' => '2026-12-31', 'name' => 'Tio Nugros & Tasya Farasya',          'phone' => '082145566774', 'email' => 'tio.tasya@gmail.com',          'pkg' => 2, 'status' => 'confirmed',            'pay' => 'dp',    'dur' => 2, 'disc' => 0],

            // 2027: Januari (Awal Tahun Baru)
            ['date' => '2027-01-05', 'name' => 'Umar Said & Syifa Hadju',             'phone' => '085756677885', 'email' => 'umar.syifa@gmail.com',         'pkg' => 0, 'status' => 'confirmed',            'pay' => 'dp',    'dur' => 2, 'disc' => 0],
            ['date' => '2027-01-09', 'name' => 'Vino Bastian & Vanesha Prescilla',    'phone' => '081267788996', 'email' => 'vino.vanesha@gmail.com',       'pkg' => 3, 'status' => 'confirmed',            'pay' => 'lunas', 'dur' => 2, 'disc' => 5],
            ['date' => '2027-01-13', 'name' => 'Wahyu Hidayat & Wulan Guritno',       'phone' => '081378899007', 'email' => 'wahyu.wulan@gmail.com',        'pkg' => 1, 'status' => 'pending_verification', 'pay' => 'dp',    'dur' => 2, 'disc' => 0],
            ['date' => '2027-01-17', 'name' => 'Yusuf Mansur & Yasmine Wildblood',    'phone' => '082289900118', 'email' => 'yusuf.yasmine@gmail.com',     'pkg' => 2, 'status' => 'confirmed',            'pay' => 'dp',    'dur' => 2, 'disc' => 10],
            ['date' => '2027-01-20', 'name' => 'Zaky Zimah & Zaskia Adya Mecca',      'phone' => '085890011229', 'email' => 'zaky.zaskia@gmail.com',        'pkg' => 4, 'status' => 'confirmed',            'pay' => 'lunas', 'dur' => 3, 'disc' => 0],
            ['date' => '2027-01-23', 'name' => 'Agung Hercules & Anggika Bolsterli',  'phone' => '081201122330', 'email' => 'agung.anggika@gmail.com',      'pkg' => 0, 'status' => 'confirmed',            'pay' => 'dp',    'dur' => 2, 'disc' => 0],
            ['date' => '2027-01-27', 'name' => 'Bobby Nasution & Bunga Citra Lestari','phone' => '081312233441', 'email' => 'bobby.bcl@gmail.com',           'pkg' => 3, 'status' => 'confirmed',            'pay' => 'lunas', 'dur' => 2, 'disc' => 5],
            ['date' => '2027-01-30', 'name' => 'Chandra Liow & Chelsea Islan',        'phone' => '082123344552', 'email' => 'chandra.chelsea@gmail.com',    'pkg' => 1, 'status' => 'pending_verification', 'pay' => 'dp',    'dur' => 2, 'disc' => 0],

            // 2027: Februari (Pra-Ramadhan & Musim Pesta)
            ['date' => '2027-02-03', 'name' => 'Dicky Difie & Dinda Kirana',          'phone' => '085734455663', 'email' => 'dicky.dinda@gmail.com',        'pkg' => 2, 'status' => 'confirmed',            'pay' => 'dp',    'dur' => 2, 'disc' => 0],
            ['date' => '2027-02-06', 'name' => 'Erwin Gutawa & Enzy Storia',          'phone' => '081245566774', 'email' => 'erwin.enzy@gmail.com',         'pkg' => 4, 'status' => 'confirmed',            'pay' => 'lunas', 'dur' => 2, 'disc' => 10],
            ['date' => '2027-02-10', 'name' => 'Ferry Maryadi & Febby Rastanty',      'phone' => '081356677885', 'email' => 'ferry.febby@gmail.com',        'pkg' => 1, 'status' => 'confirmed',            'pay' => 'dp',    'dur' => 2, 'disc' => 0],
            ['date' => '2027-02-14', 'name' => 'Gading Marten & Gisella Anastasia',   'phone' => '082267788996', 'email' => 'gading.gisel@gmail.com',       'pkg' => 3, 'status' => 'confirmed',            'pay' => 'lunas', 'dur' => 2, 'disc' => 5],
            ['date' => '2027-02-17', 'name' => 'Haris Azhar & Hanggini Purinda',      'phone' => '085878899007', 'email' => 'haris.hanggini@gmail.com',     'pkg' => 0, 'status' => 'confirmed',            'pay' => 'dp',    'dur' => 2, 'disc' => 0],
            ['date' => '2027-02-21', 'name' => 'Indra Bekti & Isyana Sarasvati',      'phone' => '081289900118', 'email' => 'indra.isyana@gmail.com',       'pkg' => 2, 'status' => 'pending_verification', 'pay' => 'dp',    'dur' => 2, 'disc' => 0],
            ['date' => '2027-02-25', 'name' => 'Jordy Onsu & Jessica Iskandar',       'phone' => '081390011229', 'email' => 'jordy.jessica@gmail.com',      'pkg' => 4, 'status' => 'confirmed',            'pay' => 'lunas', 'dur' => 3, 'disc' => 15],
            ['date' => '2027-02-28', 'name' => 'Kresna Danu & Kimberly Ryder',        'phone' => '082101122331', 'email' => 'kresna.kim@gmail.com',          'pkg' => 1, 'status' => 'confirmed',            'pay' => 'dp',    'dur' => 2, 'disc' => 0],

            // 2027: Maret (Periode Ramadhan / Syawal Planner)
            ['date' => '2027-03-05', 'name' => 'Lucky Perdana & Luna Maya',           'phone' => '085712233441', 'email' => 'lucky.luna@gmail.com',         'pkg' => 0, 'status' => 'confirmed',            'pay' => 'dp',    'dur' => 2, 'disc' => 0],
            ['date' => '2027-03-12', 'name' => 'Mahendra Dito & Maudy Ayunda',        'phone' => '081223344553', 'email' => 'mahendra.maudy@gmail.com',     'pkg' => 3, 'status' => 'confirmed',            'pay' => 'lunas', 'dur' => 2, 'disc' => 10],
            ['date' => '2027-03-24', 'name' => 'Naufal Samudra & Natasha Wilona',     'phone' => '081334455664', 'email' => 'naufal.natasha@gmail.com',     'pkg' => 1, 'status' => 'pending_verification', 'pay' => 'dp',    'dur' => 2, 'disc' => 0],
            ['date' => '2027-03-28', 'name' => 'Oka Antara & Pamela Bowie',           'phone' => '082145566775', 'email' => 'oka.pamela@gmail.com',         'pkg' => 2, 'status' => 'confirmed',            'pay' => 'dp',    'dur' => 2, 'disc' => 5],

            // 2027: April (Musim Syawal & Halalbihalal Akbar)
            ['date' => '2027-04-03', 'name' => 'Pasha Ungu & Paula Verhoeven',        'phone' => '085756677886', 'email' => 'pasha.paula@gmail.com',        'pkg' => 4, 'status' => 'confirmed',            'pay' => 'lunas', 'dur' => 2, 'disc' => 0],
            ['date' => '2027-04-07', 'name' => 'Raffi Ahmad & Nagita Slavina',        'phone' => '081267788997', 'email' => 'raffi.nagita@gmail.com',       'pkg' => 4, 'status' => 'confirmed',            'pay' => 'lunas', 'dur' => 3, 'disc' => 20],
            ['date' => '2027-04-10', 'name' => 'Sule Sutisna & Nathalie Holscher',    'phone' => '081378899008', 'email' => 'sule.nathalie@gmail.com',      'pkg' => 2, 'status' => 'confirmed',            'pay' => 'dp',    'dur' => 2, 'disc' => 0],
            ['date' => '2027-04-14', 'name' => 'Tarra Budiman & Gya Sadiqah',         'phone' => '082289900119', 'email' => 'tarra.gya@gmail.com',          'pkg' => 0, 'status' => 'confirmed',            'pay' => 'dp',    'dur' => 2, 'disc' => 5],
            ['date' => '2027-04-18', 'name' => 'Uya Kuya & Astrid Khairunisha',       'phone' => '085890011230', 'email' => 'uya.astrid@gmail.com',         'pkg' => 1, 'status' => 'pending_verification', 'pay' => 'dp',    'dur' => 2, 'disc' => 0],
            ['date' => '2027-04-21', 'name' => 'Vidi Aldiano & Sheila Dara Aisha',    'phone' => '081201122331', 'email' => 'vidi.sheila@gmail.com',        'pkg' => 3, 'status' => 'confirmed',            'pay' => 'lunas', 'dur' => 2, 'disc' => 10],
            ['date' => '2027-04-25', 'name' => 'Wendy Cagur & Ayu Ting Ting',         'phone' => '081312233442', 'email' => 'wendy.ayu@gmail.com',           'pkg' => 2, 'status' => 'confirmed',            'pay' => 'dp',    'dur' => 2, 'disc' => 0],
            ['date' => '2027-04-29', 'name' => 'Yayan Ruhian & Yura Yunita',          'phone' => '082123344553', 'email' => 'yayan.yura@gmail.com',         'pkg' => 0, 'status' => 'confirmed',            'pay' => 'dp',    'dur' => 2, 'disc' => 0],

            // 2027: Mei (Mid-Year Peak Wedding Season)
            ['date' => '2027-05-02', 'name' => 'Zikri Daulay & Ziva Magnolya',        'phone' => '085734455664', 'email' => 'zikri.ziva@gmail.com',         'pkg' => 1, 'status' => 'confirmed',            'pay' => 'dp',    'dur' => 2, 'disc' => 5],
            ['date' => '2027-05-05', 'name' => 'Aliando Syarief & Aurelie Moeremans', 'phone' => '081245566775', 'email' => 'aliando.aurelie@gmail.com',    'pkg' => 3, 'status' => 'confirmed',            'pay' => 'lunas', 'dur' => 2, 'disc' => 10],
            ['date' => '2027-05-08', 'name' => 'Bastian Steel & Brisia Jodie',        'phone' => '081356677886', 'email' => 'bastian.jodie@gmail.com',      'pkg' => 2, 'status' => 'pending_verification', 'pay' => 'dp',    'dur' => 2, 'disc' => 0],
            ['date' => '2027-05-12', 'name' => 'Chicco Jerikho & Putri Marino',       'phone' => '082267788997', 'email' => 'chicco.putri@gmail.com',       'pkg' => 4, 'status' => 'confirmed',            'pay' => 'lunas', 'dur' => 2, 'disc' => 15],
            ['date' => '2027-05-15', 'name' => 'Devano Danendra & Naura Ayu',         'phone' => '085878899008', 'email' => 'devano.naura@gmail.com',       'pkg' => 0, 'status' => 'confirmed',            'pay' => 'dp',    'dur' => 2, 'disc' => 0],
            ['date' => '2027-05-19', 'name' => 'El Rumi & Syifa Savira',              'phone' => '081289900119', 'email' => 'el.syifa@gmail.com',           'pkg' => 1, 'status' => 'confirmed',            'pay' => 'dp',    'dur' => 2, 'disc' => 5],
            ['date' => '2027-05-22', 'name' => 'Fadil Jaidi & Fadly Faisal',          'phone' => '081390011230', 'email' => 'fadil.fadly@gmail.com',         'pkg' => 2, 'status' => 'confirmed',            'pay' => 'lunas', 'dur' => 2, 'disc' => 0],
            ['date' => '2027-05-26', 'name' => 'Giorgino Abraham & Yasmin Napper',    'phone' => '082101122332', 'email' => 'gio.yasmin@gmail.com',         'pkg' => 3, 'status' => 'pending_verification', 'pay' => 'dp',    'dur' => 2, 'disc' => 0],
            ['date' => '2027-05-29', 'name' => 'Hitto Caesar & Felicya Angelista',    'phone' => '085712233442', 'email' => 'hitto.felicya@gmail.com',      'pkg' => 4, 'status' => 'confirmed',            'pay' => 'lunas', 'dur' => 3, 'disc' => 10],

            // 2027: Juni (Liburan Sekolah & Tanggal Favorit)
            ['date' => '2027-06-02', 'name' => 'Iqbaal Ramadhan & Vanesha Prescilla', 'phone' => '081223344554', 'email' => 'iqbaal.milea@gmail.com',       'pkg' => 1, 'status' => 'confirmed',            'pay' => 'dp',    'dur' => 2, 'disc' => 0],
            ['date' => '2027-06-05', 'name' => 'Jefri Nichol & Shenina Cinnamon',     'phone' => '081334455665', 'email' => 'jefri.shenina@gmail.com',      'pkg' => 2, 'status' => 'confirmed',            'pay' => 'lunas', 'dur' => 2, 'disc' => 5],
            ['date' => '2027-06-09', 'name' => 'Kaesang Pangarep & Erina Gudono',     'phone' => '082145566776', 'email' => 'kaesang.erina@gmail.com',      'pkg' => 4, 'status' => 'confirmed',            'pay' => 'lunas', 'dur' => 3, 'disc' => 15],
            ['date' => '2027-06-12', 'name' => 'Luthfi Aulia & Hanggini Purinda',     'phone' => '085756677887', 'email' => 'luthfi.jeha@gmail.com',        'pkg' => 3, 'status' => 'confirmed',            'pay' => 'dp',    'dur' => 2, 'disc' => 0],
            ['date' => '2027-06-16', 'name' => 'Morgan Oey & Asmara Abigail',         'phone' => '081267788998', 'email' => 'morgan.asmara@gmail.com',      'pkg' => 0, 'status' => 'pending_verification', 'pay' => 'dp',    'dur' => 2, 'disc' => 0],
            ['date' => '2027-06-19', 'name' => 'Nino Kayam & Dhabitannisa',           'phone' => '081378899009', 'email' => 'nino.dhabita@gmail.com',       'pkg' => 1, 'status' => 'confirmed',            'pay' => 'dp',    'dur' => 2, 'disc' => 5],
            ['date' => '2027-06-23', 'name' => 'Onadio Leonardo & Beby Prisillia',    'phone' => '082289900120', 'email' => 'onad.beby@gmail.com',          'pkg' => 2, 'status' => 'confirmed',            'pay' => 'lunas', 'dur' => 2, 'disc' => 0],
            ['date' => '2027-06-26', 'name' => 'Pradikta Wicaksono & Enzy Storia',    'phone' => '085890011231', 'email' => 'dikta.enzy@gmail.com',         'pkg' => 3, 'status' => 'confirmed',            'pay' => 'dp',    'dur' => 2, 'disc' => 10],
            ['date' => '2027-06-30', 'name' => 'Rizky Febian & Mahalini Raharja',     'phone' => '081201122333', 'email' => 'iki.lini@gmail.com',           'pkg' => 4, 'status' => 'confirmed',            'pay' => 'lunas', 'dur' => 3, 'disc' => 15],

            // 2027: Juli (Musim Kemarau Cerah & Outdoor / Tenda Idaman)
            ['date' => '2027-07-04', 'name' => 'Stefan William & Ria Andrews',        'phone' => '081312233443', 'email' => 'stefan.ria@gmail.com',         'pkg' => 0, 'status' => 'confirmed',            'pay' => 'dp',    'dur' => 2, 'disc' => 0],
            ['date' => '2027-07-07', 'name' => 'Thariq Halilintar & Aaliyah Massaid', 'phone' => '082123344554', 'email' => 'thariq.aaliyah@gmail.com',    'pkg' => 4, 'status' => 'confirmed',            'pay' => 'lunas', 'dur' => 2, 'disc' => 10], // 07-07
            ['date' => '2027-07-11', 'name' => 'Verrell Bramasta & Natasha Wilona',   'phone' => '085734455665', 'email' => 'verrell.natasha@gmail.com',    'pkg' => 2, 'status' => 'pending_verification', 'pay' => 'dp',    'dur' => 2, 'disc' => 0],
            ['date' => '2027-07-14', 'name' => 'Wafda Saifan & Safira Kamila',        'phone' => '081245566776', 'email' => 'wafda.safira@gmail.com',       'pkg' => 1, 'status' => 'confirmed',            'pay' => 'dp',    'dur' => 2, 'disc' => 5],
            ['date' => '2027-07-18', 'name' => 'Zayn Malik & Gigi Hadid Indah',       'phone' => '081356677887', 'email' => 'zayn.gigi@gmail.com',          'pkg' => 3, 'status' => 'confirmed',            'pay' => 'lunas', 'dur' => 2, 'disc' => 0],
            ['date' => '2027-07-21', 'name' => 'Aldi Maldini & Erika Carlina',        'phone' => '082267788998', 'email' => 'aldi.erika@gmail.com',         'pkg' => 0, 'status' => 'confirmed',            'pay' => 'dp',    'dur' => 2, 'disc' => 0],
            ['date' => '2027-07-25', 'name' => 'Baim Wong & Paula Verhoeven Sari',    'phone' => '085878899009', 'email' => 'baim.paula@gmail.com',         'pkg' => 2, 'status' => 'confirmed',            'pay' => 'lunas', 'dur' => 2, 'disc' => 5],
            ['date' => '2027-07-28', 'name' => 'Cakra Khan & Marion Jola',            'phone' => '081289900120', 'email' => 'cakra.marion@gmail.com',       'pkg' => 1, 'status' => 'pending_verification', 'pay' => 'dp',    'dur' => 2, 'disc' => 0],
            ['date' => '2027-07-31', 'name' => 'Desta Mahendra & Natasha Rizky',      'phone' => '081390011231', 'email' => 'desta.acha@gmail.com',         'pkg' => 4, 'status' => 'confirmed',            'pay' => 'lunas', 'dur' => 3, 'disc' => 20],

            // 2027: Agustus (Bulan Kemerdekaan & Pesta Rakyat)
            ['date' => '2027-08-04', 'name' => 'Ernest Prakasa & Meira Anastasia',    'phone' => '082101122333', 'email' => 'ernest.meira@gmail.com',       'pkg' => 2, 'status' => 'confirmed',            'pay' => 'dp',    'dur' => 2, 'disc' => 0],
            ['date' => '2027-08-08', 'name' => 'Fero Walandouw & Steffi Zamora',      'phone' => '085712233443', 'email' => 'fero.steffi@gmail.com',        'pkg' => 3, 'status' => 'confirmed',            'pay' => 'lunas', 'dur' => 2, 'disc' => 10], // 08-08
            ['date' => '2027-08-11', 'name' => 'Gading Danu & Anya Geraldine',        'phone' => '081223344555', 'email' => 'gading.anya@gmail.com',         'pkg' => 1, 'status' => 'confirmed',            'pay' => 'dp',    'dur' => 2, 'disc' => 5],
            ['date' => '2027-08-15', 'name' => 'Haikal Kamil & Tantri Namirah',       'phone' => '081334455666', 'email' => 'haykal.tantri@gmail.com',      'pkg' => 0, 'status' => 'confirmed',            'pay' => 'dp',    'dur' => 2, 'disc' => 0],
            ['date' => '2027-08-18', 'name' => 'Irfan Hakim & Della Sabrina',         'phone' => '082145566777', 'email' => 'irfan.della@gmail.com',        'pkg' => 4, 'status' => 'confirmed',            'pay' => 'lunas', 'dur' => 2, 'disc' => 15],
            ['date' => '2027-08-22', 'name' => 'Judika Sihotang & Duma Riris',        'phone' => '085756677888', 'email' => 'judika.duma@gmail.com',        'pkg' => 2, 'status' => 'confirmed',            'pay' => 'dp',    'dur' => 2, 'disc' => 0],
            ['date' => '2027-08-26', 'name' => 'Kiki Farrel & Yuriko Angeline',       'phone' => '081267788999', 'email' => 'kiki.yuriko@gmail.com',        'pkg' => 1, 'status' => 'pending_verification', 'pay' => 'dp',    'dur' => 2, 'disc' => 0],
            ['date' => '2027-08-29', 'name' => 'Lukman Sardi & Pricillia Pullunggono','phone' => '081378899010', 'email' => 'lukman.lia@gmail.com',          'pkg' => 3, 'status' => 'confirmed',            'pay' => 'lunas', 'dur' => 2, 'disc' => 5],

            // 2027: September & Oktober (Musim Gugur & Pra Akhir Tahun)
            ['date' => '2027-09-05', 'name' => 'Miller Khan & Farina Rebecca',        'phone' => '082289900121', 'email' => 'miller.farina@gmail.com',      'pkg' => 0, 'status' => 'confirmed',            'pay' => 'dp',    'dur' => 2, 'disc' => 0],
            ['date' => '2027-09-09', 'name' => 'Naufal Ghani & Caitlin Halderman',     'phone' => '085890011232', 'email' => 'naufal.caitlin@gmail.com',     'pkg' => 4, 'status' => 'confirmed',            'pay' => 'lunas', 'dur' => 3, 'disc' => 10], // 09-09
            ['date' => '2027-09-15', 'name' => 'Omar Daniel & Amanda Rawles',         'phone' => '081201122334', 'email' => 'omar.amanda@gmail.com',        'pkg' => 1, 'status' => 'confirmed',            'pay' => 'dp',    'dur' => 2, 'disc' => 5],
            ['date' => '2027-09-22', 'name' => 'Putra Siregar & Septia Yetri',        'phone' => '081312233444', 'email' => 'putra.septia@gmail.com',       'pkg' => 2, 'status' => 'confirmed',            'pay' => 'lunas', 'dur' => 2, 'disc' => 0],
            ['date' => '2027-09-29', 'name' => 'Qausar Harta & Masayu Clara',         'phone' => '082123344555', 'email' => 'qausar.clara@gmail.com',       'pkg' => 0, 'status' => 'pending_verification', 'pay' => 'dp',    'dur' => 2, 'disc' => 0],
            ['date' => '2027-10-06', 'name' => 'Rendy Kjaernett & Lady Nayoan',       'phone' => '085734455666', 'email' => 'rendy.lady@gmail.com',         'pkg' => 3, 'status' => 'confirmed',            'pay' => 'dp',    'dur' => 2, 'disc' => 0],
            ['date' => '2027-10-10', 'name' => 'Samuel Zylgwyn & Franda Heckenbücker','phone' => '081245566777', 'email' => 'samuel.franda@gmail.com',      'pkg' => 4, 'status' => 'confirmed',            'pay' => 'lunas', 'dur' => 3, 'disc' => 15], // 10-10
            ['date' => '2027-10-16', 'name' => 'Teuku Wisnu & Shireen Sungkar',       'phone' => '081356677888', 'email' => 'wisnu.shireen@gmail.com',      'pkg' => 1, 'status' => 'confirmed',            'pay' => 'dp',    'dur' => 2, 'disc' => 5],
            ['date' => '2027-10-24', 'name' => 'Umar Lubis & Ravelra Ruth',           'phone' => '082267788999', 'email' => 'umar.ravelra@gmail.com',       'pkg' => 2, 'status' => 'confirmed',            'pay' => 'lunas', 'dur' => 2, 'disc' => 0],
            ['date' => '2027-10-31', 'name' => 'Vincent Rompies & Fifi Karamoy',      'phone' => '085878899010', 'email' => 'vincent.fifi@gmail.com',       'pkg' => 0, 'status' => 'pending_verification', 'pay' => 'dp',    'dur' => 2, 'disc' => 0],

            // 2027: November & Desember (Tutup Tahun 2027)
            ['date' => '2027-11-07', 'name' => 'Wendi Cagur & Ayu Natasha Sari',      'phone' => '081289900121', 'email' => 'wendi.ayu@gmail.com',          'pkg' => 1, 'status' => 'confirmed',            'pay' => 'dp',    'dur' => 2, 'disc' => 5],
            ['date' => '2027-11-11', 'name' => 'Xander Bogaerts & Kimberly Anggun',   'phone' => '081390011232', 'email' => 'xander.kimberly@gmail.com',    'pkg' => 3, 'status' => 'confirmed',            'pay' => 'lunas', 'dur' => 2, 'disc' => 10], // 11-11
            ['date' => '2027-11-20', 'name' => 'Yoga Pratama & Jessica Veranda',      'phone' => '082101122334', 'email' => 'yoga.ve@gmail.com',             'pkg' => 2, 'status' => 'confirmed',            'pay' => 'dp',    'dur' => 2, 'disc' => 0],
            ['date' => '2027-11-28', 'name' => 'Zulham Zamrun & Nurul Qomaril',       'phone' => '085712233444', 'email' => 'zulham.nurul@gmail.com',       'pkg' => 0, 'status' => 'confirmed',            'pay' => 'lunas', 'dur' => 2, 'disc' => 0],
            ['date' => '2027-12-05', 'name' => 'Arya Saloka & Putri Anne Lestari',    'phone' => '081223344556', 'email' => 'arya.anne@gmail.com',           'pkg' => 4, 'status' => 'confirmed',            'pay' => 'dp',    'dur' => 2, 'disc' => 10],
            ['date' => '2027-12-12', 'name' => 'Bryan Domani & Mawar Eva de Jongh',    'phone' => '081334455667', 'email' => 'bryan.mawar@gmail.com',         'pkg' => 4, 'status' => 'confirmed',            'pay' => 'lunas', 'dur' => 3, 'disc' => 20], // 12-12
            ['date' => '2027-12-19', 'name' => 'Cicio Manassero & Natasha Ryder',     'phone' => '082145566778', 'email' => 'cio.natasha@gmail.com',         'pkg' => 1, 'status' => 'confirmed',            'pay' => 'dp',    'dur' => 2, 'disc' => 5],
            ['date' => '2027-12-26', 'name' => 'Daffa Wardhana & Chelsea Shania',     'phone' => '085756677889', 'email' => 'daffa.chelsea@gmail.com',       'pkg' => 2, 'status' => 'confirmed',            'pay' => 'lunas', 'dur' => 2, 'disc' => 0],
            ['date' => '2027-12-31', 'name' => 'Endy Arfian & Megan Domani Putri',    'phone' => '081267789000', 'email' => 'endy.megan@gmail.com',          'pkg' => 3, 'status' => 'confirmed',            'pay' => 'dp',    'dur' => 2, 'disc' => 10],
        ];

        $invoiceCounterByMonth = [];
        $couples = array_slice($couples, 0, 100);

        foreach ($couples as $idx => $item) {
            $eventDate = Carbon::parse($item['date']);
            $durationDays = $item['dur'] ?? 2;
            $eventEndDate = $eventDate->copy()->addDays($durationDays - 1);

            $monthKey = $eventDate->format('m');
            $invoiceCounterByMonth[$monthKey] = ($invoiceCounterByMonth[$monthKey] ?? 0) + 1;
            $invoiceNumber = sprintf('NKB/%s/%02d', $monthKey, $invoiceCounterByMonth[$monthKey]);

            $pkg = $packages[$item['pkg'] % $packages->count()];
            $packagePrice = (float) $pkg->base_price;

            // Extra charge jika lebih dari 2 hari (@ 15% per hari ekstra)
            $extraDays = max(0, $durationDays - 2);
            $additionalDayCharge = $extraDays * 0.15 * $packagePrice;

            // Variasi add-on realistis untuk setiap pesanan (1 - 3 add-on)
            $selectedAddons = [];
            $addonTotal = 0;
            $addonCount = ($idx % 3) + 1;

            for ($a = 0; $a < $addonCount; $a++) {
                $targetAddon = $addons[($idx * 2 + $a) % $addons->count()];
                $qty = match ($targetAddon->unit) {
                    'pcs'   => 10 + (($idx * 5) % 40),
                    'meter' => 10 + (($idx * 6) % 30),
                    'unit'  => 1 + ($idx % 3),
                    'hari'  => $durationDays,
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

            // Subtotal kotor sebelum diskon
            $subtotalBeforeDiscount = $packagePrice + $additionalDayCharge + $addonTotal;

            // Diskon Vendor Rekanan (%)
            $discountPercent = (float) ($item['disc'] ?? 0);
            $discountAmount = $discountPercent > 0 ? round(($discountPercent / 100) * $subtotalBeforeDiscount) : 0;

            // Grand Total Bersih
            $grandTotal = max(0, $subtotalBeforeDiscount - $discountAmount);

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

            // Venue & Event Details
            $venueData = $venues[$idx % count($venues)];
            $parents = $parentsPool[$idx % count($parentsPool)];
            $instagram = '@' . strtolower(str_replace([' ', '&', '·', '-'], ['_', '', '', ''], explode(' & ', $item['name'])[0])) . '_wedding';
            $eventTime = match ($idx % 4) {
                0 => '08:00 - Selesai',
                1 => '09:00 - 15:00 WIB',
                2 => '10:00 - 16:00 WIB',
                default => '08:00 - 14:00 WIB',
            };

            $vendorName = $discountPercent > 0 ? $muaList[$idx % count($muaList)] : null;
            $vendorMua = $muaList[($idx + 1) % count($muaList)];
            $vendorDecor = $decorList[$idx % count($decorList)];
            $vendorPhoto = $photoList[($idx + 2) % count($photoList)];
            $vendorMc = $mcList[$idx % count($mcList)];

            $booking = Booking::create([
                'invoice_number'        => $invoiceNumber,
                'customer_name'         => strtoupper($item['name']),
                'customer_phone'        => $item['phone'],
                'customer_email'        => $item['email'],
                'venue_address'         => $venueData['address'],
                'address_landmark'      => $venueData['landmark'],
                'parents_name'          => $parents,
                'instagram_account'     => $instagram,
                'event_time'            => $eventTime,
                'vendor_name'           => $vendorName,
                'discount_percent'      => $discountPercent,
                'discount_amount'       => $discountAmount,
                'vendor_mua'            => $vendorMua,
                'vendor_decor'          => $vendorDecor,
                'vendor_photo_video'    => $vendorPhoto,
                'vendor_mc_wo'          => $vendorMc,
                'event_date'            => $eventDate->toDateString(),
                'event_end_date'        => $eventEndDate->toDateString(),
                'duration_days'         => $durationDays,
                'package_id'            => $pkg->id,
                'package_name'          => $pkg->name,
                'package_price'         => $packagePrice,
                'additional_day_charge' => $additionalDayCharge,
                'addon_total_price'     => $addonTotal,
                'grand_total'           => $grandTotal,
                'amount_paid'           => $amountPaid,
                'remaining_balance'     => $remaining,
                'payment_type'          => $payType,
                'payment_proof_path'    => 'proofs/sample-receipt.jpg',
                'status'                => $item['status'],
                'synced_to_sheets'      => $item['status'] === 'confirmed',
                'created_at'            => $createdAt,
                'updated_at'            => $createdAt,
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
