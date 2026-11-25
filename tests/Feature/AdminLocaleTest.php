<?php

namespace Tests\Feature;

use App\Http\Middleware\SetAdminLocale;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminLocaleTest extends TestCase
{
    use RefreshDatabase;

    private function createOwner(): User
    {
        return User::create([
            'name'      => 'Owner NK Barokah',
            'email'     => 'owner@nkbarokah.com',
            'role'      => User::ROLE_OWNER,
            'password'  => Hash::make('secret123'),
            'pin'       => Hash::make('123456'),
            'is_active' => true,
        ]);
    }

    private function createStaff(): User
    {
        return User::create([
            'name'      => 'Field Coordinator',
            'email'     => 'staff@nkbarokah.com',
            'role'      => User::ROLE_STAFF,
            'password'  => Hash::make('secret123'),
            'pin'       => Hash::make('112233'),
            'is_active' => true,
        ]);
    }

    public function test_admin_defaults_to_english_locale(): void
    {
        $owner = $this->createOwner();

        $loginResponse = $this->get(route('admin.login'));
        $loginResponse->assertOk();
        $loginResponse->assertSee('Enter 6-Digit PIN');
        $loginResponse->assertSee('Sign In to Dashboard');

        $dashboardResponse = $this->actingAs($owner)->get(route('admin.bookings.index'));
        $dashboardResponse->assertOk();
        $dashboardResponse->assertSee('lang="en"', false);
        $dashboardResponse->assertSee('Settings & Business Rules');
        $dashboardResponse->assertSee('Appearance');
        $dashboardResponse->assertSee('Language');
    }

    public function test_owner_can_switch_admin_locale_to_indonesian_and_back_to_english(): void
    {
        $owner = $this->createOwner();

        $switchToId = $this->actingAs($owner)->post(route('admin.locale.update'), [
            'locale' => 'id',
        ]);
        $switchToId->assertRedirect();
        $switchToId->assertCookie(SetAdminLocale::COOKIE_NAME, 'id');

        $idDashboard = $this->actingAs($owner)
            ->withCookie(SetAdminLocale::COOKIE_NAME, 'id')
            ->get(route('admin.bookings.index'));
        $idDashboard->assertOk();
        $idDashboard->assertSee('lang="id"', false);
        $idDashboard->assertSee('Pengaturan & Aturan Bisnis');
        $idDashboard->assertSee('Tampilan');
        $idDashboard->assertSee('Bahasa');
        $idDashboard->assertSee('Pesanan');

        $switchToEn = $this->actingAs($owner)->post(route('admin.locale.update'), [
            'locale' => 'en',
        ]);
        $switchToEn->assertRedirect();
        $switchToEn->assertCookie(SetAdminLocale::COOKIE_NAME, 'en');
    }

    public function test_field_coordinator_can_switch_locale_independently(): void
    {
        $staff = $this->createStaff();

        $response = $this->actingAs($staff)->post(route('admin.locale.update'), [
            'locale' => 'id',
        ]);
        $response->assertRedirect();
        $response->assertCookie(SetAdminLocale::COOKIE_NAME, 'id');

        $staffDashboard = $this->actingAs($staff)
            ->withCookie(SetAdminLocale::COOKIE_NAME, 'id')
            ->get(route('admin.bookings.index'));
        $staffDashboard->assertOk();
        $staffDashboard->assertSee('Koordinator Lapangan');
        $staffDashboard->assertSee('Pasang & Bongkar');
        $staffDashboard->assertSee('Antrean Survei Lokasi');
    }

    public function test_invalid_locale_is_rejected_and_tampered_cookie_falls_back_to_english(): void
    {
        $owner = $this->createOwner();

        $invalidPost = $this->actingAs($owner)->post(route('admin.locale.update'), [
            'locale' => 'fr',
        ]);
        $invalidPost->assertSessionHasErrors('locale');

        $fallbackResponse = $this->actingAs($owner)
            ->withCookie(SetAdminLocale::COOKIE_NAME, 'jp')
            ->get(route('admin.bookings.index'));
        $fallbackResponse->assertOk();
        $fallbackResponse->assertSee('lang="en"', false);
        $fallbackResponse->assertSee('Dashboard');
    }

    public function test_login_page_respects_saved_indonesian_locale_cookie(): void
    {
        $response = $this->withCookie(SetAdminLocale::COOKIE_NAME, 'id')
            ->get(route('admin.login'));
        $response->assertOk();
        $response->assertSee('lang="id"', false);
        $response->assertSee('Masukkan 6 Digit PIN');
        $response->assertSee('Masuk ke Dasbor');
    }
}
