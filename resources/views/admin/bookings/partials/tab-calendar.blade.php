    <!-- ================================================================== -->
    <!-- TAB 3: CALENDAR (DYNAMIC REAL MONTHLY GRID + CONFLICT ALERT ⚠)     -->
    <!-- ================================================================== -->
    <div x-show="$store.nav.tab === 'calendar'"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 translate-y-1"
         x-transition:enter-end="opacity-100 translate-y-0"
         class="bg-white rounded-[28px] shadow-card border border-[#EDF2F0] overflow-hidden"
         x-cloak>

        <div class="px-4 sm:px-6 py-3.5 sm:py-4 border-b border-[#EFF4F2] flex flex-col lg:flex-row lg:items-center justify-between gap-3 sm:gap-4">
            <!-- Sisi Kiri di Desktop: Month Navigator + Controls (Mode & View Dropdown, Today) -->
            <div class="flex flex-col lg:flex-row lg:items-center gap-2.5 sm:gap-3 w-full lg:w-auto">
                <!-- Row 1 di Mobile: Navigator Bulan + Today Button (mobile) -->
                <div class="flex items-center justify-between gap-2 w-full lg:w-auto lg:gap-3">
                    <!-- Navigator Bulan -->
                    <div class="flex items-center gap-1.5 sm:gap-2.5">
                        <button type="button" @click="prevCalMonth()"
                                class="w-8 h-8 rounded-full hover:bg-[#F4F7F6] active:scale-90 text-slate-500 flex items-center justify-center transition cursor-pointer shrink-0"
                                title="Previous Month">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M15 19l-7-7 7-7"/></svg>
                        </button>

                        <!-- Mini Date Sheet (hanya tampil di sm ke atas agar di HP tidak sesak) -->
                        <div class="hidden sm:block w-11 rounded-xl border border-[#E5ECE9] overflow-hidden text-center shadow-2xs shrink-0">
                            <div class="bg-[#F4F7F6] text-[9px] font-bold uppercase tracking-wider text-slate-500 py-0.5"
                                 x-text="monthShort[calMonth - 1]"></div>
                            <div class="bg-white text-xs font-extrabold text-slate-900 py-1 leading-none"
                                 x-text="(calYear === currentRealYear && calMonth === currentRealMonth) ? currentRealDay : '01'"></div>
                        </div>

                        <div class="min-w-0">
                            <h2 class="text-sm sm:text-[15px] font-bold text-slate-900 leading-tight whitespace-nowrap" x-text="calHeaderLabel"></h2>
                            <p class="hidden sm:block text-[11px] font-medium text-slate-400 mt-0.5 whitespace-nowrap" x-text="calRangeLabel"></p>
                        </div>

                        <button type="button" @click="nextCalMonth()"
                                class="w-8 h-8 rounded-full hover:bg-[#F4F7F6] active:scale-90 text-slate-500 flex items-center justify-center transition cursor-pointer shrink-0"
                                title="Next Month">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M9 5l7 7-7 7"/></svg>
                        </button>
                    </div>

                    <!-- Tombol Today (tampil di mobile di Row 1 sebelah kanan agar praktis) -->
                    <button type="button"
                            @click="goToTodayCalendar()"
                            :class="(calYear === currentRealYear && calMonth === currentRealMonth) ? 'bg-[#F4F7F6] text-slate-400 border-[#E5ECE9] cursor-default' : 'bg-white hover:bg-[#F4F7F6] text-slate-700 border-[#E5ECE9] cursor-pointer shadow-2xs active:scale-95'"
                            class="lg:hidden px-3 py-1.5 rounded-xl border text-xs font-semibold transition shrink-0">
                        {{ __('admin.calendar.today') }}
                    </button>
                </div>

                <!-- Row 2 di Mobile (2 Kolom Grid) / Inline di Desktop (di sebelah Navigator Bulan) -->
                <div class="grid grid-cols-2 gap-2 sm:flex sm:items-center sm:gap-2 w-full lg:w-auto shrink-0">
                    <!-- Mode Switcher Dropdown (Event Schedule vs Field Operations) -->
                    <div class="relative w-full sm:w-auto" @click.away="calendarModeOpen = false">
                        <button type="button"
                                @click="calendarModeOpen = !calendarModeOpen"
                                class="w-full sm:w-auto inline-flex items-center justify-between gap-2 bg-white hover:bg-[#FAFDFC] rounded-xl border border-[#E5ECE9] px-3 py-2 text-xs font-semibold text-slate-700 transition cursor-pointer shadow-2xs">
                            <span class="truncate" x-text="calendarMode === 'events' ? '{{ __('admin.calendar.event_schedule') }}' : '{{ __('admin.calendar.field_operations') }}'"></span>
                            <svg class="w-3.5 h-3.5 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-show="calendarModeOpen" class="absolute left-0 mt-2 w-48 bg-white rounded-2xl shadow-xl border border-[#EAF0EE] py-1.5 z-30" x-cloak>
                            @if($isOwner ?? false)
                            <button type="button"
                                    @click="calendarMode = 'events'; calendarModeOpen = false"
                                    :class="calendarMode === 'events' ? 'bg-brand-50 text-brand-700 font-semibold' : 'text-slate-600 hover:bg-[#F6F8F7]'"
                                    class="w-full text-left px-4 py-2 text-xs transition flex items-center justify-between cursor-pointer">
                                <span>{{ __('admin.calendar.event_schedule') }}</span>
                                <span x-show="calendarMode === 'events'" class="text-brand-600 font-bold">✓</span>
                            </button>
                            <button type="button"
                                    @click="calendarMode = 'operations'; calendarModeOpen = false"
                                    :class="calendarMode === 'operations' ? 'bg-brand-50 text-brand-700 font-semibold' : 'text-slate-600 hover:bg-[#F6F8F7]'"
                                    class="w-full text-left px-4 py-2 text-xs transition flex items-center justify-between cursor-pointer">
                                <span>{{ __('admin.calendar.field_operations') }}</span>
                                <span x-show="calendarMode === 'operations'" class="text-brand-600 font-bold">✓</span>
                            </button>
                            @else
                            <button type="button"
                                    @click="calendarMode = 'operations'; calendarModeOpen = false"
                                    :class="calendarMode === 'operations' ? 'bg-brand-50 text-brand-700 font-semibold' : 'text-slate-600 hover:bg-[#F6F8F7]'"
                                    class="w-full text-left px-4 py-2 text-xs transition flex items-center justify-between cursor-pointer">
                                <span>{{ __('admin.calendar.field_operations') }}</span>
                                <span x-show="calendarMode === 'operations'" class="text-brand-600 font-bold">✓</span>
                            </button>
                            <button type="button"
                                    @click="calendarMode = 'events'; calendarModeOpen = false"
                                    :class="calendarMode === 'events' ? 'bg-brand-50 text-brand-700 font-semibold' : 'text-slate-600 hover:bg-[#F6F8F7]'"
                                    class="w-full text-left px-4 py-2 text-xs transition flex items-center justify-between cursor-pointer">
                                <span>{{ __('admin.calendar.event_schedule') }}</span>
                                <span x-show="calendarMode === 'events'" class="text-brand-600 font-bold">✓</span>
                            </button>
                            @endif
                        </div>
                    </div>

                    <!-- Tombol Today (hanya tampil di desktop di sebelah view dropdown) -->
                    <button type="button"
                            @click="goToTodayCalendar()"
                            :class="(calYear === currentRealYear && calMonth === currentRealMonth) ? 'bg-[#F4F7F6] text-slate-400 border-[#E5ECE9] cursor-default' : 'bg-white hover:bg-[#F4F7F6] text-slate-700 border-[#E5ECE9] cursor-pointer shadow-2xs active:scale-95'"
                            class="hidden lg:inline-flex px-3 py-2 rounded-xl border text-xs font-semibold transition shrink-0">
                        {{ __('admin.calendar.today') }}
                    </button>

                    <!-- View Switcher Dropdown (Monthly View vs Agenda List) -->
                    <div class="relative w-full sm:w-auto" @click.away="calendarViewOpen = false">
                        <button type="button"
                                @click="calendarViewOpen = !calendarViewOpen"
                                class="w-full sm:w-auto inline-flex items-center justify-between gap-2 bg-white hover:bg-[#FAFDFC] rounded-xl border border-[#E5ECE9] px-3 py-2 text-xs font-semibold text-slate-700 transition cursor-pointer shadow-2xs">
                            <span x-text="calendarView === 'Monthly View' ? '{{ __('admin.calendar.monthly_short') }}' : '{{ __('admin.calendar.agenda_short') }}'" class="sm:hidden"></span>
                            <span x-text="tCalendarView(calendarView)" class="hidden sm:inline truncate"></span>
                            <svg class="w-3.5 h-3.5 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-show="calendarViewOpen" class="absolute right-0 sm:left-0 mt-2 w-40 bg-white rounded-2xl shadow-xl border border-[#EAF0EE] py-1.5 z-30" x-cloak>
                            <template x-for="v in ['Monthly View', 'Agenda List']" :key="v">
                                <button type="button" @click="calendarView = v; calendarViewOpen = false"
                                        :class="calendarView === v ? 'bg-brand-50 text-brand-700 font-semibold' : 'text-slate-600 hover:bg-[#F6F8F7]'"
                                        class="w-full text-left px-4 py-2 text-xs transition flex items-center justify-between cursor-pointer">
                                    <span x-text="tCalendarView(v)"></span>
                                    <span x-show="calendarView === v" class="text-brand-600 font-bold">✓</span>
                                </button>
                            </template>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sisi Kanan di Desktop: Legend + Export Actions / Row 3 di Mobile -->
            <div class="w-full lg:w-auto flex flex-col sm:flex-row sm:items-center sm:justify-between lg:justify-end gap-2.5 lg:gap-4 shrink-0">
                <!-- Legend Dinamis (Acara vs Field Operations) -->
                <div x-show="calendarMode === 'events'"
                     class="grid {{ ($isOwner ?? false) ? 'grid-cols-4' : 'grid-cols-2' }} gap-1 py-1.5 px-2 bg-[#F8FAFA] border border-[#EAF0EE] rounded-xl text-[11px] font-medium text-slate-600 lg:bg-transparent lg:border-0 lg:p-0 lg:flex lg:flex-wrap lg:items-center lg:gap-3.5 lg:text-xs">
                    @if($isOwner ?? false)
                    <span class="inline-flex items-center justify-center lg:justify-start gap-1.5 whitespace-nowrap">
                        <span class="w-2 h-2 sm:w-2.5 sm:h-2.5 rounded-[3px] bg-blue-600 shrink-0"></span>
                        {{ __('admin.calendar.legend_inquiry') }}
                    </span>
                    @endif
                    <span class="inline-flex items-center justify-center lg:justify-start gap-1.5 whitespace-nowrap">
                        <span class="w-2 h-2 sm:w-2.5 sm:h-2.5 rounded-[3px] bg-emerald-500 shrink-0"></span>
                        {{ __('admin.calendar.legend_confirmed') }}
                    </span>
                    <span class="inline-flex items-center justify-center lg:justify-start gap-1.5 whitespace-nowrap">
                        <span class="w-2 h-2 sm:w-2.5 sm:h-2.5 rounded-[3px] bg-purple-500 shrink-0"></span>
                        {{ __('admin.calendar.legend_complete') }}
                    </span>
                    @if($isOwner ?? false)
                    <span class="inline-flex items-center justify-center lg:justify-start gap-1.5 whitespace-nowrap">
                        <span class="w-2 h-2 sm:w-2.5 sm:h-2.5 rounded-[3px] bg-rose-500 shrink-0"></span>
                        {{ __('admin.calendar.legend_cancelled') }}
                    </span>
                    @endif
                </div>

                <div x-show="calendarMode === 'operations'"
                     class="grid grid-cols-4 gap-1 py-1.5 px-2 bg-[#F8FAFA] border border-[#EAF0EE] rounded-xl text-[11px] font-medium text-slate-600 lg:bg-transparent lg:border-0 lg:p-0 lg:flex lg:flex-wrap lg:items-center lg:gap-3.5 lg:text-xs">
                    <span class="inline-flex items-center justify-center lg:justify-start gap-1.5 whitespace-nowrap">
                        <span class="w-2 h-2 sm:w-2.5 sm:h-2.5 rounded-[3px] bg-purple-600 shrink-0"></span>
                        {{ __('admin.calendar.legend_survey') }}
                    </span>
                    <span class="inline-flex items-center justify-center lg:justify-start gap-1.5 whitespace-nowrap">
                        <span class="w-2 h-2 sm:w-2.5 sm:h-2.5 rounded-[3px] bg-emerald-500 shrink-0"></span>
                        {{ __('admin.calendar.legend_survey_done') }}
                    </span>
                    <span class="inline-flex items-center justify-center lg:justify-start gap-1.5 whitespace-nowrap">
                        <span class="w-2 h-2 sm:w-2.5 sm:h-2.5 rounded-[3px] bg-blue-600 shrink-0"></span>
                        {{ __('admin.calendar.legend_setup') }}
                    </span>
                    <span class="inline-flex items-center justify-center lg:justify-start gap-1.5 whitespace-nowrap">
                        <span class="w-2 h-2 sm:w-2.5 sm:h-2.5 rounded-[3px] bg-amber-500 shrink-0"></span>
                        {{ __('admin.calendar.legend_teardown') }}
                    </span>
                </div>

                <!-- Action Button: Download Schedule PDF (Secondary Style) -->
                <div class="shrink-0">
                    <a :href="calendarExportPdfUrl"
                       target="_blank"
                       title="{{ __('admin.calendar.download_pdf') }}"
                       aria-label="{{ __('admin.calendar.download_pdf') }}"
                       class="w-full sm:w-auto inline-flex items-center justify-center gap-1.5 px-3 py-2 rounded-xl bg-white hover:bg-[#F4F7F6] text-slate-700 border border-[#E5ECE9] text-xs font-semibold transition shadow-2xs active:scale-95 whitespace-nowrap">
                        <svg class="w-3.5 h-3.5 text-slate-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                        </svg>
                        <span>PDF</span>
                    </a>
                </div>
            </div>
    </div>

        <!-- MONTHLY VIEW GRID -->
        <div x-show="calendarView === 'Monthly View'" class="overflow-x-auto">
            <div class="min-w-[780px]">
                <div class="grid grid-cols-7 border-b border-[#EFF4F2] bg-[#FCFDFD]">
                    <template x-for="dName in dayNamesShort" :key="dName">
                        <div class="py-3 text-center text-xs font-semibold text-slate-700" x-text="dName"></div>
                    </template>
                </div>

                <div class="grid grid-cols-7">
                    <template x-for="cell in calendarCells" :key="cell.key">
                        <div :class="{
                                'bg-[#FAFDFC]/60': cell.blank,
                                'bg-[#F4F9F7] ring-1 ring-brand-300 ring-inset': !cell.blank && cell.isToday,
                                'bg-white hover:bg-[#F8FBFA]': !cell.blank && !cell.isToday
                             }"
                             :title="cell.blank ? '' : (calendarMode === 'events' ? 'Click date to view ' + cell.events.length + ' bookings' : 'Click date to view ' + cell.operations.length + ' field operations')"
                             @click="if (!cell.blank) openDayModal(cell)"
                             class="min-h-[120px] p-2 border-r border-b border-[#EFF4F2] transition-colors flex flex-col gap-1.5 cursor-pointer select-none group relative">
                            <template x-if="!cell.blank">
                                <div class="flex flex-col gap-1.5 flex-1">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center gap-1.5">
                                            <span :class="cell.isToday
                                                    ? 'w-6 h-6 rounded-full bg-brand-600 text-white text-[11px] font-bold inline-flex items-center justify-center shadow-xs'
                                                    : ((calendarMode === 'events' ? cell.events.length : cell.operations.length) > 0
                                                        ? 'w-6 h-6 rounded-full bg-slate-900 text-white text-[11px] font-bold inline-flex items-center justify-center group-hover:bg-brand-600 transition'
                                                        : 'text-xs font-bold text-slate-800')"
                                                  x-text="cell.day"></span>
                                            <template x-if="cell.isToday">
                                                <span class="px-1.5 py-0.5 rounded-full text-[9.5px] font-bold bg-brand-50 text-brand-700 border border-brand-200">
                                                    {{ __('admin.calendar.today') }}
                                                </span>
                                            </template>
                                        </div>
                                        <template x-if="cell.hasConflict && calendarMode === 'events'">
                                            <span title="Daily capacity limit exceeded!" class="text-orange-500">
                                                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor">
                                                    <path d="M12 2L1 21h22L12 2zm0 4.5l7.5 13h-15l7.5-13zM11 10v5h2v-5h-2zm0 6v2h2v-2h-2z"/>
                                                </svg>
                                            </span>
                                        </template>
                                    </div>

                                    <!-- Mode: Event Schedule Chips -->
                                    <div class="space-y-1" x-show="calendarMode === 'events'">
                                        <template x-for="ev in cell.events.slice(0, 2)" :key="ev.id">
                                            <button type="button"
                                                    @click.stop="openDetail(ev)"
                                                    :class="{
                                                        'bg-[#E6F6EF] hover:bg-[#D7F0E5] text-emerald-800': ev.bookingStatus === 'Confirmed',
                                                        'bg-[#F3EAFF] hover:bg-[#E9DAFF] text-purple-800': ev.bookingStatus === 'Complete',
                                                        'bg-[#EAF3FF] hover:bg-[#DCEBFF] text-blue-800': ev.bookingStatus === 'Inquiry',
                                                        'bg-[#FEECEB] hover:bg-[#FCDCD9] text-rose-700': ev.bookingStatus === 'Cancelled'
                                                    }"
                                                    class="w-full text-left px-2 py-1 rounded-lg transition cursor-pointer text-xs block group/chip">
                                                <p class="text-[10px] font-bold leading-tight truncate" x-text="'#' + ev.code + ' · ' + ev.shortName + (ev.duration_days > 1 ? ' (' + ev.duration_days + '{{ __('admin.calendar.day_short') }})' : '')"></p>
                                                <p class="text-[9.5px] opacity-80 leading-tight truncate mt-0.5" x-text="ev.service"></p>
                                            </button>
                                        </template>

                                        <!-- "+X more" Button if > 2 -->
                                        <template x-if="cell.events.length > 2">
                                            <button type="button"
                                                    @click.stop="openDayModal(cell)"
                                                    class="w-full py-1 px-2 rounded-lg bg-[#F4F7F6] hover:bg-brand-50 hover:text-brand-700 hover:border-brand-300 text-slate-700 font-bold text-[10px] text-center transition flex items-center justify-center gap-1 border border-[#E2EAE7] cursor-pointer">
                                                <span x-text="'+ ' + (cell.events.length - 2) + ' {{ __('admin.calendar.more_items') }}'"></span>
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M9 5l7 7-7 7"/></svg>
                                            </button>
                                        </template>
                                    </div>

                                    <!-- Mode: Field Operations Chips (Pasang H-2, Hari H, Bongkar H+1) -->
                                    <div class="space-y-1" x-show="calendarMode === 'operations'">
                                        <template x-for="op in cell.operations.slice(0, 2)" :key="op.id">
                                            <button type="button"
                                                    @click.stop="openDetail(op.booking)"
                                                    :class="op.chipClass"
                                                    class="w-full text-left px-2 py-1 rounded-lg transition cursor-pointer text-xs block group/chip">
                                                <p class="text-[10px] font-bold leading-tight truncate" x-text="op.chipTitle"></p>
                                                <p class="text-[9.5px] opacity-80 leading-tight truncate mt-0.5" x-text="op.chipSubtitle"></p>
                                            </button>
                                        </template>

                                        <!-- "+X more" Button if > 2 -->
                                        <template x-if="cell.operations.length > 2">
                                            <button type="button"
                                                    @click.stop="openDayModal(cell)"
                                                    class="w-full py-1 px-2 rounded-lg bg-[#F4F7F6] hover:bg-brand-50 hover:text-brand-700 hover:border-brand-300 text-slate-700 font-bold text-[10px] text-center transition flex items-center justify-center gap-1 border border-[#E2EAE7] cursor-pointer">
                                                <span x-text="'+ ' + (cell.operations.length - 2) + ' {{ __('admin.calendar.more_items') }}'"></span>
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M9 5l7 7-7 7"/></svg>
                                            </button>
                                        </template>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </template>
                </div>
            </div>
        </div>

        <!-- AGENDA LIST VIEW -->
        <div x-show="calendarView === 'Agenda List'" class="p-4 sm:p-6 space-y-3 sm:space-y-4">
            <!-- Mode 1: Event Bookings Agenda -->
            <div x-show="calendarMode === 'events'" class="space-y-3 sm:space-y-4">
                <div class="flex items-center justify-between gap-3 pb-3 border-b border-[#EFF4F2]">
                    <div>
                        <h3 class="text-sm font-bold text-slate-900 leading-snug">
                            <span class="sm:hidden" x-text="'{{ __('admin.calendar.agenda_short') }} · ' + calHeaderLabel"></span>
                            <span class="hidden sm:inline" x-text="'{{ __('admin.calendar.monthly_agenda') }} · ' + calHeaderLabel"></span>
                        </h3>
                        <p class="text-[11px] text-slate-400 sm:hidden">{{ __('admin.calendar.all_scheduled') }}</p>
                    </div>
                    <span class="px-2.5 py-1 rounded-full bg-[#F4F7F6] border border-[#E8EEEB] text-xs font-bold text-slate-700 whitespace-nowrap shrink-0"
                          x-text="monthBookings.length + ' {{ __('admin.calendar.bookings_unit') }}'"></span>
                </div>

                <template x-if="monthBookings.length === 0">
                    <div class="py-12 text-center text-xs text-slate-400">
                        {{ __('admin.calendar.no_events_month') }}
                    </div>
                </template>

                <div class="space-y-3" x-show="monthBookings.length > 0">
                    <template x-for="ev in monthBookings" :key="'agenda-' + ev.id">
                        <div @click="openDetail(ev)"
                             class="p-3.5 sm:p-4 rounded-2xl bg-[#FAFDFC] hover:bg-white border border-[#EDF2F0] hover:border-brand-300 hover:shadow-card transition cursor-pointer flex flex-col sm:flex-row sm:items-center justify-between gap-3 sm:gap-4 group">
                            
                            <!-- Bagian Atas / Kiri: Date Sheet + Info Reservasi -->
                            <div class="flex items-center gap-3 sm:gap-3.5 min-w-0">
                                <!-- Date Sheet -->
                                <div class="w-11 h-11 sm:w-12 sm:h-12 rounded-xl bg-white border border-[#E5ECE9] flex flex-col items-center justify-center text-center shrink-0 shadow-2xs group-hover:border-brand-200 transition">
                                    <span class="text-[9px] font-bold text-slate-400 uppercase leading-none" x-text="monthShort[ev.event_month - 1]"></span>
                                    <span class="text-sm sm:text-base font-extrabold text-slate-900 leading-none mt-0.5" x-text="ev.event_day"></span>
                                </div>

                                <!-- Text Info -->
                                <div class="min-w-0 flex-1">
                                    <div class="flex items-center gap-1.5 sm:gap-2 flex-wrap">
                                        <span class="text-xs font-bold font-mono text-[#0284C7]" x-text="'#' + ev.code"></span>
                                        <span class="text-xs sm:text-sm font-bold text-slate-900 truncate" x-text="ev.name"></span>
                                        <span x-show="ev.duration_days > 1" class="px-1.5 py-0.2 rounded-full text-[9.5px] font-bold bg-amber-50 text-amber-700 border border-amber-200" x-text="ev.duration_days + ' {{ __('admin.calendar.days_unit') }}'"></span>
                                    </div>
                                    <p class="text-[11.5px] sm:text-xs text-slate-500 truncate mt-0.5" x-text="ev.service + ' · ' + ev.date"></p>
                                </div>
                            </div>

                            <!-- Bagian Bawah di Mobile / Kanan di Desktop: Keuangan & Badge Status -->
                            <div class="border-t border-[#F0F4F2] pt-2.5 sm:border-t-0 sm:pt-0 flex items-center justify-between sm:justify-end gap-3 shrink-0">
                                <div class="flex items-baseline sm:block sm:text-right gap-1.5">
                                    <p class="text-xs sm:text-xs font-bold text-slate-900" x-text="ev.grand_total_formatted"></p>
                                    <span class="text-[10px] sm:text-[11px] font-semibold px-1.5 py-0.2 sm:px-0 sm:py-0 rounded sm:rounded-none inline-block"
                                          :class="ev.remaining_balance <= 0 ? 'text-emerald-700 bg-emerald-50 sm:bg-transparent' : 'text-amber-700 bg-amber-50 sm:bg-transparent'"
                                          x-text="ev.remaining_balance <= 0 ? tInvoiceStatus('Paid') : '{{ __('admin.calendar.due_prefix') }} ' + ev.remaining_formatted"></span>
                                </div>

                                <div class="flex items-center gap-2">
                                    <span :class="{
                                            'bg-[#E6F6EF] text-emerald-700': ev.bookingStatus === 'Confirmed',
                                            'bg-[#F3EAFF] text-purple-600': ev.bookingStatus === 'Complete',
                                            'bg-[#EAF3FF] text-blue-600': ev.bookingStatus === 'Inquiry',
                                            'bg-[#FEECEB] text-rose-600': ev.bookingStatus === 'Cancelled'
                                          }"
                                          class="px-2.5 sm:px-3 py-1 rounded-full text-[11px] font-semibold whitespace-nowrap"
                                          x-text="tStatus(ev.bookingStatus)"></span>
                                    <svg class="w-4 h-4 text-slate-300 group-hover:text-brand-600 group-hover:translate-x-0.5 transition hidden sm:block shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                    </svg>
                                </div>
                            </div>

                        </div>
                    </template>
                </div>
            </div>

            <!-- Mode 2: Field Operations Agenda -->
            <div x-show="calendarMode === 'operations'" class="space-y-3 sm:space-y-4">
                <div class="flex items-center justify-between gap-3 pb-3 border-b border-[#EFF4F2]">
                    <div>
                        <h3 class="text-sm font-bold text-slate-900 leading-snug">
                            <span class="sm:hidden" x-text="'{{ __('admin.calendar.field_operations') }} · ' + calHeaderLabel"></span>
                            <span class="hidden sm:inline" x-text="'{{ __('admin.calendar.monthly_ops') }} · ' + calHeaderLabel"></span>
                        </h3>
                        <p class="text-[11px] text-slate-400">{{ __('admin.calendar.ops_subtitle') }}</p>
                    </div>
                    <span class="px-2.5 py-1 rounded-full bg-[#F4F7F6] border border-[#E8EEEB] text-xs font-bold text-slate-700 whitespace-nowrap shrink-0"
                          x-text="monthOperations.length + ' {{ __('admin.calendar.tasks_unit') }}'"></span>
                </div>

                <template x-if="monthOperations.length === 0">
                    <div class="py-12 text-center text-xs text-slate-400">
                        {{ __('admin.calendar.no_ops_month') }}
                    </div>
                </template>

                <div class="space-y-3" x-show="monthOperations.length > 0">
                    <template x-for="op in monthOperations" :key="op.key">
                        <div @click="openDetail(op.booking)"
                             class="p-3.5 sm:p-4 rounded-2xl bg-[#FAFDFC] hover:bg-white border border-[#EDF2F0] hover:border-brand-300 hover:shadow-card transition flex flex-col sm:flex-row sm:items-center justify-between gap-3 sm:gap-4 cursor-pointer group">
                            
                            <!-- Date Sheet + Info Operasional -->
                            <div class="flex items-center gap-3 sm:gap-3.5 min-w-0 flex-1">
                                <div class="w-11 h-11 sm:w-12 sm:h-12 rounded-xl bg-white border border-[#E5ECE9] flex flex-col items-center justify-center text-center shrink-0 shadow-2xs group-hover:border-brand-200 transition">
                                    <span class="text-[9px] font-bold text-slate-400 uppercase leading-none" x-text="monthShort[op.month - 1]"></span>
                                    <span class="text-sm sm:text-base font-extrabold text-slate-900 leading-none mt-0.5" x-text="op.day"></span>
                                </div>

                                <div class="min-w-0 flex-1">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <span class="text-xs font-bold font-mono text-[#0284C7]" x-text="'#' + op.booking.code"></span>
                                        <span class="text-xs sm:text-sm font-bold text-slate-900 truncate" x-text="op.booking.name"></span>
                                    </div>
                                    <p class="text-[11.5px] text-slate-500 truncate mt-0.5">
                                        <span class="font-semibold text-slate-700" x-text="op.booking.service"></span>
                                        <span class="text-slate-400">&bull;</span>
                                        <span x-text="op.booking.venue_address"></span>
                                    </p>
                                </div>
                            </div>

                            <!-- Status Badge & Chevron -->
                            <div class="flex items-center justify-between sm:justify-end gap-3 shrink-0">
                                <span :class="op.badgeClass"
                                      class="px-2.5 sm:px-3 py-1 rounded-full text-[11px] font-semibold whitespace-nowrap"
                                      x-text="op.statusLabel"></span>
                                <svg class="w-4 h-4 text-slate-300 group-hover:text-brand-600 group-hover:translate-x-0.5 transition hidden sm:block shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </div>

                        </div>
                    </template>
                </div>
            </div>
        </div>

        <!-- ================================================================== -->
        <!-- MODAL: DETAIL AGENDA TANGGAL / DAY BOOKINGS MODAL                  -->
        <!-- ================================================================== -->
        <div x-show="selectedDayModal"
             @keydown.window.escape="closeDayModal()"
             class="fixed inset-0 z-50 overflow-y-auto"
             x-cloak>
            <!-- Backdrop Blur -->
            <div class="fixed inset-0 bg-slate-900/40 backdrop-blur-xs transition-opacity"
                 @click="closeDayModal()"
                 x-transition:enter="ease-out duration-200"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="ease-in duration-150"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"></div>

            <!-- Modal Dialog Box -->
            <div class="min-h-full flex items-center justify-center p-4 sm:p-6 text-center">
                <div @click.stop
                     x-transition:enter="ease-out duration-200"
                     x-transition:enter-start="opacity-0 scale-95 translate-y-2"
                     x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                     x-transition:leave="ease-in duration-150"
                     x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                     x-transition:leave-end="opacity-0 scale-95 translate-y-2"
                     class="relative bg-white rounded-[24px] sm:rounded-[28px] max-w-lg w-full p-4 sm:p-6 text-left shadow-2xl border border-[#EDF2F0] space-y-4 transform transition-all overflow-hidden">

                    <!-- Header Modal -->
                    <div class="flex items-start justify-between gap-3 pb-3 sm:pb-4 border-b border-[#EFF4F2]">
                        <div class="flex items-center gap-3.5">
                            <div class="w-12 h-12 rounded-2xl bg-brand-50 border border-brand-100 text-brand-700 flex flex-col items-center justify-center text-center shrink-0 shadow-2xs">
                                <span class="text-[9px] font-bold uppercase leading-none" x-text="selectedDayModal ? monthShort[calMonth - 1] : ''"></span>
                                <span class="text-base font-extrabold leading-none mt-0.5 text-slate-900" x-text="selectedDayModal?.day"></span>
                            </div>
                            <div>
                                <div class="flex items-center gap-2">
                                    <h3 class="text-base sm:text-lg font-bold text-slate-900 tracking-tight" x-text="selectedDayModal?.dateFull"></h3>
                                    <template x-if="selectedDayModal?.isToday">
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-brand-100 text-brand-800">{{ __('admin.calendar.today') }}</span>
                                    </template>
                                </div>
                                <p class="text-xs text-slate-500 mt-0.5"
                                   x-text="calendarMode === 'operations' 
                                       ? ((selectedDayModal?.operations?.length || 0) + ' {{ __('admin.calendar.field_ops_unit') }}') 
                                       : ((selectedDayModal?.events?.length || 0) + ' {{ __('admin.calendar.scheduled_bookings') }}')"></p>
                            </div>
                        </div>
                        <button type="button"
                                @click="closeDayModal()"
                                class="w-8 h-8 rounded-full bg-[#F4F7F6] hover:bg-[#EAEFEA] text-slate-500 hover:text-slate-800 flex items-center justify-center transition cursor-pointer shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>

                    <!-- Quota / Conflict Banner (Hanya di mode Acara) -->
                    <template x-if="calendarMode === 'events' && selectedDayModal && (selectedDayModal.events.filter(e => e.raw_status !== 'rejected').length > maxEventsPerDay)">
                        <div class="p-3.5 rounded-2xl bg-amber-50 border border-amber-200 text-amber-900 flex items-start gap-3 text-xs">
                            <span class="text-base shrink-0 mt-0.5">⚠️</span>
                            <div>
                                <p class="font-bold">Warning: Daily Capacity Limit Exceeded!</p>
                                <p class="text-[11.5px] text-amber-800 mt-0.5"
                                   x-text="'There are ' + selectedDayModal.events.filter(e => e.raw_status !== 'rejected').length + ' events on this date (Standard capacity: ' + maxEventsPerDay + ' events/day). Ensure crew and inventory readiness.'"></p>
                            </div>
                        </div>
                    </template>

                    <!-- MODE 1: BOOKING EVENTS CARDS -->
                    <div x-show="calendarMode === 'events'" class="max-h-[55vh] overflow-y-auto space-y-3 pr-1 divide-y divide-[#F4F7F6]">
                        <template x-if="selectedDayModal?.events?.length === 0">
                            <div class="py-12 text-center space-y-2">
                                <div class="w-12 h-12 rounded-2xl bg-[#F4F7F6] text-slate-400 mx-auto flex items-center justify-center">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                </div>
                                <p class="text-xs font-semibold text-slate-700">{{ __('admin.calendar.no_bookings_date') }}</p>
                                <p class="text-[11px] text-slate-400">{{ __('admin.calendar.date_open_desc') }}</p>
                            </div>
                        </template>

                        <template x-for="(ev, idx) in (selectedDayModal?.events || [])" :key="ev.id">
                            <div class="pt-3 first:pt-0">
                                <div class="p-4 rounded-2xl bg-[#FAFDFC] hover:bg-white border border-[#EAF0EE] hover:border-brand-300 hover:shadow-card transition space-y-3 group">
                                    <div class="flex items-center justify-between gap-2">
                                        <div class="flex items-center gap-2">
                                            <span class="text-xs font-bold font-mono text-[#0284C7]" x-text="'#' + ev.code"></span>
                                            <template x-if="ev.duration_days > 1">
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-semibold bg-amber-50 text-amber-700 border border-amber-200"
                                                      x-text="ev.date_short"></span>
                                            </template>
                                        </div>
                                        <span :class="{
                                                'bg-[#E6F6EF] text-emerald-700': ev.bookingStatus === 'Confirmed',
                                                'bg-[#F3EAFF] text-purple-600': ev.bookingStatus === 'Complete',
                                                'bg-[#EAF3FF] text-blue-600': ev.bookingStatus === 'Inquiry',
                                                'bg-[#FEECEB] text-rose-600': ev.bookingStatus === 'Cancelled'
                                              }"
                                              class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-semibold">
                                            <span class="w-1.5 h-1.5 rounded-full"
                                                  :class="{
                                                    'bg-emerald-500': ev.bookingStatus === 'Confirmed',
                                                    'bg-purple-500': ev.bookingStatus === 'Complete',
                                                    'bg-blue-500': ev.bookingStatus === 'Inquiry',
                                                    'bg-rose-500': ev.bookingStatus === 'Cancelled'
                                                  }"></span>
                                            <span x-text="tStatus(ev.bookingStatus)"></span>
                                        </span>
                                    </div>

                                    <div class="flex items-start justify-between gap-3">
                                        <div class="flex items-center gap-3 min-w-0">
                                            <img :src="ev.avatar" :alt="ev.name"
                                                 class="w-10 h-10 rounded-xl object-cover shrink-0">
                                            <div class="min-w-0">
                                                <p class="text-xs sm:text-sm font-bold text-slate-900 truncate" x-text="ev.name"></p>
                                                <p class="text-[11px] text-slate-500 truncate" x-text="ev.phone"></p>
                                            </div>
                                        </div>
                                        <div class="text-right shrink-0">
                                            <p class="text-xs font-bold text-brand-700" x-text="ev.service"></p>
                                            <p class="text-[11px] text-slate-500" x-text="ev.grand_total_formatted"></p>
                                        </div>
                                    </div>

                                    <div class="pt-2.5 border-t border-[#EDF2F0] flex flex-wrap items-center justify-between gap-2.5 text-xs">
                                        <div class="flex items-center gap-2">
                                            <span class="text-[11px] text-slate-400">{{ __('admin.calendar.payment_label') }}</span>
                                            <span class="font-bold text-[11.5px]"
                                                  :class="ev.remaining_balance <= 0 ? 'text-emerald-600' : 'text-amber-600'"
                                                  x-text="ev.remaining_balance <= 0 ? '{{ __('admin.calendar.paid_full') }}' : '{{ __('admin.calendar.due_prefix') }} ' + ev.remaining_formatted"></span>
                                        </div>
                                        <div class="flex items-center gap-2 ml-auto">
                                            <template x-if="ev.whatsapp_url">
                                                <a :href="ev.whatsapp_url" target="_blank"
                                                   class="inline-flex items-center gap-1 px-3 py-1.5 rounded-full bg-emerald-50 hover:bg-emerald-100 text-emerald-700 text-xs font-semibold transition cursor-pointer">
                                                    <span>WhatsApp</span>
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                                </a>
                                            </template>
                                            <button type="button"
                                                    @click="closeDayModal(); openDetail(ev)"
                                                    class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full bg-brand-600 hover:bg-brand-700 text-white text-xs font-semibold transition shadow-xs cursor-pointer">
                                                <span>{{ __('admin.calendar.view_details') }}</span>
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>

                    <!-- MODE 2: FIELD OPERATIONS CARDS (SETUP & TEARDOWN) -->
                    <div x-show="calendarMode === 'operations'" class="max-h-[60vh] overflow-y-auto space-y-3 pr-1 divide-y divide-[#F4F7F6]">
                        <template x-if="selectedDayModal?.operations?.length === 0">
                            <div class="py-12 text-center space-y-2">
                                <div class="w-12 h-12 rounded-2xl bg-[#F4F7F6] text-slate-400 mx-auto flex items-center justify-center">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                </div>
                                <p class="text-xs font-semibold text-slate-700">{{ __('admin.calendar.no_ops_date') }}</p>
                                <p class="text-[11px] text-slate-400">{{ __('admin.calendar.no_ops_date_desc') }}</p>
                            </div>
                        </template>

                        <template x-for="op in (selectedDayModal?.operations || [])" :key="op.id">
                            <div class="pt-3 first:pt-0">
                                <div class="p-3.5 sm:p-4 rounded-2xl bg-white border border-[#EAF0EE] hover:border-brand-300 hover:shadow-card transition space-y-2.5 group">
                                    <!-- Top Row: Code, Date & Pill Badge -->
                                    <div class="flex items-center justify-between gap-2">
                                        <div class="flex items-center gap-2 min-w-0">
                                            <span class="text-xs font-bold font-mono text-[#0284C7] shrink-0" x-text="'#' + op.booking.code"></span>
                                            <span class="text-slate-300 shrink-0">&bull;</span>
                                            <span class="text-[11px] font-semibold text-slate-600 truncate"
                                                  x-text="op.booking.date_short"></span>
                                        </div>
                                        <span :class="op.badgeClass"
                                              class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-semibold shrink-0">
                                            <span class="w-1.5 h-1.5 rounded-full" :class="op.badgeDot"></span>
                                            <span x-text="op.statusLabel"></span>
                                        </span>
                                    </div>

                                    <!-- Middle Row: Customer Info & Service -->
                                    <div class="flex items-start justify-between gap-2.5">
                                        <div class="flex items-center gap-2.5 min-w-0">
                                            <img :src="op.booking.avatar" :alt="op.booking.name"
                                                 class="w-9 h-9 rounded-xl object-cover shrink-0">
                                            <div class="min-w-0">
                                                <p class="text-xs sm:text-sm font-bold text-slate-900 truncate" x-text="op.booking.name"></p>
                                                <p class="text-[11px] text-slate-500 font-mono truncate" x-text="op.booking.phone"></p>
                                            </div>
                                        </div>
                                        <div class="text-right shrink-0">
                                            <p class="text-xs font-bold text-brand-700 truncate max-w-[130px] sm:max-w-none" x-text="op.booking.service"></p>
                                            <p class="text-[11px] text-slate-500 font-mono" x-text="op.booking.grand_total_formatted"></p>
                                        </div>
                                    </div>

                                    <!-- Location & Landmark -->
                                    <div class="text-[11px] sm:text-xs text-slate-600 flex items-start gap-1.5">
                                        <svg class="w-3.5 h-3.5 text-slate-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                        <span class="line-clamp-2" x-text="op.booking.venue_address + (op.booking.address_landmark && op.booking.address_landmark !== '-' ? ' ({{ __('admin.calendar.landmark_prefix') }} ' + op.booking.address_landmark + ')' : '')"></span>
                                    </div>

                                    <!-- SOP Instruction -->
                                    <div class="px-2.5 py-1.5 rounded-xl bg-slate-50 border border-slate-200/60 text-[11px] text-slate-600 flex items-start gap-1.5">
                                        <span class="font-bold text-slate-700 shrink-0">SOP:</span>
                                        <span x-text="op.instruction"></span>
                                    </div>

                                    <!-- Bottom Row: WhatsApp & Detail Buttons -->
                                    <div class="pt-2 border-t border-[#EDF2F0] flex items-center justify-between gap-2 text-xs">
                                        <span class="text-[11px] text-slate-400 font-medium" x-text="op.booking.duration_days === 1 ? '{{ __('admin.calendar.day_event_singular') }}' : (op.booking.duration_days + ' {{ __('admin.calendar.days_event') }}')"></span>
                                        <div class="flex items-center gap-2 ml-auto">
                                            <template x-if="op.booking.whatsapp_url">
                                                <a :href="op.booking.whatsapp_url" target="_blank"
                                                   class="inline-flex items-center gap-1 px-3 py-1.5 rounded-xl bg-emerald-50 hover:bg-emerald-100 text-emerald-700 text-xs font-semibold transition cursor-pointer">
                                                    <span>WhatsApp</span>
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                                </a>
                                            </template>
                                            <button type="button"
                                                    @click="closeDayModal(); openDetail(op.booking)"
                                                    class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl bg-brand-600 hover:bg-brand-700 text-white text-xs font-semibold transition shadow-xs cursor-pointer">
                                                <span>{{ __('admin.calendar.view_details') }}</span>
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>

                    <!-- Footer Modal -->
                    <div class="pt-3 border-t border-[#EFF4F2] flex items-center justify-between gap-3">
                        @if($isOwner ?? false)
                        <a href="{{ route('booking.create') }}" target="_blank"
                           class="inline-flex items-center gap-1.5 text-xs font-semibold text-brand-700 hover:text-brand-800 hover:underline">
                            <span>+ {{ __('admin.bookings.new_booking') }}</span>
                        </a>
                        @else
                        <span></span>
                        @endif
                        <button type="button"
                                @click="closeDayModal()"
                                class="px-5 py-2 rounded-full bg-[#F4F7F6] hover:bg-[#EAEFEA] text-slate-700 text-xs font-semibold transition cursor-pointer">
                            {{ __('admin.nav.close') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

