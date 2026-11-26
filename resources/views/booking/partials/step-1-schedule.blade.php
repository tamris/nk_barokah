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
                    <h1 class="text-xl sm:text-2xl font-extrabold tracking-tight text-slate-900 leading-tight" x-text="step1Heading"></h1>
                    <p class="text-xs sm:text-sm text-slate-500 leading-relaxed" x-text="step1Subtitle"></p>
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

                    <!-- Pilihan Kategori / Jenis Acara (Custom Dropdown matching UI/UX) -->
                    <div class="space-y-1.5 pt-3 border-t border-slate-100">
                        <label class="block text-xs font-semibold text-slate-700 tracking-tight">
                            Jenis Acara <span class="text-rose-500">*</span>
                        </label>
                        
                        <!-- Hidden input for form submission -->
                        <input type="hidden" name="event_type" :value="eventType" required>

                        <!-- Custom Dropdown Container -->
                        <div class="relative" @click.away="eventTypeOpen = false">
                            <button type="button" 
                                    @click="eventTypeOpen = !eventTypeOpen"
                                    class="w-full px-3.5 sm:px-4 py-2.5 sm:py-3 rounded-xl border transition flex items-center justify-between cursor-pointer select-none active:scale-[0.99]"
                                    :class="eventTypeOpen 
                                        ? 'bg-white border-brand-600 ring-2 ring-brand-600/15 text-slate-900 shadow-subtle' 
                                        : 'bg-white hover:bg-slate-50/80 border-slate-200 text-slate-800 shadow-2xs'">
                                <div class="flex items-center gap-2.5 min-w-0">
                                    <span class="w-2 h-2 rounded-full shrink-0 transition-colors"
                                          :class="eventType ? 'bg-brand-600' : 'bg-slate-300'"></span>
                                    <span class="truncate text-xs sm:text-sm"
                                          :class="eventType ? 'font-bold text-slate-800' : 'font-medium text-slate-400'"
                                          x-text="eventTypeLabel"></span>
                                </div>
                                <div class="flex items-center shrink-0 pl-2 text-slate-400">
                                    <svg class="w-4 h-4 transition-transform duration-200"
                                         :class="{ 'rotate-180 text-brand-600': eventTypeOpen }"
                                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/>
                                    </svg>
                                </div>
                            </button>

                            <!-- Custom Popover Menu Options (Drop-up ke atas agar tidak terhalang bottom dock di HP) -->
                            <div x-show="eventTypeOpen" x-cloak
                                 x-transition:enter="transition ease-out duration-150"
                                 x-transition:enter-start="opacity-0 scale-[0.98] translate-y-1"
                                 x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                                 x-transition:leave="transition ease-in duration-100"
                                 x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                                 x-transition:leave-end="opacity-0 scale-[0.98] translate-y-1"
                                 class="absolute left-0 right-0 bottom-full mb-1.5 bg-white rounded-2xl shadow-xl border border-slate-200/90 p-1.5 z-40 space-y-0.5 max-h-64 overflow-y-auto">
                                <template x-for="item in eventTypes" :key="item.id">
                                    <button type="button"
                                            @click="setEventType(item.id)"
                                            :class="eventType === item.id 
                                                ? 'bg-brand-50 text-brand-800 font-bold border border-brand-200/70' 
                                                : 'text-slate-700 hover:bg-slate-50 hover:text-slate-900 border border-transparent font-medium'"
                                            class="w-full text-left px-3.5 py-2.5 text-xs sm:text-sm rounded-xl transition flex items-center justify-between cursor-pointer group">
                                        <div class="flex items-center gap-2.5 min-w-0">
                                            <span class="w-1.5 h-1.5 rounded-full transition shrink-0"
                                                  :class="eventType === item.id ? 'bg-brand-600' : 'bg-slate-300 group-hover:bg-slate-400'"></span>
                                            <span class="truncate" x-text="item.label"></span>
                                        </div>
                                        <template x-if="eventType === item.id">
                                            <svg class="w-4 h-4 text-brand-600 shrink-0 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                            </svg>
                                        </template>
                                    </button>
                                </template>
                            </div>
                        </div>
                    </div>

                    <!-- Step 1 Inline Error Alert -->
                    <div x-show="formErrors.step1" x-cloak x-transition
                         class="p-3.5 rounded-xl bg-rose-50 border border-rose-200 text-xs text-rose-700 flex items-center gap-2.5 shadow-subtle">
                        <svg class="w-4 h-4 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        <span class="font-medium" x-text="formErrors.step1"></span>
                    </div>

                    <!-- DESKTOP & TABLET INLINE ACTION FOOTER (RIGHT BENEATH THE CALENDAR!) -->
                    <div x-show="step === 1" class="hidden md:flex items-center justify-end pt-3 border-t border-slate-100">
                        <button type="button" @click="goToStep(2)" :disabled="!selectedDate || isDateBooked || !eventType"
                                class="px-6 py-3 rounded-xl bg-brand-600 hover:bg-brand-700 disabled:bg-slate-200 disabled:text-slate-400 active:scale-95 text-white font-bold text-xs tracking-wide transition flex items-center gap-2 cursor-pointer shadow-subtle">
                            <span x-text="eventType === 'sewa_alat' ? 'Lanjut ke Pilih Alat' : 'Lanjut ke Pilih Paket'"></span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                        </button>
                    </div>

                </div>

            </div>
