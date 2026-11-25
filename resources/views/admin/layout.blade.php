<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" class="h-full bg-[#F6F8F7]">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <meta name="color-scheme" content="light dark">
    <script>
        (function () {
            var stored = null;
            try { stored = localStorage.getItem('color-scheme'); } catch (e) {}
            var mode = (stored === 'light' || stored === 'dark') ? stored : 'light dark';
            var meta = document.querySelector('meta[name="color-scheme"]');
            if (meta) meta.content = mode;
            var isDark = mode === 'dark' || (mode === 'light dark' && window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches);
            document.documentElement.classList.toggle('dark', isDark);
            document.documentElement.classList.toggle('light', !isDark);
        })();
    </script>
    <title>@yield('title', 'Dashboard') | NK Barokah Tenda</title>

    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">

    <!-- Google Fonts: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Tailwind CSS CDN & Alpine.js -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="{{ asset('css/admin-dark.css') }}?v={{ file_exists(public_path('css/admin-dark.css')) ? filemtime(public_path('css/admin-dark.css')) : time() }}">

    <script>
        tailwind.config = {
            darkMode: 'class',
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
            const validMainTabs = @js((Auth::user()?->isOwner() ?? false)
                ? ['dashboard', 'bookings', 'calendar', 'clients', 'services', 'invoice', 'settings']
                : ['dashboard', 'bookings', 'calendar']);
            const urlParams = new URLSearchParams(window.location.search);
            const savedTab = sessionStorage.getItem('admin_active_tab');
            const safeSavedTab = validMainTabs.includes(savedTab) ? savedTab : 'dashboard';
            const initialTab = urlParams.get('tab') || window.location.hash.replace('#', '') || safeSavedTab;
            
            // Bersihkan jika ada nilai sub-view yang tersisa di sessionStorage
            if (!validMainTabs.includes(savedTab)) {
                try { sessionStorage.setItem('admin_active_tab', 'dashboard'); } catch (e) {}
            }

            Alpine.store('theme', {
                preference: (() => {
                    try {
                        const s = localStorage.getItem('color-scheme');
                        return (s === 'light' || s === 'dark') ? s : 'system';
                    } catch (e) {
                        return 'system';
                    }
                })(),
                systemDark: window.matchMedia ? window.matchMedia('(prefers-color-scheme: dark)').matches : false,

                init() {
                    this.apply();
                    if (window.matchMedia) {
                        const mql = window.matchMedia('(prefers-color-scheme: dark)');
                        const onSystemChange = (e) => {
                            this.systemDark = e.matches;
                            this.apply();
                        };
                        if (mql.addEventListener) {
                            mql.addEventListener('change', onSystemChange);
                        } else if (mql.addListener) {
                            mql.addListener(onSystemChange);
                        }
                    }
                    window.addEventListener('storage', (e) => {
                        if (e.key === 'color-scheme') {
                            this.preference = (e.newValue === 'light' || e.newValue === 'dark') ? e.newValue : 'system';
                            this.apply();
                        }
                    });
                },

                get resolved() {
                    if (this.preference === 'dark') return 'dark';
                    if (this.preference === 'light') return 'light';
                    return this.systemDark ? 'dark' : 'light';
                },

                set(mode) {
                    const next = (mode === 'light' || mode === 'dark') ? mode : 'system';
                    this.preference = next;
                    try {
                        if (next === 'system') {
                            localStorage.removeItem('color-scheme');
                        } else {
                            localStorage.setItem('color-scheme', next);
                        }
                    } catch (e) {}
                    this.apply();
                },

                apply() {
                    const meta = document.querySelector('meta[name="color-scheme"]');
                    if (meta) {
                        meta.content = this.preference === 'system' ? 'light dark' : this.preference;
                    }
                    const isDark = this.resolved === 'dark';
                    document.documentElement.classList.toggle('dark', isDark);
                    document.documentElement.classList.toggle('light', !isDark);
                }
            });

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
      x-data="{ mobileMenuOpen: false, syncModalOpen: false, notifOpen: false, profileModalOpen: false }"
      @close-notifs.window="notifOpen = false">

    @php
        $isDashboardPage = request()->routeIs('admin.bookings.index');
        $isOwner = Auth::user()?->isOwner() ?? false;
        $ownerName = Auth::user()->name ?? 'Owner NK Barokah';
        $ownerEmail = Auth::user()->email ?? 'owner@nkbarokah.com';
        $roleLabel = Auth::user()?->roleLabel() ?? 'NK Barokah Owner';
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
                    $navItems = $isOwner
                        ? [
                            ['key' => 'dashboard', 'label' => __('admin.nav.dashboard')],
                            ['key' => 'bookings',  'label' => __('admin.nav.bookings')],
                            ['key' => 'calendar',  'label' => __('admin.nav.calendar')],
                            ['key' => 'clients',   'label' => __('admin.nav.clients')],
                            ['key' => 'services',  'label' => __('admin.nav.services')],
                            ['key' => 'invoice',   'label' => __('admin.nav.invoice')],
                        ]
                        : [
                            ['key' => 'dashboard', 'label' => __('admin.nav.dashboard')],
                            ['key' => 'bookings',  'label' => __('admin.nav.bookings')],
                            ['key' => 'calendar',  'label' => __('admin.nav.calendar')],
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

                @include('admin.partials.spotlight-search')

                <!-- Settings & Google Sync Button (Owner Only) -->
                @if($isOwner)
                    @if($isDashboardPage)
                        <button type="button"
                                @click="$store.nav.setTab('settings')"
                                title="{{ __('admin.nav.settings_tooltip') }}"
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
                @endif

                @include('admin.partials.notifications')

                <!-- Avatar Owner -->
                <button type="button"
                        @click="profileModalOpen = true"
                        title="{{ __('admin.nav.profile_tooltip') }}"
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

        @include('admin.partials.flash-banner')

        <!-- KONTEN UTAMA -->
        <main class="mt-6">
            @yield('content')
        </main>

    </div>

    @include('admin.partials.mobile-nav')

    @include('admin.partials.owner-profile-modal')

</body>
</html>
