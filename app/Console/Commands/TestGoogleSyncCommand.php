<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Google\Client;
use Google\Service\Sheets;
use Google\Service\Calendar;

class TestGoogleSyncCommand extends Command
{
    protected $signature = 'google:test';
    protected $description = 'Uji koneksi Google Service Account ke Google Sheets dan Google Calendar';

    public function handle(): int
    {
        $this->info("=== PENGUJIAN KONEKSI GOOGLE SERVICES (NK BAROKAH) ===");
        $this->newLine();

        $credentialsPath = storage_path('app/google/service-account.json');

        // 1. Cek Berkas JSON
        if (!file_exists($credentialsPath)) {
            $jsonFiles = glob(storage_path('app/google/*.json'));
            if (!empty($jsonFiles)) {
                $credentialsPath = $jsonFiles[0];
            } else {
                $this->error("[-] Berkas kredensial belum ditemukan di folder storage/app/google/");
                $this->warn("    Silakan unduh file Key JSON dari Google Cloud Console dan letakkan di folder tersebut.");
                return self::FAILURE;
            }
        }

        $jsonContent = json_decode(file_get_contents($credentialsPath), true);
        $clientEmail = $jsonContent['client_email'] ?? 'Tidak ditemukan';
        $projectId = $jsonContent['project_id'] ?? 'Tidak ditemukan';

        $this->info("[✓] Berkas kredensial ditemukan: " . basename($credentialsPath));
        $this->line("    Project ID: <comment>{$projectId}</comment>");
        $this->line("    Email Service Account: <comment>{$clientEmail}</comment>");
        $this->newLine();

        // Siapkan Google Client
        $client = new Client();
        $client->setAuthConfig($credentialsPath);
        $client->addScope([
            Sheets::SPREADSHEETS,
            Calendar::CALENDAR
        ]);

        // 2. Uji Koneksi Google Sheets
        $sheetId = config('services.google.sheet_id');
        $sheetName = config('services.google.sheet_name', 'Sheet1');

        $this->info("--- 1. Menguji Google Sheets ---");
        if (empty($sheetId)) {
            $this->warn("[-] GOOGLE_SHEET_ID di file .env masih KOSONG.");
        } else {
            try {
                $sheetsService = new Sheets($client);
                $spreadsheet = $sheetsService->spreadsheets->get($sheetId);
                $this->info("[✓] Sukses terhubung ke Google Sheet!");
                $this->line("    Judul Spreadsheet: <comment>{$spreadsheet->getProperties()->getTitle()}</comment>");

                // Cek apakah tab sheet ada
                $sheetTitles = collect($spreadsheet->getSheets())->map(fn($s) => $s->getProperties()->getTitle())->toArray();
                if (in_array($sheetName, $sheetTitles)) {
                    $this->info("    [✓] Tab sheet '{$sheetName}' ditemukan.");
                } else {
                    $this->warn("    [!] Tab sheet '{$sheetName}' tidak ditemukan. Tab yang ada: " . implode(', ', $sheetTitles));
                    $this->line("        Silakan sesuaikan GOOGLE_SHEET_NAME di .env dengan nama tab Anda.");
                }
            } catch (\Exception $e) {
                $this->error("[-] Gagal mengakses Google Sheets:");
                $this->line("    Pesan: " . $e->getMessage());
                $this->warn("    Tips: Pastikan spreadsheet sudah dibagikan (Share) ke email: {$clientEmail} sebagai Editor.");
            }
        }

        $this->newLine();

        // 3. Uji Koneksi Google Calendar
        $calendarId = config('services.google.calendar_id');
        $this->info("--- 2. Menguji Google Calendar ---");
        if (empty($calendarId)) {
            $this->warn("[-] GOOGLE_CALENDAR_ID di file .env masih KOSONG.");
        } else {
            try {
                $calendarService = new Calendar($client);
                $calendar = $calendarService->calendars->get($calendarId);
                $this->info("[✓] Sukses terhubung ke Google Calendar!");
                $this->line("    Nama Kalender: <comment>{$calendar->getSummary()}</comment>");
                $this->line("    Zona Waktu: <comment>{$calendar->getTimeZone()}</comment>");
            } catch (\Exception $e) {
                $this->error("[-] Gagal mengakses Google Calendar:");
                $this->line("    Pesan: " . $e->getMessage());
                $this->warn("    Tips: Pastikan kalender sudah di-share ke email: {$clientEmail} dengan izin 'Make changes to events'.");
            }
        }

        $this->newLine();
        $this->info("=== PENGUJIAN SELESAI ===");

        return self::SUCCESS;
    }
}

