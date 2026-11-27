<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use InvalidArgumentException;

class BookingResetService
{
    /**
     * Reset seluruh data transaksi booking (bookings & booking_addons) sampai bersih,
     * mengembalikan auto-increment ID ke 1, dan mengembalikan urutan nomor invoice ke NKB/MM/01.
     *
     * AMAN: Tidak pernah menyentuh tabel master (users, packages, addons, settings).
     *
     * @param  bool    $confirmed         Wajib true sebagai pengaman agar tidak terpanggil tidak sengaja.
     * @param  bool    $deleteProofFiles  Hapus file bukti transfer di disk public (folder proofs/).
     * @param  string  $actor             Identitas pemanggil untuk catatan audit log.
     * @return array{
     *     deleted_bookings: int,
     *     deleted_booking_addons: int,
     *     deleted_proof_files: int,
     *     next_booking_id: int,
     *     next_invoice_number: string
     * }
     */
    public function resetAll(
        bool $confirmed = false,
        bool $deleteProofFiles = true,
        string $actor = 'system'
    ): array {
        if (!$confirmed) {
            throw new InvalidArgumentException(
                'Konfirmasi eksplisit (confirmed: true) wajib diberikan sebelum mereset seluruh data booking.'
            );
        }

        $deletedBookings = DB::table('bookings')->count();
        $deletedAddons = DB::table('booking_addons')->count();

        $proofPaths = $deleteProofFiles
            ? DB::table('bookings')
                ->whereNotNull('payment_proof_path')
                ->where('payment_proof_path', '!=', '')
                ->pluck('payment_proof_path')
                ->all()
            : [];

        $driver = DB::getDriverName();

        Schema::disableForeignKeyConstraints();
        try {
            if ($driver === 'pgsql') {
                DB::statement('TRUNCATE TABLE booking_addons, bookings RESTART IDENTITY CASCADE');
            } else {
                DB::table('booking_addons')->truncate();
                DB::table('bookings')->truncate();

                if (in_array($driver, ['mysql', 'mariadb'], true)) {
                    DB::statement('ALTER TABLE booking_addons AUTO_INCREMENT = 1');
                    DB::statement('ALTER TABLE bookings AUTO_INCREMENT = 1');
                } elseif ($driver === 'sqlite') {
                    $hasSequenceTable = DB::selectOne(
                        "SELECT name FROM sqlite_master WHERE type = 'table' AND name = 'sqlite_sequence'"
                    );
                    if ($hasSequenceTable) {
                        DB::table('sqlite_sequence')
                            ->whereIn('name', ['booking_addons', 'bookings'])
                            ->delete();
                    }
                }
            }
        } finally {
            Schema::enableForeignKeyConstraints();
        }

        $deletedProofFiles = 0;
        if ($deleteProofFiles) {
            $deletedProofFiles = $this->cleanupProofFiles($proofPaths);
        }

        $nextInvoice = 'NKB/' . date('m') . '/01';

        $summary = [
            'deleted_bookings'       => $deletedBookings,
            'deleted_booking_addons' => $deletedAddons,
            'deleted_proof_files'    => $deletedProofFiles,
            'next_booking_id'        => 1,
            'next_invoice_number'    => $nextInvoice,
        ];

        Log::warning('Seluruh data booking telah direset bersih oleh ' . $actor, $summary);

        return $summary;
    }

    /**
     * Hapus file bukti transfer hanya di dalam folder proofs/ pada disk public.
     *
     * @param  array<int, string>  $referencedPaths
     */
    protected function cleanupProofFiles(array $referencedPaths): int
    {
        $disk = Storage::disk('public');
        $deletedCount = 0;

        // 1. Hapus semua file di direktori proofs/ (termasuk sub-folder YYYY/MM)
        if ($disk->exists('proofs')) {
            $allProofFiles = $disk->allFiles('proofs');
            foreach ($allProofFiles as $file) {
                if ($disk->delete($file)) {
                    $deletedCount++;
                }
            }

            // Bersihkan sub-direktori kosong dari yang terdalam terlebih dahulu
            $dirs = $disk->allDirectories('proofs');
            usort($dirs, fn (string $a, string $b) => substr_count($b, '/') <=> substr_count($a, '/'));
            foreach ($dirs as $dir) {
                if ($disk->exists($dir) && empty($disk->files($dir)) && empty($disk->directories($dir))) {
                    $disk->deleteDirectory($dir);
                }
            }
        }

        // 2. Pastikan path yang tercatat di DB (jika masih ada & aman di dalam proofs/) ikut terhapus
        foreach ($referencedPaths as $rawPath) {
            $path = ltrim(str_replace('\\', '/', trim((string) $rawPath)), '/');
            if ($path === '' || str_contains($path, '..') || !str_starts_with($path, 'proofs/')) {
                continue;
            }
            if ($disk->exists($path) && $disk->delete($path)) {
                $deletedCount++;
            }
        }

        return $deletedCount;
    }
}
