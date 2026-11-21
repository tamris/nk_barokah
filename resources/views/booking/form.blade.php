<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <title>Reserve Your Wedding Date | NK Barokah</title>

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
    </style>
</head>
<body class="min-h-full bg-slate-50 text-slate-900 font-sans antialiased flex flex-col justify-between selection:bg-brand-100"
      x-data="bookingReservation({
          packages: {{ json_encode($packages) }},
          addons: {{ json_encode($addons) }},
          bookedDates: {{ json_encode($bookedDates) }},
          minDpAmount: {{ config('wo.min_dp_amount', 1000000) }},
          oldEventDate: '{{ old('event_date', '') }}',
          oldEventEndDate: '{{ old('event_end_date', '') }}',
          oldDurationDays: {{ (int) old('duration_days', 1) }}
      })"
      x-cloak>

    <!-- ============================================================== -->
    <!-- STICKY TOP APP HEADER                                          -->
    <!-- ============================================================== -->
    <header class="sticky top-0 z-40 bg-white/95 backdrop-blur-md border-b border-slate-200/90 pt-safe">
        <div class="max-w-2xl mx-auto px-4 h-14 sm:h-16 flex items-center justify-between">
            <!-- Brand Identity -->
            <a href="{{ url('/') }}" class="flex items-center gap-2.5">
                <span class="w-7 h-7 sm:w-8 sm:h-8 rounded-lg bg-brand-600 text-white flex items-center justify-center font-extrabold text-xs sm:text-sm tracking-tight shadow-sm">
                    N
                </span>
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
    <main class="max-w-2xl mx-auto w-full px-4 sm:px-6 pt-5 pb-28 md:pb-12 flex-1">

        <!-- Server Validation Errors -->
        @if (isset($errors) && $errors->any())
            <div class="mb-5 p-4 rounded-2xl bg-rose-50 border border-rose-200 text-xs text-rose-800 space-y-1">
                <div class="font-bold flex items-center gap-1.5">
                    <svg class="w-4 h-4 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    <span>Please correct the following errors:</span>
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
                    <span class="text-[10px] sm:text-[11px] font-bold uppercase tracking-[0.12em] text-brand-600">Step 1 of 4 • Event Schedule</span>
                    <h1 class="text-xl sm:text-2xl font-extrabold tracking-tight text-slate-900 leading-tight">When is your wedding date?</h1>
                    <p class="text-xs sm:text-sm text-slate-500 leading-relaxed">
                        We accept only 1 exclusive wedding per date. Pick an open date from the calendar.
                    </p>
                </div>

                <!-- Calendar Card -->
                <div class="bg-white rounded-2xl border border-slate-200/90 p-4 sm:p-6 shadow-card space-y-4">

                    <!-- Month Switcher Header (Left: Prev | Center: Month, Year, Today | Right: Next) -->
                    <div class="flex items-center justify-between gap-1.5 sm:gap-2 pb-3 border-b border-slate-100">
                        <!-- Left: Prev Month Button -->
                        <button type="button" @click="prevCalMonth()"
                                class="w-8.5 h-8.5 sm:w-9 sm:h-9 rounded-xl border border-slate-200 hover:bg-slate-50 active:scale-95 flex items-center justify-center text-slate-600 transition cursor-pointer shrink-0" title="Previous Month">
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
                                Today
                            </button>
                        </div>

                        <!-- Right: Next Month Button -->
                        <button type="button" @click="nextCalMonth()"
                                class="w-8.5 h-8.5 sm:w-9 sm:h-9 rounded-xl border border-slate-200 hover:bg-slate-50 active:scale-95 flex items-center justify-center text-slate-600 transition cursor-pointer shrink-0" title="Next Month">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                        </button>
                    </div>

                    <!-- Days of Week Headers (7 Columns with Unique Index Key) -->
                    <div class="grid grid-cols-7 gap-1 text-center">
                        <template x-for="(dow, idx) in ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat']" :key="idx">
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
                                            <span class="text-[8px] sm:text-[9px] font-bold text-rose-400 leading-none mt-0.5">FULL</span>
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
                                    <span class="text-rose-600 font-semibold">Tanggal yang dipilih sudah penuh (FULL)</span>
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
                            <span>Continue to Package</span>
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
                    <span class="text-[10px] sm:text-[11px] font-bold uppercase tracking-[0.12em] text-brand-600">Step 2 of 4 • Package & Services</span>
                    <h1 class="text-xl sm:text-2xl font-extrabold tracking-tight text-slate-900 leading-tight">Choose your wedding package</h1>
                    <p class="text-xs sm:text-sm text-slate-500 leading-relaxed">
                        Tap a package to select. You can add extra services below.
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
                                                Favorite
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
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                        <div>
                            <h4 class="text-xs sm:text-sm font-bold text-slate-900 uppercase tracking-wider">Optional Add-ons (20 Pilihan)</h4>
                            <p class="text-[11px] sm:text-xs text-slate-400 mt-0.5">Pilih perlengkapan tambahan sesuai kebutuhan venue Anda</p>
                        </div>
                        <span class="text-xs font-mono font-bold px-2.5 py-1 rounded-lg transition"
                              :class="activeAddons.length > 0 ? 'bg-brand-50 text-brand-700 border border-brand-200/80 shadow-xs' : 'bg-slate-100 text-slate-500'"
                              x-text="`${activeAddons.length} added`"></span>
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
                        <span>Back</span>
                    </button>
                    <div class="flex items-center gap-4 sm:gap-5">
                        <div class="text-right">
                            <span class="text-[10px] uppercase font-bold text-slate-400 tracking-wider block">Estimated Total</span>
                            <span class="text-base sm:text-lg font-extrabold font-mono text-brand-700" x-text="formatRupiah(grandTotal)"></span>
                        </div>
                        <button type="button" @click="goToStep(3)"
                                class="px-6 py-2.5 sm:py-3 rounded-xl bg-brand-600 hover:bg-brand-700 active:scale-95 text-white font-bold text-xs tracking-wide transition flex items-center gap-2 cursor-pointer shadow-pill">
                            <span>Continue to Couple Details</span>
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
                    <span class="text-[10px] sm:text-[11px] font-bold uppercase tracking-[0.12em] text-brand-600">Step 3 of 4 • Couple Details</span>
                    <h1 class="text-xl sm:text-2xl font-extrabold tracking-tight text-slate-900 leading-tight">Who are we planning for?</h1>
                    <p class="text-xs sm:text-sm text-slate-500 leading-relaxed">
                        Enter couple names and WhatsApp contact for contract and coordination.
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
                            Couple Names <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" name="customer_name" required x-model="customerName"
                               @input="customerName = $event.target.value.toUpperCase(); formErrors.customer_name = null; formErrors.step3 = null"
                               autocapitalize="characters" autocomplete="name"
                               placeholder="e.g. MUHAMMAD REZA & DIAN SASTROWARDOYO"
                               class="w-full px-4 py-3 rounded-xl border text-base sm:text-sm uppercase placeholder:normal-case placeholder:text-slate-400 focus:outline-none transition"
                               :class="formErrors.customer_name 
                                   ? 'border-rose-400 ring-2 ring-rose-100 bg-rose-50/20 text-slate-900' 
                                   : 'border-slate-200 focus:border-brand-600 focus:ring-2 focus:ring-brand-600/15'">
                        <p x-show="formErrors.customer_name" x-cloak class="text-xs font-semibold text-rose-600 flex items-center gap-1.5 pt-0.5">
                            <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                            <span x-text="formErrors.customer_name"></span>
                        </p>
                        <p x-show="!formErrors.customer_name" class="text-[11px] text-slate-400">Both bride and groom names to appear on the official contract.</p>
                    </div>

                    <div class="space-y-1.5">
                        <label class="block text-xs font-semibold text-slate-700 tracking-tight">
                            WhatsApp Phone Number <span class="text-rose-500">*</span>
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
                        <p x-show="!formErrors.customer_phone" class="text-[11px] text-slate-400">Invoice receipt and schedule confirmation are delivered here.</p>
                    </div>

                    <div class="space-y-1.5">
                        <label class="block text-xs font-semibold text-slate-700 tracking-tight">
                            Email Address <span class="text-slate-400 font-normal">(Optional)</span>
                        </label>
                        <input type="email" name="customer_email" x-model="customerEmail"
                               @input="customerEmail = $event.target.value.toLowerCase()"
                               inputmode="email" autocomplete="email"
                               autocapitalize="none" autocorrect="off" spellcheck="false"
                               placeholder="reza.dian@example.com"
                               class="w-full px-4 py-3 rounded-xl border border-slate-200 text-base sm:text-sm lowercase placeholder:normal-case placeholder:text-slate-400 focus:outline-none focus:border-brand-600 focus:ring-2 focus:ring-brand-600/15 transition">
                    </div>

                </div>

                <!-- DESKTOP & TABLET INLINE ACTION FOOTER (CARD CONTAINER) -->
                <div class="hidden md:flex items-center justify-between gap-4 bg-white rounded-2xl border border-slate-200/90 p-4 sm:p-5 shadow-card">
                    <button type="button" @click="goToStep(2)"
                            class="px-4 py-2.5 rounded-xl border border-slate-200 hover:bg-slate-50 text-slate-700 font-semibold text-xs transition flex items-center gap-1.5 cursor-pointer active:scale-95">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
                        <span>Back</span>
                    </button>
                    <button type="button" @click="goToStep(4)"
                            class="px-6 py-2.5 sm:py-3 rounded-xl bg-brand-600 hover:bg-brand-700 active:scale-95 text-white font-bold text-xs tracking-wide transition flex items-center gap-2 cursor-pointer shadow-pill">
                        <span>Continue to Payment</span>
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
                    <span class="text-[10px] sm:text-[11px] font-bold uppercase tracking-[0.12em] text-brand-600">Step 4 of 4 • Confirmation & Payment</span>
                    <h1 class="text-xl sm:text-2xl font-extrabold tracking-tight text-slate-900 leading-tight">Review & lock your reservation</h1>
                    <p class="text-xs sm:text-sm text-slate-500 leading-relaxed">
                        Verify invoice details and upload your bank transfer receipt.
                    </p>
                </div>

                <!-- Quotation Breakdown Table -->
                <div class="bg-white rounded-2xl border border-slate-200/90 p-4 sm:p-5 shadow-card space-y-3">
                    <div class="flex items-center justify-between pb-2.5 border-b border-slate-100">
                        <h3 class="text-xs font-bold text-slate-900 uppercase tracking-wider">Reservation Breakdown</h3>
                        <span class="text-xs font-bold text-brand-700 bg-brand-50 px-2.5 py-0.5 rounded-md"
                              x-text="formattedSelectedDate"></span>
                    </div>

                    <div class="space-y-2 text-xs">
                        <div class="flex justify-between items-center text-slate-600">
                            <span>Package: <strong class="text-slate-900 font-semibold" x-text="currentPackageName"></strong></span>
                            <span class="font-mono font-semibold text-slate-900" x-text="formatRupiah(currentPackagePrice)"></span>
                        </div>

                        <template x-for="ad in activeAddons" :key="ad.id">
                            <div class="flex justify-between items-center text-slate-500 pl-2 border-l border-slate-200">
                                <span x-text="`${ad.name} (${ad.selectedQty}x)`"></span>
                                <span class="font-mono text-slate-700" x-text="formatRupiah(ad.selectedQty * ad.unit_price)"></span>
                            </div>
                        </template>

                        <!-- Extra Day Charge if > 2 days -->
                        <template x-if="extraDays > 0">
                            <div class="flex justify-between items-center text-amber-800 bg-amber-50/80 p-2.5 rounded-xl border border-amber-200/80">
                                <div class="flex items-center gap-1.5">
                                    <svg class="w-3.5 h-3.5 text-amber-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    <span>Tambahan Durasi (<span class="font-bold" x-text="`${extraDays} hari ekstra @ 15%`"></span>)</span>
                                </div>
                                <span class="font-mono font-bold text-amber-900" x-text="`+ ${formatRupiah(additionalDayCharge)}`"></span>
                            </div>
                        </template>

                        <div class="pt-2.5 border-t border-slate-100 flex justify-between items-baseline font-bold text-sm text-slate-900">
                            <span>Grand Total</span>
                            <span class="text-base sm:text-lg font-mono font-extrabold text-brand-700" x-text="formatRupiah(grandTotal)"></span>
                        </div>
                    </div>
                </div>

                <!-- Payment Options -->
                <div class="bg-white rounded-2xl border border-slate-200/90 p-4 sm:p-5 shadow-card space-y-4">
                    <h3 class="text-xs font-bold text-slate-900 uppercase tracking-wider">Payment Method</h3>

                    <!-- Toggle DP vs Lunas -->
                    <div class="grid grid-cols-2 gap-2.5">
                        <label class="p-3 rounded-xl border text-center cursor-pointer transition select-none active:scale-[0.98]"
                               :class="paymentType === 'dp' 
                                   ? 'border-brand-600 bg-brand-50/50 text-brand-900 font-semibold ring-1 ring-brand-600/20' 
                                   : 'border-slate-200 hover:border-slate-300 text-slate-700'">
                            <input type="radio" name="payment_type" value="dp" class="sr-only" x-model="paymentType">
                            <span class="text-xs block font-bold">Down Payment (DP)</span>
                            <span class="text-[10px] text-slate-500 mt-0.5 block">Lock date with minimum DP</span>
                        </label>

                        <label class="p-3 rounded-xl border text-center cursor-pointer transition select-none active:scale-[0.98]"
                               :class="paymentType === 'lunas' 
                                   ? 'border-brand-600 bg-brand-50/50 text-brand-900 font-semibold ring-1 ring-brand-600/20' 
                                   : 'border-slate-200 hover:border-slate-300 text-slate-700'">
                            <input type="radio" name="payment_type" value="lunas" class="sr-only" x-model="paymentType">
                            <span class="text-xs block font-bold">Full Settlement (100%)</span>
                            <span class="text-[10px] text-slate-500 mt-0.5 block">Settle full contract balance</span>
                        </label>
                    </div>

                    <!-- DP Amount Input -->
                    <div x-show="paymentType === 'dp'" class="p-3.5 rounded-xl bg-slate-50 border border-slate-200 space-y-2.5" x-cloak>
                        <div class="flex items-center justify-between text-xs">
                            <label class="font-semibold text-slate-700">DP Transfer Amount</label>
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
                                Min DP (Rp 1 Juta)
                            </button>
                            <button type="button" @click="setNominalDp(Math.round(grandTotal * 0.5))"
                                    class="px-2.5 py-1 rounded-lg text-[11px] font-semibold transition border border-slate-200 hover:bg-white text-slate-600 active:scale-95 cursor-pointer"
                                    :class="nominalDp === Math.round(grandTotal * 0.5) ? 'bg-brand-50 border-brand-300 text-brand-800 font-bold ring-1 ring-brand-500/20' : 'bg-white/60'">
                                50% DP
                            </button>
                        </div>

                        <div class="flex justify-between items-center text-xs text-slate-500 pt-1.5 border-t border-slate-200/60">
                            <span>Remaining balance due:</span>
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
                                    <span class="text-[8px] uppercase font-bold tracking-wider text-white/50 block">Cardholder / Atas Nama</span>
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
                            Silakan lakukan transfer ke rekening <strong class="text-slate-800" x-text="banks[activeBank].name"></strong> di atas atas nama <strong class="text-slate-800">EZZU RAKHMAT FIRDAUS</strong>, lalu upload bukti transfer di bawah.
                        </p>
                    </div>

                    <!-- Payment Proof Upload Dropzone -->
                    <div class="space-y-1.5">
                        <label class="block text-xs font-semibold text-slate-700 tracking-tight">
                            Upload Transfer Receipt <span class="text-rose-500">*</span>
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
                                    <span class="text-brand-600 underline">Tap to take photo or upload</span>
                                </p>
                                <p class="text-[11px] text-slate-400">JPG, PNG, or PDF up to 3MB</p>
                            </div>

                            <template x-if="previewUrl">
                                <div class="mt-3 pt-3 border-t border-slate-200">
                                    <img :src="previewUrl" alt="Receipt preview" class="max-h-28 mx-auto rounded-lg border border-slate-200 object-cover shadow-sm">
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

                <!-- Booking Rules Accordion (Sesuai Pricelist Resmi 2026 NK Barokah) -->
                <div class="bg-white rounded-2xl border border-slate-200/90 p-4 sm:p-5 shadow-card" x-data="{ rulesOpen: false }">
                    <button type="button" @click="rulesOpen = !rulesOpen" class="w-full flex items-center justify-between text-left cursor-pointer select-none">
                        <div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-xl bg-amber-50 text-amber-700 flex items-center justify-center font-bold text-xs shrink-0 border border-amber-200/60">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            </div>
                            <div>
                                <h4 class="text-xs sm:text-sm font-bold text-slate-900 tracking-tight">Aturan & Ketentuan Reservasi Resmi</h4>
                                <p class="text-[11px] text-slate-400">Ringkasan S&K resmi NK Barokah 2026</p>
                            </div>
                        </div>
                        <svg class="w-4 h-4 text-slate-400 transition-transform duration-200" :class="{ 'rotate-180': rulesOpen }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div x-show="rulesOpen" x-cloak x-transition class="mt-3.5 pt-3.5 border-t border-slate-100 text-xs text-slate-600 space-y-2">
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
                </div>

                <!-- DESKTOP & TABLET INLINE ACTION FOOTER (CARD CONTAINER) -->
                <div class="hidden md:flex items-center justify-between gap-4 bg-white rounded-2xl border border-slate-200/90 p-4 sm:p-5 shadow-card">
                    <button type="button" @click="goToStep(3)"
                            class="px-4 py-2.5 rounded-xl border border-slate-200 hover:bg-slate-50 text-slate-700 font-semibold text-xs transition flex items-center gap-1.5 cursor-pointer active:scale-95">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
                        <span>Back</span>
                    </button>
                    <button type="button" @click="submitBookingForm()" :disabled="isDateBooked"
                            class="px-7 py-3 rounded-xl bg-brand-600 hover:bg-brand-700 disabled:bg-slate-300 active:scale-95 text-white font-bold text-xs tracking-wide transition flex items-center gap-2 cursor-pointer shadow-pill">
                        <span>Confirm Reservation</span>
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
                    <p class="text-[10px] uppercase font-bold text-slate-400 tracking-wider">Date Status</p>
                    <p class="text-xs font-bold text-slate-900 truncate"
                       x-text="selectedDate ? formattedSelectedDate : 'No date chosen'"></p>
                </div>

                <div x-show="step > 1" x-cloak>
                    <p class="text-[10px] uppercase font-bold text-slate-400 tracking-wider">Grand Total</p>
                    <p class="text-sm font-bold font-mono text-brand-700 tracking-tight"
                       x-text="formatRupiah(grandTotal)"></p>
                </div>
            </div>

            <!-- Right Buttons Group -->
            <div class="flex items-center gap-2 shrink-0">
                <button type="button" x-show="step > 1" @click="step--"
                        class="px-3.5 py-2.5 rounded-xl border border-slate-200 hover:bg-slate-50 active:scale-95 text-slate-700 font-semibold text-xs transition cursor-pointer">
                    Back
                </button>

                <button type="button" x-show="step < 4" @click="goToStep(step + 1)"
                        :disabled="step === 1 && (!selectedDate || isDateBooked)"
                        class="px-5 py-2.5 rounded-xl bg-brand-600 hover:bg-brand-700 disabled:bg-slate-200 disabled:text-slate-400 active:scale-95 text-white font-bold text-xs tracking-wide transition flex items-center gap-1.5 cursor-pointer shadow-subtle">
                    <span x-text="step === 1 ? 'Select Package' : (step === 2 ? 'Couple Details' : 'Payment')"></span>
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                </button>

                <button type="button" x-show="step === 4" @click="submitBookingForm()"
                        :disabled="isDateBooked"
                        class="px-5 py-2.5 rounded-xl bg-brand-600 hover:bg-brand-700 disabled:bg-slate-300 active:scale-95 text-white font-bold text-xs tracking-wide transition flex items-center gap-1.5 cursor-pointer shadow-pill" x-cloak>
                    <span>Confirm Booking</span>
                    
                </button>
            </div>

        </div>
    </div>

    <!-- Minimal Clean Footer -->
    <footer class="border-t border-slate-200 bg-white py-6 text-center text-xs text-slate-400">
        <div class="max-w-2xl mx-auto px-4 flex flex-col sm:flex-row items-center justify-between gap-2 text-[11px]">
            <p>&copy; {{ date('Y') }} NK Barokah Wedding Organizer. All rights reserved.</p>
            <p class="text-slate-400">Direct booking & schedule locking system</p>
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

            // Form inputs
            customerName: '{{ old("customer_name", "") }}'.toUpperCase(),
            phoneDisplay: '',
            rawPhoneDigits: '',
            customerEmail: '{{ old("customer_email", "") }}'.toLowerCase(),
            selectedDate: config.oldEventDate || '',
            selectedEndDate: config.oldEventEndDate || config.oldEventDate || '',
            pickingRange: false,
            hoverDate: null,
            selectedPackageId: config.packages.length > 0 ? config.packages[0].id : null,
            paymentType: '{{ old("payment_type", "dp") }}',
            nominalDp: {{ old('nominal_dp') ? old('nominal_dp') : 'config.minDpAmount' }},
            nominalDpDisplay: '',
            activeAddonCategory: 'all',

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
                payment_proof: null
            },

            // Calendar state
            calYear: 2026,
            calMonth: 10, // November (0-indexed)
            monthOpen: false,
            yearOpen: false,
            monthNames: [
                'January', 'February', 'March', 'April', 'May', 'June',
                'July', 'August', 'September', 'October', 'November', 'December'
            ],

            activeBank: 'bca',
            banks: {
                bca: {
                    name: 'Bank BCA',
                    number: '3620648107',
                    formatted: '3620 648 107',
                    holder: 'EZZU RAKHMAT FIRDAUS'
                },
                bri: {
                    name: 'Bank BRI',
                    number: '344301026581532',
                    formatted: '3443 0102 6581 532',
                    holder: 'EZZU RAKHMAT FIRDAUS'
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
                if (this.step === 1) return 'Schedule';
                if (this.step === 2) return 'Package';
                if (this.step === 3) return 'Couple';
                return 'Payment';
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
                            this.formErrors.step1 = 'Rentang tanggal yang Anda pilih melewati tanggal yang sudah penuh (FULL). Silakan pilih rentang tanggal yang seluruhnya kosong.';
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
                return d.toLocaleDateString('id-ID', { weekday: 'short', day: 'numeric', month: 'short', year: 'numeric' });
            },

            get formattedSelectedDate() {
                if (!this.selectedDate) return '';
                const endStr = this.selectedEndDate || this.selectedDate;
                const days = this.durationDays;
                if (endStr === this.selectedDate || days === 1) {
                    return this.formatSingleDate(this.selectedDate) + ' (1 Hari)';
                }
                return this.formatSingleDate(this.selectedDate) + ' – ' + this.formatSingleDate(endStr) + ` (${days} Hari)`;
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
                return Math.max(0, this.durationDays - 2);
            },

            get additionalDayCharge() {
                if (this.extraDays <= 0 || !this.currentPackagePrice) return 0;
                return this.extraDays * 0.15 * this.currentPackagePrice;
            },

            get grandTotal() {
                return this.currentPackagePrice + this.addonTotalPrice + this.additionalDayCharge;
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
                        this.formErrors.step1 = 'Rentang tanggal yang Anda pilih melewati hari yang sudah penuh (FULL). Silakan pilih rentang lain.';
                        return;
                    }
                    this.formErrors.step1 = null;
                }

                if (nextStep === 3) {
                    if (!this.selectedPackageId) {
                        this.formErrors.step2 = 'Please select a wedding package to continue.';
                        return;
                    }
                    this.formErrors.step2 = null;
                }

                if (nextStep === 4) {
                    let hasErr = false;
                    const name = (this.customerName || '').trim();
                    const phone = (this.rawPhoneDigits || '').trim();

                    if (!name) {
                        this.formErrors.customer_name = 'Please enter bride & groom couple names.';
                        hasErr = true;
                    } else {
                        this.formErrors.customer_name = null;
                    }

                    if (!phone) {
                        this.formErrors.customer_phone = 'Please enter WhatsApp phone number.';
                        hasErr = true;
                    } else if (phone.length < 8) {
                        this.formErrors.customer_phone = 'Please enter a valid WhatsApp number (min. 8 digits).';
                        hasErr = true;
                    } else {
                        this.formErrors.customer_phone = null;
                    }

                    if (hasErr) {
                        this.formErrors.step3 = 'Please complete the required couple contact details below.';
                        this.$nextTick(() => {
                            if (!name) {
                                document.querySelector('input[name="customer_name"]')?.focus();
                            } else if (!phone || phone.length < 8) {
                                document.getElementById('phoneInput')?.focus();
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
                    this.formErrors.payment_proof = 'Please upload your bank transfer receipt before confirming.';
                    this.formErrors.step4 = 'Bank transfer receipt is required to lock your reservation.';
                    this.$nextTick(() => {
                        fileInput?.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    });
                    return;
                }
                this.formErrors.payment_proof = null;
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
