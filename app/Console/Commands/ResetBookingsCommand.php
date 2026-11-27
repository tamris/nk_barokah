<?php

namespace App\Console\Commands;

use App\Services\BookingResetService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ResetBookingsCommand extends Command
{
    protected $signature = 'bookings:reset
                            {--keep-proofs : Jangan hapus file bukti transfer di storage/app/public/proofs}
                            {--force : Lewati konfirmasi interaktif (wajib di environment production)}';

    protected $description = 'Reset seluruh data booking, auto-increment ID ke 1, dan nomor invoice ke NKB/MM/01 tanpa menghapus data master.';

    public function handle(BookingResetService $resetService): int
    {
        $bookingsCount = DB::table('bookings')->count();
        $addonsCount = DB::table('booking_addons')->count();
        $deleteProofs = !$this->option('keep-proofs');
        $proofFilesCount = Storage::disk('public')->exists('proofs')
            ? count(Storage::disk('public')->allFiles('proofs'))
            : 0;

        $this->newLine();
        $this->warn('=== RESET DATA BOOKING NK BAROKAH TENDA ===');
        $this->table(
            ['Target Data', 'Jumlah Saat Ini', 'Tindakan'],
            [
                ['bookings', $bookingsCount, 'HAPUS SEMUA & Reset ID -> 1'],
                ['booking_addons', $addonsCount, 'HAPUS SEMUA & Reset ID -> 1'],
                ['storage/public/proofs', $proofFilesCount . ' file', $deleteProofs ? 'HAPUS FILE BUKTI TRANSFER' : 'DIPERTAHANKAN (--keep-proofs)'],
                ['users, packages, addons, settings', 'Data Master', 'AMAN (TIDAK DISENTUH)'],
            ]
        );

        if (app()->environment('production') && !$this->option('force')) {
            $this->error('Dibatalkan: Environment production mewajibkan flag --force secara eksplisit.');
            return self::FAILURE;
        }

        if (!$this->option('force')) {
            $answer = $this->ask('Ketik persis "RESET BOOKING" untuk melanjutkan (atau tekan Enter untuk batal)');
            if (trim((string) $answer) !== 'RESET BOOKING') {
                $this->info('Reset dibatalkan. Tidak ada data yang diubah.');
                return self::SUCCESS;
            }
        }

        $summary = $resetService->resetAll(
            confirmed: true,
            deleteProofFiles: $deleteProofs,
            actor: 'artisan:bookings:reset'
        );

        $this->newLine();
        $this->info('Reset booking berhasil diselesaikan!');
        $this->line("• Booking dihapus       : {$summary['deleted_bookings']}");
        $this->line("• Booking add-on dihapus: {$summary['deleted_booking_addons']}");
        $this->line("• File bukti dihapus    : {$summary['deleted_proof_files']}");
        $this->line("• ID booking berikutnya : #{$summary['next_booking_id']}");
        $this->line("• No. invoice berikutnya: {$summary['next_invoice_number']}");

        return self::SUCCESS;
    }
}
