<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Package;
use App\Models\Addon;
use App\Models\Booking;
use App\Jobs\SyncBookingToGoogleServicesJob;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
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

    public function test_admin_can_login_with_six_digit_pin(): void
    {
        $response = $this->post(route('admin.login.post'), [
            'pin' => '123456',
        ]);

        $response->assertRedirect(route('admin.bookings.index'));
        $this->assertAuthenticated();
    }

    public function test_admin_cannot_login_with_invalid_pin(): void
    {
        $response = $this->post(route('admin.login.post'), [
            'pin' => '999999',
        ]);

        $response->assertSessionHasErrors(['pin']);
        $this->assertGuest();
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

        // Update package
        $updateResp = $this->actingAs($admin)->put(route('admin.packages.update', $package->id), [
            'name'        => 'Diamond Royal Package (Updated)',
            'base_price'  => 105000000,
            'description' => 'Paket Sultan Bintang 5 Plus',
        ]);
        $updateResp->assertSessionHas('success');
        $this->assertEquals('Diamond Royal Package (Updated)', $package->fresh()->name);
        $this->assertEquals(105000000, (float) $package->fresh()->base_price);

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

        // Update addon
        $updateResp = $this->actingAs($admin)->put(route('admin.addons.update', $addon->id), [
            'name'       => 'Panggung Hiburan Live Music (VIP)',
            'unit'       => 'panggung',
            'unit_price' => 6500000,
        ]);
        $updateResp->assertSessionHas('success');
        $this->assertEquals('Panggung Hiburan Live Music (VIP)', $addon->fresh()->name);
        $this->assertEquals('panggung', $addon->fresh()->unit);
        $this->assertEquals(6500000, (float) $addon->fresh()->unit_price);

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
        $response->assertSee('1 - 2 Jan 2027');
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
        $response->assertSee('Operational Rules & Event Policies', false);
        $response->assertSee('Standard Package Duration');
        $response->assertSee('Extra Day Charge Rate');
        $response->assertSee('Official Payment Bank Accounts');
        $response->assertSee('Google Cloud Integration');
        $response->assertSee('Google Spreadsheet ID');
        $response->assertSee('Google Calendar ID');
        $response->assertSee('Sync All Records Now');
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
        Queue::fake();
        Storage::fake('public');

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

        // 2. Client submits a 3-day booking (e.g. 2028-03-01 to 2028-03-03 -> 2 extra days)
        $response = $this->post(route('booking.store'), [
            'customer_name'  => 'Test Dynamic Rules',
            'customer_phone' => '081234567890',
            'event_date'     => '2028-03-01',
            'event_end_date' => '2028-03-03',
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

    public function test_admin_can_update_vendor_discount_on_booking(): void
    {
        Queue::fake();

        $admin = User::first();
        $package = Package::first();

        // Create booking with DP
        $booking = Booking::create([
            'invoice_number'     => 'INV-202611-DISCOUNT',
            'customer_name'      => 'Vendor Discount Test',
            'customer_phone'     => '081234567890',
            'event_date'         => now()->addMonths(2)->format('Y-m-d'),
            'package_id'         => $package->id,
            'package_name'       => $package->name,
            'package_price'      => 10000000,
            'addon_total_price'  => 0,
            'grand_total'        => 10000000,
            'amount_paid'        => 3000000,
            'remaining_balance'  => 7000000,
            'payment_type'       => 'dp',
            'payment_proof_path' => 'proofs/dummy.jpg',
            'status'             => 'pending_verification',
            'vendor_name'        => 'MUA Barokah Partner',
        ]);

        // 1. Admin applies 10% percentage discount
        $response = $this->actingAs($admin)->post(route('admin.bookings.discount', $booking->id), [
            'discount_percent' => 10,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $booking->refresh();
        $this->assertEquals(10, (float) $booking->discount_percent);
        $this->assertEquals(1000000, (float) $booking->discount_amount);
        $this->assertEquals(9000000, (float) $booking->grand_total);
        $this->assertEquals(6000000, (float) $booking->remaining_balance);

        Queue::assertPushed(SyncBookingToGoogleServicesJob::class);

        // 2. Admin applies nominal discount directly (fallback)
        $responseNominal = $this->actingAs($admin)->post(route('admin.bookings.discount', $booking->id), [
            'discount_amount' => '500.000',
        ]);

        $responseNominal->assertRedirect();
        $responseNominal->assertSessionHas('success');

        $booking->refresh();
        $this->assertEquals(5, (float) $booking->discount_percent);
        $this->assertEquals(500000, (float) $booking->discount_amount);
        $this->assertEquals(9500000, (float) $booking->grand_total);
        $this->assertEquals(6500000, (float) $booking->remaining_balance);
    }

    public function test_admin_can_update_transport_fee_on_booking(): void
    {
        Queue::fake();

        $admin = User::first();
        $package = Package::first();

        $booking = Booking::create([
            'invoice_number'       => 'INV-202611-TRANSPORT',
            'customer_name'        => 'Transport Test Customer',
            'customer_phone'       => '081234567890',
            'event_date'           => now()->addMonths(2)->format('Y-m-d'),
            'package_id'           => $package->id,
            'package_name'         => $package->name,
            'package_price'        => 10000000,
            'addon_total_price'    => 0,
            'delivery_distance_km' => 10,
            'transport_fee'        => 0,
            'grand_total'          => 10000000,
            'amount_paid'          => 3000000,
            'remaining_balance'    => 7000000,
            'payment_type'         => 'dp',
            'payment_proof_path'   => 'proofs/dummy.jpg',
            'status'               => 'pending_verification',
        ]);

        // Admin updates transport to 25 km and 600,000 fee
        $response = $this->actingAs($admin)->post(route('admin.bookings.transport', $booking->id), [
            'delivery_distance_km' => 25,
            'transport_fee'        => '600.000',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $booking->refresh();
        $this->assertEquals(25, (float) $booking->delivery_distance_km);
        $this->assertEquals(600000, (float) $booking->transport_fee);
        $this->assertEquals(10600000, (float) $booking->grand_total);
        $this->assertEquals(7600000, (float) $booking->remaining_balance);

        Queue::assertPushed(SyncBookingToGoogleServicesJob::class);
    }

    public function test_admin_cannot_update_transport_fee_on_paid_booking(): void
    {
        $admin = User::first();
        $package = Package::first();

        $booking = Booking::create([
            'invoice_number'       => 'INV-202611-PAID-TR',
            'customer_name'        => 'Paid Customer',
            'customer_phone'       => '081234567890',
            'event_date'           => now()->addMonths(2)->format('Y-m-d'),
            'package_id'           => $package->id,
            'package_name'         => $package->name,
            'package_price'        => 10000000,
            'addon_total_price'    => 0,
            'delivery_distance_km' => 15,
            'transport_fee'        => 300000,
            'grand_total'          => 10300000,
            'amount_paid'          => 10300000,
            'remaining_balance'    => 0,
            'payment_type'         => 'lunas',
            'payment_proof_path'   => 'proofs/dummy.jpg',
            'status'               => 'confirmed',
        ]);

        $response = $this->actingAs($admin)->post(route('admin.bookings.transport', $booking->id), [
            'delivery_distance_km' => 30,
            'transport_fee'        => '600.000',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('error');

        $booking->refresh();
        $this->assertEquals(15, (float) $booking->delivery_distance_km);
        $this->assertEquals(300000, (float) $booking->transport_fee);
    }

    public function test_admin_cannot_update_vendor_discount_on_paid_booking(): void
    {
        $admin = User::first();
        $package = Package::first();

        $booking = Booking::create([
            'invoice_number'       => 'INV-202611-PAID-DISC',
            'customer_name'        => 'Paid Customer Disc',
            'customer_phone'       => '081234567891',
            'event_date'           => now()->addMonths(2)->format('Y-m-d'),
            'package_id'           => $package->id,
            'package_name'         => $package->name,
            'package_price'        => 10000000,
            'addon_total_price'    => 0,
            'delivery_distance_km' => 10,
            'transport_fee'        => 0,
            'grand_total'          => 10000000,
            'amount_paid'          => 10000000,
            'remaining_balance'    => 0,
            'payment_type'         => 'lunas',
            'payment_proof_path'   => 'proofs/dummy.jpg',
            'status'               => 'confirmed',
            'vendor_name'          => 'Partner Test',
        ]);

        $response = $this->actingAs($admin)->post(route('admin.bookings.discount', $booking->id), [
            'discount_percent' => 10,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('error');

        $booking->refresh();
        $this->assertEquals(0, (float) $booking->discount_percent);
        $this->assertEquals(0, (float) $booking->discount_amount);
    }

    public function test_admin_can_update_owner_discount_on_booking(): void
    {
        Queue::fake();

        $admin = User::first();
        $package = Package::first();

        $booking = Booking::create([
            'invoice_number'       => 'INV-202611-OWNER-DISC',
            'customer_name'        => 'Owner Discount Customer',
            'customer_phone'       => '081234567899',
            'event_date'           => now()->addMonths(2)->format('Y-m-d'),
            'package_id'           => $package->id,
            'package_name'         => $package->name,
            'package_price'        => 5000000,
            'addon_total_price'    => 0,
            'transport_fee'        => 300000,
            'discount_percent'     => 10, // 10% of 5.300.000 = 530.000
            'discount_amount'      => 530000,
            'owner_discount'       => 0,
            'grand_total'          => 4770000,
            'amount_paid'          => 2000000,
            'remaining_balance'    => 2770000,
            'payment_type'         => 'dp',
            'payment_proof_path'   => 'proofs/dummy.jpg',
            'status'               => 'pending_verification',
        ]);

        // Admin adds special owner discount of Rp 270.000
        $response = $this->actingAs($admin)->post(route('admin.bookings.owner_discount', $booking->id), [
            'owner_discount' => '270.000',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $booking->refresh();
        $this->assertEquals(270000, (float) $booking->owner_discount);
        // Subtotal = 5.300.000
        // Vendor discount = 530.000
        // Owner discount = 270.000
        // Total discount = 800.000
        // Grand total = 5.300.000 - 800.000 = 4.500.000
        $this->assertEquals(4500000, (float) $booking->grand_total);
        // Remaining = 4.500.000 - 2.000.000 = 2.500.000
        $this->assertEquals(2500000, (float) $booking->remaining_balance);

        Queue::assertPushed(SyncBookingToGoogleServicesJob::class);
    }

    public function test_admin_cannot_update_owner_discount_on_paid_booking(): void
    {
        $admin = User::first();
        $package = Package::first();

        $booking = Booking::create([
            'invoice_number'       => 'INV-202611-OWNER-PAID',
            'customer_name'        => 'Paid Customer Owner Disc',
            'customer_phone'       => '081234567898',
            'event_date'           => now()->addMonths(2)->format('Y-m-d'),
            'package_id'           => $package->id,
            'package_name'         => $package->name,
            'package_price'        => 5000000,
            'addon_total_price'    => 0,
            'transport_fee'        => 0,
            'owner_discount'       => 0,
            'grand_total'          => 5000000,
            'amount_paid'          => 5000000,
            'remaining_balance'    => 0,
            'payment_type'         => 'lunas',
            'payment_proof_path'   => 'proofs/dummy.jpg',
            'status'               => 'confirmed',
        ]);

        $response = $this->actingAs($admin)->post(route('admin.bookings.owner_discount', $booking->id), [
            'owner_discount' => '200.000',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('error');

        $booking->refresh();
        $this->assertEquals(0, (float) $booking->owner_discount);
    }

    public function test_vendor_discount_calculation_includes_transport_fee(): void
    {
        Queue::fake();

        $admin = User::first();
        $package = Package::first();

        $booking = Booking::create([
            'invoice_number'       => 'INV-202611-DISC-TR',
            'customer_name'        => 'Disc Transport Customer',
            'customer_phone'       => '081234567892',
            'event_date'           => now()->addMonths(2)->format('Y-m-d'),
            'package_id'           => $package->id,
            'package_name'         => $package->name,
            'package_price'        => 10000000,
            'addon_total_price'    => 0,
            'delivery_distance_km' => 20,
            'transport_fee'        => 300000,
            'grand_total'          => 10300000,
            'amount_paid'          => 3000000,
            'remaining_balance'    => 7300000,
            'payment_type'         => 'dp',
            'payment_proof_path'   => 'proofs/dummy.jpg',
            'status'               => 'pending_verification',
            'vendor_name'          => 'Partner With Transport',
        ]);

        // 10% discount on subtotal (10.000.000 + 300.000 = 10.300.000) -> 1.030.000
        $response = $this->actingAs($admin)->post(route('admin.bookings.discount', $booking->id), [
            'discount_percent' => 10,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $booking->refresh();
        $this->assertEquals(10, (float) $booking->discount_percent);
        $this->assertEquals(1030000, (float) $booking->discount_amount);
        $this->assertEquals(9270000, (float) $booking->grand_total);
        $this->assertEquals(6270000, (float) $booking->remaining_balance);
    }

    public function test_admin_can_add_catalog_addon_to_booking(): void
    {
        Queue::fake();

        $admin = User::first();
        $package = Package::first();
        $addon = Addon::first();

        $booking = Booking::create([
            'invoice_number'       => 'INV-202611-ADDON-CAT',
            'customer_name'        => 'Addon Catalog Test',
            'customer_phone'       => '081234567893',
            'event_date'           => now()->addMonths(2)->format('Y-m-d'),
            'package_id'           => $package->id,
            'package_name'         => $package->name,
            'package_price'        => 5000000,
            'addon_total_price'    => 0,
            'grand_total'          => 5000000,
            'amount_paid'          => 2000000,
            'remaining_balance'    => 3000000,
            'payment_type'         => 'dp',
            'payment_proof_path'   => 'proofs/dummy.jpg',
            'status'               => 'confirmed',
        ]);

        $response = $this->actingAs($admin)->post(route('admin.bookings.addons.store', $booking->id), [
            'addon_id'   => $addon->id,
            'addon_name' => $addon->name,
            'quantity'   => 2,
            'unit_price' => $addon->unit_price,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $expectedAddonSubtotal = 2 * (float) $addon->unit_price;
        $booking->refresh();
        $this->assertEquals($expectedAddonSubtotal, (float) $booking->addon_total_price);
        $this->assertEquals(5000000 + $expectedAddonSubtotal, (float) $booking->grand_total);
        $this->assertEquals(3000000 + $expectedAddonSubtotal, (float) $booking->remaining_balance);

        $this->assertDatabaseHas('booking_addons', [
            'booking_id' => $booking->id,
            'addon_id'   => $addon->id,
            'quantity'   => 2,
        ]);

        Queue::assertPushed(SyncBookingToGoogleServicesJob::class);
    }

    public function test_admin_can_add_custom_field_addon_to_booking(): void
    {
        Queue::fake();

        $admin = User::first();
        $package = Package::first();

        $booking = Booking::create([
            'invoice_number'       => 'INV-202611-ADDON-CUST',
            'customer_name'        => 'Custom Addon Customer',
            'customer_phone'       => '081234567894',
            'event_date'           => now()->addMonths(2)->format('Y-m-d'),
            'package_id'           => $package->id,
            'package_name'         => $package->name,
            'package_price'        => 5000000,
            'addon_total_price'    => 0,
            'grand_total'          => 5000000,
            'amount_paid'          => 5000000,
            'remaining_balance'    => 0,
            'payment_type'         => 'lunas',
            'payment_proof_path'   => 'proofs/dummy.jpg',
            'status'               => 'confirmed',
        ]);

        // Client on-site requests: "Tambahan Panjang Tenda 3x6m" @ 600.000
        $response = $this->actingAs($admin)->post(route('admin.bookings.addons.store', $booking->id), [
            'addon_name' => 'Tambahan Panjang Tenda 3x6m',
            'quantity'   => 1,
            'unit_price' => '600.000',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $booking->refresh();
        $this->assertEquals(600000, (float) $booking->addon_total_price);
        $this->assertEquals(5600000, (float) $booking->grand_total);
        // Note: remaining_balance increases because grand_total exceeds previously paid amount
        $this->assertEquals(600000, (float) $booking->remaining_balance);

        $this->assertDatabaseHas('booking_addons', [
            'booking_id' => $booking->id,
            'addon_name' => 'Tambahan Panjang Tenda 3x6m',
            'addon_id'   => null,
            'subtotal'   => 600000,
        ]);
    }

    public function test_admin_can_delete_addon_from_booking(): void
    {
        Queue::fake();

        $admin = User::first();
        $package = Package::first();

        $booking = Booking::create([
            'invoice_number'       => 'INV-202611-DEL-ADDON',
            'customer_name'        => 'Delete Addon Customer',
            'customer_phone'       => '081234567895',
            'event_date'           => now()->addMonths(2)->format('Y-m-d'),
            'package_id'           => $package->id,
            'package_name'         => $package->name,
            'package_price'        => 5000000,
            'addon_total_price'    => 500000,
            'grand_total'          => 5500000,
            'amount_paid'          => 2000000,
            'remaining_balance'    => 3500000,
            'payment_type'         => 'dp',
            'payment_proof_path'   => 'proofs/dummy.jpg',
            'status'               => 'confirmed',
        ]);

        $addon = $booking->addons()->create([
            'addon_name' => 'Salah Input Kursi',
            'quantity'   => 5,
            'unit_price' => 100000,
            'subtotal'   => 500000,
        ]);

        $response = $this->actingAs($admin)->delete(route('admin.bookings.addons.delete', [$booking->id, $addon->id]));

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $booking->refresh();
        $this->assertEquals(0, (float) $booking->addon_total_price);
        $this->assertEquals(5000000, (float) $booking->grand_total);
        $this->assertEquals(3000000, (float) $booking->remaining_balance);

        $this->assertDatabaseMissing('booking_addons', [
            'id' => $addon->id,
        ]);
    }

    public function test_admin_cannot_delete_or_add_addon_on_completed_booking(): void
    {
        $admin = User::first();
        $package = Package::first();

        $booking = Booking::create([
            'invoice_number'       => 'INV-202611-COMP-ADDON',
            'customer_name'        => 'Completed Booking Addon Customer',
            'customer_phone'       => '081234567896',
            'event_date'           => now()->subMonths(1)->format('Y-m-d'),
            'package_id'           => $package->id,
            'package_name'         => $package->name,
            'package_price'        => 5000000,
            'addon_total_price'    => 200000,
            'grand_total'          => 5200000,
            'amount_paid'          => 5200000,
            'remaining_balance'    => 0,
            'payment_type'         => 'lunas',
            'payment_proof_path'   => 'proofs/dummy.jpg',
            'status'               => 'completed',
        ]);

        $addon = $booking->addons()->create([
            'addon_name' => 'Existing Addon',
            'quantity'   => 2,
            'unit_price' => 100000,
            'subtotal'   => 200000,
        ]);

        // 1. Try to delete addon on completed booking
        $deleteResponse = $this->actingAs($admin)->delete(route('admin.bookings.addons.delete', [$booking->id, $addon->id]));
        $deleteResponse->assertRedirect();
        $deleteResponse->assertSessionHas('error');

        $this->assertDatabaseHas('booking_addons', ['id' => $addon->id]);

        // 2. Try to add addon on completed booking
        $addResponse = $this->actingAs($admin)->post(route('admin.bookings.addons.store', $booking->id), [
            'addon_name' => 'New Addon Attempt',
            'quantity'   => 1,
            'unit_price' => 150000,
        ]);
        $addResponse->assertRedirect();
        $addResponse->assertSessionHas('error');
    }

    public function test_admin_can_update_survey_status_to_completed(): void
    {
        $admin = User::first();
        $package = Package::first();

        $booking = Booking::create([
            'invoice_number'     => 'INV-202611-SURVEY',
            'customer_name'      => 'Survey Test Customer',
            'customer_phone'     => '081234567890',
            'event_date'         => now()->addMonths(2)->format('Y-m-d'),
            'package_id'         => $package->id,
            'package_name'       => $package->name,
            'package_price'      => 10000000,
            'addon_total_price'  => 0,
            'grand_total'        => 10000000,
            'amount_paid'        => 3000000,
            'remaining_balance'  => 7000000,
            'payment_type'       => 'dp',
            'payment_proof_path' => 'proofs/dummy.jpg',
            'status'             => 'confirmed',
            'survey_requested'   => true,
            'survey_date'        => now()->addDays(5)->format('Y-m-d'),
            'survey_time_slot'   => 'morning',
            'survey_status'      => 'requested',
        ]);

        $response = $this->actingAs($admin)->post(route('admin.bookings.survey_status', $booking->id), [
            'survey_status' => 'completed',
            'survey_notes'  => 'Hasil ukur: area halaman 8x12m, kabel PLN aman.',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $booking->refresh();
        $this->assertEquals('completed', $booking->survey_status);
        $this->assertEquals('Hasil ukur: area halaman 8x12m, kabel PLN aman.', $booking->survey_notes);
    }

    public function test_admin_can_update_pin_in_settings(): void
    {
        $admin = User::first();

        $response = $this->actingAs($admin)->post(route('admin.settings.update_pin'), [
            'pin'              => '654321',
            'pin_confirmation' => '654321',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $admin->refresh();
        $this->assertTrue(\Illuminate\Support\Facades\Hash::check('654321', $admin->pin));
    }
}


