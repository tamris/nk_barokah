<?php

namespace Tests\Feature;

use App\Models\Addon;
use App\Models\Booking;
use App\Models\Package;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class FieldStaffAccessTest extends TestCase
{
    use RefreshDatabase;

    private User $owner;
    private User $staff;
    private Booking $booking;

    protected function setUp(): void
    {
        parent::setUp();

        Queue::fake();
        $this->seed(\Database\Seeders\DatabaseSeeder::class);

        $this->owner = User::where('role', User::ROLE_OWNER)->firstOrFail();
        $this->staff = User::where('role', User::ROLE_STAFF)->firstOrFail();

        $package = Package::where('base_price', '>', 0)->firstOrFail();

        $this->booking = Booking::create([
            'invoice_number'     => 'INV-202611-STAFF',
            'customer_name'      => 'Bpk. Hendra Setiawan',
            'customer_phone'     => '081234567890',
            'event_date'         => now()->addDays(10)->format('Y-m-d'),
            'package_id'         => $package->id,
            'package_name'       => $package->name,
            'package_price'      => 4500000,
            'addon_total_price'  => 0,
            'grand_total'        => 4500000,
            'amount_paid'        => 1000000,
            'remaining_balance'  => 3500000,
            'payment_type'       => 'dp',
            'payment_proof_path' => 'proofs/dummy.jpg',
            'status'             => 'confirmed',
            'survey_requested'   => true,
            'survey_date'        => now()->addDays(2)->format('Y-m-d'),
            'survey_time_slot'   => 'morning',
            'survey_status'      => 'scheduled',
        ]);
    }

    public function test_field_staff_can_login_with_six_digit_pin_and_sees_field_dashboard(): void
    {
        $response = $this->post(route('admin.login.post'), [
            'pin' => '112233',
        ]);

        $response->assertRedirect(route('admin.bookings.index'));
        $this->assertAuthenticatedAs($this->staff);

        $dashboard = $this->actingAs($this->staff)->get(route('admin.bookings.index'));
        $dashboard->assertOk();
        $dashboard->assertSee('Field Coordinator');
        $dashboard->assertSee('Pending Surveys');
        $dashboard->assertDontSee('Verified Revenue');
        $dashboard->assertDontSee('Settings & Business Rules');
    }

    public function test_field_staff_does_not_see_inquiry_or_cancelled_bookings(): void
    {
        $package = Package::where('base_price', '>', 0)->firstOrFail();

        Booking::create([
            'invoice_number'     => 'INV-INQUIRY-HIDDEN',
            'customer_name'      => 'Client Inquiry Hidden',
            'customer_phone'     => '081111111111',
            'event_date'         => now()->addDays(15)->format('Y-m-d'),
            'package_id'         => $package->id,
            'package_name'       => $package->name,
            'package_price'      => 4500000,
            'addon_total_price'  => 0,
            'grand_total'        => 4500000,
            'amount_paid'        => 1000000,
            'remaining_balance'  => 3500000,
            'payment_type'       => 'dp',
            'payment_proof_path' => 'proofs/dummy.jpg',
            'status'             => 'pending_verification',
        ]);

        Booking::create([
            'invoice_number'     => 'INV-CANCELLED-HIDDEN',
            'customer_name'      => 'Client Cancelled Hidden',
            'customer_phone'     => '082222222222',
            'event_date'         => now()->addDays(16)->format('Y-m-d'),
            'package_id'         => $package->id,
            'package_name'       => $package->name,
            'package_price'      => 4500000,
            'addon_total_price'  => 0,
            'grand_total'        => 4500000,
            'amount_paid'        => 1000000,
            'remaining_balance'  => 3500000,
            'payment_type'       => 'dp',
            'payment_proof_path' => 'proofs/dummy.jpg',
            'status'             => 'rejected',
        ]);

        $response = $this->actingAs($this->staff)->get(route('admin.bookings.index'));
        $response->assertOk();
        $response->assertSee('INV-202611-STAFF');
        $response->assertDontSee('INV-INQUIRY-HIDDEN');
        $response->assertDontSee('INV-CANCELLED-HIDDEN');
    }

    public function test_disabled_field_staff_cannot_login(): void
    {
        $this->staff->update(['is_active' => false]);

        $response = $this->post(route('admin.login.post'), [
            'pin' => '112233',
        ]);

        $response->assertSessionHasErrors(['pin']);
        $this->assertGuest();
    }

    public function test_field_staff_can_add_addon_survey_notes_operations_and_view_spk(): void
    {
        $addon = Addon::firstOrFail();

        // 1. Add catalog/custom add-on item on site
        $addResponse = $this->actingAs($this->staff)->post(
            route('admin.bookings.addons.store', $this->booking->id),
            [
                'addon_id'   => $addon->id,
                'addon_name' => $addon->name,
                'quantity'   => 20,
                'unit_price' => 15000,
            ]
        );
        $addResponse->assertRedirect();

        $this->booking->refresh();
        $createdAddon = $this->booking->addons()->latest('id')->first();
        $this->assertNotNull($createdAddon);
        $this->assertSame('staff', $createdAddon->added_by_role);
        $this->assertEquals(4800000, (float) $this->booking->grand_total);

        // 2. Update survey notes & mark done
        $surveyResponse = $this->actingAs($this->staff)->post(
            route('admin.bookings.survey_status', $this->booking->id),
            [
                'survey_status' => 'completed',
                'survey_notes'  => 'Yard 6x12m, small pickup access only.',
            ]
        );
        $surveyResponse->assertRedirect();

        $this->booking->refresh();
        $this->assertSame('completed', $this->booking->survey_status);
        $this->assertSame('Yard 6x12m, small pickup access only.', $this->booking->survey_notes);

        // 3. Update field operations & crew notes
        $opsResponse = $this->actingAs($this->staff)->post(
            route('admin.bookings.operations', $this->booking->id),
            [
                'tent_color_theme'  => 'White + Emerald',
                'custom_tent_size'  => '6x12m',
                'operational_notes' => 'Bring extra 30m cable.',
            ]
        );
        $opsResponse->assertRedirect();

        $this->booking->refresh();
        $this->assertSame('White + Emerald', $this->booking->tent_color_theme);
        $this->assertSame('Bring extra 30m cable.', $this->booking->operational_notes);

        // 4. View printable SPK
        $spkResponse = $this->actingAs($this->staff)->get(
            route('admin.bookings.spk', $this->booking->id)
        );
        $spkResponse->assertOk();
    }

    public function test_field_staff_is_forbidden_from_all_owner_only_actions(): void
    {
        $addonItem = $this->booking->addons()->create([
            'addon_name' => 'Kursi Futura',
            'unit_price' => 15000,
            'quantity'   => 10,
            'subtotal'   => 150000,
        ]);

        $restrictedEndpoints = [
            ['POST', route('admin.bookings.confirm', $this->booking->id), []],
            ['POST', route('admin.bookings.reject', $this->booking->id), []],
            ['POST', route('admin.bookings.settle', $this->booking->id), []],
            ['POST', route('admin.bookings.complete', $this->booking->id), []],
            ['POST', route('admin.bookings.discount', $this->booking->id), ['discount_percent' => 10]],
            ['POST', route('admin.bookings.owner_discount', $this->booking->id), ['owner_discount' => 100000]],
            ['POST', route('admin.bookings.transport', $this->booking->id), ['transport_fee' => '200000']],
            ['DELETE', route('admin.bookings.addons.delete', [$this->booking->id, $addonItem->id]), []],
            ['POST', route('admin.packages.store'), ['name' => 'Test Pkg', 'base_price' => 5000000]],
            ['POST', route('admin.settings.update_rules'), []],
            ['POST', route('admin.settings.update_banks'), []],
            ['POST', route('admin.settings.update_pin'), ['pin' => '999999', 'pin_confirmation' => '999999']],
            ['POST', route('admin.settings.update_staff'), ['staff_name' => 'Hack']],
        ];

        foreach ($restrictedEndpoints as [$method, $url, $payload]) {
            $res = $this->actingAs($this->staff)->call($method, $url, $payload);
            $res->assertForbidden();
        }
    }

    public function test_owner_can_manage_staff_access_and_pin_collision_is_blocked(): void
    {
        // Colliding with Owner PIN (123456) must fail
        $collisionRes = $this->actingAs($this->owner)->post(route('admin.settings.update_staff'), [
            'staff_name'             => 'Crew Lead Budi',
            'staff_is_active'        => 1,
            'staff_pin'              => '123456',
            'staff_pin_confirmation' => '123456',
        ]);
        $collisionRes->assertSessionHasErrors(['staff_pin']);

        // Valid unique PIN succeeds
        $validRes = $this->actingAs($this->owner)->post(route('admin.settings.update_staff'), [
            'staff_name'             => 'Crew Lead Budi',
            'staff_is_active'        => 1,
            'staff_pin'              => '889900',
            'staff_pin_confirmation' => '889900',
        ]);
        $validRes->assertRedirect();
        $validRes->assertSessionHas('success');

        $this->staff->refresh();
        $this->assertSame('Crew Lead Budi', $this->staff->name);
        $this->assertTrue(Hash::check('889900', $this->staff->pin));
    }
}
