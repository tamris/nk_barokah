# Arsitektur & Spesifikasi Sistem Otomasi Wedding Organizer (Laravel 12) - Best Practice Edition

Dokumen ini merupakan panduan implementasi teknis untuk membangun sistem reservasi dan otomasi administrasi *Wedding Organizer* (WO) berbasis **Laravel 12, MySQL, Google API (Sheets & Calendar), Alpine.js, Tailwind CSS**, serta **PDF Receipt Generator (DomPDF)** dengan standar *clean architecture*, pencegahan bentrok jadwal (*anti-overbooking*), dan *financial audit trail*.

---

## 1. Arsitektur Alur & Prinsip Sistem

### 1.1 Diagram Alur Eksekusi (High-Level Sequence)

```
[Customer via HP / Web] 
       │ 
       ├─ Buka Form Reservasi (wo.client/booking)
       ├─ Kalender: Tanggal yang sudah penuh otomatis dinonaktifkan (Disabled)
       ├─ Pilih Paket Utama & Pilih Multi Add-on (Perhitungan Real-time via Alpine.js)
       ├─ Pilih Metode Bayar (DP / Pelunasan Penuh)
       ├─ Transfer Manual via m-Banking & Upload Struk Pembayaran
       └─ Submit Form
              │
              ▼
[Laravel Controller & Form Request]
       │
       ├─ Validasi Data Form & Upload File Struk
       ├─ Validasi Anti-Overbooking: Cek kuota tanggal (maksimal N acara per hari)
       ├─ Validasi Finansial: Hitung ulang total di Server-Side & validasi nominal DP
       ├─ DB Transaction:
       │      ├─ Simpan Header Transaksi ke tabel `bookings` (Status: `pending_verification`)
       │      └─ Simpan Snapshot Detail Add-on ke tabel `booking_addons`
       ├─ Dispatch Asynchronous Queue Job:
       │      ├─ Append Record ke Google Sheets (Status: PENDING VERIFIKASI)
       │      └─ Create All-Day Event di Google Calendar Owner ([PENDING REVIEW] H+1 End Date)
       │
       ▼
[Response ke Customer]
       ├─ Redirect ke Halaman Sukses
       ├─ Unduh E-Receipt / Bukti Reservasi Resmi (PDF)
       └─ Tombol Direct CTA: "Konfirmasi ke WhatsApp Admin" (Pre-filled message)
```

### 1.2 Prinsip Best Practice:
1. **Asynchronous API Sync (Queue & Jobs):** Pemanggilan Google Sheets dan Google Calendar API **tidak boleh** menahan request HTTP customer. Eksekusi eksternal dialihkan ke background worker (`QUEUE_CONNECTION=database` / `redis`).
2. **Kalkulasi Server-Side Mutlak:** Perhitungan JavaScript di browser hanya untuk visual feedback klien. Backend menghitung ulang seluruh harga paket dan rincian add-on dari database saat penyimpanan.
3. **Pencegahan Overbooking (Anti-Overbooking):** Sistem memvalidasi ketersediaan tanggal berdasarkan kapasitas WO (default: maksimal 1 acara per hari) baik di backend maupun visual frontend.
4. **Alur Finansial 2 Tahap (Pending -> Confirmed):** Mengingat pembayaran via transfer bank manual, status awal transaksi adalah `pending_verification` dengan tanda terima reservasi sementara. Status menjadi `confirmed` (kuitansi sah) setelah diverifikasi oleh admin.
5. **Skalabilitas Add-on (Relational Pivot Snapshot):** Menggunakan tabel relasi `booking_addons` sehingga WO bebas menambah layanan add-on baru tanpa mengubah struktur tabel utama.
6. **Kepatuhan Format Google Calendar API:** Event seharian (*all-day event*) menggunakan parameter `end` eksklusif ($H+1$ dari tanggal acara) agar tidak terjadi galat API.

---

## 2. Skema Database (Database Migrations)

### 2.1 Migration: `packages` & `addons`

```php
// database/migrations/2026_10_30_000001_create_packages_and_addons_tables.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('packages', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('slug', 100)->unique();
            $table->decimal('base_price', 15, 2);
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('addons', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('unit', 50)->default('pcs'); // misal: pcs, porsi, set
            $table->decimal('unit_price', 15, 2);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('addons');
        Schema::dropIfExists('packages');
    }
};
```

### 2.2 Migration: `bookings`

```php
// database/migrations/2026_10_30_000002_create_bookings_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number', 50)->unique(); // INV-202610-XXXX
            $table->string('customer_name', 150);
            $table->string('customer_phone', 25);
            $table->string('customer_email', 100)->nullable();
            $table->date('event_date');
            
            // Relasi & Detail Snapshot Paket
            $table->foreignId('package_id')->constrained('packages');
            $table->string('package_name'); 
            $table->decimal('package_price', 15, 2);

            // Ringkasan Add-on & Finansial
            $table->decimal('addon_total_price', 15, 2)->default(0);
            $table->decimal('grand_total', 15, 2);
            $table->decimal('amount_paid', 15, 2);
            $table->decimal('remaining_balance', 15, 2);
            $table->enum('payment_type', ['dp', 'lunas']);
            $table->string('payment_proof_path');

            // Status Siklus Hidup Transaksi
            $table->enum('status', [
                'pending_verification', 
                'confirmed', 
                'rejected', 
                'completed'
            ])->default('pending_verification');

            // Integrasi Status Eksternal
            $table->boolean('synced_to_sheets')->default(false);
            $table->string('google_calendar_event_id')->nullable();

            $table->timestamps();

            // Index untuk kecepatan query pengecekan tanggal
            $table->index(['event_date', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
```

### 2.3 Migration: `booking_addons` (Detail Relasi Snapshot)

