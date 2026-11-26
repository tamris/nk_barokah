@extends('admin.layout')

@section('title', __('admin.meta.dashboard_title'))

@section('content')
@php
    $isOwner = Auth::user()?->isOwner() ?? false;
    $ownerName = Auth::user()->name ?? 'Owner NK Barokah';
    $hour = now()->hour;
    $greeting = $hour < 12
        ? __('admin.greetings.morning')
        : ($hour < 17 ? __('admin.greetings.afternoon') : __('admin.greetings.evening'));
@endphp

<script>
    window.__ADMIN_DASHBOARD_CONFIG__ = @js([
        'isOwner' => $isOwner,
        'locale' => app()->getLocale(),
        'i18n' => trans('admin.js'),
        'allBookings' => $bookingsPayload,
        'clientsList' => $clientsPayload,
        'packagesList' => $isOwner ? $packagesPayload : [],
        'addonsList' => $addonsPayload,
        'todayIso' => now()->format('Y-m-d'),
        'currentRealYear' => now()->year,
        'currentRealMonth' => now()->month,
        'currentRealDay' => now()->day,
        'calYear' => $initialCalYear,
        'calMonth' => $initialCalMonth,
        'maxEventsPerDay' => (int) ($settings['max_events_per_day'] ?? 1),
        'standardDays' => (int) ($settings['standard_package_max_days'] ?? 2),
        'extraDayRate' => (($settings['extra_day_charge_rate'] ?? 15) > 1 ? ($settings['extra_day_charge_rate'] ?? 15) / 100 : ($settings['extra_day_charge_rate'] ?? 0.15)),
        'minDpAmount' => (float) ($settings['min_dp_amount'] ?? 1000000),
        'bookingStoreUrl' => route('admin.bookings.store'),
        'packageStoreUrl' => route('admin.packages.store'),
        'addonStoreUrl' => route('admin.addons.store'),
    ]);
</script>
<script src="{{ asset('js/admin-dashboard.js') }}?v={{ filemtime(public_path('js/admin-dashboard.js')) }}"></script>

<div @open-booking-detail.window="const target = allBookings.find(b => String(b.id) === String($event.detail.id)); if (target) openDetail(target);"
     @open-client-profile.window="openClientProfile($event.detail);"
     x-data="adminDashboard(window.__ADMIN_DASHBOARD_CONFIG__)"
     class="space-y-6">

    @include('admin.bookings.partials.tab-dashboard')
    @include('admin.bookings.partials.tab-bookings')
    @include('admin.bookings.partials.tab-calendar')
    @if($isOwner)
        @include('admin.bookings.partials.tab-clients')
        @include('admin.bookings.partials.tab-services')
        @include('admin.bookings.partials.tab-invoice')
        @include('admin.bookings.partials.tab-settings')
    @endif
    @include('admin.bookings.partials.view-client-profile')
    @include('admin.bookings.partials.view-booking-detail')
    @include('admin.bookings.partials.modals')
    @if($isOwner)
        @include('admin.bookings.partials.modal-manual-booking')
        @include('admin.bookings.partials.modal-edit-booking')
    @endif

</div>
@endsection
