@extends('admin.layout')

@section('title', 'Dashboard')

@section('content')
@php
    $ownerName = Auth::user()->name ?? 'Owner NK Barokah';
    $hour = now()->hour;
    $greeting = $hour < 12 ? 'Good Morning' : ($hour < 17 ? 'Good Afternoon' : 'Good Evening');
@endphp

<script>
    window.__ADMIN_DASHBOARD_CONFIG__ = @js([
        'allBookings' => $bookingsPayload,
        'clientsList' => $clientsPayload,
        'packagesList' => $packagesPayload,
        'addonsList' => $addonsPayload,
        'todayIso' => now()->format('Y-m-d'),
        'currentRealYear' => now()->year,
        'currentRealMonth' => now()->month,
        'currentRealDay' => now()->day,
        'calYear' => $initialCalYear,
        'calMonth' => $initialCalMonth,
        'maxEventsPerDay' => (int) ($settings['max_events_per_day'] ?? 1),
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
    @include('admin.bookings.partials.tab-clients')
    @include('admin.bookings.partials.tab-services')
    @include('admin.bookings.partials.tab-invoice')
    @include('admin.bookings.partials.tab-settings')
    @include('admin.bookings.partials.view-client-profile')
    @include('admin.bookings.partials.view-booking-detail')
    @include('admin.bookings.partials.modals')

</div>
@endsection