```php
// database/migrations/2026_10_30_000003_create_booking_addons_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('booking_addons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained('bookings')->cascadeOnDelete();
            $table->foreignId('addon_id')->constrained('addons');
            $table->string('addon_name', 100);
            $table->decimal('unit_price', 15, 2);
            $table->integer('quantity');
            $table->decimal('subtotal', 15, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('booking_addons');
    }
};
```

---

## 3. Konfigurasi Sistem (`config/wo.php`)

```php
// config/wo.php
return [
    // Batas maksimal acara pernikahan per tanggal (default: 1 acara eksklusif)
    'max_events_per_day' => (int) env('WO_MAX_EVENTS_PER_DAY', 1),

    // Nomor WhatsApp resmi CS / Owner WO untuk konfirmasi (format: 628xxx)
    'admin_whatsapp'     => env('WO_ADMIN_WHATSAPP', '6281234567890'),

    // Batas minimal DP yang diperbolehkan (misal Rp 1.000.000)
    'min_dp_amount'      => (float) env('WO_MIN_DP_AMOUNT', 1000000),

    // Persentase minimal DP (opsional, misal 20% dari Grand Total)
    'min_dp_percentage'  => (float) env('WO_MIN_DP_PERCENTAGE', 20),
];
```

---

## 4. Implementasi Backend (Models, Form Request, & Controller)

### 4.1 Models & Relasi

```php
// app/Models/Booking.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Booking extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'event_date'        => 'date',
        'package_price'     => 'decimal:2',
        'addon_total_price' => 'decimal:2',
        'grand_total'       => 'decimal:2',
        'amount_paid'       => 'decimal:2',
        'remaining_balance' => 'decimal:2',
        'synced_to_sheets'  => 'boolean',
    ];

    public function package(): BelongsTo
    {
        return $this->belongsTo(Package::class);
    }

    public function addons(): HasMany
    {
        return $this->hasMany(BookingAddon::class);
    }
}
```

```php
// app/Models/BookingAddon.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BookingAddon extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'subtotal'   => 'decimal:2',
        'quantity'   => 'integer',
    ];

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function addon(): BelongsTo
    {
        return $this->belongsTo(Addon::class);
    }
}
```

### 4.2 Form Request Validation (`StoreBookingRequest`)

```php
// app/Http/Requests/StoreBookingRequest.php
namespace App\Http\Requests;

use App\Models\Booking;
use App\Models\Package;
use App\Models\Addon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreBookingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $maxPerDay = config('wo.max_events_per_day', 1);

        return [
            'customer_name'  => ['required', 'string', 'max:150'],
            'customer_phone' => ['required', 'regex:/^(\+62|62|0)8[1-9][0-9]{6,10}$/'],
            'customer_email' => ['nullable', 'email', 'max:100'],
            'event_date'     => [
                'required', 
                'date', 
                'after_or_equal:today',
                function ($attribute, $value, $fail) use ($maxPerDay) {
                    $activeBookings = Booking::where('event_date', $value)
                        ->whereIn('status', ['pending_verification', 'confirmed'])
                        ->count();

                    if ($activeBookings >= $maxPerDay) {
                        $fail('Jadwal untuk tanggal tersebut sudah penuh. Silakan pilih tanggal lain.');
                    }
                }
            ],
            'package_id'     => ['required', 'exists:packages,id'],
            'addons'         => ['nullable', 'array'],
            'addons.*.id'    => ['required_with:addons', 'exists:addons,id'],
            'addons.*.qty'   => ['nullable', 'integer', 'min:0', 'max:1000'],
            'payment_type'   => ['required', 'in:dp,lunas'],
            'nominal_dp'     => ['required_if:payment_type,dp', 'nullable', 'numeric'],
            'payment_proof'  => ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:3072'], // Max 3MB
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function ($validator) {
            if ($this->payment_type === 'dp') {
                $minDpConfig = config('wo.min_dp_amount', 1000000);
                $nominalDp = (float) $this->nominal_dp;

                // Hitung estimasi grand total server-side untuk memvalidasi batas atas & bawah DP
                $package = Package::find($this->package_id);
                if (!$package) return;

                $addonTotal = 0;
                if (!empty($this->addons)) {
                    foreach ($this->addons as $item) {
                        $qty = (int) ($item['qty'] ?? 0);
                        if ($qty > 0) {
                            $addon = Addon::find($item['id']);
                            if ($addon) {
                                $addonTotal += ($addon->unit_price * $qty);
                            }
                        }
                    }
                }

                $grandTotal = $package->base_price + $addonTotal;

                if ($nominalDp < $minDpConfig) {
                    $validator->errors()->add('nominal_dp', 'Nominal DP minimal adalah Rp ' . number_format($minDpConfig, 0, ',', '.') . '.');
                }

                if ($nominalDp > $grandTotal) {
                    $validator->errors()->add('nominal_dp', 'Nominal DP tidak boleh melebihi Grand Total tagihan (Rp ' . number_format($grandTotal, 0, ',', '.') . ').');
                }
            }
        });
    }

    public function messages(): array
    {
        return [
            'customer_phone.regex'      => 'Format nomor WhatsApp tidak valid (contoh: 081234567890).',
            'event_date.after_or_equal' => 'Tanggal acara harus hari ini atau di masa mendatang.',
            'nominal_dp.required_if'    => 'Nominal DP wajib diisi jika memilih metode pembayaran DP.',
            'payment_proof.max'         => 'Ukuran berkas bukti transfer tidak boleh melebihi 3MB.',
        ];
    }
}
```

### 4.3 Booking Controller (`BookingController`)

