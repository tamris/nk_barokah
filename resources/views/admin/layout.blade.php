<!DOCTYPE html>
<html lang="id" class="h-full bg-[#F6F8F7]">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <title>@yield('title', 'Dashboard') | NK Barokah Wedding Organizer</title>

    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">

    <!-- Google Fonts: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Tailwind CSS CDN & Alpine.js -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'system-ui', '-apple-system', 'sans-serif'],
                    },
                    colors: {
                        canvas: '#F6F8F7',
                        brand: {
                            50:  '#EBF7F5',
                            100: '#D3ECE8',
                            200: '#A8DAD3',
                            300: '#72C2B7',
                            400: '#3CA497',
                            500: '#168579',
                            600: '#045D56', // Primary Deep Emerald Teal
                            700: '#034B45',
                            800: '#023B36',
                            900: '#022E2A',
                        },
                        coral: {
                            50:  '#EBF7F5',
                            100: '#D3ECE8',
                            200: '#A8DAD3',
                            300: '#72C2B7',
                            400: '#3CA497',
                            500: '#045D56',
                            600: '#034B45',
                            700: '#023B36',
                            800: '#022E2A',
                            900: '#012320',
                        }
                    },
                    boxShadow: {
                        'nav': '0 4px 20px -2px rgba(4, 93, 86, 0.05), 0 1px 4px rgba(0, 0, 0, 0.02)',
                        'card': '0 2px 14px -2px rgba(15, 23, 42, 0.03), 0 1px 3px rgba(15, 23, 42, 0.02)',
                        'pill': '0 4px 12px -2px rgba(4, 93, 86, 0.25)',
                        'insight': '0 16px 32px -10px rgba(4, 93, 86, 0.38)',
                    }
                }
            }
        }

        document.addEventListener('alpine:init', () => {
            const validMainTabs = ['dashboard', 'bookings', 'calendar', 'clients', 'services', 'invoice', 'settings'];
            const urlParams = new URLSearchParams(window.location.search);
            const savedTab = sessionStorage.getItem('admin_active_tab');
            const safeSavedTab = validMainTabs.includes(savedTab) ? savedTab : 'dashboard';
            const initialTab = urlParams.get('tab') || window.location.hash.replace('#', '') || safeSavedTab;
            
            // Bersihkan jika ada nilai sub-view yang tersisa di sessionStorage
            if (!validMainTabs.includes(savedTab)) {
                try { sessionStorage.setItem('admin_active_tab', 'dashboard'); } catch (e) {}
            }

            Alpine.store('nav', {
                tab: validMainTabs.includes(initialTab) ? initialTab : 'dashboard',
                search: '',
                setTab(t) {
                    this.tab = t;
                    try {
                        if (validMainTabs.includes(t)) {
                            sessionStorage.setItem('admin_active_tab', t);
                        }
                        const url = new URL(window.location);
                        url.searchParams.set('tab', t);
                        if (t !== 'booking_detail') {
                            url.searchParams.delete('booking_id');
                            url.searchParams.delete('id');
                        }
                        window.history.replaceState({}, '', url);
                    } catch (e) {}
                }
            });

            Alpine.data('globalSpotlight', () => ({
                open: false,
                query: '',
                allBookings: @js($bookingsPayload ?? []),
                clientsList: @js($clientsPayload ?? []),

                get filteredBookings() {
                    const q = this.query.trim().toLowerCase();
                    if (!q) return [];
                    return this.allBookings.filter(b => {
                        return (b.name && b.name.toLowerCase().includes(q))
                            || (b.code && b.code.toLowerCase().includes(q))
                            || (b.phone && String(b.phone).includes(q))
                            || (b.service && b.service.toLowerCase().includes(q))
                            || (b.venue_address && b.venue_address.toLowerCase().includes(q))
                            || (b.child_name && b.child_name.toLowerCase().includes(q));
                    }).slice(0, 6);
                },

                get filteredClients() {
                    const q = this.query.trim().toLowerCase();
                    if (!q) return [];
                    return this.clientsList.filter(c => {
                        return (c.name && c.name.toLowerCase().includes(q))
                            || (c.phone && String(c.phone).includes(q))
                            || (c.email && c.email.toLowerCase().includes(q));
                    }).slice(0, 3);
                },

                selectBooking(booking) {
                    this.open = false;
                    this.query = '';
                    window.dispatchEvent(new CustomEvent('open-booking-detail', { detail: { id: booking.id } }));
                },

                selectClient(client) {
                    this.open = false;
                    this.query = '';
                    window.dispatchEvent(new CustomEvent('open-client-profile', { detail: client }));
                },

                submitFirstResult() {
                    if (this.filteredBookings.length > 0) {
                        this.selectBooking(this.filteredBookings[0]);
                    } else if (this.filteredClients.length > 0) {
                        this.selectClient(this.filteredClients[0]);
                    }
                },

                handleKeyDown(e) {
                    if ((e.metaKey || e.ctrlKey) && e.key.toLowerCase() === 'k') {
                        e.preventDefault();
                        this.$nextTick(() => {
                            this.$refs.searchInput?.focus();
                            if (this.query.trim().length > 0) {
                                this.open = true;
                            }
                        });
                    } else if (e.key === 'Escape') {
                        this.open = false;
                    }
                }
            }));
        });
    </script>

    <style>
        [x-cloak] { display: none !important; }
        ::-webkit-scrollbar { width: 5px; height: 5px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #DCE4E2; border-radius: 99px; }
        ::-webkit-scrollbar-thumb:hover { background: #C4D2CE; }
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
        * { -webkit-tap-highlight-color: transparent; }

        /* Smooth Apple-style micro-animations */
        @keyframes slideInRight {
            from { opacity: 0; transform: translateX(14px); }
            to   { opacity: 1; transform: translateX(0); }
        }
        @keyframes slideInLeft {
            from { opacity: 0; transform: translateX(-14px); }
            to   { opacity: 1; transform: translateX(0); }
        }
        @keyframes fadeUpSoft {
            from { opacity: 0; transform: translateY(6px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .anim-slide-right { animation: slideInRight 0.28s cubic-bezier(0.22, 1, 0.36, 1) both; }
        .anim-slide-left  { animation: slideInLeft  0.28s cubic-bezier(0.22, 1, 0.36, 1) both; }
        .anim-fade-up     { animation: fadeUpSoft   0.26s cubic-bezier(0.22, 1, 0.36, 1) both; }

        /* Flutter-style Shimmer Skeleton Effect */
        @keyframes shimmerWave {
            0%   { transform: translateX(-100%); }
            100% { transform: translateX(200%); }
        }
        .shimmer-mask {
            position: relative;
            overflow: hidden;
            background-color: #E2E8F0;
        }
        .shimmer-mask::after {
            content: '';
            position: absolute;
            inset: 0;
            transform: translateX(-100%);
            background: linear-gradient(90deg, rgba(226, 232, 240, 0) 0%, rgba(255, 255, 255, 0.85) 50%, rgba(226, 232, 240, 0) 100%);
            animation: shimmerWave 1.35s cubic-bezier(0.4, 0, 0.2, 1) infinite;
        }
    </style>
</head>
<body class="min-h-full bg-[#F6F8F7] text-slate-800 antialiased font-sans selection:bg-brand-100 selection:text-brand-900"
      x-data="{ mobileMenuOpen: false, syncModalOpen: false, notifOpen: false, profileModalOpen: false }">

    @php
        $isDashboardPage = request()->routeIs('admin.bookings.index');
        $ownerName = Auth::user()->name ?? 'Owner NK Barokah';
        $ownerEmail = Auth::user()->email ?? 'owner@nkbarokah.com';
        $notifs = $adminNotifications ?? (new \App\Services\AdminNotificationService())->getNotifications();
    @endphp

    <!-- ============================================================== -->
    <!-- CONTAINER UTAMA (FLOATING TOP PILL NAV + AIRY CANVAS)          -->
    <!-- ============================================================== -->
    <div class="max-w-[1400px] mx-auto px-3 sm:px-6 lg:px-8 pt-4 sm:pt-5 pb-32 lg:pb-10">

        <!-- TOP FLOATING PILL NAVBAR -->
        <header class="sticky top-3 z-40 bg-white/95 backdrop-blur-md rounded-full px-3.5 sm:px-5 py-2.5 shadow-nav border border-[#EAF0EE] flex items-center justify-between gap-3">

            <!-- Kiri: Logo Brand -->
            <div class="flex items-center gap-2.5 shrink-0">
                <a href="{{ route('admin.bookings.index') }}"
                   @if($isDashboardPage) @click.prevent="$store.nav.setTab('dashboard')" @endif
                   class="flex items-center gap-2.5 group">
                    <img src="{{ asset('images/logo.png') }}" alt="NK Barokah Logo" class="h-8 sm:h-9 w-auto object-contain transition group-hover:scale-105">
                    <span class="text-[17px] font-bold tracking-tight text-slate-900">NK Barokah</span>
                </a>
            </div>

            <!-- Tengah: Navigasi Pill (Desktop) -->
            <nav class="hidden xl:flex items-center gap-1 bg-[#F4F7F6] p-1 rounded-full border border-[#EDF2F0]">
                @php
                    $navItems = [
                        ['key' => 'dashboard', 'label' => 'Dashboard'],
                        ['key' => 'bookings',  'label' => 'Bookings'],
                        ['key' => 'calendar',  'label' => 'Calendar'],
                        ['key' => 'clients',   'label' => 'Clients'],
                        ['key' => 'services',  'label' => 'Services'],
                        ['key' => 'invoice',   'label' => 'Invoice'],
                       
                    ];
                @endphp

                @foreach($navItems as $nav)
                    @if($isDashboardPage)
                        <button type="button"
                                @click="$store.nav.setTab('{{ $nav['key'] }}')"
                                :class="$store.nav.tab === '{{ $nav['key'] }}'
                                    ? 'bg-brand-600 text-white shadow-pill font-semibold'
                                    : 'text-slate-600 hover:text-slate-900 hover:bg-white/60 font-medium'"
                                class="px-5 py-2 rounded-full text-[13px] transition-all duration-200 cursor-pointer">
                            {{ $nav['label'] }}
                        </button>
                    @else
                        <a href="{{ $nav['key'] === 'services' ? route('admin.masters.index') : route('admin.bookings.index') }}"
                           class="px-5 py-2 rounded-full text-[13px] transition-all duration-200 {{ ($nav['key'] === 'services' && request()->routeIs('admin.masters.*')) || ($nav['key'] === 'bookings' && request()->routeIs('admin.bookings.show')) ? 'bg-brand-600 text-white shadow-pill font-semibold' : 'text-slate-600 hover:text-slate-900 hover:bg-white/60 font-medium' }}">
                            {{ $nav['label'] }}
                        </a>
                    @endif
                @endforeach
            </nav>

            <!-- Kanan: Pencarian Pill + Pengaturan + Notifikasi + Avatar -->
            <div class="flex items-center gap-2 sm:gap-2.5 shrink-0">

                <!-- Global Spotlight Search Command Palette -->
                <div x-data="globalSpotlight()"
                     @open-spotlight.window="$nextTick(() => { $refs.searchInput?.focus(); if (query.trim().length > 0) open = true; })"
                     @keydown.window="handleKeyDown($event)"
                     @click.away="open = false"
                     class="relative hidden md:block">

                    <!-- Search Input Pill -->
                    <div class="flex items-center bg-[#F6F8F7] border border-[#E6ECEA] focus-within:border-brand-500 focus-within:bg-white focus-within:ring-2 focus-within:ring-brand-500/15 rounded-full pl-3.5 pr-1.5 py-1 w-56 lg:w-72 transition-all shadow-2xs">
                        <svg class="w-3.5 h-3.5 text-slate-400 mr-2 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M21 21l-5.2-5.2m2.2-4.8a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        <input type="text"
                               x-ref="searchInput"
                               x-model="query"
                               @input="open = query.trim().length > 0"
                               @focus="if (query.trim().length > 0) open = true"
                               @keydown.enter.prevent="submitFirstResult()"
                               placeholder="Search anything..."
                               class="w-full bg-transparent text-xs text-slate-800 placeholder-slate-400 focus:outline-none font-medium">
                        
                        <!-- Clear Button -->
                        <button type="button"
                                x-show="query"
                                x-cloak
                                @click="query = ''; open = false; $refs.searchInput.focus()"
                                class="text-slate-400 hover:text-slate-600 p-1 mr-1 transition cursor-pointer">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>

                        <!-- Keyboard Shortcut Badge -->
                        <span x-show="!query" class="hidden lg:inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-mono text-slate-400 bg-white border border-slate-200 font-semibold select-none mr-1.5 shadow-2xs">
                            Ctrl+K
                        </span>

                        <!-- Search Button -->
                        <button type="button"
                                @click="$refs.searchInput.focus(); if (query.trim().length > 0) open = true"
                                class="w-7 h-7 rounded-full bg-brand-600 hover:bg-brand-700 text-white flex items-center justify-center shrink-0 transition shadow-2xs cursor-pointer">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.4" d="M21 21l-5.2-5.2m2.2-4.8a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </button>
                    </div>

                    <!-- Floating Spotlight Results Dropdown -->
                    <div x-show="open && query.trim().length > 0"
                         x-cloak
                         x-transition:enter="transition ease-out duration-150"
                         x-transition:enter-start="opacity-0 translate-y-2 scale-98"
                         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                         x-transition:leave="transition ease-in duration-100"
                         x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                         x-transition:leave-end="opacity-0 translate-y-2 scale-98"
                         class="absolute right-0 top-full mt-2.5 w-[380px] sm:w-[460px] bg-white rounded-3xl shadow-2xl border border-[#E2ECE8] p-3.5 sm:p-4 z-50 text-left max-h-[490px] overflow-y-auto space-y-3">
                        
                        <!-- Header -->
                        <div class="flex items-center justify-between pb-2 border-b border-slate-100 px-1">
                            <span class="text-xs font-bold text-slate-800">Hasil Pencarian</span>
                            <span class="text-[10px] text-slate-400 font-mono" x-text="(filteredBookings.length + filteredClients.length) + ' ditemukan'"></span>
                        </div>

                        <!-- Bookings & Invoices Section -->
                        <template x-if="filteredBookings.length > 0">
                            <div class="space-y-1">
                                <div class="flex items-center justify-between px-1 pt-1 pb-0.5">
                                    <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">
                                        Reservasi &amp; Invoice
                                    </span>
                                    <span class="text-[10px] text-slate-400 font-medium" x-text="filteredBookings.length + ' item'"></span>
                                </div>
                                <div class="space-y-1">
                                    <template x-for="b in filteredBookings" :key="b.id">
                                        <button type="button"
                                                @click="selectBooking(b)"
                                                class="w-full text-left p-2.5 rounded-2xl hover:bg-[#F2F8F6] border border-transparent hover:border-brand-200/60 transition group flex items-center justify-between gap-3 cursor-pointer">
                                            <div class="flex items-center gap-2.5 min-w-0">
                                                <div class="w-8 h-8 rounded-xl bg-slate-100 group-hover:bg-brand-600 text-slate-500 group-hover:text-white flex items-center justify-center shrink-0 border border-slate-200/60 group-hover:border-brand-600 transition">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                                    </svg>
                                                </div>
                                                <div class="min-w-0">
                                                    <div class="flex items-center gap-1.5">
                                                        <span class="font-bold text-xs text-slate-900 group-hover:text-brand-700 truncate" x-text="b.name"></span>
                                                        <span class="text-[10px] font-mono text-slate-400 font-medium" x-text="'#' + b.code"></span>
                                                    </div>
                                                    <p class="text-[11px] text-slate-500 truncate mt-0.5">
                                                        <span x-text="b.date_short"></span>
                                                        <span>·</span>
                                                        <span class="font-medium text-slate-700" x-text="b.service"></span>
                                                        <template x-if="b.child_name">
                                                            <span class="text-brand-700 font-semibold" x-text="'(' + b.child_name + ')'"></span>
                                                        </template>
                                                    </p>
                                                </div>
                                            </div>
                                            <div class="text-right shrink-0">
                                                <span class="text-[11px] font-bold text-slate-900 block" x-text="b.grand_total_formatted"></span>
                                                <span class="text-[9px] font-black uppercase px-1.5 py-0.5 rounded-md"
                                                      :class="{
                                                          'bg-emerald-50 text-emerald-700 border border-emerald-200': b.raw_status === 'confirmed',
                                                          'bg-amber-50 text-amber-700 border border-amber-200': b.raw_status === 'pending_verification',
                                                          'bg-slate-100 text-slate-600 border border-slate-200': b.raw_status === 'completed',
                                                          'bg-rose-50 text-rose-700 border border-rose-200': b.raw_status === 'rejected'
                                                      }"
                                                      x-text="b.status"></span>
                                            </div>
                                        </button>
                                    </template>
                                </div>
                            </div>
                        </template>

                        <!-- Clients Section -->
                        <template x-if="filteredClients.length > 0">
                            <div class="space-y-1 pt-1 border-t border-slate-100">
                                <div class="flex items-center justify-between px-1 pt-1 pb-0.5">
                                    <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">
                                        Data Klien / Kontak
                                    </span>
                                    <span class="text-[10px] text-slate-400 font-medium" x-text="filteredClients.length + ' kontak'"></span>
                                </div>
                                <div class="space-y-1">
                                    <template x-for="c in filteredClients" :key="c.phone + c.name">
                                        <button type="button"
                                                @click="selectClient(c)"
                                                class="w-full text-left p-2.5 rounded-2xl hover:bg-[#F2F8F6] border border-transparent hover:border-brand-200/60 transition group flex items-center justify-between gap-3 cursor-pointer">
                                            <div class="flex items-center gap-2.5 min-w-0">
                                                <div class="w-8 h-8 rounded-xl bg-teal-50 group-hover:bg-brand-600 text-teal-700 group-hover:text-white flex items-center justify-center shrink-0 border border-teal-200/60 group-hover:border-brand-600 transition">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                                    </svg>
                                                </div>
                                                <div class="min-w-0">
                                                    <span class="font-bold text-xs text-slate-900 group-hover:text-brand-700 truncate block" x-text="c.name"></span>
                                                    <p class="text-[11px] font-mono text-slate-500 mt-0.5" x-text="c.phone"></p>
                                                </div>
                                            </div>
                                            <div class="text-right shrink-0">
                                                <span class="text-[10px] font-semibold text-brand-700 block" x-text="c.totalBookings + ' Order'"></span>
                                                <span class="text-[10px] text-slate-400" x-text="c.totalSpentFormatted"></span>
                                            </div>
                                        </button>
                                    </template>
                                </div>
                            </div>
                        </template>

                        <!-- Empty State -->
                        <template x-if="query && filteredBookings.length === 0 && filteredClients.length === 0">
                            <div class="py-6 text-center space-y-1">
                                <div class="w-9 h-9 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                    </svg>
                                </div>
                                <p class="text-xs font-bold text-slate-800">Tidak ada data ditemukan</p>
                                <p class="text-[11px] text-slate-400" x-text="'Tidak ada reservasi atau klien yang cocok dengan \'' + query + '\''"></p>
                            </div>
                        </template>

                        <!-- Footer -->
                        <div class="pt-2 border-t border-slate-100 flex items-center justify-between text-[10px] text-slate-400 px-1">
                            <span>Cari nama, invoice, telepon, atau alamat</span>
                            <span class="font-mono bg-slate-100 px-1.5 py-0.5 rounded text-[9px]">ESC batal</span>
                        </div>
                    </div>
                </div>

                <!-- Tombol Pengaturan & Google Sync -->
                @if($isDashboardPage)
                    <button type="button"
                            @click="$store.nav.setTab('settings')"
                            title="Settings & Configuration"
                            :class="$store.nav.tab === 'settings'
                                ? 'bg-brand-600 text-white shadow-pill font-semibold'
                                : 'bg-[#F6F8F7] hover:bg-[#EAF0EE] text-slate-700'"
                            class="w-9 h-9 sm:w-10 sm:h-10 rounded-full flex items-center justify-center transition cursor-pointer">
                        <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.9" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.9" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                    </button>
                @else
                    <a href="{{ route('admin.bookings.index') }}"
                       title="Settings & Configuration"
                       class="w-9 h-9 sm:w-10 sm:h-10 rounded-full bg-[#F6F8F7] hover:bg-[#EAF0EE] text-slate-700 flex items-center justify-center transition">
                        <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.9" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.9" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                    </a>
                @endif

                <!-- Tombol Notifikasi & Agenda Interaktif -->
                <div class="relative" x-data="{ notifTab: 'actions' }">
                    <button type="button"
                            @click="notifOpen = !notifOpen"
                            title="Notifications &amp; Schedule"
                            class="w-9 h-9 sm:w-10 sm:h-10 rounded-full bg-[#F6F8F7] hover:bg-[#EAF0EE] text-slate-700 flex items-center justify-center transition relative cursor-pointer">
                        <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.9" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                        @if($notifs['urgent_count'] > 0)
                            <span class="absolute -top-1 -right-1 min-w-[18px] h-[18px] px-1 bg-rose-500 text-white text-[10px] font-extrabold rounded-full flex items-center justify-center ring-2 ring-white shadow-sm">
                                {{ $notifs['urgent_count'] > 9 ? '9+' : $notifs['urgent_count'] }}
                            </span>
                        @elseif($notifs['field_count'] > 0)
                            <span class="w-2.5 h-2.5 rounded-full bg-teal-500 ring-2 ring-white absolute top-1.5 right-1.5"></span>
                        @endif
                    </button>

                    <!-- Mobile Backdrop Overlay -->
                    <div x-show="notifOpen"
                         @click="notifOpen = false"
                         x-transition:enter="transition ease-out duration-150"
                         x-transition:enter-start="opacity-0"
                         x-transition:enter-end="opacity-100"
                         class="fixed inset-0 z-40 bg-slate-900/25 backdrop-blur-2xs sm:hidden"
                         x-cloak></div>

                    <!-- Dropdown Pusat Notifikasi & Agenda Lapangan -->
                    <div x-show="notifOpen"
                         @click.away="notifOpen = false"
                         x-transition:enter="transition ease-out duration-150"
                         x-transition:enter-start="opacity-0 scale-95 -translate-y-2"
                         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                         class="fixed inset-x-3.5 top-[68px] max-w-[360px] mx-auto sm:max-w-none sm:mx-0 sm:inset-x-auto sm:absolute sm:right-0 sm:top-full sm:mt-2.5 sm:w-[380px] bg-white rounded-3xl shadow-2xl border border-[#E2ECE8] p-4 sm:p-5 z-50 text-left"
                         x-cloak>
                        
                        <!-- Header Dropdown -->
                        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                            <div>
                                <h3 class="text-xs font-bold text-slate-900 tracking-tight">Notifications &amp; Schedule</h3>
                                <p class="text-[10px] text-slate-400">Operations control center</p>
                            </div>
                            <span class="text-[10.5px] font-bold px-2.5 py-0.5 rounded-full {{ $notifs['urgent_count'] > 0 ? 'text-rose-700 bg-rose-50 border border-rose-200' : 'text-teal-700 bg-teal-50 border border-teal-200' }}">
                                {{ $notifs['urgent_count'] > 0 ? $notifs['urgent_count'] . ' Action Required' : 'All Clear' }}
                            </span>
                        </div>

                        <!-- Pill Switcher (Tab Buttons) -->
                        <div class="grid grid-cols-2 gap-1.5 p-1 bg-slate-100/80 rounded-2xl my-3 text-xs font-semibold">
                            <button type="button"
                                     @click="notifTab = 'actions'"
                                     :class="notifTab === 'actions' ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-500 hover:text-slate-800'"
                                     class="py-1.5 px-2 rounded-xl transition flex items-center justify-center gap-1.5 cursor-pointer">
                                <span>Action Required</span>
                                @if($notifs['urgent_count'] > 0)
                                    <span class="px-1.5 py-0.2 text-[9px] font-extrabold rounded-full"
                                          :class="notifTab === 'actions' ? 'bg-rose-500 text-white' : 'bg-rose-100 text-rose-700'">
                                        {{ $notifs['urgent_count'] }}
                                    </span>
                                @endif
                            </button>
                            <button type="button"
                                     @click="notifTab = 'field'"
                                     :class="notifTab === 'field' ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-500 hover:text-slate-800'"
                                     class="py-1.5 px-2 rounded-xl transition flex items-center justify-center gap-1.5 cursor-pointer">
                                <span>Field Schedule</span>
                                @if($notifs['field_count'] > 0)
                                    <span class="px-1.5 py-0.2 text-[9px] font-extrabold rounded-full"
                                          :class="notifTab === 'field' ? 'bg-teal-600 text-white' : 'bg-teal-100 text-teal-800'">
                                        {{ $notifs['field_count'] }}
                                    </span>
                                @endif
                            </button>
                        </div>

                        <!-- Content Tab: Action Required -->
                        <div x-show="notifTab === 'actions'" class="max-h-[340px] overflow-y-auto space-y-2.5 pr-0.5">
                            @forelse($notifs['action_required'] as $item)
                                <div class="p-3 bg-[#FAFDFC] hover:bg-[#F4F9F7] rounded-2xl border border-slate-100 hover:border-slate-200 transition space-y-2">
                                    <div class="flex items-center justify-between gap-2">
                                        <span class="text-[9px] font-extrabold tracking-wider uppercase px-2 py-0.5 rounded-md border {{ $item['badge_class'] }}">
                                            {{ $item['badge_text'] }}
                                        </span>
                                        <span class="text-[10px] font-mono text-slate-400">#{{ $item['invoice'] }}</span>
                                    </div>
                                    <div>
                                        <h4 class="text-xs font-bold text-slate-900 truncate">{{ $item['customer_name'] }}</h4>
                                        <p class="text-[11px] font-medium text-slate-600 mt-0.5">{{ $item['desc'] }}</p>
                                        <p class="text-[10px] text-slate-400 mt-0.5">{{ $item['meta'] }}</p>
                                    </div>
                                    <div class="pt-1 flex items-center justify-end gap-2">
                                        @if($item['action_type'] === 'whatsapp')
                                            <a href="{{ $item['action_url'] }}" target="_blank"
                                               @click="notifOpen = false"
                                               class="inline-flex items-center gap-1.5 text-[11px] font-bold px-3 py-1 bg-rose-50 text-rose-700 hover:bg-rose-100 border border-rose-200 rounded-xl transition cursor-pointer">
                                                <svg class="w-3.5 h-3.5 fill-current" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981z"/></svg>
                                                <span>{{ $item['action_label'] }}</span>
                                            </a>
                                        @else
                                            <a href="{{ $item['action_url'] }}"
                                               @click.prevent="notifOpen = false; window.dispatchEvent(new CustomEvent('open-booking-detail', { detail: { id: {{ $item['booking_id'] ?? $item['id'] }} } }));"
                                               class="inline-flex items-center gap-1 text-[11px] font-bold px-3 py-1 bg-brand-50 text-brand-700 hover:bg-brand-100 border border-brand-200 rounded-xl transition cursor-pointer">
                                                <span>{{ $item['action_label'] }}</span>
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            @empty
                                <div class="py-8 text-center">
                                    <div class="w-10 h-10 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center mx-auto mb-2">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                    </div>
                                    <p class="text-xs font-bold text-slate-800">All Caught Up!</p>
                                    <p class="text-[11px] text-slate-400 mt-0.5">No pending payment verifications or due settlements.</p>
                                </div>
                            @endforelse
                        </div>

                        <!-- Content Tab: Field Schedule -->
                        <div x-show="notifTab === 'field'" class="max-h-[340px] overflow-y-auto space-y-2.5 pr-0.5">
                            @forelse($notifs['field_operations'] as $item)
                                <div class="p-3 bg-[#FAFDFC] hover:bg-[#F4F9F7] rounded-2xl border border-slate-100 hover:border-slate-200 transition space-y-2">
                                    <div class="flex items-center justify-between gap-2">
                                        <span class="text-[9px] font-extrabold tracking-wider uppercase px-2 py-0.5 rounded-md border {{ $item['badge_class'] }}">
                                            {{ $item['badge_text'] }}
                                        </span>
                                        <span class="text-[10px] font-mono text-slate-400">#{{ $item['invoice'] }}</span>
                                    </div>
                                    <div>
                                        <h4 class="text-xs font-bold text-slate-900 truncate">{{ $item['customer_name'] }}</h4>
                                        <p class="text-[11px] font-medium text-slate-600 mt-0.5">{{ $item['desc'] }}</p>
                                        <p class="text-[10px] text-slate-400 mt-0.5">{{ $item['meta'] }}</p>
                                    </div>
                                    <div class="pt-1 flex items-center justify-end gap-2">
                                        @if($item['action_type'] === 'whatsapp')
                                            <a href="{{ $item['action_url'] }}" target="_blank"
                                               @click="notifOpen = false"
                                               class="inline-flex items-center gap-1.5 text-[11px] font-bold px-3 py-1 bg-teal-50 text-teal-800 hover:bg-teal-100 border border-teal-200 rounded-xl transition cursor-pointer">
                                                <svg class="w-3.5 h-3.5 fill-current" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981z"/></svg>
                                                <span>{{ $item['action_label'] }}</span>
                                            </a>
                                        @else
                                            <a href="{{ $item['action_url'] }}"
                                               @click.prevent="notifOpen = false; window.dispatchEvent(new CustomEvent('open-booking-detail', { detail: { id: {{ $item['booking_id'] ?? $item['id'] }} } }));"
                                               class="inline-flex items-center gap-1 text-[11px] font-bold px-3 py-1 bg-teal-50 text-teal-800 hover:bg-teal-100 border border-teal-200 rounded-xl transition cursor-pointer">
                                                <span>{{ $item['action_label'] }}</span>
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            @empty
                                <div class="py-8 text-center">
                                    <div class="w-10 h-10 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-2">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    </div>
                                    <p class="text-xs font-bold text-slate-800">No Upcoming Schedule</p>
                                    <p class="text-[11px] text-slate-400 mt-0.5">No tent loading operations in the next 2 days.</p>
                                </div>
                            @endforelse
                        </div>

                        <!-- Footer Dropdown -->
                        <div class="pt-3 mt-2 border-t border-slate-100 flex items-center justify-between">
                            <span class="text-[10px] text-slate-400">Total: {{ $notifs['total_count'] }} events</span>
                            <a href="{{ route('admin.bookings.index', ['tab' => 'bookings']) }}"
                               @click="notifOpen = false; if (typeof $store !== 'undefined' && $store.nav) $store.nav.setTab('bookings')"
                               class="text-xs font-bold text-brand-600 hover:text-brand-700 flex items-center gap-1 transition">
                                <span>View All Bookings</span>
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Avatar Owner -->
                <button type="button"
                        @click="profileModalOpen = true"
                        title="Owner Profile"
                        class="w-9 h-9 sm:w-10 sm:h-10 rounded-full bg-brand-600 text-white font-bold text-xs flex items-center justify-center ring-2 ring-brand-100 hover:ring-brand-200 transition overflow-hidden shrink-0">
                    <img src="https://ui-avatars.com/api/?name={{ urlencode($ownerName) }}&background=045D56&color=ffffff&bold=true&size=80"
                         alt="{{ $ownerName }}"
                         class="w-full h-full object-cover">
                </button>

                <!-- Tombol Menu Tablet/HP -->
                <button type="button"
                        @click="mobileMenuOpen = !mobileMenuOpen"
                        class="xl:hidden w-9 h-9 rounded-full bg-[#F6F8F7] hover:bg-[#EAF0EE] text-slate-700 flex items-center justify-center transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
            </div>
        </header>

        <!-- Menu Pill Collapsible Tablet/HP -->
        <div x-show="mobileMenuOpen"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 -translate-y-2"
             x-transition:enter-end="opacity-100 translate-y-0"
             class="xl:hidden mt-3 bg-white rounded-3xl p-3 shadow-card border border-[#EAF0EE]"
             x-cloak>
            <div class="grid grid-cols-3 sm:grid-cols-6 gap-1.5">
                @foreach($navItems as $nav)
                    @if($isDashboardPage)
                        <button type="button"
                                @click="$store.nav.setTab('{{ $nav['key'] }}'); mobileMenuOpen = false"
                                :class="$store.nav.tab === '{{ $nav['key'] }}'
                                    ? 'bg-brand-600 text-white font-semibold shadow-sm'
                                    : 'bg-[#F6F8F7] text-slate-600 hover:text-slate-900 font-medium'"
                                class="py-2 px-3 rounded-2xl text-xs transition text-center">
                            {{ $nav['label'] }}
                        </button>
                    @else
                        <a href="{{ $nav['key'] === 'services' ? route('admin.masters.index') : route('admin.bookings.index') }}"
                           class="py-2 px-3 rounded-2xl text-xs transition text-center bg-[#F6F8F7] text-slate-700 font-medium">
                            {{ $nav['label'] }}
                        </a>
                    @endif
                @endforeach
            </div>
        </div>

        <!-- Sleek App Bar Notification Banner (Positioned Right Below Top App Bar, Auto-dismiss, Progress Bar, Close Button) -->
        <div x-data="{
                show: {{ (session('success') || session('error')) ? 'true' : 'false' }},
                type: '{{ session('error') ? 'error' : 'success' }}',
                title: '{{ session('error') ? 'Error' : 'Success' }}',
                message: {{ Js::from(session('success') ?? session('error') ?? '') }},
                progress: 100,
                duration: 4500,
                remaining: 4500,
                paused: false,
                interval: null,
                init() {
                    window.addEventListener('toast', (e) => {
                        this.trigger(e.detail.message, e.detail.type || 'success', e.detail.title);
                    });
                    if (this.show && this.message) {
                        this.startTimer();
                    }
                },
                trigger(msg, t = 'success', customTitle = null) {
                    this.message = msg;
                    this.type = t;
                    this.title = customTitle || (t === 'error' ? 'Error' : 'Success');
                    this.show = true;
                    this.remaining = this.duration;
                    this.progress = 100;
                    this.startTimer();
                },
                startTimer() {
                    if (this.interval) clearInterval(this.interval);
                    const step = 50;
                    this.interval = setInterval(() => {
                        if (!this.paused && this.show) {
                            this.remaining -= step;
                            this.progress = Math.max(0, (this.remaining / this.duration) * 100);
                            if (this.remaining <= 0) {
                                this.close();
                            }
                        }
                    }, step);
                },
                pause() {
                    this.paused = true;
                },
                resume() {
                    this.paused = false;
                },
                close() {
                    if (this.interval) clearInterval(this.interval);
                    this.show = false;
                }
             }"
             x-show="show"
             x-cloak
             @mouseenter="pause()"
             @mouseleave="resume()"
             x-transition:enter="transition cubic-bezier(0.16, 1, 0.3, 1) duration-300 transform"
             x-transition:enter-start="opacity-0 -translate-y-2 scale-[0.99]"
             x-transition:enter-end="opacity-100 translate-y-0 scale-100"
             x-transition:leave="transition cubic-bezier(0.16, 1, 0.3, 1) duration-200 transform"
             x-transition:leave-start="opacity-100 translate-y-0 scale-100"
             x-transition:leave-end="opacity-0 -translate-y-2 scale-[0.99]"
             class="mt-4 sm:mt-5 rounded-2xl border shadow-card overflow-hidden select-none"
             :class="type === 'error' 
                 ? 'bg-gradient-to-r from-rose-50/90 via-white to-white border-rose-200/90 text-rose-900' 
                 : 'bg-gradient-to-r from-emerald-50/80 via-white to-white border-emerald-200/90 text-slate-800'">

            <div class="p-3 sm:p-3.5 flex items-center justify-between gap-3">
                <div class="flex items-center gap-3 min-w-0">
                    <!-- Icon Badge -->
                    <div class="w-8 h-8 rounded-xl flex items-center justify-center shrink-0 shadow-xs"
                         :class="type === 'error' ? 'bg-gradient-to-br from-rose-500 to-rose-700 text-white' : 'bg-gradient-to-br from-brand-500 to-brand-700 text-white'">
                        <template x-if="type === 'error'">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                        </template>
                        <template x-if="type !== 'error'">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        </template>
                    </div>

                    <!-- Message Body -->
                    <div class="min-w-0">
                        <div class="flex items-center gap-2">
                            <span class="text-[10px] font-bold uppercase tracking-wider px-1.5 py-0.5 rounded"
                                  :class="type === 'error' ? 'bg-rose-100 text-rose-700' : 'bg-emerald-100 text-emerald-800'"
                                  x-text="title"></span>
                            <span class="text-[10px] text-slate-400 font-medium hidden sm:inline">Baru saja</span>
                        </div>
                        <p class="text-xs font-semibold text-slate-800 mt-0.5 leading-snug break-words"
                           x-text="message"></p>
                    </div>
                </div>

                <!-- Dismiss Button -->
                <button type="button" @click="close()"
                        class="w-7 h-7 rounded-lg text-slate-400 hover:text-slate-700 hover:bg-slate-100/80 active:scale-90 flex items-center justify-center transition shrink-0 cursor-pointer"
                        title="Tutup pemberitahuan">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <!-- Progress Bar Countdown Line -->
            <div class="w-full bg-slate-100 h-1 overflow-hidden">
                <div class="h-full transition-[width] duration-75 ease-linear"
                     :class="type === 'error' ? 'bg-gradient-to-r from-rose-500 to-rose-600' : 'bg-gradient-to-r from-brand-500 to-brand-600'"
                     :style="`width: ${progress}%`"></div>
            </div>

        </div>

        <!-- KONTEN UTAMA -->
        <main class="mt-6">
            @yield('content')
        </main>

    </div>

    <!-- ============================================================== -->
    <!-- MOBILE FLOATING BOTTOM NAVIGATION BAR (KHUSUS HP)              -->
    <!-- ============================================================== -->
    <nav class="fixed bottom-4 sm:bottom-6 inset-x-4 max-w-[370px] sm:max-w-md mx-auto z-40 lg:hidden bg-white/95 backdrop-blur-xl rounded-full h-[62px] px-2.5 shadow-[0_12px_36px_rgba(0,0,0,0.12)] border border-[#DFE7E4] flex items-center justify-between">
        @php
            $mobileTabs = [
                ['key' => 'dashboard', 'label' => 'Dashboard', 'icon' => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6'],
                ['key' => 'bookings',  'label' => 'Bookings',  'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2'],
                ['key' => 'calendar',  'label' => 'Calendar',  'icon' => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z'],
                ['key' => 'invoice',   'label' => 'Invoice',   'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
            ];
        @endphp
        @foreach($mobileTabs as $mTab)
            @if($isDashboardPage)
                <button type="button"
                        @click="$store.nav.setTab('{{ $mTab['key'] }}')"
                        :class="$store.nav.tab === '{{ $mTab['key'] }}' 
                            ? 'bg-brand-600 text-white shadow-md font-bold px-4 h-11 rounded-full' 
                            : 'text-slate-400 hover:text-slate-700 w-11 h-11 rounded-full hover:bg-slate-50'"
                        class="flex items-center justify-center gap-2 text-xs transition-all active:scale-95 cursor-pointer shrink-0">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $mTab['icon'] }}"/>
                    </svg>
                    <span x-show="$store.nav.tab === '{{ $mTab['key'] }}'"
                          x-cloak
                          x-transition:enter="transition ease-out duration-150"
                          x-transition:enter-start="opacity-0 scale-95"
                          x-transition:enter-end="opacity-100 scale-100"
                          class="whitespace-nowrap">{{ $mTab['label'] }}</span>
                </button>
            @else
                <a href="{{ route('admin.bookings.index') }}"
                   class="flex items-center justify-center w-11 h-11 rounded-full text-slate-400 hover:text-slate-700 hover:bg-slate-50 transition-all">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $mTab['icon'] }}"/>
                    </svg>
                </a>
            @endif
        @endforeach
    </nav>

    <!-- ============================================================== -->
    <!-- MODAL PROFIL OWNER & LOGOUT                                    -->
    <!-- ============================================================== -->
    <div x-show="profileModalOpen"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         class="fixed inset-0 z-50 flex items-end sm:items-center justify-center p-0 sm:p-4 bg-slate-950/50 backdrop-blur-xs"
         x-cloak>
        <div class="w-full sm:max-w-xs bg-white rounded-t-3xl sm:rounded-3xl border border-[#EAF0EE] p-6 space-y-4 shadow-2xl text-center"
             @click.away="profileModalOpen = false">
            <img src="https://ui-avatars.com/api/?name={{ urlencode($ownerName) }}&background=045D56&color=ffffff&bold=true&size=128"
                 alt="{{ $ownerName }}"
                 class="w-16 h-16 rounded-full mx-auto ring-4 ring-brand-50">
            <div>
                <h3 class="text-sm font-bold text-slate-900">{{ $ownerName }}</h3>
                <p class="text-xs text-slate-400">{{ $ownerEmail }}</p>
                <span class="inline-block mt-1.5 px-3 py-0.5 rounded-full text-[10px] font-bold bg-brand-50 text-brand-700">
                    NK Barokah Owner
                </span>
            </div>
            <div class="pt-3 border-t border-slate-100 space-y-2">
                <form action="{{ route('admin.logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full py-2.5 rounded-full bg-rose-50 text-rose-600 hover:bg-rose-100 text-xs font-bold transition cursor-pointer">
                        Sign Out
                    </button>
                </form>
                <button type="button" @click="profileModalOpen = false" class="w-full py-2 text-xs font-medium text-slate-400 hover:text-slate-700 cursor-pointer">
                    Close
                </button>
            </div>
        </div>
    </div>

</body>
</html>
