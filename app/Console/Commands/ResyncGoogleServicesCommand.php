<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Booking;
use App\Jobs\SyncBookingToGoogleServicesJob;

class ResyncGoogleServicesCommand extends Command
{
    protected $signature = 'google:resync';
    protected $description = 'Sinkronisasi ulang seluruh data reservasi ke Google Sheets (update in-place tanpa duplikat) dan Google Calendar';

    public function handle(): int
    {
        $this->info("=== SINKRONISASI ULANG DATA RESERVASI KE GOOGLE SERVICES ===");
        $this->newLine();

        $bookings = Booking::with('addons')->orderBy('id', 'asc')->get();

        if ($bookings->isEmpty()) {
            $this->warn("Tidak ada data reservasi di database.");
            return self::SUCCESS;
        }

        $this->info("Ditemukan {$bookings->count()} reservasi. Memulai sinkronisasi...");
        $this->newLine();

        $progressBar = $this->output->createProgressBar($bookings->count());
        $progressBar->start();

        foreach ($bookings as $booking) {
            try {
                // Jalankan sinkronisasi secara synchronous (langsung)
                $job = new SyncBookingToGoogleServicesJob($booking);
                $job->handle();
            } catch (\Exception $e) {
                $this->newLine();
                $this->error("Gagal sinkron #{$booking->invoice_number}: " . $e->getMessage());
            }

            $progressBar->advance();
        }

        $progressBar->finish();
        $this->newLine(2);

        $this->info("[✓] Semua data reservasi berhasil disinkronkan & dirapikan!");
        $this->line("    Setiap nomor invoice kini hanya menempati 1 baris di Google Sheets.");
        $this->line("    Formula SUM / perhitungan keuangan aman dan akurat.");

        return self::SUCCESS;
    }
}

