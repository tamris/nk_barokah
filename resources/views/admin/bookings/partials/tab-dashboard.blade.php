    <!-- ================================================================== -->
    <!-- DASHBOARD SKELETON SHIMMER (FLUTTER STYLE)                         -->
    <!-- ================================================================== -->
    <div x-show="$store.nav.tab === 'dashboard' && pageLoading" class="space-y-6" {{ (request('tab') && request('tab') !== 'dashboard') ? 'x-cloak' : '' }}>
        <!-- Shimmer KPI Strip -->
        <div class="flex flex-col xl:flex-row xl:items-center justify-between gap-4 sm:gap-6 px-0.5 sm:px-1">
            <div class="space-y-2 pt-1 sm:pt-0">
                <div class="h-4 w-28 rounded-md shimmer-mask"></div>
                <div class="h-8 w-56 rounded-xl shimmer-mask"></div>
            </div>
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-2.5 sm:gap-4 xl:gap-0 xl:flex xl:items-center xl:divide-x xl:divide-[#E3EAE7]">
                <div class="bg-white rounded-2xl p-3 sm:p-4 border border-[#EDF2F0] shadow-card xl:bg-transparent xl:border-0 xl:shadow-none xl:rounded-none xl:p-0 flex items-center gap-2.5 sm:gap-3.5 xl:pr-7">
                    <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl sm:rounded-full shimmer-mask shrink-0"></div>
                    <div class="space-y-2 min-w-0">
                        <div class="h-5 sm:h-6 w-12 rounded shimmer-mask"></div>
                        <div class="h-3 w-20 sm:w-24 rounded shimmer-mask"></div>
                    </div>
                </div>
                <div class="bg-white rounded-2xl p-3 sm:p-4 border border-[#EDF2F0] shadow-card xl:bg-transparent xl:border-0 xl:shadow-none xl:rounded-none xl:p-0 flex items-center gap-2.5 sm:gap-3.5 xl:px-7">
                    <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl sm:rounded-full shimmer-mask shrink-0"></div>
                    <div class="space-y-2 min-w-0">
                        <div class="h-5 sm:h-6 w-16 rounded shimmer-mask"></div>
                        <div class="h-3 w-24 sm:w-28 rounded shimmer-mask"></div>
                    </div>
                </div>
                <div class="bg-white rounded-2xl p-3 sm:p-4 border border-[#EDF2F0] shadow-card xl:bg-transparent xl:border-0 xl:shadow-none xl:rounded-none xl:p-0 flex items-center gap-2.5 sm:gap-3.5 xl:px-7">
                    <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl sm:rounded-full shimmer-mask shrink-0"></div>
                    <div class="space-y-2 min-w-0">
                        <div class="h-5 sm:h-6 w-12 rounded shimmer-mask"></div>
                        <div class="h-3 w-24 sm:w-28 rounded shimmer-mask"></div>
                    </div>
                </div>
                <div class="bg-white rounded-2xl p-3 sm:p-4 border border-[#EDF2F0] shadow-card xl:bg-transparent xl:border-0 xl:shadow-none xl:rounded-none xl:p-0 flex items-center gap-2.5 sm:gap-3.5 xl:pl-7">
                    <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl sm:rounded-full shimmer-mask shrink-0"></div>
                    <div class="space-y-2 min-w-0">
                        <div class="h-5 sm:h-6 w-20 sm:w-24 rounded shimmer-mask"></div>
                        <div class="h-3 w-20 sm:w-24 rounded shimmer-mask"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Shimmer 3-Column Bento -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-5 items-stretch">
            <!-- Col 1: Schedule -->
            <div class="bg-white rounded-[28px] p-5 sm:p-6 shadow-card border border-[#EDF2F0] space-y-4">
                <div class="flex items-center justify-between">
                    <div class="h-5 w-32 rounded-md shimmer-mask"></div>
                    <div class="h-4 w-20 rounded-md shimmer-mask"></div>
                </div>
                <div class="h-10 w-full rounded-2xl shimmer-mask"></div>
                <div class="space-y-3">
                    <div class="h-16 w-full rounded-2xl shimmer-mask"></div>
                    <div class="h-16 w-full rounded-2xl shimmer-mask"></div>
                    <div class="h-16 w-full rounded-2xl shimmer-mask"></div>
                </div>
            </div>

            <!-- Col 2: Upcoming Bookings -->
            <div class="bg-white rounded-[28px] p-5 sm:p-6 shadow-card border border-[#EDF2F0] space-y-4">
                <div class="flex items-center justify-between">
                    <div class="h-5 w-36 rounded-md shimmer-mask"></div>
                    <div class="h-4 w-16 rounded-md shimmer-mask"></div>
                </div>
                <div class="space-y-3">
                    <div class="h-14 w-full rounded-2xl shimmer-mask"></div>
                    <div class="h-14 w-full rounded-2xl shimmer-mask"></div>
                    <div class="h-14 w-full rounded-2xl shimmer-mask"></div>
                    <div class="h-14 w-full rounded-2xl shimmer-mask"></div>
                    <div class="h-14 w-full rounded-2xl shimmer-mask"></div>
                </div>
            </div>

            <!-- Col 3: Outstanding Invoices + Business Insight -->
            <div class="flex flex-col gap-5">
                <div class="bg-white rounded-[28px] p-5 sm:p-6 shadow-card border border-[#EDF2F0] space-y-4">
                    <div class="flex items-center justify-between">
                        <div class="h-5 w-36 rounded-md shimmer-mask"></div>
                        <div class="h-4 w-16 rounded-md shimmer-mask"></div>
                    </div>
                    <div class="space-y-3">
                        <div class="h-14 w-full rounded-2xl shimmer-mask"></div>
                        <div class="h-14 w-full rounded-2xl shimmer-mask"></div>
                        <div class="h-14 w-full rounded-2xl shimmer-mask"></div>
                    </div>
                </div>
                <div class="h-32 w-full rounded-[28px] shimmer-mask"></div>
            </div>
        </div>
    </div>

    <!-- ================================================================== -->
    <!-- GREETING + 4 REAL-TIME KPI STRIP (DASHBOARD ONLY)                  -->
    <!-- ================================================================== -->
    <section x-show="$store.nav.tab === 'dashboard' && !pageLoading"
             x-cloak
             class="flex flex-col xl:flex-row xl:items-center justify-between gap-4 sm:gap-6 px-0.5 sm:px-1">

        <div class="shrink-0 pt-1 sm:pt-0">
            <p class="text-xs sm:text-sm font-medium text-slate-400">{{ $greeting }}</p>
            <h1 class="text-2xl sm:text-[30px] font-bold text-slate-900 tracking-tight leading-tight mt-0.5">
                {{ $ownerName }}
            </h1>
        </div>

        <div class="grid grid-cols-2 lg:grid-cols-4 gap-2.5 sm:gap-4 xl:gap-0 xl:flex xl:items-center xl:divide-x xl:divide-[#E3EAE7]">

            <!-- KPI 1: Today's Schedule -->
            <div class="bg-white rounded-2xl p-3 sm:p-4 border border-[#EDF2F0] shadow-card xl:bg-transparent xl:border-0 xl:shadow-none xl:rounded-none xl:p-0 flex items-center gap-2.5 sm:gap-3.5 xl:pr-7">
                <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl sm:rounded-full bg-emerald-50 text-brand-600 xl:bg-white xl:shadow-card xl:border xl:border-[#EEF2F1] flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
                <div class="min-w-0">
                    <div class="text-xl sm:text-2xl font-bold text-slate-900 leading-none">{{ $metrics['today_schedule'] }}</div>
                    <div class="text-[11px] sm:text-xs font-medium text-slate-500 mt-1 sm:mt-1.5 truncate">Today's Schedule</div>
                </div>
            </div>

            <!-- KPI 2: Upcoming Bookings -->
            <div class="bg-white rounded-2xl p-3 sm:p-4 border border-[#EDF2F0] shadow-card xl:bg-transparent xl:border-0 xl:shadow-none xl:rounded-none xl:p-0 flex items-center gap-2.5 sm:gap-3.5 xl:px-7">
                <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl sm:rounded-full bg-emerald-50 text-brand-600 xl:bg-white xl:shadow-card xl:border xl:border-[#EEF2F1] flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div class="min-w-0">
                    <div class="flex items-center gap-1.5 flex-wrap">
                        <span class="text-xl sm:text-2xl font-bold text-slate-900 leading-none">{{ $metrics['upcoming_count'] }}</span>
                        @if($metrics['pending_count'] > 0)
                            <span class="inline-flex items-center px-1.5 py-0.5 rounded-full text-[9px] sm:text-[10px] font-bold bg-amber-50 text-amber-700 whitespace-nowrap">
                                {{ $metrics['pending_count'] }} pending
                            </span>
                        @else
                            <span class="inline-flex items-center px-1.5 py-0.5 rounded-full text-[9px] sm:text-[10px] font-bold bg-[#E6F4F1] text-brand-600 whitespace-nowrap">
                                On Track
                            </span>
                        @endif
                    </div>
                    <div class="text-[11px] sm:text-xs font-medium text-slate-500 mt-1 sm:mt-1.5 truncate">Upcoming Bookings</div>
                </div>
            </div>

            <!-- KPI 3: Outstanding Invoices -->
            <div class="bg-white rounded-2xl p-3 sm:p-4 border border-[#EDF2F0] shadow-card xl:bg-transparent xl:border-0 xl:shadow-none xl:rounded-none xl:p-0 flex items-center gap-2.5 sm:gap-3.5 xl:px-7">
                <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl sm:rounded-full bg-emerald-50 text-brand-600 xl:bg-white xl:shadow-card xl:border xl:border-[#EEF2F1] flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <div class="min-w-0">
                    <div class="text-xl sm:text-2xl font-bold text-slate-900 leading-none">{{ $metrics['outstanding_count'] }}</div>
                    <div class="text-[11px] sm:text-xs font-medium text-slate-500 mt-1 sm:mt-1.5 truncate">Outstanding Invoices</div>
                </div>
            </div>

            <!-- KPI 4: Verified Revenue -->
            <div class="bg-white rounded-2xl p-3 sm:p-4 border border-[#EDF2F0] shadow-card xl:bg-transparent xl:border-0 xl:shadow-none xl:rounded-none xl:p-0 flex items-center gap-2.5 sm:gap-3.5 xl:pl-7">
                <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl sm:rounded-full bg-emerald-50 text-brand-600 xl:bg-white xl:shadow-card xl:border xl:border-[#EEF2F1] flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div class="min-w-0">
                    <div class="text-xl sm:text-2xl font-bold text-slate-900 leading-none tracking-tight">{{ $metrics['revenue_short'] }}</div>
                    <div class="text-[11px] sm:text-xs font-medium text-slate-500 mt-1 sm:mt-1.5 truncate">Verified Revenue</div>
                </div>
            </div>

        </div>
    </section>

    <!-- ================================================================== -->
    <!-- TAB 1: DASHBOARD (3-COLUMN BENTO — REAL DATABASE DATA)             -->
    <!-- ================================================================== -->
    <div x-show="$store.nav.tab === 'dashboard' && !pageLoading"
         x-cloak
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 translate-y-1"
         x-transition:enter-end="opacity-100 translate-y-0"
         class="grid grid-cols-1 lg:grid-cols-3 gap-5 items-stretch">

        <!-- COLUMN 1: TODAY'S SCHEDULE -->
        <div class="bg-white rounded-[28px] p-5 sm:p-6 shadow-card border border-[#EDF2F0] flex flex-col">
            <div class="flex items-center justify-between gap-2">
                <h2 class="text-[17px] font-bold text-slate-900 tracking-tight">Today's Schedule</h2>
                <button type="button"
                        @click="$store.nav.setTab('calendar')"
                        class="px-3.5 py-1.5 rounded-full border border-[#E5ECE9] bg-white hover:bg-[#F6F8F7] text-xs font-medium text-slate-600 transition">
                    View Calendar
                </button>
            </div>

            <div class="mt-4 bg-[#F6F8F7] rounded-full px-3 py-2 flex items-center justify-between text-xs font-semibold text-slate-700">
                <button type="button" @click="shiftScheduleMonth(-1)" title="Previous Month" class="w-7 h-7 rounded-full hover:bg-white active:scale-90 flex items-center justify-center text-slate-500 transition cursor-pointer">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.4" d="M15 19l-7-7 7-7"/></svg>
                </button>
                <span x-text="scheduleMonthLabel" class="select-none"></span>
                <button type="button" @click="shiftScheduleMonth(1)" title="Next Month" class="w-7 h-7 rounded-full hover:bg-white active:scale-90 flex items-center justify-center text-slate-500 transition cursor-pointer">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.4" d="M9 5l7 7-7 7"/></svg>
                </button>
            </div>

            <!-- 5-Day Horizontal Carousel Strip (True 60fps Physical Slide, Zero Flicker!) -->
            <div class="mt-4 flex items-center justify-between gap-1">
                <button type="button" @click="shiftScheduleDay(-1)" title="Previous Day" class="w-7 h-7 rounded-full hover:bg-[#F6F8F7] active:scale-90 flex items-center justify-center text-slate-400 hover:text-brand-600 transition shrink-0 cursor-pointer">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.4" d="M15 19l-7-7 7-7"/></svg>
                </button>

                <div class="relative flex-1 overflow-hidden py-1">
                    <!-- Stationary Centered Active Pill Backdrop -->
                    <div class="pointer-events-none absolute top-1 bottom-1 left-[40%] w-[20%] px-0.5">
                        <div class="w-full h-full rounded-2xl bg-brand-600 shadow-pill"></div>
                    </div>

                    <!-- Sliding 7-Day Track (5 Visible + 1 Buffer Each Side) -->
                    <div class="flex will-change-transform relative z-10" :style="stripTrackStyle">
                        <template x-for="d in scheduleDaysStrip" :key="d.iso">
                            <div class="w-[14.285714%] shrink-0 px-0.5">
                                <button type="button"
                                        @click="selectScheduleDate(d.iso, d.offset)"
                                        :class="(d.offset + stripOffset) === 0
                                            ? 'text-white'
                                            : 'text-slate-700 hover:bg-[#F6F8F7]'"
                                        class="w-full h-[66px] rounded-2xl flex flex-col items-center justify-center transition-colors duration-200 cursor-pointer select-none">
                                    <span class="text-[11px] font-medium leading-none transition-colors duration-200"
                                          :class="(d.offset + stripOffset) === 0 ? 'text-teal-100' : 'text-slate-400'"
                                          x-text="d.day"></span>
                                    <span class="text-sm font-bold mt-1.5 leading-none" x-text="d.num"></span>
                                    <span :class="[
                                            d.hasEvent ? 'opacity-100' : 'opacity-0',
                                            (d.offset + stripOffset) === 0 ? 'bg-emerald-300' : 'bg-brand-600'
                                          ]"
                                          class="w-1.5 h-1.5 rounded-full mt-1.5 transition-colors duration-200"></span>
                                </button>
                            </div>
                        </template>
                    </div>
                </div>

                <button type="button" @click="shiftScheduleDay(1)" title="Next Day" class="w-7 h-7 rounded-full hover:bg-[#F6F8F7] active:scale-90 flex items-center justify-center text-slate-400 hover:text-brand-600 transition shrink-0 cursor-pointer">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.4" d="M9 5l7 7-7 7"/></svg>
                </button>
            </div>

            <div class="mt-4 relative flex items-center">
                <svg class="w-4 h-4 text-slate-400 absolute left-3.5 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-5.2-5.2m2.2-4.8a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input type="text"
                       x-model="scheduleQuery"
                       placeholder="Search schedule..."
                       class="w-full pl-9 pr-4 py-2.5 rounded-2xl bg-white border border-[#E8EFED] text-xs text-slate-700 placeholder-slate-400 focus:outline-none focus:border-brand-400 transition">
            </div>

            <div class="mt-4 space-y-3 relative flex-1 overflow-hidden">
                <template x-for="item in dashboardScheduleList" :key="item.id">
                    <div @click="openDetail(item)"
                         class="p-3.5 rounded-2xl bg-[#F8FAFA] hover:bg-[#F1F6F5] border border-[#EFF4F2] transition-all duration-200 hover:-translate-y-0.5 cursor-pointer group">
                        <div class="flex items-center justify-between gap-3">
                            <div class="flex items-center gap-3 min-w-0">
                                <img :src="item.avatar" :alt="item.name" class="w-10 h-10 rounded-xl object-cover shrink-0">
                                <div class="min-w-0">
                                    <div class="flex items-center gap-1.5">
                                        <p class="text-[13px] font-bold text-slate-900 truncate" x-text="item.name"></p>
                                        <template x-if="item.job_source && item.job_source !== 'Direct Client'">
                                            <span class="px-1.5 py-0.2 rounded-full text-[9px] font-bold bg-amber-50 text-amber-800 border border-amber-200 shrink-0" x-text="item.job_source"></span>
                                        </template>
                                    </div>
                                    <p class="text-[11px] font-medium text-slate-500 mt-0.5" x-text="item.date_short + ' · #' + item.code"></p>
                                </div>
                            </div>
                            <svg class="w-4 h-4 text-slate-400 group-hover:text-brand-600 group-hover:translate-x-0.5 transition shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </div>

                        <div class="mt-3 pt-2.5 border-t border-[#EBF1EF] flex items-center justify-between gap-2">
                            <span class="text-[11px] font-medium text-slate-500 truncate" x-text="item.service"></span>
                            <span :class="{
                                    'bg-[#E6F6EF] text-emerald-700': item.bookingStatus === 'Confirmed',
                                    'bg-[#EAF3FF] text-blue-600': item.bookingStatus === 'Inquiry',
                                    'bg-[#F3EAFF] text-purple-600': item.bookingStatus === 'Complete',
                                    'bg-[#FEECEB] text-rose-600': item.bookingStatus === 'Cancelled'
                                  }"
                                  class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-bold shrink-0">
                                <span class="w-1.5 h-1.5 rounded-full"
                                      :class="{
                                        'bg-emerald-500': item.bookingStatus === 'Confirmed',
                                        'bg-blue-500': item.bookingStatus === 'Inquiry',
                                        'bg-purple-500': item.bookingStatus === 'Complete',
                                        'bg-rose-500': item.bookingStatus === 'Cancelled'
                                      }"></span>
                                <span x-text="item.bookingStatus"></span>
                            </span>
                        </div>
                    </div>
                </template>

                <div x-show="dashboardScheduleList.length === 0" class="py-8 px-3 text-center space-y-3">
                    <div class="w-10 h-10 rounded-full bg-[#F4F8F7] text-brand-600 flex items-center justify-center mx-auto">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-slate-700" x-text="'No events on ' + selectedDatePretty"></p>
                        <p class="text-[11px] text-slate-400 mt-0.5">Select another date or jump to a scheduled event:</p>
                    </div>
                    <!-- <div class="flex flex-wrap items-center justify-center gap-1.5 pt-1">
                        <template x-for="ev in upcomingList.slice(0, 3)" :key="'jump-' + ev.id">
                            <button type="button"
                                    @click="selectedDateIso = ev.event_date_iso"
                                    class="px-3 py-1 rounded-full bg-[#F4F8F7] hover:bg-brand-50 border border-[#E5ECE9] text-[11px] font-semibold text-brand-700 transition cursor-pointer"
                                    x-text="ev.date_short + ' · ' + ev.shortName">
                            </button>
                        </template>
                    </div> -->
                </div>
            </div>
        </div>

        <!-- COLUMN 2: UPCOMING BOOKINGS -->
        <div class="bg-white rounded-[28px] p-5 sm:p-6 shadow-card border border-[#EDF2F0] flex flex-col">
            <div class="flex items-center justify-between gap-2">
                <h2 class="text-[17px] font-bold text-slate-900 tracking-tight">Upcoming Bookings</h2>
                <button type="button"
                        @click="$store.nav.setTab('bookings')"
                        class="px-3.5 py-1.5 rounded-full border border-[#E5ECE9] bg-white hover:bg-[#F6F8F7] text-xs font-medium text-slate-600 transition">
                    View All
                </button>
            </div>

            <div class="mt-4 space-y-3 relative flex-1 overflow-hidden">
                <template x-for="item in upcomingList" :key="item.id">
                    <div @click="openDetail(item)"
                         class="p-3.5 rounded-2xl bg-[#F8FAFA] hover:bg-[#F1F6F5] border border-[#EFF4F2] transition cursor-pointer group">
                        <div class="flex items-center justify-between gap-3">
                            <div class="flex items-center gap-3 min-w-0">
                                <img :src="item.avatar" :alt="item.name" class="w-10 h-10 rounded-xl object-cover shrink-0">
                                <div class="min-w-0">
                                    <div class="flex items-center gap-1.5">
                                        <p class="text-[13px] font-bold text-slate-900 truncate" x-text="item.name"></p>
                                        <template x-if="item.job_source && item.job_source !== 'Direct Client'">
                                            <span class="px-1.5 py-0.2 rounded-full text-[9px] font-bold bg-amber-50 text-amber-800 border border-amber-200 shrink-0" x-text="item.job_source"></span>
                                        </template>
                                    </div>
                                    <p class="text-[11px] font-medium text-slate-500 mt-0.5 truncate" x-text="item.date_short + ' · ' + item.relative_date"></p>
                                </div>
                            </div>
                            <svg class="w-4 h-4 text-slate-400 group-hover:text-brand-600 group-hover:translate-x-0.5 transition shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </div>

                        <div class="mt-3 pt-2.5 border-t border-[#EBF1EF] flex items-center justify-between gap-2">
                            <span class="text-[11px] font-medium text-slate-500 truncate" x-text="item.service"></span>
                            <span :class="{
                                    'bg-[#E6F6EF] text-emerald-700': item.bookingStatus === 'Confirmed',
                                    'bg-[#EAF3FF] text-blue-600': item.bookingStatus === 'Inquiry',
                                    'bg-[#F3EAFF] text-purple-600': item.bookingStatus === 'Complete'
                                  }"
                                  class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-bold shrink-0">
                                <span class="w-1.5 h-1.5 rounded-full"
                                      :class="{
                                        'bg-emerald-500': item.bookingStatus === 'Confirmed',
                                        'bg-blue-500': item.bookingStatus === 'Inquiry',
                                        'bg-purple-500': item.bookingStatus === 'Complete'
                                      }"></span>
                                <span x-text="item.bookingStatus"></span>
                            </span>
                        </div>
                    </div>
                </template>

                <div x-show="upcomingList.length === 0" class="py-12 text-center text-xs text-slate-400">
                    No bookings available yet.
                </div>
            </div>
        </div>

        <!-- COLUMN 3: OUTSTANDING INVOICES + BUSINESS INSIGHT -->
        <div class="flex flex-col gap-5">

            <div class="bg-white rounded-[28px] p-5 sm:p-6 shadow-card border border-[#EDF2F0] flex-1 flex flex-col">
                <div class="flex items-center justify-between gap-2">
                    <h2 class="text-[17px] font-bold text-slate-900 tracking-tight">Outstanding Invoices</h2>
                    <button type="button"
                            @click="$store.nav.setTab('invoice')"
                            class="px-3.5 py-1.5 rounded-full border border-[#E5ECE9] bg-white hover:bg-[#F6F8F7] text-xs font-medium text-slate-600 transition">
                        View All
                    </button>
                </div>

                <div class="mt-4 space-y-3 relative flex-1 overflow-hidden">
                    <template x-for="inv in outstandingList.slice(0, 3)" :key="inv.id">
                        <div @click="openDetail(inv)"
                             class="p-3.5 rounded-2xl bg-[#F8FAFA] hover:bg-[#F1F6F5] border border-[#EFF4F2] transition cursor-pointer group">
                            <div class="flex items-center justify-between gap-3">
                                <div class="flex items-center gap-3 min-w-0">
                                    <img :src="inv.avatar" :alt="inv.name" class="w-10 h-10 rounded-xl object-cover shrink-0">
                                    <div class="min-w-0">
                                        <p class="text-[13px] font-bold text-slate-900 truncate" x-text="inv.name"></p>
                                        <p class="text-[11px] font-medium text-slate-500 mt-0.5 truncate">
                                            <span class="font-semibold text-slate-700" x-text="inv.remaining_formatted"></span>
                                            <span> · Event: </span>
                                            <span x-text="inv.date_short"></span>
                                        </p>
                                    </div>
                                </div>
                                <svg class="w-4 h-4 text-slate-400 group-hover:text-brand-600 group-hover:translate-x-0.5 transition shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </div>

                            <div class="mt-3 pt-2.5 border-t border-[#EBF1EF] flex items-center justify-between gap-2">
                                <span class="text-[11px] font-semibold font-mono text-slate-400" x-text="'#' + inv.code"></span>
                                <span :class="inv.invoiceStatus === 'Overdue' ? 'bg-rose-50 text-rose-600' : 'bg-amber-50 text-amber-600'"
                                      class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-bold shrink-0">
                                    <span class="w-1.5 h-1.5 rounded-full" :class="inv.invoiceStatus === 'Overdue' ? 'bg-rose-500' : 'bg-amber-400'"></span>
                                    <span x-text="inv.invoiceStatus"></span>
                                </span>
                            </div>
                        </div>
                    </template>

                    <div x-show="outstandingList.length === 0" class="py-10 text-center space-y-1.5">
                        <span class="inline-flex w-10 h-10 rounded-full bg-emerald-50 text-emerald-600 items-center justify-center">✓</span>
                        <p class="text-xs font-semibold text-slate-700">All Invoices Paid!</p>
                        <p class="text-[11px] text-slate-400">No outstanding balance at the moment.</p>
                    </div>
                </div>
            </div>

            <!-- Business Insight Card -->
            <div class="rounded-[28px] bg-gradient-to-br from-[#023834] via-[#045D56] to-[#0D8278] p-6 text-white shadow-insight relative overflow-hidden">
                <div class="pointer-events-none absolute -top-12 -right-12 w-44 h-44 rounded-full bg-teal-300/20 blur-2xl"></div>
                <div class="pointer-events-none absolute -bottom-10 -left-10 w-32 h-32 rounded-full bg-emerald-400/10 blur-2xl"></div>

                <div class="relative z-10 space-y-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-white text-brand-600 flex items-center justify-center shadow-sm shrink-0">
                            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M12 2l1.9 5.3L19.2 9l-5.3 1.9L12 16.2l-1.9-5.3L4.8 9l5.3-1.7L12 2zm6.5 12l.9 2.6 2.6.9-2.6.9-.9 2.6-.9-2.6-2.6-.9 2.6-.9.9-2.6z"/>
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold tracking-tight">Business Insight</h3>
                    </div>

                    <p class="text-[13px] text-teal-50/90 leading-relaxed">
                        <strong class="text-white font-semibold">{{ $metrics['top_package'] }}</strong> is your top-converting wedding package this season.
                    </p>

                    <div class="pt-1 flex flex-wrap items-center justify-between gap-3">
                        <div class="inline-flex items-center gap-1.5 text-xs font-semibold text-emerald-300">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                            </svg>
                            <span>{{ $metrics['total_bookings'] }} total bookings</span>
                        </div>

                        <button type="button"
                                @click="$store.nav.setTab('services')"
                                class="px-4 py-2 rounded-full bg-white hover:bg-teal-50 text-brand-700 text-xs font-bold transition shadow-sm">
                            Manage Packages
                        </button>
                    </div>
                </div>
            </div>

        </div>
    </div>