```php
// app/Http/Controllers/BookingController.php
namespace App\Http\Controllers;

use App\Http\Requests\StoreBookingRequest;
use App\Models\Booking;
use App\Models\Package;
use App\Models\Addon;
use App\Jobs\SyncBookingToGoogleServicesJob;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf;

class BookingController extends Controller
{
    public function create()
    {
        $packages = Package::where('is_active', true)->get();
        $addons = Addon::where('is_active', true)->get();

        // Ambil daftar tanggal yang sudah penuh (booked out) untuk di-disable pada kalender UI
        $maxPerDay = config('wo.max_events_per_day', 1);
        $bookedDates = Booking::whereIn('status', ['pending_verification', 'confirmed'])
            ->where('event_date', '>=', now()->toDateString())
            ->groupBy('event_date')
            ->havingRaw('count(*) >= ?', [$maxPerDay])
            ->pluck('event_date')
            ->map(fn($d) => \Carbon\Carbon::parse($d)->toDateString())
            ->toArray();

        return view('booking.form', compact('packages', 'addons', 'bookedDates'));
    }

    public function store(StoreBookingRequest $request)
    {
        $validated = $request->validated();

        $booking = DB::transaction(function () use ($request, $validated) {
            $package = Package::findOrFail($validated['package_id']);
            
            // Kalkulasi Server-Side untuk Add-ons
            $addonsPayload = [];
            $addonTotalPrice = 0;

            if (!empty($validated['addons'])) {
                foreach ($validated['addons'] as $item) {
                    $qty = (int) ($item['qty'] ?? 0);
                    if ($qty > 0) {
                        $addon = Addon::findOrFail($item['id']);
                        $subtotal = $addon->unit_price * $qty;
                        $addonTotalPrice += $subtotal;

                        $addonsPayload[] = [
                            'addon_id'   => $addon->id,
                            'addon_name' => $addon->name,
                            'unit_price' => $addon->unit_price,
                            'quantity'   => $qty,
                            'subtotal'   => $subtotal,
                        ];
                    }
                }
            }

            $grandTotal = $package->base_price + $addonTotalPrice;

            if ($validated['payment_type'] === 'lunas') {
                $amountPaid = $grandTotal;
                $remaining = 0;
            } else {
                $amountPaid = (float) $validated['nominal_dp'];
                $remaining = max(0, $grandTotal - $amountPaid);
            }

            // Simpan Berkas Bukti Transfer dengan nama hash yang aman
            $proofPath = $request->file('payment_proof')->store('proofs/' . date('Y/m'), 'public');

            // Format nomor invoice unik: INV-YYYYMMDD-XXXXX
            $invoiceNumber = 'INV-' . date('Ymd') . '-' . strtoupper(Str::random(5));

            // Buat Record Utama Booking
            $booking = Booking::create([
                'invoice_number'     => $invoiceNumber,
                'customer_name'      => $validated['customer_name'],
                'customer_phone'     => $validated['customer_phone'],
                'customer_email'     => $validated['customer_email'],
                'event_date'         => $validated['event_date'],
                'package_id'         => $package->id,
                'package_name'       => $package->name,
                'package_price'      => $package->base_price,
                'addon_total_price'  => $addonTotalPrice,
                'grand_total'        => $grandTotal,
                'amount_paid'        => $amountPaid,
                'remaining_balance'  => $remaining,
                'payment_type'       => $validated['payment_type'],
                'payment_proof_path' => $proofPath,
                'status'             => 'pending_verification',
            ]);

            // Simpan Detail Relasi Add-ons
            if (!empty($addonsPayload)) {
                $booking->addons()->createMany($addonsPayload);
            }

            return $booking;
        });

        // Trigger Queue Job ke Google Sheets & Calendar (Background Process)
        SyncBookingToGoogleServicesJob::dispatch($booking);

        return redirect()->route('booking.success', $booking->invoice_number);
    }

    public function success(string $invoiceNumber)
    {
        $booking = Booking::with('addons')->where('invoice_number', $invoiceNumber)->firstOrFail();
        $adminWhatsapp = config('wo.admin_whatsapp', '6281234567890');

        // Pre-filled WhatsApp message
        $waMessage = urlencode(
            "Halo Admin Wedding Organizer, saya sudah melakukan reservasi dengan rincian:\n" .
            "- No. Invoice: {$booking->invoice_number}\n" .
            "- Nama: {$booking->customer_name}\n" .
            "- Tanggal Acara: " . \Carbon\Carbon::parse($booking->event_date)->isoFormat('D MMMM Y') . "\n" .
            "- Pembayaran: " . strtoupper($booking->payment_type) . " (Rp " . number_format($booking->amount_paid, 0, ',', '.') . ")\n\n" .
            "Mohon diverifikasi pembayaran saya. Terima kasih!"
        );

        $whatsappUrl = "https://wa.me/{$adminWhatsapp}?text={$waMessage}";

        return view('booking.success', compact('booking', 'whatsappUrl'));
    }

    public function downloadReceipt(string $invoiceNumber)
    {
        $booking = Booking::with('addons')->where('invoice_number', $invoiceNumber)->firstOrFail();
        $pdf = Pdf::loadView('pdf.receipt', compact('booking'))->setPaper('a4', 'portrait');

        return $pdf->download("Kuitansi-{$booking->invoice_number}.pdf");
    }
}
```

---

## 5. Background Job: Integrasi Google Sheets & Google Calendar

Pastikan kredensial Google Service Account tersimpan di path `storage/app/google/service-account.json`. Berikan akses edit pada file Google Sheet dan Google Calendar target ke email service account.

