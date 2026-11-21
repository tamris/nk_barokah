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
        $showResponse->assertStatus(200);
        $showResponse->assertSee('(2 Hari Acara)');
    }
}

