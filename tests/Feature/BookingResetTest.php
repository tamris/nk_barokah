<?php

namespace Tests\Feature;

use App\Models\Addon;
use App\Models\Booking;
use App\Models\Package;
use App\Models\Setting;
use App\Models\User;
use App\Services\BookingResetService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use InvalidArgumentException;
use Tests\TestCase;

class BookingResetTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_reset_service_refuses_to_run_without_explicit_confirmation(): void
    {
        $package = Package::where('base_price', '>', 0)->first();

        Booking::create([
            'invoice_number'     => 'NKB/11/01',
            'customer_name'      => 'AMAN TIDAK TERHAPUS',
            'customer_phone'     => '081234567890',
            'event_date'         => now()->addDays(10)->format('Y-m-d'),
            'package_id'         => $package->id,
            'package_name'       => $package->name,
            'package_price'      => $package->base_price,
            'addon_total_price'  => 0,
            'grand_total'        => $package->base_price,
            'amount_paid'        => $package->base_price,
            'remaining_balance'  => 0,
            'payment_type'       => 'lunas',
            'payment_proof_path' => '',
            'status'             => 'confirmed',
        ]);

        $service = app(BookingResetService::class);

        $this->expectException(InvalidArgumentException::class);
        try {
            $service->resetAll();
        } finally {
            $this->assertEquals(1, Booking::count());
        }
    }

    public function test_reset_service_clears_bookings_resets_id_and_invoice_to_one_and_preserves_master_data(): void
    {
        Queue::fake();
        Storage::fake('public');

        $package = Package::where('base_price', '>', 0)->first();
        $addon = Addon::first();
        Setting::set('admin_whatsapp', '6282255969313');

        $usersBefore = User::count();
        $packagesBefore = Package::count();
        $addonsBefore = Addon::count();
        $settingsBefore = Setting::count();

        // Buat file dummy di luar proofs/ yang TIDAK boleh ikut terhapus
        Storage::disk('public')->put('branding/logo.png', 'keep-me');

        // Buat 3 booking dummy + booking_addons + bukti transfer
        for ($i = 1; $i <= 3; $i++) {
            $proofPath = "proofs/2026/11/dummy-{$i}.jpg";
            Storage::disk('public')->put($proofPath, "fake-image-{$i}");

            $b = Booking::create([
                'invoice_number'     => 'NKB/' . date('m') . '/0' . $i,
                'customer_name'      => "DUMMY CLIENT {$i}",
                'customer_phone'     => '08123456789' . $i,
                'event_date'         => now()->addDays(10 + $i)->format('Y-m-d'),
                'package_id'         => $package->id,
                'package_name'       => $package->name,
                'package_price'      => $package->base_price,
                'addon_total_price'  => $addon->unit_price,
                'grand_total'        => $package->base_price + $addon->unit_price,
                'amount_paid'        => $package->base_price + $addon->unit_price,
                'remaining_balance'  => 0,
                'payment_type'       => 'lunas',
                'payment_proof_path' => $proofPath,
                'status'             => 'confirmed',
            ]);

            $b->addons()->create([
                'addon_id'   => $addon->id,
                'addon_name' => $addon->name,
                'unit_price' => $addon->unit_price,
                'quantity'   => 1,
                'subtotal'   => $addon->unit_price,
            ]);
        }

        $service = app(BookingResetService::class);
        $summary = $service->resetAll(confirmed: true, deleteProofFiles: true, actor: 'phpunit');

        // 1. Semua booking & booking_addons bersih
        $this->assertEquals(3, $summary['deleted_bookings']);
        $this->assertEquals(3, $summary['deleted_booking_addons']);
        $this->assertEquals(3, $summary['deleted_proof_files']);
        $this->assertEquals(1, $summary['next_booking_id']);
        $this->assertEquals('NKB/' . date('m') . '/01', $summary['next_invoice_number']);
        $this->assertEquals(0, Booking::count());
        $this->assertDatabaseCount('booking_addons', 0);

        // 2. File bukti terhapus, file non-proofs tetap utuh
        Storage::disk('public')->assertMissing('proofs/2026/11/dummy-1.jpg');
        Storage::disk('public')->assertExists('branding/logo.png');

        // 3. Master data 100% utuh
        $this->assertEquals($usersBefore, User::count());
        $this->assertEquals($packagesBefore, Package::count());
        $this->assertEquals($addonsBefore, Addon::count());
        $this->assertEquals($settingsBefore, Setting::count());

        // 4. Booking baru setelah reset dimulai lagi dari ID = 1 dan Invoice = NKB/MM/01
        $response = $this->post(route('booking.store'), [
            'event_type'     => 'pernikahan',
            'customer_name'  => 'PESANAN PERTAMA RESMI',
            'customer_phone' => '081299998888',
            'event_date'     => now()->addMonths(2)->format('Y-m-d'),
            'package_id'     => $package->id,
            'payment_type'   => 'lunas',
            'payment_proof'  => UploadedFile::fake()->create('fresh.jpg', 200),
        ]);

        $response->assertRedirect();
        $firstBooking = Booking::first();
        $this->assertNotNull($firstBooking);
        $this->assertEquals(1, $firstBooking->id);
        $this->assertEquals('NKB/' . date('m') . '/01', $firstBooking->invoice_number);
    }

    public function test_reset_service_can_preserve_proof_files_when_requested(): void
    {
        Storage::fake('public');

        $package = Package::where('base_price', '>', 0)->first();
        Storage::disk('public')->put('proofs/keep.jpg', 'proof-bytes');

        Booking::create([
            'invoice_number'     => 'NKB/11/99',
            'customer_name'      => 'KEEP PROOF TEST',
            'customer_phone'     => '081234567890',
            'event_date'         => now()->addDays(15)->format('Y-m-d'),
            'package_id'         => $package->id,
            'package_name'       => $package->name,
            'package_price'      => $package->base_price,
            'addon_total_price'  => 0,
            'grand_total'        => $package->base_price,
            'amount_paid'        => $package->base_price,
            'remaining_balance'  => 0,
            'payment_type'       => 'lunas',
            'payment_proof_path' => 'proofs/keep.jpg',
            'status'             => 'confirmed',
        ]);

        $summary = app(BookingResetService::class)->resetAll(confirmed: true, deleteProofFiles: false);

        $this->assertEquals(0, Booking::count());
        $this->assertEquals(0, $summary['deleted_proof_files']);
        Storage::disk('public')->assertExists('proofs/keep.jpg');
    }

    public function test_artisan_command_requires_confirmation_and_resets_cleanly(): void
    {
        Storage::fake('public');

        $package = Package::where('base_price', '>', 0)->first();

        Booking::create([
            'invoice_number'     => 'NKB/11/01',
            'customer_name'      => 'CLI RESET TEST',
            'customer_phone'     => '081234567890',
            'event_date'         => now()->addDays(20)->format('Y-m-d'),
            'package_id'         => $package->id,
            'package_name'       => $package->name,
            'package_price'      => $package->base_price,
            'addon_total_price'  => 0,
            'grand_total'        => $package->base_price,
            'amount_paid'        => $package->base_price,
            'remaining_balance'  => 0,
            'payment_type'       => 'lunas',
            'payment_proof_path' => '',
            'status'             => 'confirmed',
        ]);

        // 1. Konfirmasi salah -> tidak menghapus apa-apa
        $this->artisan('bookings:reset')
            ->expectsQuestion('Ketik persis "RESET BOOKING" untuk melanjutkan (atau tekan Enter untuk batal)', 'salah')
            ->expectsOutput('Reset dibatalkan. Tidak ada data yang diubah.')
            ->assertExitCode(0);

        $this->assertEquals(1, Booking::count());

        // 2. Konfirmasi benar -> bersih tuntas
        $this->artisan('bookings:reset')
            ->expectsQuestion('Ketik persis "RESET BOOKING" untuk melanjutkan (atau tekan Enter untuk batal)', 'RESET BOOKING')
            ->expectsOutput('Reset booking berhasil diselesaikan!')
            ->assertExitCode(0);

        $this->assertEquals(0, Booking::count());
    }
}