```php
// app/Jobs/SyncBookingToGoogleServicesJob.php
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

        $client = new Client();
        $client->setAuthConfig(storage_path('app/google/service-account.json'));
        $client->addScope([
            Sheets::SPREADSHEETS,
            Calendar::CALENDAR
        ]);

        $this->syncToGoogleSheets($client);
        $this->syncToGoogleCalendar($client);
    }

    protected function syncToGoogleSheets(Client $client): void
    {
        try {
            $service = new Sheets($client);
            $spreadsheetId = config('services.google.sheet_id');
            $range = 'Orders!A:L';

            $addonsSummary = $this->booking->addons->map(function ($addon) {
                return "{$addon->addon_name} ({$addon->quantity}x)";
            })->implode(', ');

            if (empty($addonsSummary)) {
                $addonsSummary = '-';
            }

            $statusText = match ($this->booking->status) {
                'confirmed'            => 'TERVERIFIKASI',
                'pending_verification' => 'PENDING VERIFIKASI',
                default                => strtoupper($this->booking->status),
            };

            $values = [
                [
                    now()->format('Y-m-d H:i:s'),
                    $this->booking->invoice_number,
                    $this->booking->customer_name,
                    $this->booking->customer_phone,
                    Carbon::parse($this->booking->event_date)->format('Y-m-d'),
                    $this->booking->package_name,
                    $addonsSummary,
                    $this->booking->grand_total,
                    $this->booking->amount_paid,
                    $this->booking->remaining_balance,
                    $statusText . " (" . strtoupper($this->booking->payment_type) . ")",
                    asset('storage/' . $this->booking->payment_proof_path)
                ]
            ];

            $body = new Sheets\ValueRange(['values' => $values]);
            $params = ['valueInputOption' => 'USER_ENTERED'];

            $service->spreadsheets_values->append($spreadsheetId, $range, $body, $params);

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

            $prefix = $this->booking->status === 'confirmed' ? '[CONFIRMED]' : '[PENDING REVIEW]';
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
            // Parameter 'start' adalah tanggal acara, parameter 'end' harus H+1 (exclusive).
            $startDate = Carbon::parse($this->booking->event_date)->toDateString();
            $endDate   = Carbon::parse($this->booking->event_date)->addDay()->toDateString();

            $event = new Event([
                'summary'     => $summary,
                'description' => $description,
                'start'       => new EventDateTime(['date' => $startDate]),
                'end'         => new EventDateTime(['date' => $endDate]),
            ]);

            $createdEvent = $service->events->insert($calendarId, $event);

            $this->booking->update(['google_calendar_event_id' => $createdEvent->getId()]);
        } catch (\Exception $e) {
            Log::error('Google Calendar Sync Failed: ' . $e->getMessage(), ['booking_id' => $this->booking->id]);
            throw $e;
        }
    }
}
```

---

## 6. UI Frontend Responsif & Clean (Alpine.js + Tailwind CSS)

### 6.1 Halaman Form Booking Publik (`resources/views/booking/form.blade.php`)

