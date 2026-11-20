<?php

namespace Tests\Feature;

use App\Models\Package;
use App\Models\Addon;
use App\Models\Booking;
use App\Jobs\SyncBookingToGoogleServicesJob;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class BookingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_booking_form_renders_successfully(): void
    {
        $response = $this->get(route('booking.create'));

        $response->assertStatus(200);
        $response->assertSee('Silver Package');
        $response->assertSee('Kursi Futura');
    }

    public function test_customer_can_create_booking_with_lunas_payment(): void
    {
        Queue::fake();
        Storage::fake('public');

        $package = Package::first();
        $addon = Addon::first();

        $file = UploadedFile::fake()->create('receipt.jpg', 500, 'image/jpeg');

        $eventDate = now()->addMonth()->format('Y-m-d');

        $response = $this->post(route('booking.store'), [
            'customer_name'  => 'Budi & Ani',
            'customer_phone' => '081234567890',
            'customer_email' => 'budi@example.com',
            'event_date'     => $eventDate,
            'package_id'     => $package->id,
            'addons'         => [
                ['id' => $addon->id, 'qty' => 10],
            ],
            'payment_type'   => 'lunas',
            'payment_proof'  => $file,
        ]);

        $booking = Booking::first();
        $this->assertNotNull($booking);

        $response->assertRedirect(route('booking.success', $booking->invoice_number));

        $expectedGrandTotal = $package->base_price + ($addon->unit_price * 10);
        $this->assertEquals($expectedGrandTotal, $booking->grand_total);
        $this->assertEquals($expectedGrandTotal, $booking->amount_paid);
        $this->assertEquals(0, $booking->remaining_balance);
        $this->assertEquals('pending_verification', $booking->status);

        $this->assertCount(1, $booking->addons);
        $this->assertEquals(10, $booking->addons->first()->quantity);

        Queue::assertPushed(SyncBookingToGoogleServicesJob::class);
    }

    public function test_customer_can_create_booking_with_dp_payment(): void
    {
        Queue::fake();
        Storage::fake('public');

        $package = Package::first();
        $file = UploadedFile::fake()->create('proof.png', 500, 'image/png');
        $eventDate = now()->addMonth()->format('Y-m-d');
        $dpNominal = 5000000;

        $response = $this->post(route('booking.store'), [
            'customer_name'  => 'Joko & Rina',
            'customer_phone' => '081987654321',
            'event_date'     => $eventDate,
            'package_id'     => $package->id,
            'payment_type'   => 'dp',
            'nominal_dp'     => $dpNominal,
            'payment_proof'  => $file,
        ]);

        $booking = Booking::first();
        $this->assertNotNull($booking);
        $response->assertRedirect(route('booking.success', $booking->invoice_number));

        $this->assertEquals($package->base_price, $booking->grand_total);
        $this->assertEquals($dpNominal, $booking->amount_paid);
        $this->assertEquals($package->base_price - $dpNominal, $booking->remaining_balance);
    }

    public function test_anti_overbooking_blocks_booking_when_quota_reached(): void
    {
        Queue::fake();
        Storage::fake('public');

        $package = Package::first();
        $eventDate = now()->addMonths(2)->format('Y-m-d');

        // Existing booking on that date
        Booking::create([
            'invoice_number'     => 'INV-EXISTING-01',
            'customer_name'      => 'Klien Lama',
            'customer_phone'     => '081111111111',
            'event_date'         => $eventDate,
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

        $file = UploadedFile::fake()->create('proof.png', 500, 'image/png');

        // Second booking attempt on the same date
        $response = $this->from(route('booking.create'))->post(route('booking.store'), [
            'customer_name'  => 'Klien Baru',
            'customer_phone' => '082222222222',
            'event_date'     => $eventDate,
            'package_id'     => $package->id,
            'payment_type'   => 'lunas',
            'payment_proof'  => $file,
        ]);

        $response->assertSessionHasErrors(['event_date']);
        $this->assertEquals(1, Booking::count());
    }

    public function test_dp_validation_fails_if_dp_exceeds_grand_total_or_below_minimum(): void
    {
        Queue::fake();
        Storage::fake('public');

        $package = Package::first();
        $file = UploadedFile::fake()->create('proof.png', 500, 'image/png');
        $eventDate = now()->addMonths(3)->format('Y-m-d');

        // Case 1: Below minimum (min is Rp 1.000.000)
        $responseBelow = $this->post(route('booking.store'), [
            'customer_name'  => 'Test User',
            'customer_phone' => '081234567890',
            'event_date'     => $eventDate,
            'package_id'     => $package->id,
            'payment_type'   => 'dp',
            'nominal_dp'     => 500000, // Rp 500k is below min
            'payment_proof'  => $file,
        ]);
        $responseBelow->assertSessionHasErrors(['nominal_dp']);

        // Case 2: Exceeds grand total
        $responseAbove = $this->post(route('booking.store'), [
            'customer_name'  => 'Test User',
            'customer_phone' => '081234567890',
            'event_date'     => $eventDate,
            'package_id'     => $package->id,
            'payment_type'   => 'dp',
            'nominal_dp'     => $package->base_price + 10000000,
            'payment_proof'  => $file,
        ]);
        $responseAbove->assertSessionHasErrors(['nominal_dp']);
    }

    public function test_customer_can_download_pdf_receipt(): void
    {
        $package = Package::first();
        $booking = Booking::create([
            'invoice_number'     => 'INV-202610-TEST1',
            'customer_name'      => 'Kurniawan & Fitri',
            'customer_phone'     => '081234567890',
            'event_date'         => now()->addMonths(4)->format('Y-m-d'),
            'package_id'         => $package->id,
            'package_name'       => $package->name,
            'package_price'      => $package->base_price,
            'addon_total_price'  => 0,
            'grand_total'        => $package->base_price,
            'amount_paid'        => 5000000,
            'remaining_balance'  => $package->base_price - 5000000,
            'payment_type'       => 'dp',
            'payment_proof_path' => 'proofs/test.jpg',
            'status'             => 'pending_verification',
        ]);

        $response = $this->get(route('booking.receipt', $booking->invoice_number));

        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/pdf');
    }
}

