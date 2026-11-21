<!DOCTYPE html>
<html lang="id" class="h-full bg-[#F6F8F7]">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <title>@yield('title', 'Dasbor') | NK Barokah Wedding Organizer</title>

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
            const urlParams = new URLSearchParams(window.location.search);
            const initialTab = urlParams.get('tab') || window.location.hash.replace('#', '') || sessionStorage.getItem('admin_active_tab') || '{{ request()->routeIs('admin.masters.*') ? 'services' : 'dashboard' }}';
            Alpine.store('nav', {
                tab: initialTab,
                search: '',
                setTab(t) {
                    this.tab = t;
                    try {
                        sessionStorage.setItem('admin_active_tab', t);
                        const url = new URL(window.location);
                        url.searchParams.set('tab', t);
                        window.history.replaceState({}, '', url);
                    } catch (e) {}
                }
            });
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
    </style>
</head>
<body class="min-h-full bg-[#F6F8F7] text-slate-800 antialiased font-sans selection:bg-brand-100 selection:text-brand-900"
      x-data="{ mobileMenuOpen: false, syncModalOpen: false, notifOpen: false, profileModalOpen: false }">

    @php
        $isDashboardPage = request()->routeIs('admin.bookings.index');
        $ownerName = Auth::user()->name ?? 'Owner NK Barokah';
        $ownerEmail = Auth::user()->email ?? 'owner@nkbarokah.com';
        $pendingNotifCount = \App\Models\Booking::where('status', 'pending_verification')->count();
        $recentNotifs = \App\Models\Booking::latest()->take(3)->get();
    @endphp

    <!-- ============================================================== -->
    <!-- CONTAINER UTAMA (FLOATING TOP PILL NAV + AIRY CANVAS)          -->
    <!-- ============================================================== -->
    <div class="max-w-[1400px] mx-auto px-3 sm:px-6 lg:px-8 pt-4 sm:pt-5 pb-24 lg:pb-10">

        <!-- TOP FLOATING PILL NAVBAR -->
        <header class="sticky top-3 z-40 bg-white/95 backdrop-blur-md rounded-full px-3.5 sm:px-5 py-2.5 shadow-nav border border-[#EAF0EE] flex items-center justify-between gap-3">

            <!-- Kiri: Logo Brand -->
            <div class="flex items-center gap-2.5 shrink-0">
                <a href="{{ route('admin.bookings.index') }}"
                   @if($isDashboardPage) @click.prevent="$store.nav.setTab('dashboard')" @endif
                   class="flex items-center gap-2.5 group">
                    <div class="w-9 h-9 rounded-xl bg-brand-600 text-white flex items-center justify-center shadow-sm group-hover:bg-brand-700 transition">
                        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M5 18V7a2 2 0 0 1 3.4-1.4l7.2 7.2A2 2 0 0 0 19 11.4V6"/>
                            <path d="M5 13v5a2 2 0 0 0 3.4 1.4l1.6-1.6"/>
                        </svg>
                    </div>
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

                <!-- Pencarian Global Pill -->
                <div class="hidden md:flex items-center bg-[#F6F8F7] border border-[#E6ECEA] focus-within:border-brand-400 focus-within:bg-white rounded-full pl-4 pr-1.5 py-1 w-52 lg:w-64 transition">
                    <input type="text"
                           x-model="$store.nav.search"
                           placeholder="Search..."
                           class="w-full bg-transparent text-xs text-slate-700 placeholder-slate-400 focus:outline-none">
                    <button type="button"
                            class="w-8 h-8 rounded-full bg-brand-600 hover:bg-brand-700 text-white flex items-center justify-center shrink-0 transition shadow-sm">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.4" d="M21 21l-5.2-5.2m2.2-4.8a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </button>
                </div>

                <!-- Tombol Pengaturan & Google Sync -->
                @if($isDashboardPage)
                    <button type="button"
                            @click="$store.nav.setTab('settings')"
                            title="Pengaturan & Konfigurasi"
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
                       title="Pengaturan & Konfigurasi"
                       class="w-9 h-9 sm:w-10 sm:h-10 rounded-full bg-[#F6F8F7] hover:bg-[#EAF0EE] text-slate-700 flex items-center justify-center transition">
                        <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.9" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.9" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                    </a>
                @endif

                <!-- Tombol Notifikasi -->
                <div class="relative">
                    <button type="button"
                            @click="notifOpen = !notifOpen"
                            title="Notifikasi"
                            class="w-9 h-9 sm:w-10 sm:h-10 rounded-full bg-[#F6F8F7] hover:bg-[#EAF0EE] text-slate-700 flex items-center justify-center transition relative">
                        <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.9" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                        @if($pendingNotifCount > 0)
                            <span class="w-2 h-2 rounded-full bg-rose-500 ring-2 ring-white absolute top-2 right-2.5"></span>
                        @endif
                    </button>

                    <!-- Dropdown Notifikasi Real -->
                    <div x-show="notifOpen"
                         @click.away="notifOpen = false"
                         x-transition:enter="transition ease-out duration-150"
                         x-transition:enter-start="opacity-0 scale-95"
                         x-transition:enter-end="opacity-100 scale-100"
                         class="absolute right-0 mt-2.5 w-80 bg-white rounded-3xl shadow-xl border border-[#EAF0EE] p-4 z-50"
                         x-cloak>
                        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                            <span class="text-xs font-bold text-slate-900">Recent Activity</span>
                            <span class="text-[11px] font-semibold text-brand-600 bg-brand-50 px-2.5 py-0.5 rounded-full">
                                {{ $pendingNotifCount > 0 ? $pendingNotifCount . ' Pending' : 'All Clear' }}
                            </span>
                        </div>
                        <div class="divide-y divide-slate-100 mt-1">
                            @forelse($recentNotifs as $rn)
                                <a href="{{ route('admin.bookings.show', $rn->id) }}" class="py-3 flex items-start gap-3 hover:bg-[#F8FAFA] rounded-xl px-1.5 transition">
                                    <span class="w-2 h-2 rounded-full mt-1.5 shrink-0 {{ $rn->status === 'pending_verification' ? 'bg-amber-500' : 'bg-brand-600' }}"></span>
                                    <div class="min-w-0">
                                        <p class="text-xs font-semibold text-slate-800 truncate">#{{ $rn->invoice_number }} · {{ $rn->customer_name }}</p>
                                        <p class="text-[11px] text-slate-400 mt-0.5 truncate">{{ $rn->package_name }} · {{ $rn->created_at->diffForHumans() }}</p>
                                    </div>
                                </a>
                            @empty
                                <p class="py-4 text-center text-xs text-slate-400">Belum ada aktivitas reservasi.</p>
                            @endforelse
                        </div>
                    </div>
                </div>

                <!-- Avatar Owner -->
                <button type="button"
                        @click="profileModalOpen = true"
                        title="Profil Owner"
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
                title: '{{ session('error') ? 'Terjadi Kesalahan' : 'Aksi Berhasil' }}',
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
                    this.title = customTitle || (t === 'error' ? 'Terjadi Kesalahan' : 'Aksi Berhasil');
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
    <nav class="fixed bottom-3 inset-x-3 z-40 lg:hidden bg-white/95 backdrop-blur-md rounded-full px-3 py-2 shadow-xl border border-[#E6ECEA] flex items-center justify-around">
        @php
            $mobileTabs = [
                ['key' => 'dashboard', 'label' => 'Dashboard', 'icon' => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6'],
                ['key' => 'bookings',  'label' => 'Bookings',  'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2'],
                ['key' => 'calendar',  'label' => 'Calendar',  'icon' => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z'],
                ['key' => 'invoice',   'label' => 'Invoice',   'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
                ['key' => 'settings',  'label' => 'Settings',  'icon' => 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z'],
            ];
        @endphp
        @foreach($mobileTabs as $mTab)
            @if($isDashboardPage)
                <button type="button"
                        @click="$store.nav.setTab('{{ $mTab['key'] }}')"
                        :class="$store.nav.tab === '{{ $mTab['key'] }}' ? 'bg-brand-600 text-white px-4 py-2 rounded-full shadow-sm' : 'text-slate-500 px-3 py-2'"
                        class="flex items-center gap-1.5 text-[11px] font-semibold transition-all">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $mTab['icon'] }}"/>
                    </svg>
                    <span x-show="$store.nav.tab === '{{ $mTab['key'] }}'">{{ $mTab['label'] }}</span>
                </button>
            @else
                <a href="{{ route('admin.bookings.index') }}"
                   class="flex items-center gap-1.5 text-[11px] font-semibold text-slate-500 px-3 py-2">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                    Pemilik NK Barokah
                </span>
            </div>
            <div class="pt-3 border-t border-slate-100 space-y-2">
                <form action="{{ route('admin.logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full py-2.5 rounded-full bg-rose-50 text-rose-600 hover:bg-rose-100 text-xs font-bold transition">
                        Keluar dari Akun
                    </button>
                </form>
                <button type="button" @click="profileModalOpen = false" class="w-full py-2 text-xs font-medium text-slate-400 hover:text-slate-700">
                    Tutup
                </button>
            </div>
        </div>
    </div>

</body>
</html>