```html
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulir Reservasi & Kuitansi Resmi Wedding Organizer</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>[x-cloak] { display: none !important; }</style>
</head>
<body class="bg-slate-50 text-slate-800 antialiased min-h-screen py-8 px-4 sm:px-6">

<div class="max-w-3xl mx-auto" 
     x-data="bookingApp({
        packages: {{ json_encode($packages) }},
        addons: {{ json_encode($addons) }},
        bookedDates: {{ json_encode($bookedDates) }},
        minDpAmount: {{ config('wo.min_dp_amount', 1000000) }}
     })" 
     x-cloak>

    <!-- Header Branding -->
    <header class="text-center mb-8">
        <span class="text-xs font-bold tracking-widest text-amber-600 uppercase">Luxury Wedding Planner</span>
        <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight mt-1">Formulir Reservasi Jadwal</h1>
        <p class="text-slate-600 mt-2 text-sm sm:text-base">Kunci tanggal acara pernikahan Anda dan dapatkan tanda terima resmi seketika.</p>
    </header>

    @if ($errors->any())
        <div class="mb-6 p-4 bg-rose-50 border-l-4 border-rose-500 rounded-r-xl shadow-sm">
            <h3 class="text-sm font-semibold text-rose-800">Mohon lengkapi data dengan benar:</h3>
            <ul class="mt-2 text-xs text-rose-700 list-disc list-inside space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('booking.store') }}" method="POST" enctype="multipart/form-data" class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 sm:p-8 space-y-8">
        @csrf

        <!-- Bagian 1: Data Pengantin & Jadwal Acara -->
        <div>
            <h2 class="text-base font-bold text-slate-900 border-b border-slate-100 pb-2 mb-4">1. Identitas Calon Pengantin</h2>
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold uppercase text-slate-600 mb-1">Nama Calon Mempelai</label>
                    <input type="text" name="customer_name" required value="{{ old('customer_name') }}"
                           class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 text-sm focus:ring-2 focus:ring-slate-900 focus:outline-none transition"
                           placeholder="Contoh: Rian Pratama & Siska Anggraini">
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase text-slate-600 mb-1">Nomor WhatsApp Aktif</label>
                    <input type="tel" name="customer_phone" required value="{{ old('customer_phone') }}"
                           class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 text-sm focus:ring-2 focus:ring-slate-900 focus:outline-none transition"
                           placeholder="081234567890">
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase text-slate-600 mb-1">Email (Opsional)</label>
                    <input type="email" name="customer_email" value="{{ old('customer_email') }}"
                           class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 text-sm focus:ring-2 focus:ring-slate-900 focus:outline-none transition"
                           placeholder="nama@email.com">
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold uppercase text-slate-600 mb-1">Tanggal Acara Pernikahan</label>
                    <input type="date" name="event_date" required min="{{ date('Y-m-d') }}" 
                           x-model="selectedDate" @change="checkDateAvailability"
                           class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 text-sm focus:ring-2 focus:ring-slate-900 focus:outline-none transition">
                    
                    <p x-show="isDateBooked" class="text-xs text-rose-600 font-semibold mt-1">
                        ⚠️ Maaf, jadwal pada tanggal tersebut sudah terisi penuh oleh klien lain. Silakan pilih tanggal lain.
                    </p>
                </div>
            </div>
        </div>

        <!-- Bagian 2: Paket Utama & Multi Add-on -->
        <div>
            <h2 class="text-base font-bold text-slate-900 border-b border-slate-100 pb-2 mb-4">2. Pilihan Paket & Tambahan Layanan</h2>
            
            <div class="space-y-6">
                <!-- Paket Utama -->
                <div>
                    <label class="block text-xs font-semibold uppercase text-slate-600 mb-2">Pilih Paket Layanan</label>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <template x-for="pkg in packages" :key="pkg.id">
                            <label class="relative flex flex-col p-4 border rounded-xl cursor-pointer transition"
                                   :class="selectedPackageId == pkg.id ? 'border-slate-900 bg-slate-50 ring-2 ring-slate-900' : 'border-slate-200 hover:border-slate-400'">
                                <input type="radio" name="package_id" :value="pkg.id" class="sr-only" x-model="selectedPackageId">
                                <span class="font-bold text-sm text-slate-900" x-text="pkg.name"></span>
                                <span class="text-xs text-slate-500 mt-1" x-text="formatRupiah(pkg.base_price)"></span>
                                <p class="text-[11px] text-slate-500 mt-2 line-clamp-2" x-text="pkg.description"></p>
                            </label>
                        </template>
                    </div>
                </div>

                <!-- Multi Add-ons Looping -->
                <div x-show="addons.length > 0">
                    <label class="block text-xs font-semibold uppercase text-slate-600 mb-2">Penambahan Fasilitas (Add-ons)</label>
                    <div class="space-y-3">
                        <template x-for="(addon, idx) in addons" :key="addon.id">
                            <div class="flex items-center justify-between p-3.5 border border-slate-200 rounded-xl bg-white">
                                <div>
                                    <h4 class="text-xs font-bold text-slate-900" x-text="addon.name"></h4>
                                    <span class="text-[11px] text-slate-500" x-text="formatRupiah(addon.unit_price) + ' / ' + addon.unit"></span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <input type="hidden" :name="`addons[${idx}][id]`" :value="addon.id">
                                    <input type="number" :name="`addons[${idx}][qty]`" min="0" max="1000" 
                                           x-model.number="addon.selectedQty"
                                           class="w-20 px-2.5 py-1.5 rounded-lg border border-slate-300 text-center text-xs font-semibold focus:outline-none focus:ring-1 focus:ring-slate-900"
                                           placeholder="0">
                                    <span class="text-xs text-slate-500 w-8" x-text="addon.unit"></span>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bagian 3: Ringkasan Kalkulasi -->
        <div class="bg-slate-50 border border-slate-200 rounded-xl p-5 space-y-3">
            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-700">Rincian Perhitungan Biaya</h3>
            
            <div class="flex justify-between text-xs text-slate-600">
                <span>Paket: <span class="font-medium text-slate-800" x-text="currentPackageName"></span></span>
                <span class="font-semibold text-slate-900" x-text="formatRupiah(currentPackagePrice)"></span>
            </div>

            <template x-for="addon in activeAddons" :key="addon.id">
                <div class="flex justify-between text-xs text-slate-600">
                    <span>Add-on: <span x-text="addon.name + ' (' + addon.selectedQty + ' ' + addon.unit + ')'"></span></span>
                    <span class="font-semibold text-slate-900" x-text="formatRupiah(addon.selectedQty * addon.unit_price)"></span>
                </div>
            </template>

            <div class="border-t border-slate-200 pt-2 flex justify-between text-sm font-extrabold text-slate-900">
                <span>Grand Total</span>
                <span class="text-base text-slate-900" x-text="formatRupiah(grandTotal)"></span>
            </div>
        </div>

        <!-- Bagian 4: Opsi Pembayaran & Upload Struk -->
        <div>
            <h2 class="text-base font-bold text-slate-900 border-b border-slate-100 pb-2 mb-4">3. Pembayaran & Konfirmasi Transfer</h2>

            <div class="grid grid-cols-2 gap-3 mb-4">
                <label class="flex items-center justify-center p-3.5 border rounded-xl cursor-pointer transition text-center"
                       :class="paymentType === 'dp' ? 'border-slate-900 bg-slate-50 ring-2 ring-slate-900 font-bold' : 'border-slate-200 hover:border-slate-400'">
                    <input type="radio" name="payment_type" value="dp" class="sr-only" x-model="paymentType">
                    <span class="text-xs text-slate-900">Bayar Uang Muka (DP)</span>
                </label>

                <label class="flex items-center justify-center p-3.5 border rounded-xl cursor-pointer transition text-center"
                       :class="paymentType === 'lunas' ? 'border-slate-900 bg-slate-50 ring-2 ring-slate-900 font-bold' : 'border-slate-200 hover:border-slate-400'">
                    <input type="radio" name="payment_type" value="lunas" class="sr-only" x-model="paymentType">
                    <span class="text-xs text-slate-900">Pelunasan Penuh (100%)</span>
                </label>
            </div>

            <div class="mb-4" x-show="paymentType === 'dp'">
                <label class="block text-xs font-semibold uppercase text-slate-600 mb-1">Nominal DP yang Ditransfer</label>
                <div class="relative">
                    <span class="absolute left-3.5 top-2.5 text-xs font-bold text-slate-400">Rp</span>
                    <input type="number" name="nominal_dp" x-model.number="nominalDp"
                           class="w-full pl-10 pr-3.5 py-2.5 rounded-lg border border-slate-300 text-sm focus:ring-2 focus:ring-slate-900 focus:outline-none transition"
                           placeholder="Contoh: 5000000">
                </div>
                <div class="flex justify-between text-xs text-slate-500 mt-1.5">
                    <span>Sisa tagihan: <strong class="text-slate-800" x-text="formatRupiah(remainingBalance)"></strong></span>
                    <span class="text-[11px] text-amber-700">Min. DP: <span x-text="formatRupiah(minDpAmount)"></span></span>
                </div>
            </div>

            <!-- Rekening Resmi -->
            <div class="p-4 bg-amber-50 border border-amber-200 rounded-xl mb-4 text-xs text-amber-900 flex flex-col sm:flex-row sm:justify-between sm:items-center gap-2">
                <div>
                    <p class="font-bold">Rekening Resmi Pembayaran Wedding Organizer:</p>
                    <p class="mt-0.5">Bank BCA: <span class="font-mono font-bold text-sm text-amber-950">8831-294-819</span> a.n. Luxury Wedding Planner</p>
                </div>
                <span class="inline-block text-[10px] bg-amber-200 font-semibold px-2 py-1 rounded-md self-start sm:self-center">Transfer Antar Bank / m-Banking</span>
            </div>

            <!-- Upload Bukti -->
            <div>
                <label class="block text-xs font-semibold uppercase text-slate-600 mb-1">Unggah Struk / Tangkapan Layar Transfer</label>
                <input type="file" name="payment_proof" required accept="image/*,application/pdf"
                       class="w-full text-xs text-slate-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-slate-900 file:text-white hover:file:bg-slate-800 transition cursor-pointer">
                <span class="text-[11px] text-slate-400 mt-1 block">Format diperbolehkan: JPG, PNG, atau PDF (Maksimal 3MB)</span>
            </div>
        </div>

        <button type="submit" :disabled="isDateBooked"
                class="w-full py-4 bg-slate-900 hover:bg-slate-800 disabled:bg-slate-400 text-white font-bold rounded-xl text-sm transition shadow-sm hover:shadow active:scale-[0.99] flex items-center justify-center gap-2">
            <span>Konfirmasi Reservasi & Dapatkan Kuitansi</span>
        </button>
    </form>
</div>

<script>
function bookingApp(config) {
    return {
        packages: config.packages,
        addons: config.addons.map(a => ({ ...a, selectedQty: 0 })),
        bookedDates: config.bookedDates,
        minDpAmount: config.minDpAmount,
        selectedPackageId: config.packages.length > 0 ? config.packages[0].id : null,
        selectedDate: '',
        paymentType: 'dp',
        nominalDp: config.minDpAmount,

        get isDateBooked() {
            return this.selectedDate && this.bookedDates.includes(this.selectedDate);
        },
        get currentPackage() {
            return this.packages.find(p => p.id == this.selectedPackageId);
        },
        get currentPackageName() {
            return this.currentPackage ? this.currentPackage.name : '-';
        },
        get currentPackagePrice() {
            return this.currentPackage ? parseFloat(this.currentPackage.base_price) : 0;
        },
        get activeAddons() {
            return this.addons.filter(a => parseInt(a.selectedQty) > 0);
        },
        get addonTotalPrice() {
            return this.activeAddons.reduce((sum, a) => sum + (parseInt(a.selectedQty) * parseFloat(a.unit_price)), 0);
        },
        get grandTotal() {
            return this.currentPackagePrice + this.addonTotalPrice;
        },
        get remainingBalance() {
            if (this.paymentType === 'lunas') return 0;
            const diff = this.grandTotal - (parseFloat(this.nominalDp) || 0);
            return diff > 0 ? diff : 0;
        },
        formatRupiah(val) {
            return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(val || 0);
        }
    }
}
</script>
</body>
</html>
```

