<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Package;
use App\Models\Addon;
use App\Models\Booking;
use App\Jobs\SyncBookingToGoogleServicesJob;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class AdminBookingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_guest_is_redirected_to_admin_login(): void
    {
        $response = $this->get(route('admin.bookings.index'));

        $response->assertRedirect(route('admin.login'));
    }

    public function test_admin_can_login_with_valid_credentials(): void
    {
        $response = $this->post(route('admin.login.post'), [
            'email'    => 'owner@nkbarokah.com',
            'password' => 'password',
        ]);

        $response->assertRedirect(route('admin.bookings.index'));
        $this->assertAuthenticated();
    }

    public function test_admin_cannot_login_with_invalid_credentials(): void
    {
        $response = $this->post(route('admin.login.post'), [
            'email'    => 'owner@nkbarokah.com',
            'password' => 'wrong-password',
        ]);

        $response->assertSessionHasErrors(['email']);
        $this->assertGuest();
    }

    public function test_authenticated_admin_can_view_bookings_list(): void
    {
        $admin = User::first();
        $package = Package::first();

        $booking = Booking::create([
            'invoice_number'     => 'INV-202610-0001',
            'customer_name'      => 'Ahmad & Siti',
            'customer_phone'     => '081234567890',
            'event_date'         => now()->addMonths(2)->format('Y-m-d'),
            'package_id'         => $package->id,
            'package_name'       => $package->name,
            'package_price'      => $package->base_price,
            'addon_total_price'  => 0,
            'grand_total'        => $package->base_price,
            'amount_paid'        => $package->base_price,
            'remaining_balance'  => 0,
            'payment_type'       => 'lunas',
            'payment_proof_path' => 'proofs/dummy.jpg',
            'status'             => 'pending_verification',
        ]);

        $response = $this->actingAs($admin)->get(route('admin.bookings.index'));

        $response->assertStatus(200);
        $response->assertSee('Ahmad & Siti');
        $response->assertSee('INV-202610-0001');
        $response->assertSee('x-for="c in paginatedClients"', false);
        $response->assertSee('x-for="b in paginatedBookings"', false);
        $response->assertSee('x-for="inv in paginatedInvoices"', false);
    }

    public function test_admin_can_confirm_pending_booking(): void
    {
        Queue::fake();

        $admin = User::first();
        $package = Package::first();

        $booking = Booking::create([
            'invoice_number'     => 'INV-202610-CONFIRM',
            'customer_name'      => 'Rian & Rini',
            'customer_phone'     => '081234567890',
            'event_date'         => now()->addMonths(2)->format('Y-m-d'),
            'package_id'         => $package->id,
            'package_name'       => $package->name,
            'package_price'      => $package->base_price,
            'addon_total_price'  => 0,
            'grand_total'        => $package->base_price,
            'amount_paid'        => $package->base_price,
            'remaining_balance'  => 0,
            'payment_type'       => 'lunas',
            'payment_proof_path' => 'proofs/dummy.jpg',
            'status'             => 'pending_verification',
        ]);

        $response = $this->actingAs($admin)->post(route('admin.bookings.confirm', $booking->id));

        $response->assertSessionHas('success');
        $this->assertEquals('confirmed', $booking->fresh()->status);
        Queue::assertPushed(SyncBookingToGoogleServicesJob::class);
    }

    public function test_admin_can_reject_booking(): void
    {
        Queue::fake();

        $admin = User::first();
        $package = Package::first();

        $booking = Booking::create([
            'invoice_number'     => 'INV-202610-REJECT',
            'customer_name'      => 'Penipu Struk Palsu',
            'customer_phone'     => '081234567890',
            'event_date'         => now()->addMonths(2)->format('Y-m-d'),
            'package_id'         => $package->id,
            'package_name'       => $package->name,
            'package_price'      => $package->base_price,
            'addon_total_price'  => 0,
            'grand_total'        => $package->base_price,
            'amount_paid'        => $package->base_price,
            'remaining_balance'  => 0,
            'payment_type'       => 'lunas',
            'payment_proof_path' => 'proofs/dummy.jpg',
            'status'             => 'pending_verification',
        ]);

        $response = $this->actingAs($admin)->post(route('admin.bookings.reject', $booking->id));

        $response->assertSessionHas('success');
        $this->assertEquals('rejected', $booking->fresh()->status);
        Queue::assertPushed(SyncBookingToGoogleServicesJob::class);
    }

    public function test_admin_can_complete_booking(): void
    {
        Queue::fake();

        $admin = User::first();
        $package = Package::first();

        $booking = Booking::create([
            'invoice_number'     => 'INV-202610-COMPLETE',
            'customer_name'      => 'Pengantin Bahagia',
            'customer_phone'     => '081234567890',
            'event_date'         => now()->addMonths(2)->format('Y-m-d'),
            'package_id'         => $package->id,
            'package_name'       => $package->name,
            'package_price'      => $package->base_price,
            'addon_total_price'  => 0,
            'grand_total'        => $package->base_price,
            'amount_paid'        => $package->base_price,
            'remaining_balance'  => 0,
            'payment_type'       => 'lunas',
            'payment_proof_path' => 'proofs/dummy.jpg',
            'status'             => 'confirmed',
        ]);

        $response = $this->actingAs($admin)->post(route('admin.bookings.complete', $booking->id));

        $response->assertSessionHas('success');
        $this->assertEquals('completed', $booking->fresh()->status);
        Queue::assertPushed(SyncBookingToGoogleServicesJob::class);
    }

    public function test_admin_can_settle_booking_and_stays_on_booking_detail(): void
    {
        Queue::fake();

        $admin = User::first();
        $package = Package::first();

        $booking = Booking::create([
            'invoice_number'     => 'INV-202610-SETTLE',
            'customer_name'      => 'Ahmad & Salma',
            'customer_phone'     => '081234567890',
            'event_date'         => now()->addMonths(2)->format('Y-m-d'),
            'duration_days'      => 1,
            'package_id'         => $package->id,
            'package_name'       => $package->name,
            'package_price'      => 10000000,
            'amount_paid'        => 3000000,
            'remaining_balance'  => 7000000,
            'grand_total'        => 10000000,
            'payment_type'       => 'dp',
            'payment_proof_path' => 'proofs/dummy.jpg',
            'status'             => 'confirmed',
        ]);

        $response = $this->actingAs($admin)->post(route('admin.bookings.settle', $booking->id));

        $response->assertSessionHas('success');
        $response->assertRedirect(route('admin.bookings.index', ['booking_id' => $booking->id]));
        $this->assertEquals(0, $booking->fresh()->remaining_balance);
        $this->assertEquals('lunas', $booking->fresh()->payment_type);
        Queue::assertPushed(SyncBookingToGoogleServicesJob::class);
    }

    public function test_admin_can_create_and_toggle_package(): void
    {
        $admin = User::first();

        $response = $this->actingAs($admin)->post(route('admin.packages.store'), [
            'name'        => 'Diamond Royal Package',
            'base_price'  => 99000000,
            'description' => 'Paket Sultan Bintang 5',
        ]);

        $response->assertSessionHas('success');
        $package = Package::where('name', 'Diamond Royal Package')->first();
        $this->assertNotNull($package);
        $this->assertTrue($package->is_active);

        // Toggle to inactive
        $this->actingAs($admin)->patch(route('admin.packages.toggle', $package->id));
        $this->assertFalse($package->fresh()->is_active);
    }

    public function test_admin_can_create_and_toggle_addon(): void
    {
        $admin = User::first();

        $response = $this->actingAs($admin)->post(route('admin.addons.store'), [
            'name'       => 'Panggung Hiburan Live Music',
            'unit'       => 'set',
            'unit_price' => 5000000,
        ]);

        $response->assertSessionHas('success');
        $addon = Addon::where('name', 'Panggung Hiburan Live Music')->first();
        $this->assertNotNull($addon);
        $this->assertTrue($addon->is_active);

        // Toggle to inactive
        $this->actingAs($admin)->patch(route('admin.addons.toggle', $addon->id));
        $this->assertFalse($addon->fresh()->is_active);
    }

    public function test_admin_bookings_payload_includes_multi_day_range_and_details(): void
    {
        $admin = User::first();
        $package = Package::first();
        $startDate = '2027-01-01';
        $endDate = '2027-01-02';

        $booking = Booking::create([
            'invoice_number'     => 'INV-202701-MULTIDAY',
            'customer_name'      => 'Muchamad Rizqi Pratama',
            'customer_phone'     => '085183125526',
            'event_date'         => $startDate,
            'event_end_date'     => $endDate,
            'duration_days'      => 2,
            'package_id'         => $package->id,
            'package_name'       => $package->name,
            'package_price'      => $package->base_price,
            'addon_total_price'  => 0,
            'additional_day_charge' => 0,
            'grand_total'        => $package->base_price,
            'amount_paid'        => $package->base_price,
            'remaining_balance'  => 0,
            'payment_type'       => 'lunas',
            'payment_proof_path' => 'proofs/dummy.jpg',
            'status'             => 'confirmed',
        ]);

        $response = $this->actingAs($admin)->get(route('admin.bookings.index'));
        $response->assertStatus(200);
        $response->assertSee('2027-01-01');
        $response->assertSee('2027-01-02');
        $response->assertSee('Muchamad Rizqi Pratama');

        $showResponse = $this->actingAs($admin)->get(route('admin.bookings.show', $booking->id));
        $showResponse->assertRedirect(route('admin.bookings.index', ['booking_id' => $booking->id]));
    }

    public function test_admin_can_access_settings_tab_and_trigger_sync_all(): void
    {
        Queue::fake();

        $admin = User::first();
        $package = Package::first();

        // Create a confirmed booking
        Booking::create([
            'invoice_number'     => 'INV-202701-SYNCALL',
            'customer_name'      => 'Budi & Ani',
            'customer_phone'     => '081234567899',
            'event_date'         => '2027-02-14',
            'package_id'         => $package->id,
            'package_name'       => $package->name,
            'package_price'      => $package->base_price,
            'addon_total_price'  => 0,
            'grand_total'        => $package->base_price,
            'amount_paid'        => $package->base_price,
            'remaining_balance'  => 0,
            'payment_type'       => 'lunas',
            'payment_proof_path' => 'proofs/dummy.jpg',
            'status'             => 'confirmed',
        ]);

        // 1. Assert settings elements are present in the admin bookings page
        $response = $this->actingAs($admin)->get(route('admin.bookings.index'));
        $response->assertStatus(200);
        $response->assertSee('Aturan & Kebijakan Acara', false);
        $response->assertSee('Durasi Standar Paket');
        $response->assertSee('Charge Hari Tambahan');
        $response->assertSee('Rekening Resmi Pembayaran');
        $response->assertSee('Integrasi Google Cloud');
        $response->assertSee('Google Spreadsheet ID');
        $response->assertSee('Google Calendar ID');
        $response->assertSee('Mulai Sinkronisasi Sekarang');
        $response->assertDontSee('Service Account Credentials');
        $response->assertDontSee('storage/app/google/service-account.json');

        // 2. Test updating business rules (including formatted rupiah string input)
        $rulesResponse = $this->actingAs($admin)->post(route('admin.settings.update_rules'), [
            'standard_package_max_days' => 3,
            'extra_day_charge_rate'     => 20,
            'min_dp_amount'             => 'Rp 1.500.000',
            'max_events_per_day'        => 2,
            'admin_whatsapp'            => '628111222333',
        ]);
        $rulesResponse->assertRedirect();
        $rulesResponse->assertSessionHas('success');
        $this->assertEquals(3, (int) \App\Models\Setting::get('standard_package_max_days'));
        $this->assertEquals(20, (float) \App\Models\Setting::get('extra_day_charge_rate'));
        $this->assertEquals(1500000, (float) \App\Models\Setting::get('min_dp_amount'));
        $this->assertEquals(2, (int) \App\Models\Setting::get('max_events_per_day'));
        $this->assertEquals('628111222333', \App\Models\Setting::get('admin_whatsapp'));

        // 3. Test updating bank accounts
        $banksResponse = $this->actingAs($admin)->post(route('admin.settings.update_banks'), [
            'bca_account_number' => '999888777',
            'bca_account_name'   => 'OWNER TEST BCA',
            'bri_account_number' => '111222333',
            'bri_account_name'   => 'OWNER TEST BRI',
        ]);
        $banksResponse->assertRedirect();
        $banksResponse->assertSessionHas('success');
        $this->assertEquals('999888777', \App\Models\Setting::get('bca_account_number'));
        $this->assertEquals('OWNER TEST BCA', \App\Models\Setting::get('bca_account_name'));

        // 4. Test updating Google Cloud settings (Google Sheets & Calendar IDs)
        $googleResponse = $this->actingAs($admin)->post(route('admin.settings.update_google'), [
            'google_sheet_id'    => 'TEST_SHEET_ID_123',
            'google_calendar_id' => 'test_cal@gmail.com',
        ]);
        $googleResponse->assertRedirect();
        $googleResponse->assertSessionHas('success');
        $this->assertEquals('TEST_SHEET_ID_123', \App\Models\Setting::get('google_sheet_id'));
        $this->assertEquals('test_cal@gmail.com', \App\Models\Setting::get('google_calendar_id'));

        // 5. Trigger sync all
        $syncResponse = $this->actingAs($admin)->post(route('admin.settings.sync_all'));
        $syncResponse->assertRedirect();
        $syncResponse->assertSessionHas('success');

        Queue::assertPushed(SyncBookingToGoogleServicesJob::class);
    }

    public function test_business_rules_settings_dynamically_affect_booking_calculations(): void
    {
        $admin = User::first();
        $package = Package::first();

        // 1. Update rules: standard package is 1 day, extra charge is 20%
        $this->actingAs($admin)->post(route('admin.settings.update_rules'), [
            'standard_package_max_days' => 1,
            'extra_day_charge_rate'     => 20,
            'min_dp_amount'             => 500000,
            'max_events_per_day'        => 1,
            'admin_whatsapp'            => '62899999999',
        ]);

        // 2. Client submits a 3-day booking (e.g. 2027-03-01 to 2027-03-03 -> 2 extra days)
        $response = $this->post(route('booking.store'), [
            'customer_name'  => 'Test Dynamic Rules',
            'customer_phone' => '081234567890',
            'event_date'     => '2027-03-01',
            'event_end_date' => '2027-03-03',
            'package_id'     => $package->id,
            'payment_type'   => 'lunas',
            'payment_proof'  => \Illuminate\Http\UploadedFile::fake()->create('proof.jpg', 500),
        ]);

        $response->assertRedirect();

        $booking = Booking::where('customer_name', 'Test Dynamic Rules')->first();
        $this->assertNotNull($booking);
        $this->assertEquals(3, $booking->duration_days);

        // Expected additional charge = 2 extra days * 20% * base_price
        $expectedExtraCharge = 2 * 0.20 * (float) $package->base_price;
        $this->assertEquals($expectedExtraCharge, (float) $booking->additional_day_charge);
        $this->assertEquals((float) $package->base_price + $expectedExtraCharge, (float) $booking->grand_total);
    }
}

