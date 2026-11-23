<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <title>Reservasi Pernikahan | NK Barokah</title>

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
                    <span class="block text-[9px] sm:text-[10px] font-semibold tracking-wider text-slate-400 uppercase mt-0.5">Wedding Organizer</span>
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

            <!-- ============================================================== -->
            <!-- STEP 1: DATE & CALENDAR AVAILABILITY                           -->
            <!-- ============================================================== -->
            <div x-show="step === 1"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 translate-y-2"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 class="space-y-4 sm:space-y-5">

                <!-- Step Title -->
                <div class="space-y-1">
                    <span class="text-[10px] sm:text-[11px] font-bold uppercase tracking-[0.12em] text-brand-600">Langkah 1 dari 4 • Jadwal Acara</span>
                    <h1 class="text-xl sm:text-2xl font-extrabold tracking-tight text-slate-900 leading-tight">Kapan tanggal pernikahan Anda?</h1>
                    <p class="text-xs sm:text-sm text-slate-500 leading-relaxed">
                        Kami hanya melayani 1 pernikahan eksklusif per tanggal. Pilih tanggal yang masih kosong pada kalender.
                    </p>
                </div>

                <!-- Calendar Card -->
                <div class="bg-white rounded-2xl border border-slate-200/90 p-4 sm:p-6 shadow-card space-y-4">

                    <!-- Month Switcher Header (Left: Prev | Center: Month, Year, Today | Right: Next) -->
                    <div class="flex items-center justify-between gap-1.5 sm:gap-2 pb-3 border-b border-slate-100">
                        <!-- Left: Prev Month Button -->
                        <button type="button" @click="prevCalMonth()"
                                class="w-8.5 h-8.5 sm:w-9 sm:h-9 rounded-xl border border-slate-200 hover:bg-slate-50 active:scale-95 flex items-center justify-center text-slate-600 transition cursor-pointer shrink-0" title="Bulan Sebelumnya">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
                        </button>

                        <!-- Center: Custom Month Dropdown + Year Dropdown + Today Pill -->
                        <div class="flex-1 flex items-center justify-center gap-1 sm:gap-2 min-w-0">
                            <!-- Custom Month Dropdown -->
                            <div class="relative" @click.away="monthOpen = false">
                                <button type="button" @click="monthOpen = !monthOpen; yearOpen = false"
                                        class="inline-flex items-center justify-between gap-1 sm:gap-2 bg-white hover:bg-slate-50 border border-slate-200/90 rounded-xl px-2.5 sm:px-3.5 py-1.5 text-xs sm:text-sm font-bold text-slate-800 transition shadow-subtle cursor-pointer active:scale-95">
                                    <span x-text="monthNames[calMonth]"></span>
                                    <svg class="w-3.5 h-3.5 text-slate-400 transition-transform duration-200"
                                         :class="{ 'rotate-180 text-brand-600': monthOpen }"
                                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/>
                                    </svg>
                                </button>

                                <div x-show="monthOpen"
                                     x-transition:enter="transition ease-out duration-150"
                                     x-transition:enter-start="opacity-0 scale-95 -translate-y-1"
                                     x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                                     x-transition:leave="transition ease-in duration-100"
                                     x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                                     x-transition:leave-end="opacity-0 scale-95 -translate-y-1"
                                     class="absolute left-1/2 -translate-x-1/2 sm:left-0 sm:translate-x-0 mt-2 w-44 max-h-60 overflow-y-auto bg-white rounded-2xl shadow-xl border border-slate-200/90 p-1.5 z-30 space-y-0.5"
                                     x-cloak>
                                    <template x-for="(name, idx) in monthNames" :key="idx">
                                        <button type="button"
                                                @click="calMonth = idx; monthOpen = false"
                                                :class="calMonth === idx ? 'bg-brand-50 text-brand-700 font-bold' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900 font-medium'"
                                                class="w-full text-left px-3.5 py-2 text-xs rounded-xl transition flex items-center justify-between cursor-pointer">
                                            <span x-text="name"></span>
                                            <span x-show="calMonth === idx" class="text-brand-600 font-bold text-xs">✓</span>
                                        </button>
                                    </template>
                                </div>
                            </div>

                            <!-- Custom Year Dropdown -->
                            <div class="relative" @click.away="yearOpen = false">
                                <button type="button" @click="yearOpen = !yearOpen; monthOpen = false"
                                        class="inline-flex items-center justify-between gap-1 sm:gap-2 bg-white hover:bg-slate-50 border border-slate-200/90 rounded-xl px-2 sm:px-3 py-1.5 text-xs sm:text-sm font-bold text-slate-800 transition shadow-subtle cursor-pointer active:scale-95">
                                    <span x-text="calYear"></span>
                                    <svg class="w-3.5 h-3.5 text-slate-400 transition-transform duration-200"
                                         :class="{ 'rotate-180 text-brand-600': yearOpen }"
                                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/>
                                    </svg>
                                </button>

                                <div x-show="yearOpen"
                                     x-transition:enter="transition ease-out duration-150"
                                     x-transition:enter-start="opacity-0 scale-95 -translate-y-1"
                                     x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                                     x-transition:leave="transition ease-in duration-100"
                                     x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                                     x-transition:leave-end="opacity-0 scale-95 -translate-y-1"
                                     class="absolute left-1/2 -translate-x-1/2 sm:left-0 sm:translate-x-0 mt-2 w-32 bg-white rounded-2xl shadow-xl border border-slate-200/90 p-1.5 z-30 space-y-0.5"
                                     x-cloak>
                                    <template x-for="y in [2026, 2027, 2028, 2029]" :key="y">
                                        <button type="button"
                                                @click="calYear = y; yearOpen = false"
                                                :class="calYear === y ? 'bg-brand-50 text-brand-700 font-bold' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900 font-medium'"
                                                class="w-full text-left px-3.5 py-2 text-xs rounded-xl transition flex items-center justify-between cursor-pointer">
                                            <span x-text="y"></span>
                                            <span x-show="calYear === y" class="text-brand-600 font-bold text-xs">✓</span>
                                        </button>
                                    </template>
                                </div>
                            </div>

                            <!-- Today Button Pill -->
                            <button type="button" @click="goToTodayMonth()"
                                    class="px-2.5 sm:px-3 py-1.5 rounded-xl text-xs font-semibold border border-slate-200 hover:bg-slate-50 active:scale-95 text-slate-700 transition cursor-pointer shrink-0">
                                Hari Ini
                            </button>
                        </div>

                        <!-- Right: Next Month Button -->
                        <button type="button" @click="nextCalMonth()"
                                class="w-8.5 h-8.5 sm:w-9 sm:h-9 rounded-xl border border-slate-200 hover:bg-slate-50 active:scale-95 flex items-center justify-center text-slate-600 transition cursor-pointer shrink-0" title="Bulan Berikutnya">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                        </button>
                    </div>

                    <!-- Days of Week Headers (7 Columns with Unique Index Key) -->
                    <div class="grid grid-cols-7 gap-1 text-center">
                        <template x-for="(dow, idx) in ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab']" :key="idx">
                            <span class="text-[11px] font-bold text-slate-400 py-1" x-text="dow"></span>
                        </template>
                    </div>

                    <!-- Calendar Grid (Hotel-Style Range Selection) -->
                    <div class="grid grid-cols-7 gap-y-1 sm:gap-y-1.5 gap-x-0 text-center" @mouseleave="hoverDate = null">
                        <template x-for="(cell, idx) in calendarGrid" :key="idx">
                            <div class="relative py-0.5 px-0.5 sm:px-1 flex items-center justify-center">
                                <!-- Background Strip for Days Inside Range -->
                                <template x-if="cell.isCurrentMonth && isBetweenDates(cell.dateString)">
                                    <div class="absolute inset-y-1 inset-x-0"
                                         :class="pickingRange ? 'bg-brand-50/90 border-y border-dashed border-brand-300' : 'bg-brand-50 border-y border-brand-200/80'"></div>
                                </template>

                                <!-- Active Month Days -->
                                <template x-if="cell.isCurrentMonth">
                                    <button type="button"
                                            @click="!cell.isPast && !cell.isBooked && selectDate(cell.dateString)"
                                            @mouseenter="onDateHover(cell.dateString)"
                                            :disabled="cell.isPast || cell.isBooked"
                                            class="w-full aspect-square flex flex-col items-center justify-center relative text-xs sm:text-sm font-semibold transition active:scale-95 cursor-pointer border"
                                            :class="{
                                                // Start Date alone (single day)
                                                'bg-brand-600 border-brand-600 text-white font-extrabold rounded-xl shadow-pill z-10': isStartDate(cell.dateString) && isSingleDateSelection,
                                                
                                                // Start Date of range
                                                'bg-brand-600 border-brand-600 text-white font-extrabold rounded-l-xl rounded-r-none shadow-subtle z-10': isStartDate(cell.dateString) && !isSingleDateSelection,

                                                // End Date of range
                                                'bg-brand-600 border-brand-600 text-white font-extrabold rounded-r-xl rounded-l-none shadow-subtle z-10': isEndDate(cell.dateString),

                                                // Middle in-range days
                                                'bg-brand-50/90 border-transparent text-brand-900 font-bold rounded-none z-10': isBetweenDates(cell.dateString),

                                                // Past days
                                                'bg-slate-50/70 border-slate-100 text-slate-300 cursor-not-allowed rounded-xl': cell.isPast,
                                                
                                                // Booked / Full days
                                                'bg-rose-50/80 border-rose-100 text-rose-300 cursor-not-allowed rounded-xl': cell.isBooked && !cell.isPast,

                                                // Normal Available Day
                                                'bg-white border-slate-200/80 text-slate-700 hover:border-brand-500 hover:bg-brand-50/50 hover:text-brand-800 shadow-subtle rounded-xl': !cell.isPast && !cell.isBooked && !isInRange(cell.dateString),
                                                'ring-2 ring-brand-600/30 border-brand-600': cell.isToday && !isInRange(cell.dateString)
                                            }">
                                        <span x-text="cell.day"></span>

                                        <!-- Booked dot / label -->
                                        <template x-if="cell.isBooked && !cell.isPast">
                                            <span class="text-[8px] sm:text-[9px] font-bold text-rose-400 leading-none mt-0.5">PENUH</span>
                                        </template>

                                        <!-- Today micro dot -->
                                        <template x-if="cell.isToday && !isInRange(cell.dateString)">
                                            <span class="w-1.5 h-1.5 rounded-full bg-brand-600 absolute bottom-1"></span>
                                        </template>
                                    </button>
                                </template>

                                <!-- Padding from adjacent months -->
                                <template x-if="!cell.isCurrentMonth">
                                    <div class="w-full aspect-square rounded-xl flex items-center justify-center text-xs text-slate-200 border border-transparent select-none">
                                        <span x-text="cell.day"></span>
                                    </div>
                                </template>
                            </div>
                        </template>
                    </div>

                    <!-- Hidden inputs to submit via standard POST -->
                    <input type="hidden" name="event_date" :value="selectedDate" required>
                    <input type="hidden" name="event_end_date" :value="selectedEndDate || selectedDate">
                    <input type="hidden" name="duration_days" :value="durationDays">

                    <!-- Selected Date Live Status Bar (Clean, Minimal, Informative) -->
                    <div class="pt-3 border-t border-slate-100 flex items-center justify-between text-xs">
                        <div class="flex items-center gap-2 min-w-0">
                            <span class="w-2.5 h-2.5 rounded-full shrink-0"
                                  :class="selectedDate ? (isDateBooked ? 'bg-rose-500' : 'bg-emerald-500') : 'bg-slate-300'"></span>
                            <div class="text-slate-600 truncate">
                                <template x-if="!selectedDate">
                                    <span>Pilih tanggal acara di kalender</span>
                                </template>
                                <template x-if="selectedDate && pickingRange && !selectedEndDate">
                                    <span>Mulai: <strong class="text-slate-900 font-bold" x-text="formatSingleDate(selectedDate)"></strong> • <span class="text-brand-600 font-medium">Pilih tanggal selesai</span></span>
                                </template>
                                <template x-if="selectedDate && !isDateBooked && (!pickingRange || selectedEndDate)">
                                    <span class="text-slate-900 font-semibold" x-text="formattedSelectedDate"></span>
                                </template>
                                <template x-if="selectedDate && isDateBooked">
                                    <span class="text-rose-600 font-semibold">Tanggal yang dipilih sudah penuh (PENUH)</span>
                                </template>
                            </div>
                        </div>

                        <template x-if="selectedDate && (!pickingRange || selectedEndDate)">
                            <div class="shrink-0 ml-2">
                                <template x-if="!isDateBooked">
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200/80">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                        <span>Tersedia</span>
                                    </span>
                                </template>
                                <template x-if="isDateBooked">
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-semibold bg-rose-50 text-rose-700 border border-rose-200/80">
                                        <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                                        <span>Penuh</span>
                                    </span>
                                </template>
                            </div>
                        </template>
                    </div>

                    <!-- Micro Info Jadwal Acara (Highlight Ringan) -->
                    <div class="p-3 sm:p-3.5 rounded-xl bg-[#F4F9F7] border border-[#DCEDE7] text-[11.5px] text-brand-900 flex items-start gap-2.5">
                        <svg class="w-4 h-4 text-brand-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <div class="leading-relaxed">
                            <span class="font-bold text-brand-800">Ketentuan Durasi Acara:</span>
                            <span class="text-slate-600"> Paket sewa standar mencakup <strong>2 hari acara</strong> (Loading tenda H-2, dekorasi H-1, bongkar H+1). Acara lebih dari 2 hari dikenakan charge 15%/hari.</span>
                        </div>
                    </div>

                    <!-- Step 1 Inline Error Alert -->
                    <div x-show="formErrors.step1" x-cloak x-transition
                         class="p-3.5 rounded-xl bg-rose-50 border border-rose-200 text-xs text-rose-700 flex items-center gap-2.5 shadow-subtle">
                        <svg class="w-4 h-4 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        <span class="font-medium" x-text="formErrors.step1"></span>
                    </div>

                    <!-- DESKTOP & TABLET INLINE ACTION FOOTER (RIGHT BENEATH THE CALENDAR!) -->
                    <div class="hidden md:flex items-center justify-end pt-3 border-t border-slate-100">
                        <button type="button" @click="goToStep(2)" :disabled="!selectedDate || isDateBooked"
                                class="px-6 py-3 rounded-xl bg-brand-600 hover:bg-brand-700 disabled:bg-slate-200 disabled:text-slate-400 active:scale-95 text-white font-bold text-xs tracking-wide transition flex items-center gap-2 cursor-pointer shadow-subtle">
                            <span>Lanjut ke Pilih Paket</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                        </button>
                    </div>

                </div>

            </div>

            <!-- ============================================================== -->
            <!-- STEP 2: WEDDING PACKAGE & ADD-ONS                              -->
            <!-- ============================================================== -->
            <div x-show="step === 2"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 translate-y-2"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 class="space-y-4 sm:space-y-5" x-cloak>

                <!-- Step Title -->
                <div class="space-y-1">
                    <span class="text-[10px] sm:text-[11px] font-bold uppercase tracking-[0.12em] text-brand-600">Langkah 2 dari 4 • Paket & Layanan</span>
                    <h1 class="text-xl sm:text-2xl font-extrabold tracking-tight text-slate-900 leading-tight">Pilih paket pernikahan Anda</h1>
                    <p class="text-xs sm:text-sm text-slate-500 leading-relaxed">
                        Ketuk paket untuk memilih. Anda juga dapat menambahkan perlengkapan tambahan di bawah.
                    </p>
                </div>

                <!-- Step 2 Inline Error Alert -->
                <div x-show="formErrors.step2" x-cloak x-transition
                     class="p-3.5 rounded-xl bg-rose-50 border border-rose-200 text-xs text-rose-700 flex items-center gap-2.5 shadow-subtle">
                    <svg class="w-4 h-4 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    <span class="font-medium" x-text="formErrors.step2"></span>
                </div>

                <!-- Package Cards -->
                <div class="space-y-3">
                    <template x-for="pkg in packages" :key="pkg.id">
                        <label class="block p-4 sm:p-5 rounded-2xl border transition select-none cursor-pointer relative active:scale-[0.99]"
                               :class="selectedPackageId == pkg.id 
                                   ? 'bg-white border-brand-600 ring-2 ring-brand-600/10 shadow-card' 
                                   : 'bg-white border-slate-200/90 hover:border-slate-300 shadow-subtle'">
                            
                            <input type="radio" name="package_id" :value="pkg.id" x-model="selectedPackageId" class="sr-only">

                            <div class="flex items-start justify-between gap-3">
                                <div class="space-y-1.5 flex-1 min-w-0">
                                    <div class="flex items-center gap-2">
                                        <div class="w-5 h-5 rounded-full border-2 flex items-center justify-center shrink-0 transition"
                                             :class="selectedPackageId == pkg.id ? 'border-brand-600 bg-brand-600' : 'border-slate-300 bg-white'">
                                            <span class="w-2 h-2 rounded-full bg-white" x-show="selectedPackageId == pkg.id"></span>
                                        </div>
                                        <h3 class="font-bold text-sm sm:text-base text-slate-900 tracking-tight truncate" x-text="pkg.name"></h3>
                                        <template x-if="pkg.slug === 'paket-1' || pkg.slug === 'tenda-decor-6m'">
                                            <span class="px-2 py-0.5 rounded-full bg-brand-50 text-brand-700 text-[9px] font-bold tracking-wide uppercase border border-brand-200/60 shrink-0">
                                                Favorit
                                            </span>
                                        </template>
                                    </div>
                                    <p class="text-xs text-slate-500 leading-relaxed pl-7" x-text="pkg.description"></p>
                                </div>

                                <div class="text-right shrink-0 pt-0.5">
                                    <span class="text-xs sm:text-base font-bold font-mono text-slate-900 tracking-tight"
                                          x-text="formatRupiah(pkg.base_price)"></span>
                                </div>
                            </div>
                        </label>
                    </template>
                </div>

                <!-- Add-ons Selection -->
                <div x-show="addons.length > 0" class="bg-white rounded-2xl border border-slate-200/90 p-4 sm:p-5 shadow-card space-y-3.5">
                    <div class="pb-3 border-b border-slate-100">
                        <h4 class="text-xs sm:text-sm font-bold text-slate-900 uppercase tracking-wider">Perlengkapan Tambahan (Opsional)</h4>
                        <p class="text-[11px] sm:text-xs text-slate-400 mt-0.5">Pilih perlengkapan tambahan sesuai kebutuhan venue Anda</p>
                    </div>

                    <!-- Category Filter Tabs for 20 Add-ons -->
                    <div class="flex items-center gap-1.5 overflow-x-auto pb-1 text-xs no-scrollbar">
                        <button type="button" @click="activeAddonCategory = 'all'"
                                class="px-3 py-1.5 rounded-xl font-bold transition whitespace-nowrap cursor-pointer active:scale-95"
                                :class="activeAddonCategory === 'all' ? 'bg-brand-600 text-white shadow-xs' : 'bg-slate-100 hover:bg-slate-200 text-slate-600'">
                            Semua
                        </button>
                        <button type="button" @click="activeAddonCategory = 'kursi_meja'"
                                class="px-3 py-1.5 rounded-xl font-bold transition whitespace-nowrap cursor-pointer active:scale-95"
                                :class="activeAddonCategory === 'kursi_meja' ? 'bg-brand-600 text-white shadow-xs' : 'bg-slate-100 hover:bg-slate-200 text-slate-600'">
                            Kursi & Meja
                        </button>
                        <button type="button" @click="activeAddonCategory = 'kipas'"
                                class="px-3 py-1.5 rounded-xl font-bold transition whitespace-nowrap cursor-pointer active:scale-95"
                                :class="activeAddonCategory === 'kipas' ? 'bg-brand-600 text-white shadow-xs' : 'bg-slate-100 hover:bg-slate-200 text-slate-600'">
                            Kipas & Blower
                        </button>
                        <button type="button" @click="activeAddonCategory = 'panggung'"
                                class="px-3 py-1.5 rounded-xl font-bold transition whitespace-nowrap cursor-pointer active:scale-95"
                                :class="activeAddonCategory === 'panggung' ? 'bg-brand-600 text-white shadow-xs' : 'bg-slate-100 hover:bg-slate-200 text-slate-600'">
                            Panggung & Lantai
                        </button>
                        <button type="button" @click="activeAddonCategory = 'sound'"
                                class="px-3 py-1.5 rounded-xl font-bold transition whitespace-nowrap cursor-pointer active:scale-95"
                                :class="activeAddonCategory === 'sound' ? 'bg-brand-600 text-white shadow-xs' : 'bg-slate-100 hover:bg-slate-200 text-slate-600'">
                            Sound & Genset
                        </button>
                        <button type="button" @click="activeAddonCategory = 'prasmanan'"
                                class="px-3 py-1.5 rounded-xl font-bold transition whitespace-nowrap cursor-pointer active:scale-95"
                                :class="activeAddonCategory === 'prasmanan' ? 'bg-brand-600 text-white shadow-xs' : 'bg-slate-100 hover:bg-slate-200 text-slate-600'">
                            Prasmanan
                        </button>
                        <button type="button" @click="activeAddonCategory = 'tenda'"
                                class="px-3 py-1.5 rounded-xl font-bold transition whitespace-nowrap cursor-pointer active:scale-95"
                                :class="activeAddonCategory === 'tenda' ? 'bg-brand-600 text-white shadow-xs' : 'bg-slate-100 hover:bg-slate-200 text-slate-600'">
                            Tenda & Lampu
                        </button>
                    </div>

                    <div class="space-y-2">
                        <template x-for="(addon, idx) in addons" :key="addon.id">
                            <div x-show="activeAddonCategory === 'all' || getAddonCategory(addon.name) === activeAddonCategory"
                                 class="p-3 sm:p-3.5 rounded-xl border transition flex items-center justify-between gap-3 text-xs"
                                 :class="addon.selectedQty > 0 
                                     ? 'bg-brand-50/40 border-brand-200/80 shadow-xs' 
                                     : 'bg-slate-50/40 hover:bg-slate-50/80 border-slate-200/60'">
                                
                                <div class="min-w-0 flex-1 space-y-1">
                                    <div class="flex items-center gap-2">
                                        <p class="font-bold text-slate-800 tracking-tight truncate text-xs sm:text-sm" x-text="addon.name"></p>
                                        <span x-show="addon.selectedQty > 0" x-cloak
                                              class="px-1.5 py-0.5 rounded-md bg-brand-600 text-white font-mono font-bold text-[10px] shrink-0">
                                            <span x-text="`${addon.selectedQty}x`"></span>
                                        </span>
                                    </div>
                                    <div class="flex items-center gap-2 text-[11px]">
                                        <span class="text-slate-500 font-mono font-medium"
                                               x-text="`${formatRupiah(addon.unit_price)} / ${addon.unit}`"></span>
                                        <template x-if="addon.selectedQty > 0">
                                            <span class="text-brand-700 font-mono font-bold">
                                                • Subtotal: <span x-text="formatRupiah(addon.selectedQty * addon.unit_price)"></span>
                                            </span>
                                        </template>
                                    </div>
                                </div>

                                <div class="flex items-center gap-1 shrink-0 bg-white rounded-xl border border-slate-200 p-1 shadow-subtle">
                                    <input type="hidden" :name="`addons[${idx}][id]`" :value="addon.id">
                                    
                                    <button type="button"
                                            @click="addon.selectedQty = Math.max(0, (parseInt(addon.selectedQty) || 0) - 1)"
                                            :disabled="!addon.selectedQty || addon.selectedQty <= 0"
                                            class="w-7 h-7 sm:w-8 sm:h-8 rounded-lg flex items-center justify-center transition"
                                            :class="!addon.selectedQty || addon.selectedQty <= 0 
                                                ? 'text-slate-300 cursor-not-allowed bg-transparent' 
                                                : 'text-slate-700 hover:bg-slate-100 active:scale-95 cursor-pointer bg-slate-50'">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M20 12H4"/></svg>
                                    </button>

                                    <input type="number" :name="`addons[${idx}][qty]`" min="0" max="1000"
                                           x-model.number="addon.selectedQty"
                                           class="w-8 sm:w-10 text-center bg-transparent font-mono font-extrabold text-xs sm:text-sm text-slate-900 focus:outline-none" placeholder="0">

                                    <button type="button"
                                            @click="addon.selectedQty = (parseInt(addon.selectedQty) || 0) + 1"
                                            class="w-7 h-7 sm:w-8 sm:h-8 rounded-lg flex items-center justify-center transition active:scale-95 cursor-pointer"
                                            :class="addon.selectedQty > 0 
                                                ? 'bg-brand-600 hover:bg-brand-700 text-white shadow-xs' 
                                                : 'bg-slate-100 hover:bg-brand-50 text-slate-700 hover:text-brand-700'">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                                    </button>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                <!-- DESKTOP & TABLET INLINE ACTION FOOTER (CARD CONTAINER) -->
                <div class="hidden md:flex items-center justify-between gap-4 bg-white rounded-2xl border border-slate-200/90 p-4 sm:p-5 shadow-card">
                    <button type="button" @click="goToStep(1)"
                            class="px-4 py-2.5 rounded-xl border border-slate-200 hover:bg-slate-50 text-slate-700 font-semibold text-xs transition flex items-center gap-1.5 cursor-pointer active:scale-95">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
                        <span>Kembali</span>
                    </button>
                    <div class="flex items-center gap-4 sm:gap-5">
                        <div class="text-right">
                            <span class="text-[10px] uppercase font-bold text-slate-400 tracking-wider block">Estimasi Total</span>
                            <span class="text-base sm:text-lg font-extrabold font-mono text-brand-700" x-text="formatRupiah(grandTotal)"></span>
                        </div>
                        <button type="button" @click="goToStep(3)"
                                class="px-6 py-2.5 sm:py-3 rounded-xl bg-brand-600 hover:bg-brand-700 active:scale-95 text-white font-bold text-xs tracking-wide transition flex items-center gap-2 cursor-pointer shadow-pill">
                            <span>Lanjut ke Data Pemesan</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                        </button>
                    </div>
                </div>

            </div>

            <!-- ============================================================== -->
            <!-- STEP 3: COUPLE INFORMATION                                     -->
            <!-- ============================================================== -->
            <div x-show="step === 3"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 translate-y-2"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 class="space-y-4 sm:space-y-5" x-cloak>

                <div class="space-y-1">
                    <span class="text-[10px] sm:text-[11px] font-bold uppercase tracking-[0.12em] text-brand-600">Langkah 3 dari 4 • Data Calon Pengantin</span>
                    <h1 class="text-xl sm:text-2xl font-extrabold tracking-tight text-slate-900 leading-tight">Data Calon Pengantin</h1>
                    <p class="text-xs sm:text-sm text-slate-500 leading-relaxed">
                        Masukkan nama kedua mempelai dan kontak WhatsApp untuk koordinasi dan surat perjanjian resmi.
                    </p>
                </div>

                <!-- Input Fields Card -->
                <div class="bg-white rounded-2xl border border-slate-200/90 p-4 sm:p-6 shadow-card space-y-4">
                    
                    <!-- Step 3 Inline Error Alert -->
                    <div x-show="formErrors.step3" x-cloak x-transition
                         class="p-3.5 rounded-xl bg-rose-50 border border-rose-200 text-xs text-rose-700 flex items-center gap-2.5 shadow-subtle">
                        <svg class="w-4 h-4 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        <span class="font-medium" x-text="formErrors.step3"></span>
                    </div>

                    <div class="space-y-1.5">
                        <label class="block text-xs font-semibold text-slate-700 tracking-tight">
                            Nama Calon Pengantin (Kedua Mempelai) <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" name="customer_name" required x-model="customerName"
                               @input="customerName = $event.target.value.toUpperCase(); formErrors.customer_name = null; formErrors.step3 = null"
                               autocapitalize="characters" autocomplete="name"
                               placeholder="Nama kedua mempelai (cth: Reza & Dian)"
                               class="w-full px-4 py-3 rounded-xl border text-base sm:text-sm uppercase placeholder:normal-case placeholder:text-slate-400 focus:outline-none transition"
                               :class="formErrors.customer_name 
                                   ? 'border-rose-400 ring-2 ring-rose-100 bg-rose-50/20 text-slate-900' 
                                   : 'border-slate-200 focus:border-brand-600 focus:ring-2 focus:ring-brand-600/15'">
                        <p x-show="formErrors.customer_name" x-cloak class="text-xs font-semibold text-rose-600 flex items-center gap-1.5 pt-0.5">
                            <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                            <span x-text="formErrors.customer_name"></span>
                        </p>
                        <p x-show="!formErrors.customer_name" class="text-[11px] text-slate-400">Nama kedua mempelai akan tercantum pada invoice dan surat perjanjian resmi.</p>
                    </div>

                    <div class="space-y-1.5">
                        <label class="block text-xs font-semibold text-slate-700 tracking-tight">
                            Nomor WhatsApp Aktif <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative flex items-center">
                            <span class="absolute left-4 text-xs font-bold text-slate-400 select-none">+62</span>
                            <input id="phoneInput" type="tel" required
                                   :value="phoneDisplay"
                                   @input="handlePhoneInput($event)"
                                   inputmode="numeric"
                                   placeholder="857-1234-5678"
                                   class="w-full pl-14 pr-4 py-3 rounded-xl border text-base sm:text-sm placeholder:text-slate-400 focus:outline-none transition font-mono"
                                   :class="formErrors.customer_phone 
                                       ? 'border-rose-400 ring-2 ring-rose-100 bg-rose-50/20 text-slate-900' 
                                       : 'border-slate-200 focus:border-brand-600 focus:ring-2 focus:ring-brand-600/15'">
                            <input type="hidden" name="customer_phone" :value="rawPhoneDigits ? ('0' + rawPhoneDigits) : ''">
                        </div>
                        <p x-show="formErrors.customer_phone" x-cloak class="text-xs font-semibold text-rose-600 flex items-center gap-1.5 pt-0.5">
                            <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                            <span x-text="formErrors.customer_phone"></span>
                        </p>
                        <p x-show="!formErrors.customer_phone" class="text-[11px] text-slate-400">Kwitansi pembayaran dan konfirmasi jadwal akan dikirimkan ke nomor ini.</p>
                    </div>

                    <div class="space-y-1.5">
                        <label class="block text-xs font-semibold text-slate-700 tracking-tight">
                            Alamat Email <span class="text-slate-400 font-normal">(Opsional)</span>
                        </label>
                        <input type="email" name="customer_email" x-model="customerEmail"
                               @input="customerEmail = $event.target.value.toLowerCase()"
                               inputmode="email" autocomplete="email"
                               autocapitalize="none" autocorrect="off" spellcheck="false"
                               placeholder="email@contoh.com"
                               class="w-full px-4 py-3 rounded-xl border border-slate-200 text-base sm:text-sm lowercase placeholder:normal-case placeholder:text-slate-400 focus:outline-none focus:border-brand-600 focus:ring-2 focus:ring-brand-600/15 transition">
                    </div>

                    <!-- Alamat Lengkap Lokasi Acara (Venue) -->
                    <div class="space-y-1.5 pt-1">
                        <label class="block text-xs font-semibold text-slate-700 tracking-tight">
                            Alamat Lengkap Lokasi Acara (Pemasangan Tenda) <span class="text-rose-500">*</span>
                        </label>
                        <textarea name="venue_address" required x-model="venueAddress"
                                  @input="formErrors.venue_address = null; formErrors.step3 = null"
                                  rows="2"
                                  placeholder="Jl. Nama Jalan, No. Rumah, RT/RW, Kelurahan, Kecamatan"
                                  class="w-full px-4 py-2.5 rounded-xl border text-base sm:text-sm placeholder:text-slate-400 focus:outline-none transition resize-none leading-relaxed"
                                  :class="formErrors.venue_address 
                                      ? 'border-rose-400 ring-2 ring-rose-100 bg-rose-50/20 text-slate-900' 
                                      : 'border-slate-200 focus:border-brand-600 focus:ring-2 focus:ring-brand-600/15'"></textarea>
                        <p x-show="formErrors.venue_address" x-cloak class="text-xs font-semibold text-rose-600 flex items-center gap-1.5 pt-0.5">
                            <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                            <span x-text="formErrors.venue_address"></span>
                        </p>
                        <p x-show="!formErrors.venue_address" class="text-[11px] text-slate-400">Digunakan oleh kru operasional untuk survey lokasi dan pengiriman muatan tenda.</p>
                    </div>

                    <!-- Jarak & Wilayah Pengiriman Muatan Tenda -->
                    <div class="space-y-2 pt-1">
                        <div class="flex items-center justify-between">
                            <label class="block text-xs font-semibold text-slate-700 tracking-tight">
                                Jarak Lokasi Acara dari Slawi <span class="text-rose-500">*</span>
                            </label>
                            <span class="text-[10px] text-slate-400 font-medium">
                                Pilih estimasi jarak tempuh
                            </span>
                        </div>

                        <input type="hidden" name="delivery_distance_km" :value="deliveryDistanceKm">

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-xs">
                            <!-- Tier 1: <= 10 km (FREE) -->
                            <label class="relative p-3 rounded-xl border transition cursor-pointer flex items-center justify-between gap-2.5"
                                   :class="selectedDistanceTier === '0_10' 
                                       ? 'border-brand-600 bg-brand-50/40 ring-1 ring-brand-600' 
                                       : 'border-slate-200 hover:border-slate-300 bg-white'">
                                <div class="flex items-center gap-2.5 min-w-0">
                                    <input type="radio" name="_distance_tier" value="0_10" 
                                           x-model="selectedDistanceTier"
                                           @change="selectDistanceTier('0_10', 0)"
                                           class="text-brand-600 focus:ring-brand-600 shrink-0">
                                    <div class="min-w-0">
                                        <p class="font-bold text-slate-900 leading-tight">&le; 10 km</p>
                                        <p class="text-[10px] text-slate-500 leading-tight mt-0.5 truncate">Slawi, Lebaksiu, Adiwerna, Pangkah</p>
                                    </div>
                                </div>
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-emerald-100 text-emerald-800 shrink-0">
                                    Gratis Ongkir
                                </span>
                            </label>

                            <!-- Tier 2: 11 - 20 km -->
                            <label class="relative p-3 rounded-xl border transition cursor-pointer flex items-center justify-between gap-2.5"
                                   :class="selectedDistanceTier === '11_20' 
                                       ? 'border-brand-600 bg-brand-50/40 ring-1 ring-brand-600' 
                                       : 'border-slate-200 hover:border-slate-300 bg-white'">
                                <div class="flex items-center gap-2.5 min-w-0">
                                    <input type="radio" name="_distance_tier" value="11_20" 
                                           x-model="selectedDistanceTier"
                                           @change="selectDistanceTier('11_20', 15)"
                                           class="text-brand-600 focus:ring-brand-600 shrink-0">
                                    <div class="min-w-0">
                                        <p class="font-bold text-slate-900 leading-tight">11 – 20 km</p>
                                        <p class="text-[10px] text-slate-500 leading-tight mt-0.5 truncate">Kota Tegal, Balapulang, Jatibarang</p>
                                    </div>
                                </div>
                            </label>

                            <!-- Tier 3: 21 - 30 km -->
                            <label class="relative p-3 rounded-xl border transition cursor-pointer flex items-center justify-between gap-2.5"
                                   :class="selectedDistanceTier === '21_30' 
                                       ? 'border-brand-600 bg-brand-50/40 ring-1 ring-brand-600' 
                                       : 'border-slate-200 hover:border-slate-300 bg-white'">
                                <div class="flex items-center gap-2.5 min-w-0">
                                    <input type="radio" name="_distance_tier" value="21_30" 
                                           x-model="selectedDistanceTier"
                                           @change="selectDistanceTier('21_30', 25)"
                                           class="text-brand-600 focus:ring-brand-600 shrink-0">
                                    <div class="min-w-0">
                                        <p class="font-bold text-slate-900 leading-tight">21 – 30 km</p>
                                        <p class="text-[10px] text-slate-500 leading-tight mt-0.5 truncate">Brebes Kota, Suradadi, Margasari</p>
                                    </div>
                                </div>
                            </label>

                            <!-- Tier 4: 31 - 40 km -->
                            <label class="relative p-3 rounded-xl border transition cursor-pointer flex items-center justify-between gap-2.5"
                                   :class="selectedDistanceTier === '31_40' 
                                       ? 'border-brand-600 bg-brand-50/40 ring-1 ring-brand-600' 
                                       : 'border-slate-200 hover:border-slate-300 bg-white'">
                                <div class="flex items-center gap-2.5 min-w-0">
                                    <input type="radio" name="_distance_tier" value="31_40" 
                                           x-model="selectedDistanceTier"
                                           @change="selectDistanceTier('31_40', 35)"
                                           class="text-brand-600 focus:ring-brand-600 shrink-0">
                                    <div class="min-w-0">
                                        <p class="font-bold text-slate-900 leading-tight">31 – 40 km</p>
                                        <p class="text-[10px] text-slate-500 leading-tight mt-0.5 truncate">Bumiayu, Pemalang Kota, Tanjung</p>
                                    </div>
                                </div>
                            </label>

                            <!-- Tier 5: > 40 km -->
                            <label class="relative p-3 rounded-xl border transition cursor-pointer flex items-center justify-between gap-2.5 sm:col-span-2"
                                   :class="selectedDistanceTier === 'gt_40' 
                                       ? 'border-brand-600 bg-brand-50/40 ring-1 ring-brand-600' 
                                       : 'border-slate-200 hover:border-slate-300 bg-white'">
                                <div class="flex items-center gap-2.5 min-w-0">
                                    <input type="radio" name="_distance_tier" value="gt_40" 
                                           x-model="selectedDistanceTier"
                                           @change="selectDistanceTier('gt_40', 45)"
                                           class="text-brand-600 focus:ring-brand-600 shrink-0">
                                    <div class="min-w-0">
                                        <p class="font-bold text-slate-900 leading-tight">&gt; 40 km</p>
                                        <p class="text-[10px] text-slate-500 leading-tight mt-0.5">Wilayah Lainnya / Luar Kota (Comal, Losari, Pekalongan, dll)</p>
                                    </div>
                                </div>
                            </label>
                        </div>

                        <!-- GPS Button & Status -->
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-1.5 pt-1 text-[11px]">
                            <button type="button" @click="detectGpsDistance()" 
                                    :disabled="gpsLoading"
                                    class="inline-flex items-center gap-1.5 text-brand-700 hover:text-brand-800 font-semibold cursor-pointer transition">
                                <template x-if="!gpsLoading">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                </template>
                                <template x-if="gpsLoading">
                                    <svg class="w-3.5 h-3.5 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                </template>
                                <span x-text="gpsLoading ? 'Mendeteksi titik...' : 'Cek Jarak Otomatis via Lokasi Anda'"></span>
                            </button>
                            <span class="text-slate-400" x-text="gpsStatus"></span>
                        </div>
                    </div>

                    <!-- Grid: Ancer-ancer Rumah & Jam Acara -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="space-y-1.5">
                            <label class="block text-xs font-semibold text-slate-700 tracking-tight">
                                Patokan / Ancer-ancer Rumah <span class="text-slate-400 font-normal">(Opsional)</span>
                            </label>
                            <input type="text" name="address_landmark" x-model="addressLandmark"
                                   placeholder="Dekat masjid, gapura, atau warna pagar"
                                   class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-base sm:text-sm placeholder:text-slate-400 focus:outline-none focus:border-brand-600 focus:ring-2 focus:ring-brand-600/15 transition">
                        </div>
                        <div class="space-y-1.5">
                            <label class="block text-xs font-semibold text-slate-700 tracking-tight">
                                Jam Acara <span class="text-slate-400 font-normal">(Opsional)</span>
                            </label>
                            <input type="text" name="event_time" x-model="eventTime"
                                   placeholder="08:00 - Selesai"
                                   class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-base sm:text-sm placeholder:text-slate-400 focus:outline-none focus:border-brand-600 focus:ring-2 focus:ring-brand-600/15 transition">
                        </div>
                    </div>

                    <!-- Grid: Request Tanggal Survey & Pilihan Waktu -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <input type="hidden" name="survey_requested" :value="surveyDate ? '1' : '0'">
                        <div class="space-y-1.5">
                            <label class="block text-xs font-semibold text-slate-700 tracking-tight">
                                Request Tanggal Survey Tempat <span class="text-slate-400 font-normal">(Opsional)</span>
                            </label>
                            <input type="date" name="survey_date" x-model="surveyDate"
                                   :min="minSurveyDate" :max="maxSurveyDate"
                                   class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-base sm:text-sm focus:outline-none focus:border-brand-600 focus:ring-2 focus:ring-brand-600/15 transition bg-white text-slate-800">
                        </div>
                        <div class="space-y-1.5">
                            <label class="block text-xs font-semibold text-slate-700 tracking-tight">
                                Waktu Survey <span class="text-slate-400 font-normal">(Opsional)</span>
                            </label>
                            <div class="relative">
                                <select name="survey_time_slot" x-model="surveyTimeSlot"
                                        class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-base sm:text-sm text-slate-700 focus:outline-none focus:border-brand-600 focus:ring-2 focus:ring-brand-600/15 transition bg-white appearance-none cursor-pointer">
                                    <option value="flexible">Fleksibel (Sesuai Janji Temu)</option>
                                    <option value="morning">Pagi (09:00 – 12:00 WIB)</option>
                                    <option value="afternoon">Siang – Sore (13:00 – 17:00 WIB)</option>
                                </select>
                                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3.5 text-slate-400">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Grid: Nama Orang Tua & Akun Instagram -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="space-y-1.5">
                            <label class="block text-xs font-semibold text-slate-700 tracking-tight">
                                Nama Orang Tua <span class="text-slate-400 font-normal">(Opsional)</span>
                            </label>
                            <input type="text" name="parents_name" x-model="parentsName"
                                   placeholder="Bpk. Rahmat & Ibu Siti"
                                   class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-base sm:text-sm placeholder:text-slate-400 focus:outline-none focus:border-brand-600 focus:ring-2 focus:ring-brand-600/15 transition">
                        </div>
                        <div class="space-y-1.5">
                            <label class="block text-xs font-semibold text-slate-700 tracking-tight">
                                Akun Instagram <span class="text-slate-400 font-normal">(Opsional)</span>
                            </label>
                            <input type="text" name="instagram_account" x-model="instagramAccount"
                                   placeholder="@akun_instagram"
                                   class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-base sm:text-sm placeholder:text-slate-400 focus:outline-none focus:border-brand-600 focus:ring-2 focus:ring-brand-600/15 transition">
                        </div>
                    </div>

                </div>

                <!-- Vendor Rekanan / Rujukan (Opsional) -->
                <div class="bg-white rounded-2xl border border-slate-200/90 p-4 sm:p-5 shadow-card space-y-2.5">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-xl bg-slate-100 text-slate-600 flex items-center justify-center font-bold text-xs shrink-0 border border-slate-200">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                        </div>
                        <div>
                            <h4 class="text-xs sm:text-sm font-bold text-slate-900 tracking-tight">Vendor <span class="text-slate-400 font-normal text-xs">(Opsional)</span></h4>
                            <!-- <p class="text-[11px] text-slate-400">Diisi jika pemesanan dikoordinasikan atau dipesan langsung oleh vendor rekanan.</p> -->
                        </div>
                    </div>
                    <input type="text" name="vendor_name" x-model="vendorName"
                           placeholder="Nama vendor rekanan (cth: MUA Lestari / WO Mas Dimas)"
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-base sm:text-sm placeholder:text-slate-400 focus:outline-none focus:border-brand-600 focus:ring-2 focus:ring-brand-600/15 transition">
                </div>

                <!-- Accordion Informasi Vendor Lainnya (Opsional) -->
                <div class="bg-white rounded-2xl border border-slate-200/90 p-4 sm:p-5 shadow-card" x-data="{ vendorsOpen: Boolean(vendorMua || vendorDecor || vendorPhotoVideo || vendorMcWo) }">
                    <button type="button" @click="vendorsOpen = !vendorsOpen"
                            class="w-full flex items-center justify-between text-left cursor-pointer select-none">
                        <div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-xl bg-slate-100 text-slate-600 flex items-center justify-center font-bold text-xs shrink-0 border border-slate-200">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                            </div>
                            <div>
                                <h4 class="text-xs sm:text-sm font-bold text-slate-900 tracking-tight">Rincian Vendor Acara Lainnya <span class="text-slate-400 font-normal text-xs">(Opsional)</span></h4>
                                <!-- <p class="text-[11px] text-slate-400">Bisa diisi jika sudah ada, atau dilewati jika belum ditentukan.</p> -->
                            </div>
                        </div>
                        <svg class="w-4 h-4 text-slate-400 transition-transform duration-200" :class="{ 'rotate-180': vendorsOpen }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/></svg>
                    </button>

                    <div x-show="vendorsOpen" x-cloak x-transition class="mt-4 pt-4 border-t border-slate-100 space-y-3.5">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                            <div class="space-y-1">
                                <label class="text-[11px] font-semibold text-slate-700">MUA / Tata Rias</label>
                                <input type="text" name="vendor_mua" x-model="vendorMua" placeholder="Nama MUA"
                                       class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs placeholder:text-slate-400 focus:outline-none focus:border-brand-600 focus:ring-1 focus:ring-brand-600 transition">
                            </div>
                            <div class="space-y-1">
                                <label class="text-[11px] font-semibold text-slate-700">Vendor Dekorasi</label>
                                <input type="text" name="vendor_decor" x-model="vendorDecor" placeholder="Nama Vendor Dekorasi (jika dari luar)"
                                       class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs placeholder:text-slate-400 focus:outline-none focus:border-brand-600 focus:ring-1 focus:ring-brand-600 transition">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                            <div class="space-y-1">
                                <label class="text-[11px] font-semibold text-slate-700">Fotografer / Videografer</label>
                                <input type="text" name="vendor_photo_video" x-model="vendorPhotoVideo" placeholder="Nama Vendor Dokumentasi"
                                       class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs placeholder:text-slate-400 focus:outline-none focus:border-brand-600 focus:ring-1 focus:ring-brand-600 transition">
                            </div>
                            <div class="space-y-1">
                                <label class="text-[11px] font-semibold text-slate-700">MC / Wedding Organizer Luar</label>
                                <input type="text" name="vendor_mc_wo" x-model="vendorMcWo" placeholder="Nama MC atau WO"
                                       class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs placeholder:text-slate-400 focus:outline-none focus:border-brand-600 focus:ring-1 focus:ring-brand-600 transition">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- DESKTOP & TABLET INLINE ACTION FOOTER (CARD CONTAINER) -->
                <div class="hidden md:flex items-center justify-between gap-4 bg-white rounded-2xl border border-slate-200/90 p-4 sm:p-5 shadow-card">
                    <button type="button" @click="goToStep(2)"
                            class="px-4 py-2.5 rounded-xl border border-slate-200 hover:bg-slate-50 text-slate-700 font-semibold text-xs transition flex items-center gap-1.5 cursor-pointer active:scale-95">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
                        <span>Kembali</span>
                    </button>
                    <button type="button" @click="goToStep(4)"
                            class="px-6 py-2.5 sm:py-3 rounded-xl bg-brand-600 hover:bg-brand-700 active:scale-95 text-white font-bold text-xs tracking-wide transition flex items-center gap-2 cursor-pointer shadow-pill">
                        <span>Lanjut ke Pembayaran</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </button>
                </div>

            </div>

            <!-- ============================================================== -->
            <!-- STEP 4: REVIEW, DOWN PAYMENT & RECEIPT UPLOAD                 -->
            <!-- ============================================================== -->
            <div x-show="step === 4"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 translate-y-2"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 class="space-y-4 sm:space-y-5" x-cloak>

                <div class="space-y-1">
                    <span class="text-[10px] sm:text-[11px] font-bold uppercase tracking-[0.12em] text-brand-600">Langkah 4 dari 4 • Konfirmasi & Pembayaran</span>
                    <h1 class="text-xl sm:text-2xl font-extrabold tracking-tight text-slate-900 leading-tight">Periksa & Kunci Reservasi Anda</h1>
                    <p class="text-xs sm:text-sm text-slate-500 leading-relaxed">
                        Pastikan rincian pesanan sudah sesuai dan unggah bukti transfer pembayaran.
                    </p>
                </div>

                <!-- Quotation Breakdown Table -->
                <div class="bg-white rounded-2xl border border-slate-200/90 p-4 sm:p-5 shadow-card space-y-3.5">
                    <div class="flex items-center justify-between gap-2 pb-3 border-b border-slate-100">
                        <h3 class="text-[11px] sm:text-xs font-bold text-slate-900 uppercase tracking-wider shrink-0">Rincian Reservasi</h3>
                        <span class="inline-flex items-center text-[10.5px] sm:text-xs font-bold text-brand-800 bg-brand-50 px-2.5 py-1 rounded-lg border border-brand-200/70 whitespace-nowrap shrink-0"
                              x-text="formattedSelectedDate"></span>
                    </div>

                    <div class="space-y-2.5 text-xs">
                        <div class="flex justify-between items-center py-0.5 text-slate-600">
                            <span class="truncate pr-2">Paket: <strong class="text-slate-900 font-bold" x-text="currentPackageName"></strong></span>
                            <span class="font-mono font-bold text-slate-900 whitespace-nowrap shrink-0" x-text="formatRupiah(currentPackagePrice)"></span>
                        </div>

                        <template x-for="ad in activeAddons" :key="ad.id">
                            <div class="flex justify-between items-center text-slate-600 pl-2.5 py-1 border-l-2 border-brand-200 bg-slate-50/60 rounded-r-lg">
                                <span class="truncate pr-2 font-medium" x-text="`${ad.name} (${ad.selectedQty}x)`"></span>
                                <span class="font-mono font-semibold text-slate-800 whitespace-nowrap shrink-0" x-text="formatRupiah(ad.selectedQty * ad.unit_price)"></span>
                            </div>
                        </template>

                        <!-- Extra Day Charge if > 2 days -->
                        <template x-if="extraDays > 0">
                            <div class="flex items-center justify-between gap-3 text-amber-900 bg-amber-50/90 px-3 py-2.5 rounded-xl border border-amber-200/90 shadow-2xs">
                                <div class="flex items-center gap-2.5 min-w-0">
                                    <div class="w-6 h-6 rounded-lg bg-amber-100 flex items-center justify-center shrink-0 text-amber-700">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    </div>
                                    <div class="min-w-0">
                                        <div class="text-xs font-bold text-amber-950 leading-tight">Tambahan Durasi</div>
                                        <div class="text-[10px] text-amber-700 font-medium leading-tight mt-0.5" x-text="`${extraDays} hari ekstra • +15%/hari`"></div>
                                    </div>
                                </div>
                                <span class="font-mono font-extrabold text-xs sm:text-sm text-amber-900 whitespace-nowrap shrink-0" x-text="`+ ${formatRupiah(additionalDayCharge)}`"></span>
                            </div>
                        </template>

                        <!-- Biaya Transport Muatan Tenda -->
                        <template x-if="transportFee > 0">
                            <div class="flex items-center justify-between gap-3 text-slate-700 bg-slate-50 px-3 py-2.5 rounded-xl border border-slate-200/90 shadow-2xs">
                                <div class="flex items-center gap-2.5 min-w-0">
                                    <div class="w-6 h-6 rounded-lg bg-teal-50 flex items-center justify-center shrink-0 text-brand-700">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"/></svg>
                                    </div>
                                    <div class="min-w-0">
                                        <div class="text-xs font-bold text-slate-900 leading-tight">Biaya Transport Muatan Tenda</div>
                                        <div class="text-[10px] text-slate-500 font-medium leading-tight mt-0.5" x-text="`Estimasi jarak tempuh: ${deliveryDistanceKm} km`"></div>
                                    </div>
                                </div>
                                <span class="font-mono font-bold text-xs sm:text-sm text-slate-900 whitespace-nowrap shrink-0" x-text="`+ ${formatRupiah(transportFee)}`"></span>
                            </div>
                        </template>

                        <!-- Ringkasan Request Survey Lokasi (Jika Diisi) -->
                        <template x-if="surveyDate">
                            <div class="flex items-center justify-between gap-3 text-slate-700 bg-slate-50 px-3.5 py-2.5 rounded-xl border border-slate-200/90 shadow-2xs">
                                <div class="flex items-center gap-2.5 min-w-0">
                                    <div class="w-6 h-6 rounded-lg bg-emerald-50 text-brand-700 flex items-center justify-center shrink-0 border border-brand-200/60">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                    </div>
                                    <div class="min-w-0">
                                        <div class="text-xs font-semibold text-slate-800 leading-tight">Request Survey Lokasi Tempat</div>
                                        <div class="text-[10px] text-slate-500 font-medium leading-tight mt-0.5" x-text="`Rencana: ${surveyDate} (${surveyTimeSlot === 'morning' ? 'Pagi (09:00 - 12:00)' : (surveyTimeSlot === 'afternoon' ? 'Siang - Sore (13:00 - 17:00)' : 'Fleksibel')})`"></div>
                                    </div>
                                </div>
                                <span class="text-[10px] font-semibold text-brand-700 bg-emerald-50 border border-brand-200/60 px-2 py-0.5 rounded-full whitespace-nowrap shrink-0">Gratis</span>
                            </div>
                        </template>

                        <div class="pt-3 border-t border-slate-200/90 flex justify-between items-center">
                            <div>
                                <span class="text-xs font-bold text-slate-800 uppercase tracking-wider block">Total Tagihan</span>
                                <span class="text-[10px] text-slate-400">Termasuk seluruh layanan & fasilitas sewa</span>
                            </div>
                            <span class="text-base sm:text-lg font-mono font-black text-brand-700 tracking-tight" x-text="formatRupiah(grandTotal)"></span>
                        </div>
                    </div>
                </div>

                <!-- Payment Options -->
                <div class="bg-white rounded-2xl border border-slate-200/90 p-4 sm:p-5 shadow-card space-y-4">
                    <h3 class="text-xs font-bold text-slate-900 uppercase tracking-wider">Metode Pembayaran</h3>

                    <!-- Toggle DP vs Lunas -->
                    <div class="grid grid-cols-2 gap-2.5">
                        <label class="p-3 rounded-xl border text-center cursor-pointer transition select-none active:scale-[0.98]"
                               :class="paymentType === 'dp' 
                                   ? 'border-brand-600 bg-brand-50/50 text-brand-900 font-semibold ring-1 ring-brand-600/20' 
                                   : 'border-slate-200 hover:border-slate-300 text-slate-700'">
                            <input type="radio" name="payment_type" value="dp" class="sr-only" x-model="paymentType">
                            <span class="text-xs block font-bold">Uang Muka (DP)</span>
                            <span class="text-[10px] text-slate-500 mt-0.5 block">Kunci tanggal dengan DP minimal</span>
                        </label>

                        <label class="p-3 rounded-xl border text-center cursor-pointer transition select-none active:scale-[0.98]"
                               :class="paymentType === 'lunas' 
                                   ? 'border-brand-600 bg-brand-50/50 text-brand-900 font-semibold ring-1 ring-brand-600/20' 
                                   : 'border-slate-200 hover:border-slate-300 text-slate-700'">
                            <input type="radio" name="payment_type" value="lunas" class="sr-only" x-model="paymentType">
                            <span class="text-xs block font-bold">Pelunasan (100%)</span>
                            <span class="text-[10px] text-slate-500 mt-0.5 block">Bayar lunas seluruh tagihan</span>
                        </label>
                    </div>

                    <!-- DP Amount Input -->
                    <div x-show="paymentType === 'dp'" class="p-3.5 rounded-xl bg-slate-50 border border-slate-200 space-y-2.5" x-cloak>
                        <div class="flex items-center justify-between text-xs">
                            <label class="font-semibold text-slate-700">Nominal Transfer DP</label>
                            <span class="text-slate-500 text-[11px]">Min: <span class="font-mono font-bold text-slate-800" x-text="formatRupiah(minDpAmount)"></span></span>
                        </div>
                        <div class="relative flex items-center">
                            <span class="absolute left-4 text-xs font-bold text-slate-400 select-none">Rp</span>
                            <input id="nominalDpInput" type="text" inputmode="numeric"
                                   :value="nominalDpDisplay"
                                   @input="handleNominalDpInput($event)"
                                   placeholder="1.000.000"
                                   class="w-full pl-12 pr-4 py-2.5 rounded-xl border border-slate-200 bg-white text-base sm:text-sm font-mono font-bold text-slate-900 focus:outline-none focus:border-brand-600 focus:ring-2 focus:ring-brand-600/15 transition">
                            <input type="hidden" name="nominal_dp" :value="nominalDp">
                        </div>

                        <!-- Quick DP Preset Pills -->
                        <div class="flex items-center gap-2 pt-0.5">
                            <button type="button" @click="setNominalDp(minDpAmount)"
                                     class="px-2.5 py-1 rounded-lg text-[11px] font-semibold transition border border-slate-200 hover:bg-white text-slate-600 active:scale-95 cursor-pointer"
                                     :class="nominalDp === minDpAmount ? 'bg-brand-50 border-brand-300 text-brand-800 font-bold ring-1 ring-brand-500/20' : 'bg-white/60'">
                                <span x-text="`Min DP (${formatRupiah(minDpAmount)})`">Min DP (Rp 1.000.000)</span>
                            </button>
                            <button type="button" @click="setNominalDp(Math.round(grandTotal * 0.5))"
                                     class="px-2.5 py-1 rounded-lg text-[11px] font-semibold transition border border-slate-200 hover:bg-white text-slate-600 active:scale-95 cursor-pointer"
                                     :class="nominalDp === Math.round(grandTotal * 0.5) ? 'bg-brand-50 border-brand-300 text-brand-800 font-bold ring-1 ring-brand-500/20' : 'bg-white/60'">
                                50% DP
                            </button>
                        </div>

                        <div class="flex justify-between items-center text-xs text-slate-500 pt-1.5 border-t border-slate-200/60">
                            <span>Sisa tagihan yang harus dibayar:</span>
                            <strong class="font-mono font-bold text-slate-900" x-text="formatRupiah(remainingBalance)"></strong>
                        </div>
                    </div>

                    <!-- Step 4 Inline Error Alert -->
                    <div x-show="formErrors.step4" x-cloak x-transition
                         class="p-3.5 rounded-xl bg-rose-50 border border-rose-200 text-xs text-rose-700 flex items-center gap-2.5 shadow-subtle">
                        <svg class="w-4 h-4 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        <span class="font-medium" x-text="formErrors.step4"></span>
                    </div>

                    <!-- Destination Bank Section (Authentic ATM / Debit Card UI) -->
                    <div class="space-y-3">
                        <div class="flex items-center justify-between gap-2">
                            <span class="text-xs font-bold text-slate-700 uppercase tracking-wider">
                                Rekening Resmi Transfer
                            </span>
                            <!-- Bank Switcher Tabs -->
                            <div class="inline-flex p-1 bg-slate-100 rounded-xl border border-slate-200 shadow-subtle">
                                <button type="button" @click="activeBank = 'bca'"
                                        class="px-3.5 py-1.5 rounded-lg text-xs font-bold transition flex items-center gap-1.5 cursor-pointer active:scale-95"
                                        :class="activeBank === 'bca' 
                                            ? 'bg-blue-700 text-white shadow-xs' 
                                            : 'text-slate-600 hover:text-slate-900'">
                                    <span class="w-2 h-2 rounded-full" :class="activeBank === 'bca' ? 'bg-white' : 'bg-blue-600'"></span>
                                    <span>Bank BCA</span>
                                </button>
                                <button type="button" @click="activeBank = 'bri'"
                                        class="px-3.5 py-1.5 rounded-lg text-xs font-bold transition flex items-center gap-1.5 cursor-pointer active:scale-95"
                                        :class="activeBank === 'bri' 
                                            ? 'bg-[#00529C] text-white shadow-xs' 
                                            : 'text-slate-600 hover:text-slate-900'">
                                    <span class="w-2 h-2 rounded-full" :class="activeBank === 'bri' ? 'bg-white' : 'bg-sky-600'"></span>
                                    <span>Bank BRI</span>
                                </button>
                            </div>
                        </div>

                        <!-- Realistic ATM / Debit Bank Card -->
                        <div class="w-full rounded-2xl sm:rounded-3xl p-5 sm:p-6 text-white relative overflow-hidden shadow-xl transition-all duration-300 min-h-[210px] flex flex-col justify-between select-none"
                             :class="activeBank === 'bca' 
                                 ? 'bg-gradient-to-br from-[#003B73] via-[#00519E] to-[#001D38] border border-blue-400/30' 
                                 : 'bg-gradient-to-br from-[#00529C] via-[#00396B] to-[#01182D] border border-sky-400/30'">
                            
                            <!-- Ambient Decorative Elements (Bank Holographic Waves) -->
                            <div class="absolute -right-10 -bottom-10 w-48 h-48 rounded-full bg-white/5 blur-2xl pointer-events-none"></div>
                            <div class="absolute -left-10 -top-10 w-40 h-40 rounded-full bg-cyan-400/10 blur-xl pointer-events-none"></div>
                            <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_top_right,_var(--tw-gradient-stops))] from-white/10 via-transparent to-transparent pointer-events-none"></div>

                            <!-- Card Top Bar: Chip, Contactless & Bank Brand Logo -->
                            <div class="flex items-center justify-between relative z-10">
                                <div class="flex items-center gap-3">
                                    <!-- Metallic Gold EMV Chip -->
                                    <div class="w-11 h-8 rounded-md bg-gradient-to-br from-amber-200 via-amber-300 to-amber-500 shadow border border-amber-400/80 p-0.5 relative overflow-hidden shrink-0">
                                        <div class="w-full h-full border border-amber-700/30 rounded-[3px] grid grid-cols-2 gap-0.5 p-0.5 opacity-80">
                                            <div class="border-r border-b border-amber-700/40 rounded-tl"></div>
                                            <div class="border-b border-amber-700/40 rounded-tr"></div>
                                            <div class="border-r border-amber-700/40 rounded-bl"></div>
                                            <div class="rounded-br"></div>
                                        </div>
                                    </div>
                                    <!-- Contactless NFC Wave Icon -->
                                    <svg class="w-5 h-5 text-white/70 rotate-90" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M8.111 16.404a5.5 5.5 0 017.778 0M12 20h.01m-7.08-7.071c3.904-3.905 10.236-3.905 14.141 0M1.394 9.393c5.857-5.857 15.355-5.857 21.213 0"/>
                                    </svg>
                                </div>

                                <!-- Bank Brand Logo Badge -->
                                <div class="flex items-center gap-2 bg-white/10 backdrop-blur-md px-3.5 py-1.5 rounded-xl border border-white/20 shadow-xs">
                                    <span class="font-extrabold tracking-tight text-base text-white"
                                          x-text="activeBank === 'bca' ? 'BCA' : 'BRI'"></span>
                                    <span class="text-[9px] font-mono tracking-widest text-white/80 uppercase px-1.5 py-0.5 rounded bg-white/15"
                                          x-text="activeBank === 'bca' ? 'PASPOR' : 'BRITAMA'"></span>
                                </div>
                            </div>

                            <!-- Card Middle: Embossed Account Number & Copy Button -->
                            <div class="my-4 relative z-10 space-y-1">
                                <div class="flex items-center justify-between">
                                    <span class="text-[9px] uppercase font-bold tracking-widest text-white/60">Nomor Rekening Resmi</span>
                                    <span class="text-[10px] font-medium text-white/70" x-text="activeBank === 'bca' ? 'BCA Virtual / Kantor Cabang' : 'BRI Kantor Unit / Cabang'"></span>
                                </div>
                                <div class="flex items-center justify-between gap-3 pt-0.5">
                                    <p class="font-mono text-xl sm:text-2xl md:text-[26px] font-extrabold tracking-widest text-white drop-shadow-md select-all"
                                       x-text="banks[activeBank].formatted"></p>
                                    <button type="button" @click="copyBankAccount()"
                                            class="px-3.5 py-1.5 rounded-xl bg-white/20 hover:bg-white/30 active:scale-95 text-xs text-white transition font-bold flex items-center gap-1.5 border border-white/30 shadow-sm cursor-pointer backdrop-blur-md shrink-0">
                                        <svg x-show="!copiedBank" class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                                        <span x-text="copiedBank ? 'Tersalin!' : 'Salin'"></span>
                                    </button>
                                </div>
                            </div>

                            <!-- Card Bottom: Cardholder Name & GPN / Debit Badge -->
                            <div class="pt-3 border-t border-white/15 flex items-center justify-between text-xs relative z-10">
                                <div>
                                    <span class="text-[8px] uppercase font-bold tracking-wider text-white/50 block">Atas Nama Pemilik Rekening</span>
                                    <p class="font-mono font-bold tracking-wider text-white text-xs sm:text-sm uppercase" x-text="banks[activeBank].holder"></p>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="px-2 py-0.5 rounded bg-white/15 font-mono text-[9px] font-bold tracking-wider text-white/90 uppercase border border-white/20">
                                        DEBIT
                                    </span>
                                    <span class="px-2 py-0.5 rounded bg-white text-slate-900 font-extrabold text-[9px] tracking-wider uppercase shadow-xs">
                                        GPN
                                    </span>
                                </div>
                            </div>
                        </div>

                        <p class="text-[11px] text-slate-500 text-center leading-relaxed">
                            Silakan lakukan transfer ke rekening <strong class="text-slate-800" x-text="banks[activeBank].name"></strong> di atas a.n. <strong class="text-slate-800" x-text="banks[activeBank].holder"></strong>, lalu unggah bukti transfer di bawah.
                        </p>
                    </div>

                    <!-- Payment Proof Upload Dropzone -->
                    <div class="space-y-1.5">
                        <label class="block text-xs font-semibold text-slate-700 tracking-tight">
                            Unggah Bukti Transfer <span class="text-rose-500">*</span>
                        </label>
                        
                        <div class="border-2 border-dashed rounded-2xl p-5 text-center transition cursor-pointer relative group"
                             :class="formErrors.payment_proof 
                                 ? 'border-rose-400 bg-rose-50/40' 
                                 : 'border-slate-200 hover:border-brand-600 bg-slate-50/70 active:bg-slate-100'">
                            <input type="file" name="payment_proof" required accept="image/*,application/pdf"
                                   @change="handleFileUpload($event)"
                                   class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                            
                            <div class="space-y-1.5">
                                <div class="w-10 h-10 rounded-full bg-slate-100 group-hover:bg-brand-50 text-slate-500 group-hover:text-brand-600 mx-auto flex items-center justify-center transition">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                                </div>
                                <p class="text-xs font-semibold text-slate-800">
                                    <span class="text-brand-600 underline">Ketuk untuk foto atau unggah berkas</span>
                                </p>
                                <p class="text-[11px] text-slate-400">JPG, PNG, atau PDF maksimal 3MB</p>
                            </div>

                            <template x-if="previewUrl">
                                <div class="mt-3 pt-3 border-t border-slate-200">
                                    <img :src="previewUrl" alt="Bukti transfer" class="max-h-28 mx-auto rounded-lg border border-slate-200 object-cover shadow-sm">
                                </div>
                            </template>
                            <template x-if="!previewUrl && fileName">
                                <p class="text-xs font-mono font-semibold text-slate-800 mt-2" x-text="fileName"></p>
                            </template>
                        </div>
                        <p x-show="formErrors.payment_proof" x-cloak class="text-xs font-semibold text-rose-600 flex items-center gap-1.5 pt-0.5">
                            <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                            <span x-text="formErrors.payment_proof"></span>
                        </p>
                    </div>

                </div>

                <!-- Unified Booking Rules & Consent Card -->
                <div class="bg-white rounded-2xl border border-slate-200/90 shadow-card divide-y divide-slate-100 overflow-hidden" x-data="{ rulesOpen: false }">
                    <!-- Rules Accordion Header -->
                    <button type="button" @click="rulesOpen = !rulesOpen"
                            class="w-full p-4 sm:p-5 flex items-center justify-between text-left cursor-pointer select-none hover:bg-slate-50/60 transition">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-xl bg-amber-50 text-amber-700 flex items-center justify-center font-bold text-xs shrink-0 border border-amber-200/60">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            </div>
                            <div>
                                <h4 class="text-xs sm:text-sm font-bold text-slate-900 tracking-tight">Aturan & Ketentuan Reservasi Resmi</h4>
                                <p class="text-[11px] text-slate-400">Ringkasan S&K resmi NK Barokah 2026</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-1.5 text-xs font-semibold text-brand-600">
                            <span class="text-[11px] hidden sm:inline" x-text="rulesOpen ? 'Tutup' : 'Lihat'"></span>
                            <svg class="w-4 h-4 text-slate-400 transition-transform duration-200" :class="{ 'rotate-180': rulesOpen }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/></svg>
                        </div>
                    </button>

                    <!-- Rules Content (Expandable) -->
                    <div x-show="rulesOpen" x-cloak x-transition class="p-4 sm:p-5 bg-slate-50/50 text-xs text-slate-600 space-y-2">
                        <ul class="list-disc pl-4 space-y-1.5 leading-relaxed text-[11px] sm:text-xs text-slate-600">
                            <li><strong>Keep Tanggal:</strong> Wajib DP minimal Rp 1.000.000 via transfer atau langsung ke galeri (sertakan bukti transfer).</li>
                            <li><strong>Durasi Acara:</strong> Pemakaian paket tenda berlaku maksimal <strong>2 hari acara</strong> (Loading tenda H-2, loading dekor H-1 sore/malam, bongkar H+1). Acara lebih dari 2 hari dikenakan <strong>charge 15% per hari</strong>.</li>
                            <li><strong>Survey Lokasi:</strong> Survey lokasi dilakukan setelah pembayaran DP. Biaya transport diinfokan jika ada (belum termasuk transport vendor).</li>
                            <li><strong>Penataan:</strong> Penataan meja & kursi dilakukan oleh pihak klien agar memahami tata letak yang diinginkan. Penataan alat prasmanan & karpet dilakukan oleh crew kami.</li>
                            <li><strong>Kebijakan Pembatalan:</strong> Pembatalan sepihak DP hangus. Reschedule dapat dilakukan apabila tanggal pengganti masih tersedia (available).</li>
                            <li><strong>Kelistrikan:</strong> Disediakan & ditangani crew untuk seputar tenda; diluar area tenda dikenakan Rp 100.000/titik. Saluran listrik dari klien.</li>
                            <li><strong>Alat Prasmanan:</strong> Pengembalian alat prasmanan dalam kondisi bersih & dicuci.</li>
                        </ul>
                    </div>

                    <!-- Consent Checkbox Row -->
                    <label id="termsConsentCheckbox"
                           class="flex items-center gap-3 p-4 sm:p-4.5 cursor-pointer transition select-none group"
                           :class="agreedToTerms ? 'bg-brand-50/40 text-brand-900' : 'bg-white hover:bg-slate-50/60 text-slate-700'">
                        <input type="checkbox" x-model="agreedToTerms" required
                               class="w-4 h-4 rounded text-brand-600 focus:ring-brand-500 border-slate-300 cursor-pointer shrink-0">
                        <span class="text-xs leading-snug">
                            Saya telah membaca dan menyetujui seluruh <strong class="text-slate-900 group-hover:text-brand-700 transition">Aturan & Ketentuan Reservasi Resmi</strong> di atas.
                        </span>
                    </label>
                </div>

                <!-- DESKTOP & TABLET INLINE ACTION FOOTER (CARD CONTAINER) -->
                <div class="hidden md:flex items-center justify-between gap-4 bg-white rounded-2xl border border-slate-200/90 p-4 sm:p-5 shadow-card">
                    <button type="button" @click="goToStep(3)"
                            class="px-4 py-2.5 rounded-xl border border-slate-200 hover:bg-slate-50 text-slate-700 font-semibold text-xs transition flex items-center gap-1.5 cursor-pointer active:scale-95">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
                        <span>Kembali</span>
                    </button>
                    <button type="button" @click="submitBookingForm()" :disabled="isDateBooked || !agreedToTerms"
                            class="px-7 py-3 rounded-xl bg-brand-600 hover:bg-brand-700 disabled:bg-slate-300 active:scale-95 text-white font-bold text-xs tracking-wide transition flex items-center gap-2 cursor-pointer shadow-pill">
                        <span>Konfirmasi Reservasi</span>
                    </button>
                </div>

            </div>

        </form>

    </main>

    <!-- ============================================================== -->
    <!-- STICKY BOTTOM DOCK (MOBILE-ONLY THUMB NAVIGATION < 768px)      -->
    <!-- ============================================================== -->
    <div class="block md:hidden fixed bottom-0 inset-x-0 z-50 bg-white/95 backdrop-blur-md border-t border-slate-200/90 shadow-dock pb-safe">
        <div class="max-w-2xl mx-auto px-4 py-3 flex items-center justify-between gap-3">
            
            <!-- Left Info / Running Total Indicator -->
            <div class="min-w-0 flex-1">
                <div x-show="step === 1">
                    <p class="text-[10px] uppercase font-bold text-slate-400 tracking-wider">Status Tanggal</p>
                    <p class="text-xs font-bold text-slate-900 truncate"
                       x-text="selectedDate ? formattedSelectedDate : 'Belum pilih tanggal'"></p>
                </div>

                <div x-show="step > 1" x-cloak>
                    <p class="text-[10px] uppercase font-bold text-slate-400 tracking-wider">Total Tagihan</p>
                    <p class="text-sm font-bold font-mono text-brand-700 tracking-tight"
                       x-text="formatRupiah(grandTotal)"></p>
                </div>
            </div>

            <!-- Right Buttons Group -->
            <div class="flex items-center gap-2 shrink-0">
                <button type="button" x-show="step > 1" @click="step--"
                        class="px-3.5 py-2.5 rounded-xl border border-slate-200 hover:bg-slate-50 active:scale-95 text-slate-700 font-semibold text-xs transition cursor-pointer">
                    Kembali
                </button>

                <button type="button" x-show="step < 4" @click="goToStep(step + 1)"
                        :disabled="step === 1 && (!selectedDate || isDateBooked)"
                        class="px-5 py-2.5 rounded-xl bg-brand-600 hover:bg-brand-700 disabled:bg-slate-200 disabled:text-slate-400 active:scale-95 text-white font-bold text-xs tracking-wide transition flex items-center gap-1.5 cursor-pointer shadow-subtle">
                    <span x-text="step === 1 ? 'Pilih Paket' : (step === 2 ? 'Data Pemesan' : 'Pembayaran')"></span>
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                </button>

                <button type="button" x-show="step === 4" @click="submitBookingForm()"
                        :disabled="isDateBooked || !agreedToTerms"
                        class="px-5 py-2.5 rounded-xl bg-brand-600 hover:bg-brand-700 disabled:bg-slate-300 active:scale-95 text-white font-bold text-xs tracking-wide transition flex items-center gap-1.5 cursor-pointer shadow-pill" x-cloak>
                    <span>Konfirmasi Reservasi</span>
                </button>
            </div>

        </div>
    </div>

    <!-- Minimal Clean Footer -->
    <footer class="border-t border-slate-200 bg-white pt-3.5 pb-20 md:py-3.5 text-center text-xs text-slate-400">
        <div class="max-w-2xl mx-auto px-4 flex flex-col sm:flex-row items-center justify-between gap-1 text-[11px]">
            <p>&copy; {{ date('Y') }} NK Barokah Wedding Organizer. Hak cipta dilindungi.</p>
            <p class="text-slate-400">Sistem reservasi & penguncian jadwal resmi</p>
        </div>
    </footer>

    <!-- Alpine.js Reservation App Logic -->
    <script>
    function bookingReservation(config) {
        return {
            step: 1,
            packages: config.packages,
            addons: config.addons.map(a => ({ ...a, selectedQty: 0 })),
            bookedDates: config.bookedDates,
            minDpAmount: config.minDpAmount,
            standardDays: config.standardDays || 2,
            extraDayRate: config.extraDayRate || 0.15,
            warehouseLat: config.warehouseLat || -6.9857806,
            warehouseLng: config.warehouseLng || 109.136971,
            warehouseAddress: config.warehouseAddress || 'Jl. K.H. Ahmad Dahlan, RT 04 / RW 01, Slawi Kulon, Kec. Slawi, Kab. Tegal',
            freeDistanceKm: config.freeDistanceKm || 10,
            distanceTierKm: config.distanceTierKm || 10,
            transportFeePerTier: config.transportFeePerTier || 300000,
            deliveryDistanceKm: config.oldDistanceKm || 0,
            selectedDistanceTier: (config.oldDistanceKm > 40 ? 'gt_40' : (config.oldDistanceKm > 30 ? '31_40' : (config.oldDistanceKm > 20 ? '21_30' : (config.oldDistanceKm > 10 ? '11_20' : '0_10')))),
            gpsLoading: false,
            gpsStatus: '',

            // Form inputs
            customerName: '{{ old("customer_name", "") }}'.toUpperCase(),
            phoneDisplay: '',
            rawPhoneDigits: '',
            customerEmail: '{{ old("customer_email", "") }}'.toLowerCase(),
            venueAddress: '{{ old("venue_address", "") }}',
            addressLandmark: '{{ old("address_landmark", "") }}',
            parentsName: '{{ old("parents_name", "") }}',
            instagramAccount: '{{ old("instagram_account", "") }}',
            eventTime: '{{ old("event_time", "") }}',
            vendorName: '{{ old("vendor_name", "") }}',
            vendorMua: '{{ old("vendor_mua", "") }}',
            vendorDecor: '{{ old("vendor_decor", "") }}',
            vendorPhotoVideo: '{{ old("vendor_photo_video", "") }}',
            vendorMcWo: '{{ old("vendor_mc_wo", "") }}',
            surveyRequested: {{ old('survey_requested') ? 'true' : 'false' }},
            surveyDate: '{{ old("survey_date", "") }}',
            surveyTimeSlot: '{{ old("survey_time_slot", "morning") }}',
            surveyNotes: '{{ old("survey_notes", "") }}',
            get minSurveyDate() {
                const d = new Date();
                d.setDate(d.getDate() + 1);
                return d.toISOString().split('T')[0];
            },
            get maxSurveyDate() {
                if (!this.selectedDate) return '';
                const d = new Date(this.selectedDate);
                d.setDate(d.getDate() - 1);
                return d.toISOString().split('T')[0];
            },
            selectedDate: config.oldEventDate || '',
            selectedEndDate: config.oldEventEndDate || config.oldEventDate || '',
            pickingRange: false,
            hoverDate: null,
            selectedPackageId: config.packages.length > 0 ? config.packages[0].id : null,
            paymentType: '{{ old("payment_type", "dp") }}',
            nominalDp: {{ old('nominal_dp') ? old('nominal_dp') : 'config.minDpAmount' }},
            nominalDpDisplay: '',
            activeAddonCategory: 'all',
            agreedToTerms: false,

            getAddonCategory(name) {
                const n = (name || '').toLowerCase();
                if (n.includes('kursi') || n.includes('meja')) return 'kursi_meja';
                if (n.includes('kipas') || n.includes('blower')) return 'kipas';
                if (n.includes('panggung') || n.includes('flooring') || n.includes('karpet')) return 'panggung';
                if (n.includes('sound')) return 'sound';
                if (n.includes('prasmanan') || n.includes('pondokan')) return 'prasmanan';
                if (n.includes('tenda') || n.includes('lampu') || n.includes('cover')) return 'tenda';
                return 'lainnya';
            },

            // Inline Reactive Form Validation Errors
            formErrors: {
                step1: null,
                step2: null,
                step3: null,
                step4: null,
                customer_name: null,
                customer_phone: null,
                venue_address: null,
                survey_date: null,
                payment_proof: null
            },

            // Calendar state
            calYear: 2026,
            calMonth: 10, // November (0-indexed)
            monthOpen: false,
            yearOpen: false,
            monthNames: [
                'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
            ],

            activeBank: 'bca',
            banks: {
                bca: {
                    name: 'Bank BCA',
                    number: '{{ $settings['bca_account_number'] ?? '3620648107' }}',
                    formatted: '{{ wordwrap($settings['bca_account_number'] ?? '3620648107', 4, ' ', true) }}',
                    holder: '{{ $settings['bca_account_name'] ?? 'EZZU RAKHMAT FIRDAUS' }}'
                },
                bri: {
                    name: 'Bank BRI',
                    number: '{{ $settings['bri_account_number'] ?? '344301026581532' }}',
                    formatted: '{{ wordwrap($settings['bri_account_number'] ?? '344301026581532', 4, ' ', true) }}',
                    holder: '{{ $settings['bri_account_name'] ?? 'EZZU RAKHMAT FIRDAUS' }}'
                }
            },
            copiedBank: false,
            previewUrl: null,
            fileName: '',

            init() {
                const now = new Date();
                if (this.selectedDate) {
                    this.syncCalendarToDate(this.selectedDate);
                } else {
                    this.calYear = now.getFullYear();
                    this.calMonth = now.getMonth();
                }

                if (this.customerName) {
                    this.customerName = this.customerName.toUpperCase();
                }

                const oldPhone = '{{ old("customer_phone", "") }}';
                if (oldPhone) {
                    this.setPhoneValue(oldPhone);
                }

                if (this.nominalDp) {
                    this.nominalDpDisplay = new Intl.NumberFormat('id-ID').format(this.nominalDp);
                }
            },

            formatPhoneNumber(raw) {
                let digits = (raw || '').replace(/\D/g, '');
                if (digits.startsWith('62')) {
                    digits = digits.substring(2);
                }
                digits = digits.replace(/^0+/, '');
                if (digits.length > 13) {
                    digits = digits.substring(0, 13);
                }

                let formatted = '';
                if (digits.length <= 3) {
                    formatted = digits;
                } else if (digits.length <= 7) {
                    formatted = digits.slice(0, 3) + '-' + digits.slice(3);
                } else if (digits.length <= 11) {
                    formatted = digits.slice(0, 3) + '-' + digits.slice(3, 7) + '-' + digits.slice(7);
                } else {
                    formatted = digits.slice(0, 3) + '-' + digits.slice(3, 7) + '-' + digits.slice(7, 11) + '-' + digits.slice(11);
                }

                return { digits, formatted };
            },

            setPhoneValue(val) {
                const { digits, formatted } = this.formatPhoneNumber(val);
                this.rawPhoneDigits = digits;
                this.phoneDisplay = formatted;
            },

            handlePhoneInput(event) {
                const { digits, formatted } = this.formatPhoneNumber(event.target.value);
                this.rawPhoneDigits = digits;
                this.phoneDisplay = formatted;
                event.target.value = formatted;

                this.formErrors.customer_phone = null;
                this.formErrors.step3 = null;
            },

            setNominalDp(amount) {
                this.nominalDp = amount;
                this.nominalDpDisplay = new Intl.NumberFormat('id-ID').format(amount);
                this.formErrors.step4 = null;
            },

            handleNominalDpInput(event) {
                const raw = event.target.value.replace(/\D/g, '');
                if (!raw) {
                    this.nominalDp = 0;
                    this.nominalDpDisplay = '';
                    event.target.value = '';
                    return;
                }
                const num = parseInt(raw, 10);
                this.nominalDp = num;
                const formatted = new Intl.NumberFormat('id-ID').format(num);
                this.nominalDpDisplay = formatted;
                event.target.value = formatted;
                this.formErrors.step4 = null;
            },

            get stepTitle() {
                if (this.step === 1) return 'Jadwal Acara';
                if (this.step === 2) return 'Pilih Paket';
                if (this.step === 3) return 'Data Pemesan';
                return 'Pembayaran';
            },

            get calendarGrid() {
                const year = this.calYear;
                const month = this.calMonth;
                const firstDayIndex = new Date(year, month, 1).getDay();
                const totalDays = new Date(year, month + 1, 0).getDate();
                
                const cells = [];
                const prevMonthTotal = new Date(year, month, 0).getDate();
                for (let i = firstDayIndex - 1; i >= 0; i--) {
                    cells.push({
                        day: prevMonthTotal - i,
                        dateString: null,
                        isCurrentMonth: false,
                        isPast: true,
                        isBooked: false,
                        isToday: false
                    });
                }

                const today = new Date();
                today.setHours(0,0,0,0);

                for (let d = 1; d <= totalDays; d++) {
                    const mStr = String(month + 1).padStart(2, '0');
                    const dStr = String(d).padStart(2, '0');
                    const dateStr = `${year}-${mStr}-${dStr}`;
                    const dayDate = new Date(year, month, d);
                    dayDate.setHours(0,0,0,0);

                    const isPast = dayDate < today;
                    const isBooked = this.bookedDates.includes(dateStr);
                    const isToday = dayDate.getTime() === today.getTime();

                    cells.push({
                        day: d,
                        dateString: dateStr,
                        isCurrentMonth: true,
                        isPast: isPast,
                        isBooked: isBooked,
                        isToday: isToday
                    });
                }
                return cells;
            },

            prevCalMonth() {
                if (this.calMonth === 0) {
                    this.calMonth = 11;
                    this.calYear--;
                } else {
                    this.calMonth--;
                }
            },

            nextCalMonth() {
                if (this.calMonth === 11) {
                    this.calMonth = 0;
                    this.calYear++;
                } else {
                    this.calMonth++;
                }
            },

            goToTodayMonth() {
                const now = new Date();
                this.calYear = now.getFullYear();
                this.calMonth = now.getMonth();
            },

            syncCalendarToDate(dateStr) {
                if (!dateStr) return;
                const parts = dateStr.split('-');
                if (parts.length === 3) {
                    this.calYear = parseInt(parts[0], 10);
                    this.calMonth = parseInt(parts[1], 10) - 1;
                }
            },

            selectDate(dateStr) {
                this.formErrors.step1 = null;
                if (this.bookedDates.includes(dateStr)) return;

                if (!this.selectedDate || (!this.pickingRange && this.selectedEndDate)) {
                    // First click: sets start date
                    this.selectedDate = dateStr;
                    this.selectedEndDate = null;
                    this.hoverDate = null;
                    this.pickingRange = true;
                } else if (this.pickingRange) {
                    // Second click
                    if (dateStr < this.selectedDate) {
                        // Clicked earlier date: reset start to this earlier date
                        this.selectedDate = dateStr;
                        this.selectedEndDate = null;
                        this.hoverDate = null;
                        this.pickingRange = true;
                    } else if (dateStr === this.selectedDate) {
                        // Clicked same date: 1 day duration
                        this.selectedEndDate = dateStr;
                        this.hoverDate = null;
                        this.pickingRange = false;
                    } else {
                        // Clicked later date: check if any date in between is booked
                        if (this.hasBookedDateBetween(this.selectedDate, dateStr)) {
                            this.formErrors.step1 = 'Rentang tanggal yang Anda pilih melewati tanggal yang sudah terisi (PENUH). Silakan pilih rentang tanggal lain.';
                            return;
                        }
                        this.selectedEndDate = dateStr;
                        this.hoverDate = null;
                        this.pickingRange = false;
                    }
                }
            },

            onDateHover(dateStr) {
                if (this.pickingRange && this.selectedDate && dateStr >= this.selectedDate) {
                    this.hoverDate = dateStr;
                }
            },

            hasBookedDateBetween(startStr, endStr) {
                const parts1 = startStr.split('-');
                const parts2 = endStr.split('-');
                const start = new Date(parseInt(parts1[0], 10), parseInt(parts1[1], 10) - 1, parseInt(parts1[2], 10));
                const end = new Date(parseInt(parts2[0], 10), parseInt(parts2[1], 10) - 1, parseInt(parts2[2], 10));
                const curr = new Date(start);

                while (curr <= end) {
                    const y = curr.getFullYear();
                    const m = String(curr.getMonth() + 1).padStart(2, '0');
                    const d = String(curr.getDate()).padStart(2, '0');
                    const dStr = `${y}-${m}-${d}`;
                    if (this.bookedDates.includes(dStr)) {
                        return true;
                    }
                    curr.setDate(curr.getDate() + 1);
                }
                return false;
            },

            resetDateRange() {
                this.selectedDate = '';
                this.selectedEndDate = '';
                this.hoverDate = null;
                this.pickingRange = false;
                this.formErrors.step1 = null;
            },

            get effectiveEndDate() {
                if (this.selectedEndDate) return this.selectedEndDate;
                if (this.pickingRange && this.hoverDate && this.hoverDate > this.selectedDate) {
                    return this.hoverDate;
                }
                return this.selectedDate;
            },

            get durationDays() {
                if (!this.selectedDate) return 0;
                const endStr = this.selectedEndDate || this.selectedDate;
                const parts1 = this.selectedDate.split('-');
                const parts2 = endStr.split('-');
                const d1 = new Date(parseInt(parts1[0], 10), parseInt(parts1[1], 10) - 1, parseInt(parts1[2], 10));
                const d2 = new Date(parseInt(parts2[0], 10), parseInt(parts2[1], 10) - 1, parseInt(parts2[2], 10));
                const diffTime = d2.getTime() - d1.getTime();
                return Math.max(1, Math.round(diffTime / (1000 * 60 * 60 * 24)) + 1);
            },

            get isSingleDateSelection() {
                const end = this.effectiveEndDate;
                return !end || end === this.selectedDate;
            },

            isStartDate(dateStr) {
                return this.selectedDate === dateStr;
            },

            isEndDate(dateStr) {
                const end = this.effectiveEndDate;
                return end === dateStr && end !== this.selectedDate;
            },

            isInRange(dateStr) {
                if (!this.selectedDate || !dateStr) return false;
                const end = this.effectiveEndDate;
                if (!end || end === this.selectedDate) {
                    return this.selectedDate === dateStr;
                }
                return dateStr >= this.selectedDate && dateStr <= end;
            },

            isBetweenDates(dateStr) {
                if (!this.selectedDate || !dateStr) return false;
                const end = this.effectiveEndDate;
                if (!end || end === this.selectedDate) return false;
                return dateStr > this.selectedDate && dateStr < end;
            },

            get isDateBooked() {
                if (!this.selectedDate) return false;
                if (this.bookedDates.includes(this.selectedDate)) return true;
                const endStr = this.selectedEndDate || this.selectedDate;
                if (endStr !== this.selectedDate) {
                    return this.hasBookedDateBetween(this.selectedDate, endStr);
                }
                return false;
            },

            formatSingleDate(dateStr) {
                if (!dateStr) return '';
                const parts = dateStr.split('-');
                if (parts.length !== 3) return dateStr;
                const d = new Date(parseInt(parts[0], 10), parseInt(parts[1], 10) - 1, parseInt(parts[2], 10));
                return d.toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' });
            },

            get formattedSelectedDate() {
                if (!this.selectedDate) return '';
                const endStr = this.selectedEndDate || this.selectedDate;
                const days = this.durationDays;
                const dayLabel = `${days} Hari`;
                if (endStr === this.selectedDate || days === 1) {
                    return `${this.formatSingleDate(this.selectedDate)} (${dayLabel})`;
                }
                const pStart = this.selectedDate.split('-');
                const pEnd = endStr.split('-');
                if (pStart[0] === pEnd[0] && pStart[1] === pEnd[1]) {
                    const dayStart = parseInt(pStart[2], 10);
                    return `${dayStart} – ${this.formatSingleDate(endStr)} (${dayLabel})`;
                }
                return `${this.formatSingleDate(this.selectedDate)} – ${this.formatSingleDate(endStr)} (${dayLabel})`;
            },

            get currentPackage() {
                return this.packages.find(p => p.id == this.selectedPackageId);
            },

            get currentPackageName() {
                return this.currentPackage ? this.currentPackage.name : '-';
            },

            get currentPackagePrice() {
                return this.currentPackage ? parseFloat(this.currentPackage.base_price) : 0;
            },

            get activeAddons() {
                return this.addons.filter(a => parseInt(a.selectedQty) > 0);
            },

            get addonTotalPrice() {
                return this.activeAddons.reduce((sum, a) => sum + (parseInt(a.selectedQty) * parseFloat(a.unit_price)), 0);
            },

            get extraDays() {
                return Math.max(0, this.durationDays - this.standardDays);
            },

            get additionalDayCharge() {
                if (this.extraDays <= 0 || !this.currentPackagePrice) return 0;
                return this.extraDays * this.extraDayRate * this.currentPackagePrice;
            },

            selectDistanceTier(tier, km) {
                this.selectedDistanceTier = tier;
                this.deliveryDistanceKm = km;
                this.gpsStatus = km === 0 ? 'Bebas ongkir hingga 10 km' : `Estimasi jarak: ${km} km`;
            },

            detectGpsDistance() {
                if (!navigator.geolocation) {
                    this.gpsStatus = 'Browser tidak mendukung GPS';
                    return;
                }
                this.gpsLoading = true;
                this.gpsStatus = 'Mendeteksi titik koordinat HP...';

                navigator.geolocation.getCurrentPosition(
                    (position) => {
                        const lat1 = position.coords.latitude;
                        const lon1 = position.coords.longitude;
                        const lat2 = parseFloat(this.warehouseLat);
                        const lon2 = parseFloat(this.warehouseLng);

                        const R = 6371;
                        const dLat = (lat2 - lat1) * Math.PI / 180;
                        const dLon = (lon2 - lon1) * Math.PI / 180;
                        const a = Math.sin(dLat/2) * Math.sin(dLat/2) +
                                  Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
                                  Math.sin(dLon/2) * Math.sin(dLon/2);
                        const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
                        const rawDistance = R * c;
                        const roadDistance = Math.round(rawDistance * 1.25 * 10) / 10;

                        this.deliveryDistanceKm = roadDistance;
                        if (roadDistance <= 10) {
                            this.selectedDistanceTier = '0_10';
                        } else if (roadDistance <= 20) {
                            this.selectedDistanceTier = '11_20';
                        } else if (roadDistance <= 30) {
                            this.selectedDistanceTier = '21_30';
                        } else if (roadDistance <= 40) {
                            this.selectedDistanceTier = '31_40';
                        } else {
                            this.selectedDistanceTier = 'gt_40';
                        }
                        this.gpsStatus = `Terdeteksi ~${roadDistance} km (via GPS)`;
                        this.gpsLoading = false;
                    },
                    (error) => {
                        this.gpsLoading = false;
                        this.gpsStatus = 'Gagal mendeteksi GPS (izin ditolak atau timeout)';
                    },
                    { enableHighAccuracy: true, timeout: 10000, maximumAge: 60000 }
                );
            },

            get transportFee() {
                const km = parseFloat(this.deliveryDistanceKm) || 0;
                if (km <= this.freeDistanceKm) return 0;
                const extraKm = km - this.freeDistanceKm;
                const tiers = Math.ceil(extraKm / this.distanceTierKm);
                return tiers * this.transportFeePerTier;
            },

            get grandTotal() {
                return this.currentPackagePrice + this.addonTotalPrice + this.additionalDayCharge + this.transportFee;
            },

            get remainingBalance() {
                if (this.paymentType === 'lunas') return 0;
                const diff = this.grandTotal - (parseFloat(this.nominalDp) || 0);
                return diff > 0 ? diff : 0;
            },

            goToStep(nextStep) {
                // Moving back: clear step errors and allow smooth transition
                if (nextStep < this.step) {
                    this.formErrors.step1 = null;
                    this.formErrors.step2 = null;
                    this.formErrors.step3 = null;
                    this.formErrors.step4 = null;
                    this.step = nextStep;
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                    return;
                }

                if (nextStep === 2) {
                    if (this.pickingRange && this.selectedDate && !this.selectedEndDate) {
                        this.selectedEndDate = this.selectedDate;
                        this.pickingRange = false;
                    }
                    if (!this.selectedDate) {
                        this.formErrors.step1 = 'Silakan pilih tanggal acara terlebih dahulu pada kalender.';
                        return;
                    }
                    if (this.isDateBooked) {
                        this.formErrors.step1 = 'Rentang tanggal yang Anda pilih melewati tanggal yang sudah terisi (PENUH). Silakan pilih rentang tanggal lain.';
                        return;
                    }
                    this.formErrors.step1 = null;
                }

                if (nextStep === 3) {
                    if (!this.selectedPackageId) {
                        this.formErrors.step2 = 'Silakan pilih paket pernikahan terlebih dahulu untuk melanjutkan.';
                        return;
                    }
                    this.formErrors.step2 = null;
                }

                if (nextStep === 4) {
                    let hasErr = false;
                    const name = (this.customerName || '').trim();
                    const phone = (this.rawPhoneDigits || '').trim();
                    const address = (this.venueAddress || '').trim();

                    if (!name) {
                        this.formErrors.customer_name = 'Silakan masukkan nama kedua calon pengantin.';
                        hasErr = true;
                    } else {
                        this.formErrors.customer_name = null;
                    }

                    if (!phone) {
                        this.formErrors.customer_phone = 'Silakan masukkan nomor WhatsApp aktif.';
                        hasErr = true;
                    } else if (phone.length < 8) {
                        this.formErrors.customer_phone = 'Nomor WhatsApp tidak valid (minimal 8 digit).';
                        hasErr = true;
                    } else {
                        this.formErrors.customer_phone = null;
                    }

                    if (!address) {
                        this.formErrors.venue_address = 'Silakan masukkan alamat lengkap lokasi acara.';
                        hasErr = true;
                    } else {
                        this.formErrors.venue_address = null;
                    }

                    if (this.surveyDate) {
                        if (this.selectedDate && this.surveyDate >= this.selectedDate) {
                            this.formErrors.survey_date = 'Tanggal survey harus sebelum tanggal pelaksanaan acara.';
                            hasErr = true;
                        } else {
                            this.formErrors.survey_date = null;
                        }
                    } else {
                        this.formErrors.survey_date = null;
                    }

                    if (hasErr) {
                        this.formErrors.step3 = 'Silakan lengkapi data pemesan dan alamat acara yang wajib di bawah ini.';
                        this.$nextTick(() => {
                            if (!name) {
                                document.querySelector('input[name="customer_name"]')?.focus();
                            } else if (!phone || phone.length < 8) {
                                document.getElementById('phoneInput')?.focus();
                            } else if (!address) {
                                document.querySelector('textarea[name="venue_address"]')?.focus();
                            } else if (this.surveyDate && this.selectedDate && this.surveyDate >= this.selectedDate) {
                                document.querySelector('input[name="survey_date"]')?.focus();
                            }
                        });
                        return;
                    }
                    this.formErrors.step3 = null;
                }

                this.step = nextStep;
                window.scrollTo({ top: 0, behavior: 'smooth' });
            },

            submitBookingForm() {
                const form = document.getElementById('reservationForm');

                if (this.paymentType === 'dp') {
                    if (!this.nominalDp || this.nominalDp < this.minDpAmount) {
                        this.formErrors.step4 = 'Nominal DP minimal adalah ' + this.formatRupiah(this.minDpAmount) + '.';
                        this.$nextTick(() => {
                            document.getElementById('nominalDpInput')?.focus();
                        });
                        return;
                    }
                    if (this.nominalDp > this.grandTotal) {
                        this.formErrors.step4 = 'Nominal DP tidak boleh melebihi Grand Total tagihan (' + this.formatRupiah(this.grandTotal) + ').';
                        this.$nextTick(() => {
                            document.getElementById('nominalDpInput')?.focus();
                        });
                        return;
                    }
                }

                const fileInput = form.querySelector('input[name="payment_proof"]');
                if (!fileInput || !fileInput.files || fileInput.files.length === 0) {
                    this.formErrors.payment_proof = 'Silakan unggah bukti transfer pembayaran sebelum konfirmasi.';
                    this.formErrors.step4 = 'Bukti transfer pembayaran wajib diunggah untuk mengunci reservasi.';
                    this.$nextTick(() => {
                        fileInput?.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    });
                    return;
                }
                this.formErrors.payment_proof = null;

                if (!this.agreedToTerms) {
                    this.formErrors.step4 = 'Harap centang persetujuan Aturan & Ketentuan Reservasi Resmi sebelum konfirmasi.';
                    this.$nextTick(() => {
                        document.getElementById('termsConsentCheckbox')?.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    });
                    return;
                }

                this.formErrors.step4 = null;
                form.submit();
            },

            copyBankAccount() {
                const current = this.banks[this.activeBank];
                if (current) {
                    navigator.clipboard.writeText(current.number);
                    this.copiedBank = true;
                    setTimeout(() => { this.copiedBank = false; }, 2000);
                }
            },

            handleFileUpload(event) {
                const file = event.target.files[0];
                if (!file) {
                    this.previewUrl = null;
                    this.fileName = '';
                    return;
                }
                this.formErrors.payment_proof = null;
                this.formErrors.step4 = null;
                this.fileName = file.name;
                if (file.type.startsWith('image/')) {
                    this.previewUrl = URL.createObjectURL(file);
                } else {
                    this.previewUrl = null;
                }
            },

            formatRupiah(val) {
                return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(val || 0);
            }
        }
    }
    </script>
</body>
</html>
