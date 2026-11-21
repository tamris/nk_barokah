<?php

namespace App\Jobs;

use App\Models\Booking;
use Google\Client;
use Google\Service\Sheets;
use Google\Service\Calendar;
use Google\Service\Calendar\Event;
use Google\Service\Calendar\EventDateTime;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class SyncBookingToGoogleServicesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $backoff = 60; // retry delay 60 detik jika network gagal

    public function __construct(public Booking $booking) {}

    public function handle(): void
    {
        $this->booking->loadMissing('addons');

        $credentialsPath = storage_path('app/google/service-account.json');

        if (!file_exists($credentialsPath)) {
            $jsonFiles = glob(storage_path('app/google/*.json'));
            if (!empty($jsonFiles)) {
                $credentialsPath = $jsonFiles[0];
            } else {
                Log::warning('Google Service Account credentials file not found in storage/app/google. Skipping Google Sync for Booking #' . $this->booking->invoice_number);
                return;
            }
        }

        $client = new Client();
        $client->setAuthConfig($credentialsPath);
        $client->addScope([
            Sheets::SPREADSHEETS,
            Calendar::CALENDAR
        ]);

        if (config('services.google.sheet_id')) {
            $this->syncToGoogleSheets($client);
        } else {
            Log::info('Google Sheet ID not configured in .env. Skipping Google Sheets sync.');
        }

        if (config('services.google.calendar_id')) {
            $this->syncToGoogleCalendar($client);
        } else {
            Log::info('Google Calendar ID not configured in .env. Skipping Google Calendar sync.');
        }
    }

    protected function syncToGoogleSheets(Client $client): void
    {
        try {
            $service = new Sheets($client);
            $spreadsheetId = config('services.google.sheet_id');
            $sheetName = config('services.google.sheet_name', 'Sheet1');
            $range = "{$sheetName}!A:L";

            $addonsSummary = $this->booking->addons->map(function ($addon) {
                return "{$addon->addon_name} ({$addon->quantity}x)";
            })->implode(', ');

            if (empty($addonsSummary)) {
                $addonsSummary = '-';
            }

            // Menyesuaikan persis dengan opsi Data Validation (Dropdown Chips) di Google Sheets:
            // 1. "Menunggu Pembayaran" (Kuning) -> saat klien booking & menunggu verifikasi owner
            // 2. "DP" (Biru) -> saat terverifikasi pembayaran DP & masih ada sisa tagihan
            // 3. "Lunas" (Hijau) -> saat lunas / selesai
            // 4. "Batal" (Merah) -> saat pesanan ditolak
            $statusDropdown = match ($this->booking->status) {
                'pending_verification'   => 'Menunggu Pembayaran',
                'rejected'               => 'Batal',
                'confirmed', 'completed' => ($this->booking->remaining_balance > 0 && $this->booking->payment_type === 'dp') ? 'DP' : 'Lunas',
                default                  => 'Menunggu Pembayaran',
            };

            $values = [
                [
                    $this->booking->created_at ? $this->booking->created_at->format('Y-m-d H:i') : now()->format('Y-m-d H:i'),
                    $this->booking->invoice_number,
                    $this->booking->customer_name,
                    $this->booking->customer_phone,
                    $this->booking->formatted_event_date,
                    $this->booking->package_name,
                    $addonsSummary,
                    (float) $this->booking->grand_total,
                    (float) $this->booking->amount_paid,
                    (float) $this->booking->remaining_balance,
                    $statusDropdown,
                    asset('storage/' . $this->booking->payment_proof_path)
                ]
            ];

            $body = new Sheets\ValueRange(['values' => $values]);
            $params = ['valueInputOption' => 'USER_ENTERED'];

            // 1. Ambil data kolom B (No Invoice) untuk mencari apakah invoice ini sudah pernah dicatat sebelumnya
            $searchRange = "{$sheetName}!B:B";
            $response = $service->spreadsheets_values->get($spreadsheetId, $searchRange);
            $existingRows = $response->getValues() ?? [];

            $matchedRowIndex = null;
            $duplicateIndices = [];

            foreach ($existingRows as $index => $row) {
                if (!empty($row[0]) && trim($row[0]) === $this->booking->invoice_number) {
                    $sheetRowNumber = $index + 1; // Baris ke-N di Google Sheets (1-based index)
                    if ($matchedRowIndex === null) {
                        $matchedRowIndex = $sheetRowNumber;
                    } else {
                        $duplicateIndices[] = $sheetRowNumber;
                    }
                }
            }

            // Dapatkan sheet numeric ID (untuk menghapus baris duplikat jika ada)
            $sheetNumericId = 0;
            try {
                $spreadsheetInfo = $service->spreadsheets->get($spreadsheetId);
                foreach ($spreadsheetInfo->getSheets() as $sheet) {
                    if ($sheet->getProperties()->getTitle() === $sheetName) {
                        $sheetNumericId = $sheet->getProperties()->getSheetId();
                        break;
                    }
                }
            } catch (\Exception $e) {
                // Ignore jika gagal mendapatkan numeric ID
            }

            if ($matchedRowIndex !== null) {
                // UPDATE BARIS YANG SUDAH ADA (TIDAK MEMBUAT BARIS BARU)
                $updateRange = "{$sheetName}!A{$matchedRowIndex}:L{$matchedRowIndex}";
                $service->spreadsheets_values->update($spreadsheetId, $updateRange, $body, $params);

                // Hapus baris duplikat yang sebelumnya sempat terbuat (jika ada)
                if (!empty($duplicateIndices)) {
                    rsort($duplicateIndices);
                    $requests = [];
                    foreach ($duplicateIndices as $dupRow) {
                        $requests[] = new Sheets\Request([
                            'deleteDimension' => [
                                'range' => [
                                    'sheetId'    => $sheetNumericId,
                                    'dimension'  => 'ROWS',
                                    'startIndex' => $dupRow - 1, // 0-based
                                    'endIndex'   => $dupRow,
                                ]
                            ]
                        ]);
                    }

                    try {
                        $batchRequest = new Sheets\BatchUpdateSpreadsheetRequest(['requests' => $requests]);
                        $service->spreadsheets->batchUpdate($spreadsheetId, $batchRequest);
                    } catch (\Exception $e) {
                        foreach ($duplicateIndices as $dupRow) {
                            $service->spreadsheets_values->clear($spreadsheetId, "{$sheetName}!A{$dupRow}:L{$dupRow}", new Sheets\ClearValuesRequest());
                        }
                    }
                }
            } else {
                // Tambahkan baris baru HANYA jika invoice ini memang belum ada
                $appendRange = "{$sheetName}!A:L";
                $service->spreadsheets_values->append($spreadsheetId, $appendRange, $body, $params);
            }

            $this->booking->update(['synced_to_sheets' => true]);
        } catch (\Exception $e) {
            Log::error('Google Sheets Sync Failed: ' . $e->getMessage(), ['booking_id' => $this->booking->id]);
            throw $e;
        }
    }

    protected function syncToGoogleCalendar(Client $client): void
    {
        try {
            $service = new Calendar($client);
            $calendarId = config('services.google.calendar_id');

            $prefix = match ($this->booking->status) {
                'confirmed' => '[CONFIRMED]',
                'completed' => '[SELESAI]',
                'rejected'  => '[DITOLAK]',
                default     => '[PENDING REVIEW]',
            };
            $payType = strtoupper($this->booking->payment_type);
            $summary = "{$prefix} [{$payType}] Wedding: {$this->booking->customer_name}";

            $addonsText = $this->booking->addons->map(function ($addon) {
                return "- {$addon->addon_name}: {$addon->quantity} pcs (Rp " . number_format($addon->subtotal, 0, ',', '.') . ")";
            })->implode("\n");

            if (empty($addonsText)) {
                $addonsText = "- Tidak ada add-on tambahan";
            }

            $description = "No. Invoice: {$this->booking->invoice_number}\n"
                . "Status: {$this->booking->status}\n"
                . "Paket: {$this->booking->package_name}\n"
                . "Add-ons:\n{$addonsText}\n\n"
                . "Grand Total: Rp " . number_format($this->booking->grand_total, 0, ',', '.') . "\n"
                . "Dibayar: Rp " . number_format($this->booking->amount_paid, 0, ',', '.') . "\n"
                . "Sisa Tagihan: Rp " . number_format($this->booking->remaining_balance, 0, ',', '.') . "\n"
                . "Kontak WhatsApp: {$this->booking->customer_phone}\n"
                . "Bukti Transfer: " . asset('storage/' . $this->booking->payment_proof_path);

            // FIX GOOGLE CALENDAR ALL-DAY EVENT:
            // Parameter 'start' adalah tanggal acara, parameter 'end' harus H+1 dari hari terakhir (exclusive).
            $startDate = Carbon::parse($this->booking->event_date)->toDateString();
            $actualEnd = $this->booking->event_end_date ? Carbon::parse($this->booking->event_end_date) : Carbon::parse($this->booking->event_date);
            $endDate   = $actualEnd->addDay()->toDateString();

            $event = new Event([
                'summary'     => $summary,
                'description' => $description,
                'start'       => new EventDateTime(['date' => $startDate]),
                'end'         => new EventDateTime(['date' => $endDate]),
            ]);

            if ($this->booking->google_calendar_event_id) {
                $service->events->patch($calendarId, $this->booking->google_calendar_event_id, $event);
            } else {
                $createdEvent = $service->events->insert($calendarId, $event);
                $this->booking->update(['google_calendar_event_id' => $createdEvent->getId()]);
            }
        } catch (\Exception $e) {
            Log::error('Google Calendar Sync Failed: ' . $e->getMessage(), ['booking_id' => $this->booking->id]);
            throw $e;
        }
    }
}

