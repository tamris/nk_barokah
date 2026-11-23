<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Package;
use App\Models\User;
use App\Services\AdminNotificationService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminNotificationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_notification_service_categorizes_pending_verifications(): void
    {
        $package = Package::first();

        // Buat booking pending_verification
        Booking::create([
            'invoice_number'        => 'NKB/11/90',
            'customer_name'         => 'Rudi & Maya',
            'customer_phone'        => '081234567890',
            'event_date'            => Carbon::today()->addDays(20)->toDateString(),
            'duration_days'         => 2,
            'package_id'            => $package->id,
            'package_name'          => $package->name,
            'package_price'         => $package->base_price,
            'additional_day_charge' => 0,
            'addon_total_price'     => 0,
            'grand_total'           => 10000000,
            'amount_paid'           => 2000000,
            'remaining_balance'     => 8000000,
            'payment_type'          => 'dp',
            'payment_proof_path'    => 'proofs/test.jpg',
            'status'                => 'pending_verification',
        ]);

        $service = new AdminNotificationService();
        $notifications = $service->getNotifications();

        $this->assertGreaterThanOrEqual(1, $notifications['urgent_count']);
        
        $pendingItems = $notifications['action_required']->where('type', 'verification');
        $this->assertTrue($pendingItems->contains('invoice', 'NKB/11/90'));
    }

    public function test_notification_service_identifies_h7_due_settlements(): void
    {
        $package = Package::first();

        // Buat booking confirmed dengan sisa saldo dan acara 5 hari lagi (masuk rentang H-7)
        Booking::create([
            'invoice_number'        => 'NKB/11/91',
            'customer_name'         => 'Doni & Ratna',
            'customer_phone'        => '081234567891',
            'event_date'            => Carbon::today()->addDays(5)->toDateString(),
            'duration_days'         => 2,
            'package_id'            => $package->id,
            'package_name'          => $package->name,
            'package_price'         => $package->base_price,
            'additional_day_charge' => 0,
            'addon_total_price'     => 0,
            'grand_total'           => 15000000,
            'amount_paid'           => 5000000,
            'remaining_balance'     => 10000000,
            'payment_type'          => 'dp',
            'payment_proof_path'    => 'proofs/test.jpg',
            'status'                => 'confirmed',
        ]);

        $service = new AdminNotificationService();
        $notifications = $service->getNotifications();

        $settlementItems = $notifications['action_required']->where('type', 'settlement');
        $target = $settlementItems->firstWhere('invoice', 'NKB/11/91');

        $this->assertNotNull($target);
        $this->assertSame('DUE SETTLEMENT (H-7)', $target['badge_text']);
        $this->assertStringContainsString('wa.me', $target['action_url']);
    }

    public function test_notification_service_identifies_h2_loading_operations(): void
    {
        $package = Package::first();

        // Buat booking confirmed dengan acara 2 hari lagi (jadwal loading tenda)
        Booking::create([
            'invoice_number'        => 'NKB/11/92',
            'customer_name'         => 'Budi & Dewi',
            'customer_phone'        => '081234567892',
            'event_date'            => Carbon::today()->addDays(2)->toDateString(),
            'duration_days'         => 2,
            'package_id'            => $package->id,
            'package_name'          => $package->name,
            'package_price'         => $package->base_price,
            'additional_day_charge' => 0,
            'addon_total_price'     => 0,
            'grand_total'           => 12000000,
            'amount_paid'           => 12000000,
            'remaining_balance'     => 0,
            'payment_type'          => 'lunas',
            'payment_proof_path'    => 'proofs/test.jpg',
            'status'                => 'confirmed',
        ]);

        $service = new AdminNotificationService();
        $notifications = $service->getNotifications();

        $fieldItems = $notifications['field_operations']->where('type', 'field');
        $target = $fieldItems->firstWhere('invoice', 'NKB/11/92');

        $this->assertNotNull($target);
        $this->assertSame('TENT LOADING (H-2)', $target['badge_text']);
    }

    public function test_notification_service_identifies_ongoing_multiday_event_as_event_day(): void
    {
        $package = Package::first();

        // Buat booking confirmed 2 hari yang sedang berlangsung hari ini (hari ke-2 dari 2 hari acara)
        Booking::create([
            'invoice_number'        => 'NKB/11/93',
            'customer_name'         => 'Bagas & Maya',
            'customer_phone'        => '081234567893',
            'event_date'            => Carbon::yesterday()->toDateString(),
            'event_end_date'        => Carbon::today()->toDateString(),
            'duration_days'         => 2,
            'package_id'            => $package->id,
            'package_name'          => $package->name,
            'package_price'         => $package->base_price,
            'additional_day_charge' => 0,
            'addon_total_price'     => 0,
            'grand_total'           => 12000000,
            'amount_paid'           => 12000000,
            'remaining_balance'     => 0,
            'payment_type'          => 'lunas',
            'payment_proof_path'    => 'proofs/test.jpg',
            'status'                => 'confirmed',
        ]);

        $service = new AdminNotificationService();
        $notifications = $service->getNotifications();

        $fieldItems = $notifications['field_operations']->where('type', 'field');
        $target = $fieldItems->firstWhere('invoice', 'NKB/11/93');

        $this->assertNotNull($target);
        $this->assertSame('EVENT DAY (TODAY)', $target['badge_text']);
        $this->assertStringContainsString('Event is ongoing today (Day 2 of 2)!', $target['desc']);
    }

    public function test_authenticated_admin_layout_renders_notification_center(): void
    {
        $admin = User::first();

        $response = $this->actingAs($admin)->get(route('admin.bookings.index'));

        $response->assertStatus(200);
        $response->assertSee('Notifications &amp; Schedule', false);
        $response->assertSee('Action Required');
        $response->assertSee('Field Schedule');
    }

    public function test_notification_service_identifies_upcoming_site_surveys(): void
    {
        $package = Package::first();

        Booking::create([
            'invoice_number'     => 'NKB/11/SURVEY1',
            'customer_name'      => 'Survey Candidate Pair',
            'customer_phone'     => '081234567800',
            'event_date'         => Carbon::today()->addDays(20)->toDateString(),
            'duration_days'      => 2,
            'package_id'         => $package->id,
            'package_name'       => $package->name,
            'package_price'      => $package->base_price,
            'addon_total_price'  => 0,
            'grand_total'        => $package->base_price,
            'amount_paid'        => 2000000,
            'remaining_balance'  => $package->base_price - 2000000,
            'payment_type'       => 'dp',
            'payment_proof_path' => 'proofs/test.jpg',
            'status'             => 'confirmed',
            'survey_requested'   => true,
            'survey_date'        => Carbon::today()->addDays(1)->toDateString(),
            'survey_time_slot'   => 'morning',
            'survey_status'      => 'requested',
            'venue_address'      => 'Slawi Kulon RT 02/01',
        ]);

        $service = new AdminNotificationService();
        $notifications = $service->getNotifications();

        $surveyItems = $notifications['field_operations']->where('type', 'survey');
        $target = $surveyItems->firstWhere('invoice', 'NKB/11/SURVEY1');

        $this->assertNotNull($target);
        $this->assertSame('SITE SURVEY (TOMORROW)', $target['badge_text']);
        $this->assertStringContainsString('Morning', $target['desc']);
    }
}