### 6.2 Halaman Sukses & Download Kuitansi (`resources/views/booking/success.blade.php`)

```html
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reservasi Berhasil - Wedding Organizer</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-50 text-slate-800 min-h-screen flex items-center justify-center p-4">
    <div class="max-w-md w-full bg-white rounded-2xl shadow-sm border border-slate-200 p-8 text-center space-y-6">
        
        <div class="w-16 h-16 bg-amber-100 text-amber-700 rounded-full flex items-center justify-center mx-auto text-2xl font-black">
            ⏳
        </div>

        <div>
            <span class="inline-block px-3 py-1 bg-amber-50 text-amber-800 text-xs font-mono font-bold rounded-full mb-2 border border-amber-200">
                MENUNGGU VERIFIKASI PEMBAYARAN
            </span>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">Reservasi Berhasil Diajukan!</h1>
            <p class="text-xs text-slate-500 mt-2">
                Terima kasih, <strong>{{ $booking->customer_name }}</strong>. Jadwal acara pernikahan Anda untuk tanggal 
                <strong>{{ \Carbon\Carbon::parse($booking->event_date)->isoFormat('D MMMM Y') }}</strong> 
                sedang diproses untuk verifikasi mutasi bank.
            </p>
        </div>

        <div class="bg-slate-50 border border-slate-100 rounded-xl p-4 text-left text-xs space-y-2">
            <div class="flex justify-between"><span class="text-slate-500">No. Invoice:</span> <span class="font-mono font-bold">{{ $booking->invoice_number }}</span></div>
            <div class="flex justify-between"><span class="text-slate-500">Paket:</span> <span class="font-bold">{{ $booking->package_name }}</span></div>
            <div class="flex justify-between"><span class="text-slate-500">Tipe Pembayaran:</span> <span class="font-bold uppercase text-slate-900">{{ $booking->payment_type }}</span></div>
            <div class="flex justify-between"><span class="text-slate-500">Jumlah Ditransfer:</span> <span class="font-bold text-emerald-700">Rp {{ number_format($booking->amount_paid, 0, ',', '.') }}</span></div>
            @if($booking->remaining_balance > 0)
                <div class="flex justify-between"><span class="text-slate-500">Sisa Pelunasan:</span> <span class="font-bold text-rose-600">Rp {{ number_format($booking->remaining_balance, 0, ',', '.') }}</span></div>
            @endif
        </div>

        <div class="space-y-3">
            <!-- CTA Utama: Konfirmasi WhatsApp Cepat -->
            <a href="{{ $whatsappUrl }}" target="_blank"
               class="flex items-center justify-center gap-2 w-full py-3.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl text-sm transition shadow-sm">
                <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981z"/></svg>
                <span>Konfirmasi ke WhatsApp Admin</span>
            </a>

            <!-- Tombol Unduh Kuitansi PDF -->
            <a href="{{ route('booking.receipt', $booking->invoice_number) }}" 
               class="block w-full py-3 bg-white border border-slate-300 hover:bg-slate-50 text-slate-800 font-semibold rounded-xl text-xs transition">
                Unduh Tanda Terima Reservasi (PDF)
            </a>
        </div>

        <p class="text-[11px] text-slate-400">Admin kami akan mencocokkan mutasi transfer Anda dalam waktu maksimal 1x24 jam.</p>
    </div>
</body>
</html>
```

