<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <title>Reservasi Layanan & Tenda | NK Barokah</title>

    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">

    <!-- Typography: Plus Jakarta Sans & JetBrains Mono -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@500;600;700&display=swap" rel="stylesheet">

    <!-- Tailwind CSS CDN & Alpine.js -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'system-ui', '-apple-system', 'sans-serif'],
                        mono: ['"JetBrains Mono"', 'ui-monospace', 'monospace'],
                    },
                    colors: {
                        brand: {
                            50: '#F0FDF8',
                            100: '#DCFCEE',
                            200: '#BBF7DE',
                            500: '#168579',
                            600: '#045D56', // NK Barokah Primary Deep Emerald
                            700: '#034B45',
                            800: '#023B36',
                            900: '#022E2A',
                        }
                    },
                    boxShadow: {
                        'subtle': '0 1px 2px 0 rgba(0, 0, 0, 0.04)',
                        'card': '0 1px 3px 0 rgba(0, 0, 0, 0.05), 0 20px 25px -5px rgba(0, 0, 0, 0.02)',
                        'dock': '0 -4px 20px -2px rgba(0, 0, 0, 0.06)',
                        'pill': '0 4px 14px -2px rgba(4, 93, 86, 0.28)',
                    }
                }
            }
        }
    </script>

    <style>
        [x-cloak] { display: none !important; }
        * { -webkit-tap-highlight-color: transparent; }
        html { scroll-behavior: smooth; }
        
        /* Prevent number spinners */
        input[type=number]::-webkit-inner-spin-button, 
        input[type=number]::-webkit-outer-spin-button { 
            -webkit-appearance: none; 
            margin: 0; 
        }
        input[type=number] {
            -moz-appearance: textfield;
        }

        /* Safe area support for iOS home bar */
        .pb-safe {
            padding-bottom: max(0.85rem, env(safe-area-inset-bottom));
        }
        .pt-safe {
            padding-top: max(0.5rem, env(safe-area-inset-top));
        }

        /* Hide scrollbars for categories & horizontal scrolling tabs */
        .no-scrollbar::-webkit-scrollbar {
            display: none;
            width: 0;
            height: 0;
        }
        .no-scrollbar {
            -ms-overflow-style: none; /* IE and Edge */
            scrollbar-width: none; /* Firefox */
            -webkit-overflow-scrolling: touch;
        }
    </style>
</head>
<body class="min-h-full bg-slate-50 text-slate-900 font-sans antialiased flex flex-col justify-between selection:bg-brand-100"
      x-data="bookingReservation({
          packages: {{ json_encode($packages) }},
          addons: {{ json_encode($addons) }},
          bookedDates: {{ json_encode($bookedDates) }},
          minDpAmount: {{ $settings['min_dp_amount'] ?? config('wo.min_dp_amount', 1000000) }},
          standardDays: {{ $settings['standard_package_max_days'] ?? config('wo.standard_package_max_days', 2) }},
          extraDayRate: {{ (($settings['extra_day_charge_rate'] ?? 15) > 1 ? ($settings['extra_day_charge_rate'] ?? 15) / 100 : ($settings['extra_day_charge_rate'] ?? 0.15)) }},
          warehouseLat: {{ $settings['warehouse_lat'] ?? '-6.9857806' }},
          warehouseLng: {{ $settings['warehouse_lng'] ?? '109.136971' }},
          warehouseAddress: '{{ $settings['warehouse_address'] ?? 'Jl. K.H. Ahmad Dahlan, RT 04 / RW 01, Slawi Kulon, Kec. Slawi, Kab. Tegal' }}',
          freeDistanceKm: {{ $settings['free_distance_km'] ?? 10 }},
          distanceTierKm: {{ $settings['distance_tier_km'] ?? 10 }},
          transportFeePerTier: {{ $settings['transport_fee_per_tier'] ?? 300000 }},
          oldEventDate: '{{ old('event_date', '') }}',
          oldEventEndDate: '{{ old('event_end_date', '') }}',
          oldDurationDays: {{ (int) old('duration_days', 1) }},
          oldDistanceKm: {{ (float) old('delivery_distance_km', 0) }}
      })"
      x-cloak>

    <!-- ============================================================== -->
    <!-- STICKY TOP APP HEADER                                          -->
    <!-- ============================================================== -->
    <header class="sticky top-0 z-40 bg-white/95 backdrop-blur-md border-b border-slate-200/90 pt-safe">
        <div class="max-w-2xl mx-auto px-4 h-14 sm:h-16 flex items-center justify-between">
            <!-- Brand Identity -->
            <a href="{{ url('/') }}" class="flex items-center gap-2.5">
                <img src="{{ asset('images/logo.png') }}" alt="NK Barokah Logo" class="h-8 sm:h-9 w-auto object-contain">
                <div class="leading-none">
                    <span class="block text-xs sm:text-sm font-bold tracking-tight text-slate-900">NK Barokah</span>
                    <span class="block text-[9px] sm:text-[10px] font-semibold tracking-wider text-slate-400 uppercase mt-0.5">Persewaan Tenda &amp; Alat Pesta</span>
                </div>
            </a>

            <!-- Step Progress Indicator -->
            <div class="flex flex-col items-end gap-1.5">
                <span class="text-[11px] font-bold text-slate-700 tracking-tight" x-text="stepTitle"></span>
                <div class="flex items-center gap-1">
                    <template x-for="s in [1, 2, 3, 4]" :key="s">
                        <div class="w-6 sm:w-8 h-1.5 rounded-full transition-all duration-300"
                             :class="s <= step ? 'bg-brand-600' : 'bg-slate-200'"></div>
                    </template>
                </div>
            </div>
        </div>
    </header>

    <!-- ============================================================== -->
    <!-- MAIN FORM BODY (BALANCED PADDING FOR DESKTOP & MOBILE)         -->
    <!-- ============================================================== -->
    <main class="max-w-2xl mx-auto w-full px-4 sm:px-6 pt-5 pb-6 md:pb-10 flex-1">

        <!-- Server Validation Errors -->
        @if (isset($errors) && $errors->any())
            <div class="mb-5 p-4 rounded-2xl bg-rose-50 border border-rose-200 text-xs text-rose-800 space-y-1">
                <div class="font-bold flex items-center gap-1.5">
                    <svg class="w-4 h-4 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    <span>Mohon periksa kesalahan berikut:</span>
                </div>
                <ul class="list-disc list-inside space-y-0.5 text-rose-700 pl-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form id="reservationForm" action="{{ route('booking.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            @include('booking.partials.step-1-schedule')

            @include('booking.partials.step-2-package')

            @include('booking.partials.step-3-client')

            @include('booking.partials.step-4-payment')

        </form>

    </main>

    @include('booking.partials.mobile-dock')
    @include('booking.partials.scripts')
</body>
</html>
