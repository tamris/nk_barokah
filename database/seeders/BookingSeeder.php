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
        // 1. Reset data bookings & booking_addons dan kembalikan Auto-Increment ke 1
        if (DB::getDriverName() === 'mysql') {
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            DB::table('booking_addons')->truncate();
            DB::table('bookings')->truncate();
            DB::statement('ALTER TABLE bookings AUTO_INCREMENT = 1;');
            DB::statement('ALTER TABLE booking_addons AUTO_INCREMENT = 1;');
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        } else {
            DB::table('booking_addons')->delete();
            DB::table('bookings')->delete();
            if (DB::getDriverName() === 'sqlite') {
                DB::statement("DELETE FROM sqlite_sequence WHERE name IN ('bookings', 'booking_addons')");
            }
        }

        $packages = Package::all();
        $addons = Addon::all();

        if ($packages->isEmpty() || $addons->isEmpty()) {
            $this->call(DatabaseSeeder::class);
            $packages = Package::all();
            $addons = Addon::all();
        }

        // =====================================================================
        // 55+ Alamat Asli Lokal di Wilayah Operasional NK Barokah
        // (Kab. Tegal, Slawi, Adiwerna, Dukuhturi, Talang, Pangkah, Tarub, Kramat, Lebaksiu, Kota Tegal)
        // Jarak dari Gudang NK Barokah (Slawi Kulon): 2 km - 28 km
        // =====================================================================
        $venues = [
            // Area Slawi (Pusat & Sekitarnya: Jarak 1 - 5 km)
            ['address' => 'Jl. K.H. Ahmad Dahlan No. 18, Slawi Kulon, Kec. Slawi, Kab. Tegal', 'landmark' => 'Depan Masjid Al-Kautsar, gapura warna krem', 'dist' => 1.5],
            ['address' => 'Jl. Dr. Soeselo No. 42, RT 02/RW 03, Slawi Wetan, Kec. Slawi, Kab. Tegal', 'landmark' => 'Dekat RSUD dr. Soeselo Slawi, masuk gang mangga', 'dist' => 2.8],
            ['address' => 'Jl. Gajah Mada No. 88, Kalisapu, Kec. Slawi, Kab. Tegal', 'landmark' => 'Sebelah barat Kantor Kemenag Slawi', 'dist' => 3.2],
            ['address' => 'Jl. Jenderal Sudirman No. 15, Procot, Kec. Slawi, Kab. Tegal', 'landmark' => 'Depan Monumen GBN Procot, pagar hitam', 'dist' => 3.0],
            ['address' => 'Jl. Prof. Moh. Yamin No. 24, Kudaile, Kec. Slawi, Kab. Tegal', 'landmark' => 'Belakang Stadion Tri Sanja Slawi', 'dist' => 2.5],
            ['address' => 'Jl. Cut Nyak Dien, Trayeman, Kec. Slawi, Kab. Tegal', 'landmark' => 'Samping barat Pasar Trayeman', 'dist' => 2.2],
            ['address' => 'Jl. Agus Salim No. 09, Pakembaran, Kec. Slawi, Kab. Tegal', 'landmark' => 'Dekat SMK Negeri 1 Slawi', 'dist' => 4.0],
            ['address' => 'Jl. Letjen Suprapto, Kagok, Kec. Slawi, Kab. Tegal', 'landmark' => 'Dekat Taman Rakyat Slawi Ayu (TRASA)', 'dist' => 2.0],
            ['address' => 'Jl. Merpati No. 31, Dukuhwringin, Kec. Slawi, Kab. Tegal', 'landmark' => 'Masuk gang barat Puskesmas Slawi', 'dist' => 4.5],

            // Area Adiwerna & Talang (Jarak 6 - 12 km)
            ['address' => 'Jl. Raya Singkil - Tembok Luwung No. 77, Tembok Luwung, Kec. Adiwerna, Kab. Tegal', 'landmark' => 'Sentra Bordir, seberang Mushola Baitul Muttaqin', 'dist' => 7.5],
            ['address' => 'Jl. Sunan Amangkurat No. 12, Pesarean, Kec. Adiwerna, Kab. Tegal', 'landmark' => 'Dekat komplek Makam Amangkurat I', 'dist' => 6.8],
            ['address' => 'Jl. Raya Ujungrusi No. 50, Kec. Adiwerna, Kab. Tegal', 'landmark' => 'Dekat RS Islam PKU Muhammadiyah Tegal', 'dist' => 8.2],
            ['address' => 'Jl. Raya Harjosari Kidul No. 102, Kec. Adiwerna, Kab. Tegal', 'landmark' => 'Depan Masjid Jami Harjosari', 'dist' => 7.0],
            ['address' => 'Jl. Raya Kalimati No. 34, Kalimati, Kec. Adiwerna, Kab. Tegal', 'landmark' => 'Dekat jembatan Kalimati perbatasan', 'dist' => 8.5],
            ['address' => 'Jl. Pagiyanten Indah No. 19, Kec. Adiwerna, Kab. Tegal', 'landmark' => 'Sebelah barat Balai Desa Pagiyanten', 'dist' => 6.2],
            ['address' => 'Jl. Raya Talang No. 89, Tegalwangi, Kec. Talang, Kab. Tegal', 'landmark' => 'Dekat Pasar Talang, ruko tingkat hijau', 'dist' => 9.5],
            ['address' => 'Jl. Kyai Mojo, Pesayangan, Kec. Talang, Kab. Tegal', 'landmark' => 'Sentra Logam Pesayangan, masuk gang 3', 'dist' => 10.0],
            ['address' => 'Jl. Raya Pegirikan No. 14, Pegirikan, Kec. Talang, Kab. Tegal', 'landmark' => 'Depan Puskesmas Pegirikan', 'dist' => 8.8],
            ['address' => 'Jl. Pasangan Raya No. 27, Pasangan, Kec. Talang, Kab. Tegal', 'landmark' => 'Belakang balai desa Pasangan', 'dist' => 9.2],

            // Area Pangkah & Kedungbanteng (Jarak 5 - 12 km)
            ['address' => 'Jl. Raya Bogares No. 45, Bogares Kidul, Kec. Pangkah, Kab. Tegal', 'landmark' => 'Sentra Kacang Asin Bogares, rumah pagar besi tempa', 'dist' => 6.0],
            ['address' => 'Jl. Raya Bogares Lor No. 18, Bogares Lor, Kec. Pangkah, Kab. Tegal', 'landmark' => 'Dekat pertigaan tugu Bogares', 'dist' => 7.2],
            ['address' => 'Jl. Pabrik Gula Pangkah No. 05, Kec. Pangkah, Kab. Tegal', 'landmark' => 'Sebelah selatan gerbang PG Pangkah', 'dist' => 6.5],
            ['address' => 'Jl. Curug Indah RT 03/RW 02, Curug, Kec. Pangkah, Kab. Tegal', 'landmark' => 'Belakang lapangan sepakbola Curug', 'dist' => 8.0],
            ['address' => 'Jl. Grobog Wetan No. 11, Grobog Kulon, Kec. Pangkah, Kab. Tegal', 'landmark' => 'Dekat Balai Desa Grobog', 'dist' => 9.0],
            ['address' => 'Jl. Raya Penusupan No. 88, Penusupan, Kec. Pangkah, Kab. Tegal', 'landmark' => 'Depan Masjid Baiturrahman Penusupan', 'dist' => 7.8],
            ['address' => 'Jl. Raya Tonggara No. 23, Tonggara, Kec. Kedungbanteng, Kab. Tegal', 'landmark' => 'Dekat waduk Cacaban Slawi', 'dist' => 11.5],

            // Area Dukuhturi & Tarub (Jarak 8 - 14 km)
            ['address' => 'Jl. Raya Kupu No. 64, Kupu, Kec. Dukuhturi, Kab. Tegal', 'landmark' => 'Dekat jembatan kali Kupu', 'dist' => 11.0],
            ['address' => 'Jl. Raya Sidakaton No. 90, Sidakaton, Kec. Dukuhturi, Kab. Tegal', 'landmark' => 'Perempatan pasar Sidakaton, ruko batik', 'dist' => 10.5],
            ['address' => 'Jl. Bandasari Permai No. 15, Bandasari, Kec. Dukuhturi, Kab. Tegal', 'landmark' => 'Belakang kantor balai desa Bandasari', 'dist' => 12.0],
            ['address' => 'Jl. Mindaka Raya No. 42, Mindaka, Kec. Tarub, Kab. Tegal', 'landmark' => 'Dekat perlintasan rel KA Mindaka', 'dist' => 12.5],
            ['address' => 'Jl. Raya Brekat No. 08, Brekat, Kec. Tarub, Kab. Tegal', 'landmark' => 'Sebelah timur Balai Desa Brekat', 'dist' => 13.0],
            ['address' => 'Jl. Kesadikan No. 37, Kesadikan, Kec. Tarub, Kab. Tegal', 'landmark' => 'Depan SDN Kesadikan 01', 'dist' => 14.0],

            // Area Lebaksiu, Balapulang & Bojong (Arah Selatan: Jarak 8 - 22 km)
            ['address' => 'Jl. Raya Yomani - Guci KM 2, Yamansari, Kec. Lebaksiu, Kab. Tegal', 'landmark' => 'Dekat pertigaan patung sapi Yomani', 'dist' => 8.5],
            ['address' => 'Jl. Pondok Pesantren Babakan No. 12, Babakan, Kec. Lebaksiu, Kab. Tegal', 'landmark' => 'Masuk gerbang komplek Ponpes Babakan', 'dist' => 9.0],
            ['address' => 'Jl. Raya Lebakgowah No. 56, Kec. Lebaksiu, Kab. Tegal', 'landmark' => 'Dekat jembatan Kali Gung Lebaksiu', 'dist' => 7.8],
            ['address' => 'Jl. Raya Balapulang No. 101, Balapulang Wetan, Kec. Balapulang, Kab. Tegal', 'landmark' => 'Dekat Pasar Balapulang, seberang Bank BRI', 'dist' => 11.0],
            ['address' => 'Jl. Raya Kalibakung No. 25, Kalibakung, Kec. Balapulang, Kab. Tegal', 'landmark' => 'Dekat Wisata Kesehatan Jamu (RSOB Kalibakung)', 'dist' => 15.0],
            ['address' => 'Jl. Raya Tuwel - Guci KM 5, Tuwel, Kec. Bojong, Kab. Tegal', 'landmark' => 'Jalur utama wisata air panas Guci', 'dist' => 20.0],
            ['address' => 'Jl. Bojong Kulon No. 40, Kec. Bojong, Kab. Tegal', 'landmark' => 'Dekat Pasar Bojong, jalan tanjakan', 'dist' => 18.0],
            ['address' => 'Jl. Raya Bumijawa No. 15, Kec. Bumijawa, Kab. Tegal', 'landmark' => 'Dekat Kantor Camat Bumijawa hawa sejuk', 'dist' => 25.0],
            ['address' => 'Jl. Raya Margasari No. 67, Kec. Margasari, Kab. Tegal', 'landmark' => 'Dekat Stasiun Prupuk / Margasari', 'dist' => 22.0],

            // Area Kramat & Suradadi (Jalur Pantura Tegal: Jarak 14 - 24 km)
            ['address' => 'Perum Mejasem Baru Blok C No. 12, Mejasem Barat, Kec. Kramat, Kab. Tegal', 'landmark' => 'Dekat Lapangan Barat Mejasem, gerbang portal putih', 'dist' => 13.5],
            ['address' => 'Jl. Pala Raya No. 48, Mejasem Timur, Kec. Kramat, Kab. Tegal', 'landmark' => 'Sebelah utara Pasar Mejasem', 'dist' => 14.2],
            ['address' => 'Jl. Raya Pantura Kramat No. 210, Padaharja, Kec. Kramat, Kab. Tegal', 'landmark' => 'Depan Rumah Makan Luwes Pantura', 'dist' => 18.0],
            ['address' => 'Jl. Pantai Maribaya No. 19, Maribaya, Kec. Kramat, Kab. Tegal', 'landmark' => 'Dekat gerbang wisata Pantai Maribaya', 'dist' => 21.0],
            ['address' => 'Jl. Raya Purwahamba No. 55, Suradadi, Kab. Tegal', 'landmark' => 'Dekat Obyek Wisata Purwahamba Indah (Purin)', 'dist' => 24.0],

            // Area Kota Tegal (Tetangga Langsung Slawi: Jarak 10 - 16 km)
            ['address' => 'Jl. Kartini No. 20, Kejambon, Kec. Tegal Timur, Kota Tegal', 'landmark' => 'Dekat RSUD Kardinah Kota Tegal', 'dist' => 12.0],
            ['address' => 'Jl. Pancasila No. 04, Panggung, Kec. Tegal Timur, Kota Tegal', 'landmark' => 'Dekat Alun-Alun Kota Tegal & Stasiun Tegal', 'dist' => 13.5],
            ['address' => 'Jl. KH. Mansyur No. 33, Pekauman, Kec. Tegal Barat, Kota Tegal', 'landmark' => 'Belakang Masjid Agung Kota Tegal', 'dist' => 13.0],
            ['address' => 'Jl. Teuku Umar No. 82, Debong Tengah, Kec. Tegal Selatan, Kota Tegal', 'landmark' => 'Dekat RS Harapan Anda Tegal', 'dist' => 10.8],
            ['address' => 'Jl. Kapten Sudibyo No. 115, Randugunting, Kec. Tegal Selatan, Kota Tegal', 'landmark' => 'Dekat Pacific Mall Tegal', 'dist' => 11.2],
            ['address' => 'Jl. Mataram No. 08, Muarareja, Kec. Tegal Barat, Kota Tegal', 'landmark' => 'Dekat Pantai Muarareja Indah', 'dist' => 14.5],
            ['address' => 'Jl. Raya Kolonel Sugiono No. 202, Margadana, Kota Tegal', 'landmark' => 'Dekat Terminal Bis Tegal Pantura', 'dist' => 15.0],
            ['address' => 'Jl. Raya Jatibarang - Slawi KM 4, Jatibarang Lor, Kab. Brebes', 'landmark' => 'Dekat Pabrik Gula Jatibarang', 'dist' => 9.5],
        ];

        $parentsPool = [
            'Bpk. H. Rahmat Hidayat & Ibu Hj. Siti Aminah',
            'Bpk. Bambang Sutrisno, S.E. & Ibu Sri Wahyuni',
            'Bpk. Hendra Gunawan & Ibu Ratna Dewi',
            'Bpk. H. Slamet Riyadi & Ibu Hj. Endang Lestari',
            'Bpk. Joko Purnomo & Ibu Suhartini',
            'Bpk. Drs. H. Agus Susanto & Ibu Dra. Hj. Nurul Hidayah',
            'Bpk. Ir. Budi Santoso & Ibu Maria Ulfah',
            'Bpk. Eko Prasetyo & Ibu Tri Astuti',
            'Bpk. H. Wahyudi & Ibu Hj. Marwiyah',
            'Bpk. Danang Wicaksono & Ibu Kartini',
            'Bpk. Drs. H. Mulyono & Ibu Hj. Khodijah',
            'Bpk. Edi Suwarno & Ibu Tutik Handayani',
            'Bpk. H. Syaiful Bahri & Ibu Hj. Umi Kalsum',
            'Bpk. Toto Sugiharto & Ibu Widianingsih',
            'Bpk. Sukirman, S.Pd. & Ibu Siti Romlah',
        ];

        // Vendor Rekanan Lokal Tegal & Slawi
        $muaList = [
            'Rias Pengantin Tegal Ayu',
            'Griya Pengantin Slawi Ayu',
            'Barokah MUA & Hijab Style',
            'Cantika Paes & MakeUp Slawi',
            'Lestari Wedding Gallery Tegal',
            'Diva Kebaya & Paes Adiwerna',
            'Ayu Glamour MakeUp Artist',
        ];
        $decorList = [
            'NK Barokah Floral Decor',
            'Slawi Rustic & Modern Decor',
            'Pelaminan Asri Pangkah',
            'Gardenia Floral Art Tegal',
            'Kencana Sari Dekorasi Slawi',
            'Maharani Wedding Decor Adiwerna',
        ];
        $photoList = [
            'Lensa Tegal Fotografi',
            'Kisah Indah Cinema Slawi',
            'Visi Visual Wedding Tegal',
            'Cahaya Asmara Studio Procot',
            'Momentum Cinematic Slawi',
            'Cerita Bahagia Photography',
        ];
        $mcList = [
            'Mas Dimas MC Tegal',
            'Pak RT Slamet MC Adiwerna',
            'Kang Aji Pranata Adicara',
            'Pratama Wedding Host Slawi',
            'Bagus MC Tegal Ayu',
            'WO Berkah Bersama Slawi',
        ];

        // 200 Nama Pasangan Pengantin Realistis Indonesia
        $coupleNames = [
            'Dimas Pratama & Anindya Putri',
            'Fajar Nugroho & Nabila Maharani',
            'Ilham Maulana & Annisa Rahmawati',
            'Rizky Kurniawan & Amanda Permatasari',
            'Bima Sakti & Citra Kirana Dewi',
            'Muhammad Reza & Dian Sastrowardoyo',
            'Aditya Pratama & Bella Safira',
            'Bagus Wicaksono & Maya Indah Sari',
            'Andi Wijaya & Tiara Andini',
            'Bayu Kusuma & Rina Marlina',
            'Doni Saputra & Siska Febriana',
            'Eko Prasetyo & Winda Amalia',
            'Gilang Ramadhan & Hesti Purwadinata',
            'Hendra Setiawan & Jessica Mila',
            'Joko Widodo & Kartika Sari Dewi',
            'Kevin Sanjaya & Larasati Nugroho',
            'Lukman Hakim & Mega Utami Putri',
            'Mario Teguh & Nadya Arina',
            'Niko Al-Hakim & Olivia Jensen',
            'Panji Trihatmodjo & Prilly Maharani',
            'Rangga Azof & Qory Sandioriva',
            'Satria Tama & Raisa Andriana',
            'Tio Nugros & Tasya Farasya',
            'Umar Said & Syifa Hadju',
            'Vino Bastian & Vanesha Prescilla',
            'Wahyu Hidayat & Wulan Guritno',
            'Yusuf Mansur & Yasmine Wildblood',
            'Zaky Zimah & Zaskia Adya Mecca',
            'Agung Hercules & Anggika Bolsterli',
            'Bobby Nasution & Bunga Citra Lestari',
            'Chandra Liow & Chelsea Islan',
            'Dicky Difie & Dinda Kirana',
            'Erwin Gutawa & Enzy Storia',
            'Ferry Maryadi & Febby Rastanty',
            'Gading Marten & Gisella Anastasia',
            'Haris Azhar & Hanggini Purinda',
            'Indra Bekti & Isyana Sarasvati',
            'Jordy Onsu & Jessica Iskandar',
            'Kresna Danu & Kimberly Ryder',
            'Lucky Perdana & Luna Maya',
            'Mahendra Dito & Maudy Ayunda',
            'Naufal Samudra & Natasha Wilona',
            'Oka Antara & Pamela Bowie',
            'Pasha Ungu & Paula Verhoeven',
            'Raffi Ahmad & Nagita Slavina',
            'Sule Sutisna & Nathalie Holscher',
            'Tarra Budiman & Gya Sadiqah',
            'Uya Kuya & Astrid Khairunisha',
            'Vidi Aldiano & Sheila Dara Aisha',
            'Wendy Cagur & Ayu Ting Ting',
            'Yayan Ruhian & Yura Yunita',
            'Zikri Daulay & Ziva Magnolya',
            'Aliando Syarief & Aurelie Moeremans',
            'Bastian Steel & Brisia Jodie',
            'Chicco Jerikho & Putri Marino',
            'Devano Danendra & Naura Ayu',
            'El Rumi & Syifa Savira',
            'Fadil Jaidi & Fadly Faisal',
            'Giorgino Abraham & Yasmin Napper',
            'Hitto Caesar & Felicya Angelista',
            'Iqbaal Ramadhan & Vanesha Angel',
            'Jefri Nichol & Shenina Cinnamon',
            'Kaesang Pangarep & Erina Gudono',
            'Luthfi Aulia & Hanggini Purinda',
            'Morgan Oey & Asmara Abigail',
            'Nino Kayam & Dhabitannisa',
            'Onadio Leonardo & Beby Prisillia',
            'Pradikta Wicaksono & Enzy Storia',
            'Rizky Febian & Mahalini Raharja',
            'Stefan William & Ria Andrews',
            'Thariq Halilintar & Aaliyah Massaid',
            'Verrell Bramasta & Natasha Wilona',
            'Wafda Saifan & Safira Kamila',
            'Zayn Malik & Gigi Hadid Indah',
            'Aldi Maldini & Erika Carlina',
            'Baim Wong & Paula Verhoeven Sari',
            'Cakra Khan & Marion Jola',
            'Desta Mahendra & Natasha Rizky',
            'Ernest Prakasa & Meira Anastasia',
            'Fero Walandouw & Steffi Zamora',
            'Gading Danu & Anya Geraldine',
            'Haikal Kamil & Tantri Namirah',
            'Irfan Hakim & Della Sabrina',
            'Judika Sihotang & Duma Riris',
            'Kiki Farrel & Yuriko Angeline',
            'Lukman Sardi & Pricillia Pullunggono',
            'Miller Khan & Farina Rebecca',
            'Naufal Ghani & Caitlin Halderman',
            'Omar Daniel & Amanda Rawles',
            'Putra Siregar & Septia Yetri',
            'Qausar Harta & Masayu Clara',
            'Rendy Kjaernett & Lady Nayoan',
            'Samuel Zylgwyn & Franda Heckenbücker',
            'Teuku Wisnu & Shireen Sungkar',
            'Umar Lubis & Ravelra Ruth',
            'Vincent Rompies & Fifi Karamoy',
            'Wendi Cagur & Ayu Natasha Sari',
            'Xander Bogaerts & Kimberly Anggun',
            'Yoga Pratama & Jessica Veranda',
            'Zulham Zamrun & Nurul Qomaril',
            'Arya Saloka & Putri Anne Lestari',
            'Bryan Domani & Mawar Eva de Jongh',
            'Cicio Manassero & Natasha Ryder',
            'Daffa Wardhana & Chelsea Shania',
            'Endy Arfian & Megan Domani Putri',
            'Fachri Albar & Renata Kusmanto',
            'Giring Ganesha & Cynthia Riza',
            'Hito Caesar & Felicya Angelista',
            'Ibnu Jamil & Ririn Ekawati',
            'Jonas Rivanno & Asmirandah Zantman',
            'Kunto Aji & Dewi Syariati',
            ' Marcell Siahaan & Rima Melati',
            'Nino Fernandez & Hannah Al Rashid',
            'Omesh Anandayu & Dian Ayu Lestari',
            'Pevita Pearce & Mirzan Meer',
            'Qibil Changcuters & Triani Amalia',
            'Rio Dewanto & Atiqah Hasiholan',
            'Surya Saputra & Cynthia Lamusu',
            'Tora Sudiro & Mieke Amalia',
            'Uus Rizky & Kartika Wijaksana',
            'Vino Giovanni & Marsha Timothy',
            'Winky Wiryawan & Kenes Andari',
            'Yuki Kato & Fero Anggara',
            'Zack Lee & Nafa Urbach',
            'Adipati Dolken & Canti Tachril',
            'Bams Samsons & Mikhavita Wijaya',
            'Christian Sugiono & Titi Kamal',
            'Darius Sinathrya & Donna Agnesia',
            'Eza Gionino & Meiza Aulia',
            'Fandy Christian & Dahlia Poland',
            'Glenn Alinskie & Chelsea Olivia',
            'Haikal Baron & Soraya Larasati',
            'Irwansyah & Zaskia Sungkar',
            'Junior Liem & Putri Titian',
            'Kevin Julio & Imelda Therinne',
            'Lucky Hakim & Tiara Dewi',
            'Mike Lewis & Janisaa Pradja',
            'Navicula Robi & Lakota Moira',
            'Ozy Syahputra & Rina Diana',
            'Panji Petualang & Nenty Estevany',
            'Qomaruddin & Siti Aisyah',
            'Rezky Aditya & Citra Kirana',
            'Sultan Djorghi & Annisa Trihapsari',
            'Teuku Zacky & Ilmira Usmanova',
            'Usman Harun & Dewi Sartika',
            'Vicky Nitinegoro & Cynthia Ramlan',
            'Whisnu Santoso & Nadila Ernesta',
            'Xavier Marks & Angela Gilsha',
            'Yuda Pratama & Dinda Hauw',
            'Zul Arifin & Nurul Huda',
            'Ananda Omesh & Dian Ayu',
            'Bima Arya & Yane Ardian',
            'Coki Pardede & Firda Rahma',
            'Didi Riyadi & Nirina Zubir',
            'Engku Emran & Laudya Cynthia Bella',
            'Fadlan Muhammad & Lyra Virna',
            'Gatot Brajamusti & Reza Artamevia',
            'Hardi Fadhillah & Kiki Azhari',
            'Indra Priawan & Nikita Willy',
            'Jerry Aurum & Denada Tambunan',
            'Kiki Rizky & Ratu Sofya',
            'Luthfi Agizal & Nadya Indry',
            'Muzammil Hasballah & Sonia Ristanti',
            'Natta Reza & Wardah Maulina',
            'Opie Kumis & Siti Jubaedah',
            'Pasha Chrismansyah & Adelia Wilhelmina',
            'Qory Sandioriva & Shah Rei',
            'Rizal Armada & Monica Imas',
            'Sahrul Gunawan & Dine Mutiara',
            'Taufik Hidayat & Ami Gumelar',
            'Ungu Makki & Lala Hamid',
            'Virgoun Tambunan & Inara Rusli',
            'Wawan Teamlo & Endang Sukamti',
            'Xabiru Oshe & Chava Al-Hakim',
            'Yosi Mokalu & Aprillia Trina',
            'Zumi Zola & Sherrin Tharia',
            'Ahmad Dhani & Mulan Jameela',
            'Bambang Pamungkas & Tribuana Tungga Dewi',
            'Cakra Birawa & Dewi Perssik',
            'Didi Kempot & Yan Vellia',
            'Epy Kusnandar & Karina Ranau',
            'Farhat Abbas & Nia Daniaty',
            'Guruh Soekarnoputra & Garniana',
            'Hengky Kurniawan & Sonya Fatmala',
            'Iwan Fals & Rosana Listanto',
            'Jamal Mirdad & Lydia Kandou',
            'Kelik Pelur & Anik Sunyahni',
            'Limbad & Susi Indrawati',
            'Mandra Naih & Mila Sari Juwita',
            'Nassar Sungkar & Muzdalifah',
            'Oon Project Pop & Ocha',
            'Parto Patrio & Diena Risty',
            'Qomar Empat Sekawan & Dedeh',
            'Rhoma Irama & Ricca Rachim',
            'Saipul Jamil & Dewi Murya',
            'Tessy Kabul & Sri Handayani',
            'Ucup Nirin & Siti Munawaroh',
            'Vicky Prasetyo & Angel Lelga',
            'Willy Dozan & Betharia Sonata',
            'Yanto Tampan & Nurhasanah',
            'Zaenal Abidin & Titiek Puspa',
        ];

        // Buat 200 tanggal event terdistribusi realistis:
        // - 30 Event di masa lalu (Okt 2026 s/d 22 Nov 2026) -> Status 'completed'
        // - 10 Event di sisa Nov 2026 (23 - 30 Nov 2026) -> Status 'confirmed' / 'pending_verification'
        // - 160 Event sepanjang 2027 (Januari - Desember 2027) -> Campuran 'confirmed', 'pending_verification', dan 5 'rejected'
        $eventDates = [];

        // 1. Oktober & Awal November 2026 (Masa lalu)
        $pastBase = Carbon::create(2026, 10, 1);
        for ($i = 0; $i < 30; $i++) {
            $eventDates[] = [
                'date'   => $pastBase->copy()->addDays($i * 1.7)->toDateString(),
                'status' => 'completed',
                'pay'    => 'lunas',
            ];
        }

        // 2. Sisa November 2026 (Hari Ini s/d Akhir Bulan)
        $novDates = ['2026-11-23', '2026-11-24', '2026-11-25', '2026-11-26', '2026-11-27', '2026-11-28', '2026-11-29', '2026-11-30', '2026-11-24', '2026-11-28'];
        foreach ($novDates as $idx => $nd) {
            $eventDates[] = [
                'date'   => $nd,
                'status' => $idx % 4 === 0 ? 'pending_verification' : 'confirmed',
                'pay'    => $idx % 3 === 0 ? 'lunas' : 'dp',
            ];
        }

        // 3. Sepanjang Tahun 2027 (160 Event: Rata-rata 13-14 event/bulan, terutama weekend & tanggal cantik)
        $year2027Start = Carbon::create(2027, 1, 1);
        for ($i = 0; $i < 160; $i++) {
            // Distribusi merata sepanjang 365 hari 2027
            $dayOffset = (int) ($i * 2.25);
            $evDate = $year2027Start->copy()->addDays($dayOffset);
            if ($evDate->year > 2027) {
                $evDate = Carbon::create(2027, 12, 28);
            }

            // Status: 85% confirmed, 12% pending_verification, 3% rejected
            $randStatus = match ($i % 25) {
                0, 13 => 'rejected',
                3, 7, 18 => 'pending_verification',
                default => 'confirmed',
            };

            $payType = ($i % 3 === 0) ? 'lunas' : 'dp';

            $eventDates[] = [
                'date'   => $evDate->toDateString(),
                'status' => $randStatus,
                'pay'    => $payType,
            ];
        }

        // Pastikan genap 200 data
        $eventDates = array_slice($eventDates, 0, 200);

        // Sorting urut berdasarkan tanggal
        usort($eventDates, fn($a, $b) => strcmp($a['date'], $b['date']));

        $invoiceCounterByMonth = [];

        foreach ($eventDates as $idx => $item) {
            $eventDate = Carbon::parse($item['date']);
            // Durasi acara: mayoritas 2 hari (standar paket tenda), sebagian 1 hari atau 3 hari
            $durationDays = match ($idx % 7) {
                0 => 1,
                3, 6 => 3,
                default => 2,
            };
            $eventEndDate = $eventDate->copy()->addDays($durationDays - 1);

            $monthKey = $eventDate->format('m');
            $invoiceCounterByMonth[$monthKey] = ($invoiceCounterByMonth[$monthKey] ?? 0) + 1;
            $invoiceNumber = sprintf('NKB/%s/%02d', $monthKey, $invoiceCounterByMonth[$monthKey]);

            // Pilih paket (Tenda Set, Paket 1, Paket 2, Decor 6M, Decor 8M)
            $pkg = $packages[$idx % $packages->count()];
            $packagePrice = (float) $pkg->base_price;

            // Extra charge jika lebih dari 2 hari (@ 15% per hari ekstra)
            $extraDays = max(0, $durationDays - 2);
            $additionalDayCharge = $extraDays * 0.15 * $packagePrice;

            // Add-on realistis (1 - 3 add-on per booking)
            $selectedAddons = [];
            $addonTotal = 0;
            $addonCount = ($idx % 3) + 1;

            for ($a = 0; $a < $addonCount; $a++) {
                $targetAddon = $addons[($idx * 3 + $a) % $addons->count()];
                $qty = match ($targetAddon->unit) {
                    'pcs'   => 20 + (($idx * 10) % 60),
                    'meter' => 15 + (($idx * 5) % 25),
                    'unit'  => 1 + ($idx % 2),
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

            // Venue lokal Tegal/Slawi & Jarak dari Gudang Slawi Kulon
            $venueData = $venues[$idx % count($venues)];
            $distanceKm = (float) $venueData['dist'];

            // Hitung Ongkos Kirim: Free <= 10 km, selanjutnya 300.000 per 10 km
            $freeDistanceKm = 10.0;
            $transportFee = 0.0;
            if ($distanceKm > $freeDistanceKm) {
                $extraKm = $distanceKm - $freeDistanceKm;
                $tiers = ceil($extraKm / 10.0);
                $transportFee = $tiers * 300000.0;
            }

            // Subtotal kotor sebelum diskon
            $subtotalBeforeDiscount = $packagePrice + $additionalDayCharge + $addonTotal + $transportFee;

            // Diskon Vendor Rekanan (%) jika ada
            $discountPercent = match ($idx % 6) {
                0 => 5.0,
                2 => 10.0,
                5 => 15.0,
                default => 0.0,
            };
            $discountAmount = $discountPercent > 0 ? round(($discountPercent / 100) * $subtotalBeforeDiscount) : 0;

            // Grand Total Bersih
            $grandTotal = max(0, $subtotalBeforeDiscount - $discountAmount);

            if ($item['pay'] === 'lunas' || $item['status'] === 'completed') {
                $amountPaid = $grandTotal;
                $remaining = 0;
                $payType = 'lunas';
            } else {
                // DP minimal 1jt (default rules) atau sekitar 35% grand total
                $minDp = 1000000.0;
                $calculatedDp = round($grandTotal * 0.35, -5);
                $amountPaid = max($minDp, min($calculatedDp, $grandTotal));
                $remaining = max(0, $grandTotal - $amountPaid);
                $payType = 'dp';
            }

            // Tanggal dibuat reservasi
            $createdAt = $eventDate->copy()->subDays(rand(15, 60));
            if ($createdAt->isFuture()) {
                $createdAt = Carbon::today()->subDays(rand(1, 10));
            }

            // Pasangan, Orang Tua & Detail
            $coupleName = $coupleNames[$idx % count($coupleNames)];
            $parents = $parentsPool[$idx % count($parentsPool)];
            $firstGroom = explode(' & ', $coupleName)[0];
            $cleanName = strtolower(preg_replace('/[^a-zA-Z]/', '', $firstGroom));
            $instagram = '@' . $cleanName . '_wedding';
            $phone = sprintf('08%d%07d', [1, 2, 5][($idx % 3)], rand(1000000, 9999999));
            $email = $cleanName . '.' . rand(10, 99) . '@gmail.com';

            $eventTime = match ($idx % 4) {
                0 => '08:00 - Selesai',
                1 => '09:00 - 16:00 WIB',
                2 => '10:00 - 17:00 WIB',
                default => '08:00 - 15:00 WIB',
            };

            $vendorName = $discountPercent > 0 ? $muaList[$idx % count($muaList)] : null;
            $vendorMua = $muaList[($idx + 1) % count($muaList)];
            $vendorDecor = $decorList[$idx % count($decorList)];
            $vendorPhoto = $photoList[($idx + 2) % count($photoList)];
            $vendorMc = $mcList[$idx % count($mcList)];

            // Survey Request (~35% dari pemesan me-request survey lokasi)
            $surveyRequested = ($idx % 3 === 0);
            $surveyDate = null;
            $surveySlot = null;
            $surveyStatus = 'none';
            $surveyNotes = null;

            if ($surveyRequested) {
                // Survey dijadwalkan 7 s/d 18 hari sebelum hari H
                $surveyDate = $eventDate->copy()->subDays(rand(7, 18));
                $surveySlot = match ($idx % 3) {
                    0 => 'morning',
                    1 => 'afternoon',
                    default => 'flexible',
                };
                if ($eventDate->isPast()) {
                    $surveyStatus = 'completed';
                } else {
                    $surveyStatus = match ($idx % 4) {
                        0 => 'completed',
                        1, 2 => 'scheduled',
                        default => 'requested',
                    };
                }
                $surveyNotes = match ($idx % 5) {
                    0 => 'Mobil pikap muat masuk sampai depan teras, mohon kabari WA 1 jam sebelumnya.',
                    1 => 'Jalan kampung paving 3.5 meter, bisa koordinasi dengan ketua RT setempat.',
                    2 => 'Dekat masjid jami desa, halaman luas muat pasang tenda lengkung.',
                    3 => 'Ada pohon mangga di halaman depan yang perlu diukur ketinggian atap tenda.',
                    default => 'Rumah pagar warna hijau, mohon survey saat jam istirahat siang.',
                };
            }

            $booking = Booking::create([
                'invoice_number'        => $invoiceNumber,
                'customer_name'         => strtoupper($coupleName),
                'customer_phone'        => $phone,
                'customer_email'        => $email,
                'venue_address'         => $venueData['address'],
                'address_landmark'      => $venueData['landmark'],
                'parents_name'          => $parents,
                'instagram_account'     => $instagram,
                'event_time'            => $eventTime,
                'vendor_name'           => $vendorName,
                'discount_percent'      => $discountPercent,
                'discount_amount'       => $discountAmount,
                'owner_discount'        => 0,
                'delivery_distance_km'  => $distanceKm,
                'transport_fee'         => $transportFee,
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
                'survey_requested'      => $surveyRequested,
                'survey_date'           => $surveyDate?->toDateString(),
                'survey_time_slot'      => $surveySlot,
                'survey_notes'          => $surveyNotes,
                'survey_status'         => $surveyStatus,
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