---

## 7. Template PDF Kuitansi Resmi (DomPDF Compatible)

```html
<!-- resources/views/pdf/receipt.blade.php -->
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Kuitansi Reservasi - {{ $booking->invoice_number }}</title>
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size: 11px; color: #333; line-height: 1.5; padding: 15px; }
        .header { border-bottom: 2px solid #111; padding-bottom: 12px; margin-bottom: 20px; }
        .brand-title { font-size: 18px; font-weight: bold; text-transform: uppercase; color: #111; margin: 0; }
        .brand-subtitle { font-size: 9px; color: #666; margin: 2px 0 0 0; }
        .invoice-meta { float: right; text-align: right; }
        .invoice-meta h2 { margin: 0; font-size: 14px; color: #111; }
        .invoice-meta p { margin: 2px 0; font-size: 9px; color: #555; }
        .clear { clear: both; }
        
        .client-info { margin-bottom: 20px; padding: 10px; background: #fafafa; border: 1px solid #eee; border-radius: 4px; }
        .client-info table { width: 100%; }
        .client-info td { font-size: 10px; padding: 2px 0; }
        
        .items-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .items-table th { background: #111; color: #fff; text-align: left; padding: 7px; font-size: 10px; text-transform: uppercase; }
        .items-table td { padding: 8px 7px; border-bottom: 1px solid #eee; font-size: 10px; }
        .text-right { text-align: right; }
        
        .summary-table { width: 45%; float: right; border-collapse: collapse; }
        .summary-table td { padding: 4px 6px; font-size: 10px; }
        .summary-total { font-weight: bold; font-size: 11px; border-top: 1px solid #111; }
        
        .badge { display: inline-block; padding: 2px 6px; font-weight: bold; font-size: 9px; border-radius: 3px; }
        .badge-pending { background: #fef3c7; color: #92400e; }
        .badge-confirmed { background: #d1fae5; color: #065f46; }
        
        .watermark { text-align: center; margin-top: 25px; padding: 8px; border: 1px dashed #bbb; background: #fcfcfc; font-size: 9px; color: #666; }
        .footer { margin-top: 40px; border-top: 1px solid #eee; padding-top: 10px; font-size: 8px; color: #888; text-align: center; }
    </style>
</head>
<body>

    <div class="header">
        <div class="invoice-meta">
            <h2>{{ $booking->status === 'confirmed' ? 'KUITANSI RESMI' : 'TANDA TERIMA RESERVASI' }}</h2>
            <p>No: <strong>{{ $booking->invoice_number }}</strong></p>
            <p>Tanggal Terbit: {{ now()->format('d/m/Y H:i') }}</p>
            <span class="badge {{ $booking->status === 'confirmed' ? 'badge-confirmed' : 'badge-pending' }}">
                STATUS: {{ $booking->status === 'confirmed' ? 'TERVERIFIKASI' : 'MENUNGGU VERIFIKASI' }}
            </span>
        </div>
        <div>
            <h1 class="brand-title">LUXURY WEDDING ORGANIZER</h1>
            <p class="brand-subtitle">Professional Wedding Planner & Event Management</p>
            <p class="brand-subtitle">Jl. Ringroad Barat No. 88, Yogyakarta | WhatsApp: {{ config('wo.admin_whatsapp', '0812-3456-7890') }}</p>
        </div>
        <div class="clear"></div>
    </div>

    <div class="client-info">
        <table>
            <tr>
                <td width="20%"><strong>Nama Klien:</strong></td>
                <td width="40%">{{ $booking->customer_name }}</td>
                <td width="20%"><strong>Tanggal Acara:</strong></td>
                <td width="20%">{{ \Carbon\Carbon::parse($booking->event_date)->isoFormat('D MMMM Y') }}</td>
            </tr>
            <tr>
                <td><strong>No. Kontak (WA):</strong></td>
                <td>{{ $booking->customer_phone }}</td>
                <td><strong>Metode Bayar:</strong></td>
                <td>Transfer Manual BCA ({{ strtoupper($booking->payment_type) }})</td>
            </tr>
        </table>
    </div>

    <table class="items-table">
        <thead>
            <tr>
                <th>Rincian Layanan / Fasilitas</th>
                <th width="15%">Qty</th>
                <th width="20%" class="text-right">Harga Satuan</th>
                <th width="25%" class="text-right">Total</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><strong>{{ $booking->package_name }}</strong><br><span style="color:#666; font-size:8px;">Paket Utama Pernikahan</span></td>
                <td>1 Paket</td>
                <td class="text-right">Rp {{ number_format($booking->package_price, 0, ',', '.') }}</td>
                <td class="text-right">Rp {{ number_format($booking->package_price, 0, ',', '.') }}</td>
            </tr>
            @foreach($booking->addons as $addon)
            <tr>
                <td><strong>Add-on: {{ $addon->addon_name }}</strong></td>
                <td>{{ $addon->quantity }}</td>
                <td class="text-right">Rp {{ number_format($addon->unit_price, 0, ',', '.') }}</td>
                <td class="text-right">Rp {{ number_format($addon->subtotal, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <table class="summary-table">
        <tr>
            <td>Grand Total:</td>
            <td class="text-right">Rp {{ number_format($booking->grand_total, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td>Nominal Dibayar ({{ strtoupper($booking->payment_type) }}):</td>
            <td class="text-right" style="color:#059669; font-weight:bold;">
                Rp {{ number_format($booking->amount_paid, 0, ',', '.') }}
            </td>
        </tr>
        <tr class="summary-total">
            <td>Sisa Pelunasan:</td>
            <td class="text-right" style="color: {{ $booking->remaining_balance > 0 ? '#dc2626' : '#111' }};">
                Rp {{ number_format($booking->remaining_balance, 0, ',', '.') }}
            </td>
        </tr>
    </table>
    <div class="clear"></div>

    <div class="watermark">
        @if($booking->status === 'confirmed')
            ✓ Pembayaran telah diverifikasi secara sah oleh bagian administrasi keuangan Luxury Wedding Organizer.
        @else
            ⏳ Dokumen ini merupakan tanda terima digital sementara. Jadwal terkunci setelah bukti pembayaran diverifikasi oleh admin.
        @endif
    </div>

    <div class="footer">
        Dicetak secara otomatis oleh Sistem Otomasi Wedding Organizer. Simpan kuitansi digital ini sebagai referensi administrasi Anda.
    </div>

</body>
</html>
```

---

## 8. Setup & Konfigurasi Google Service Account

1. Buka [Google Cloud Console](https://console.cloud.google.com/) $\rightarrow$ Buat Project baru (misal: `wo-automation`).
2. Masuk ke **APIs & Services** $\rightarrow$ Aktifkan:
   * **Google Sheets API**
   * **Google Calendar API**
3. Masuk ke **Credentials** $\rightarrow$ Buat **Service Account** $\rightarrow$ Buat & Unduh Key dalam format **JSON**.
4. Ganti nama file key tersebut menjadi `service-account.json` dan tempatkan di path folder Laravel: `storage/app/google/service-account.json`.
5. Buka spreadsheet Google Sheets owner $\rightarrow$ Klik tombol **Share (Bagikan)** $\rightarrow$ Masukkan email Service Account (`xxx@project-id.iam.gserviceaccount.com`) dengan akses **Editor**.
6. Buka Google Calendar owner $\rightarrow$ Masuk ke **Settings of calendar** $\rightarrow$ **Share with specific people** $\rightarrow$ Masukkan email Service Account dengan izin **Make changes to events**.
7. Konfigurasi variabel di file `.env` Laravel:

```env
APP_NAME="NK Barokah Wedding Organizer"
APP_URL=http://localhost:8000

# Pengaturan Kapasitas & WhatsApp WO
WO_MAX_EVENTS_PER_DAY=1
WO_ADMIN_WHATSAPP=6281234567890
WO_MIN_DP_AMOUNT=1000000

# Integrasi Google API
GOOGLE_SHEET_ID=1a2B3c4D5e6F7g8H9i0J_SpreadsheetID_Disini
GOOGLE_CALENDAR_ID=alamat_email_owner@gmail.com

# Asynchronous Queue
QUEUE_CONNECTION=database
```

---

## 9. Portal & Dashboard Admin/Owner

Sistem dilengkapi panel admin khusus bagi Owner/Pengelola WO untuk mengaudit dan mengeksekusi konfirmasi transaksi:

### 9.1 Akses & Autentikasi
* URL Login: `/admin/login` (atau `/login`)
* Akun Default: `owner@nkbarokah.com` / Kata Sandi: `password`

### 9.2 Fitur Dashboard Admin
1. **Pusat Metrik:** Total reservasi, jumlah menunggu verifikasi, jumlah terkonfirmasi, dan total uang masuk sah.
2. **Filter & Pencarian:** Tab filter status (*Perlu Verifikasi*, *Dikonfirmasi*, *Selesai*, *Ditolak*) serta kolom pencarian instan (nama pengantin, no invoice, no WA).
3. **Pemeriksaan Bukti Transfer:** Preview berkas gambar atau PDF struk transfer langsung di halaman detail.
4. **Tombol Verifikasi Sekali Klik:**
   * Klik tombol **"✓ Verifikasi & Setujui"** $\rightarrow$ status otomatis menjadi `confirmed`, men-dispatch pembaruan status ke Google Sheets & Google Calendar.
   * Tombol **"✕ Tolak"** $\rightarrow$ status menjadi `rejected`.
5. **Direct WhatsApp Klien:** Tombol untuk mengirimkan pesan konfirmasi sah langsung ke WhatsApp calon pengantin.
6. **Katalog Master Paket & Add-ons (`/admin/masters`):** Menambah, mengedit harga, dan mengaktifkan/menonaktifkan paket dan add-on tanpa menyentuh database.

