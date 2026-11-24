    <!-- ================================================================== -->
    <!-- TAB 2: BOOKINGS SKELETON SHIMMER (FLUTTER STYLE)                   -->
    <!-- ================================================================== -->
    <div x-show="$store.nav.tab === 'bookings' && pageLoading"
         class="space-y-5"
         {{ request('tab') === 'bookings' ? '' : 'x-cloak' }}>
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-3">
            <div class="flex flex-wrap items-center gap-2.5 sm:gap-3">
                <div class="h-10 w-64 sm:w-72 rounded-full shimmer-mask"></div>
                <div class="h-10 w-36 rounded-full shimmer-mask"></div>
                <div class="h-10 w-36 rounded-full shimmer-mask"></div>
            </div>
            <div class="flex items-center gap-2.5">
                <div class="h-10 w-28 rounded-full shimmer-mask"></div>
                <div class="h-10 w-36 rounded-full shimmer-mask"></div>
            </div>
        </div>

        <div class="bg-white rounded-[28px] shadow-card border border-[#EDF2F0] overflow-hidden p-6 space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-[#EFF4F2]">
                <div class="h-4 w-28 rounded shimmer-mask"></div>
                <div class="h-4 w-36 rounded shimmer-mask"></div>
                <div class="h-4 w-32 rounded shimmer-mask"></div>
                <div class="h-4 w-28 rounded shimmer-mask"></div>
                <div class="h-4 w-24 rounded shimmer-mask"></div>
                <div class="h-4 w-24 rounded shimmer-mask"></div>
            </div>
            <div class="space-y-4">
                <template x-for="i in 6" :key="'shimmer-b-row-' + i">
                    <div class="flex items-center justify-between py-2 border-b border-[#F8FAFA]">
                        <div class="h-4 w-24 rounded shimmer-mask"></div>
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg shimmer-mask shrink-0"></div>
                            <div class="h-4 w-32 rounded shimmer-mask"></div>
                        </div>
                        <div class="h-4 w-28 rounded shimmer-mask"></div>
                        <div class="h-4 w-28 rounded shimmer-mask"></div>
                        <div class="h-6 w-20 rounded-full shimmer-mask"></div>
                        <div class="h-6 w-16 rounded-full shimmer-mask"></div>
                    </div>
                </template>
            </div>
            <div class="pt-4 flex items-center justify-between border-t border-[#EFF4F2]">
                <div class="h-4 w-40 rounded shimmer-mask"></div>
                <div class="h-8 w-48 rounded-lg shimmer-mask"></div>
            </div>
        </div>
    </div>

    <!-- ================================================================== -->
    <!-- TAB 2: BOOKINGS (6-COLUMN TABLE — REAL DATABASE DATA)              -->
    <!-- ================================================================== -->
    <div x-show="$store.nav.tab === 'bookings' && !pageLoading"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 translate-y-1"
         x-transition:enter-end="opacity-100 translate-y-0"
         class="space-y-5"
         x-cloak>

        <div class="flex flex-col md:flex-row md:items-center justify-between gap-3">
            <div class="flex flex-wrap items-center gap-2.5 sm:gap-3">
                <div class="relative flex items-center bg-white rounded-full border border-[#E7EEEC] shadow-[0_2px_8px_rgba(0,0,0,0.02)] focus-within:border-brand-400 transition w-full sm:w-64 lg:w-72">
                    <svg class="w-4 h-4 text-slate-400 absolute left-4 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-5.2-5.2m2.2-4.8a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input type="text"
                           x-model="bookingSearch"
                           placeholder="Search Bookings..."
                           class="w-full bg-transparent pl-10 pr-4 py-2.5 text-xs text-slate-700 placeholder-slate-400 focus:outline-none">
                </div>

                <div class="relative" @click.away="statusMenuOpen = false">
                    <button type="button"
                            @click="statusMenuOpen = !statusMenuOpen; serviceMenuOpen = false"
                            class="inline-flex items-center justify-between gap-4 bg-white hover:bg-[#FAFDFC] rounded-full border border-[#E7EEEC] shadow-[0_2px_8px_rgba(0,0,0,0.02)] px-4 py-2.5 text-xs font-medium text-slate-700 transition min-w-[148px]">
                        <span class="inline-flex items-center gap-2">
                            <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4h18M6 12h12M10 20h4"/>
                            </svg>
                            <span x-text="bookingFilter === 'all' ? 'All Status' : bookingFilter"></span>
                        </span>
                        <svg class="w-3.5 h-3.5 text-slate-400 transition-transform" :class="statusMenuOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>

                    <div x-show="statusMenuOpen"
                         class="absolute left-0 mt-2 w-44 bg-white rounded-2xl shadow-xl border border-[#EAF0EE] py-1.5 z-30"
                         x-cloak>
                        <template x-for="st in ['all', 'Inquiry', 'Confirmed', 'Complete', 'Cancelled']" :key="st">
                            <button type="button"
                                    @click="bookingFilter = st; statusMenuOpen = false"
                                    :class="bookingFilter === st ? 'bg-brand-50 text-brand-700 font-semibold' : 'text-slate-600 hover:bg-[#F6F8F7]'"
                                    class="w-full text-left px-4 py-2 text-xs transition flex items-center justify-between">
                                <span x-text="st === 'all' ? 'All Status' : st"></span>
                                <span x-show="bookingFilter === st" class="text-brand-600">✓</span>
                            </button>
                        </template>
                    </div>
                </div>

                <div class="relative" @click.away="serviceMenuOpen = false">
                    <button type="button"
                            @click="serviceMenuOpen = !serviceMenuOpen; statusMenuOpen = false"
                            class="inline-flex items-center justify-between gap-4 bg-white hover:bg-[#FAFDFC] rounded-full border border-[#E7EEEC] shadow-[0_2px_8px_rgba(0,0,0,0.02)] px-4 py-2.5 text-xs font-medium text-slate-700 transition min-w-[155px]">
                        <span class="truncate max-w-[135px]" x-text="bookingServiceFilter === 'all' ? 'All Services' : bookingServiceFilter"></span>
                        <svg class="w-3.5 h-3.5 text-slate-400 transition-transform shrink-0" :class="serviceMenuOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>

                    <div x-show="serviceMenuOpen"
                         class="absolute left-0 mt-2 w-56 bg-white rounded-2xl shadow-xl border border-[#EAF0EE] py-1.5 z-30"
                         x-cloak>
                        <button type="button"
                                @click="bookingServiceFilter = 'all'; serviceMenuOpen = false"
                                :class="bookingServiceFilter === 'all' ? 'bg-brand-50 text-brand-700 font-semibold' : 'text-slate-600 hover:bg-[#F6F8F7]'"
                                class="w-full text-left px-4 py-2 text-xs transition flex items-center justify-between">
                            <span>All Services</span>
                            <span x-show="bookingServiceFilter === 'all'" class="text-brand-600">✓</span>
                        </button>
                        <template x-for="srv in availableServices" :key="srv">
                            <button type="button"
                                    @click="bookingServiceFilter = srv; serviceMenuOpen = false"
                                    :class="bookingServiceFilter === srv ? 'bg-brand-50 text-brand-700 font-semibold' : 'text-slate-600 hover:bg-[#F6F8F7]'"
                                    class="w-full text-left px-4 py-2 text-xs transition flex items-center justify-between gap-2">
                                <span class="truncate" x-text="srv"></span>
                                <span x-show="bookingServiceFilter === srv" class="text-brand-600 shrink-0">✓</span>
                            </button>
                        </template>
                    </div>
                </div>
            </div>

            <a href="{{ route('booking.create') }}" target="_blank"
               class="inline-flex items-center justify-center gap-1.5 px-5 py-2.5 rounded-full bg-brand-600 hover:bg-brand-700 text-white text-xs font-semibold transition shadow-pill shrink-0">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                <span>New Booking</span>
            </a>
        </div>

        <div class="hidden md:block bg-white rounded-[28px] shadow-card border border-[#EDF2F0] overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="text-xs font-semibold text-slate-600 border-b border-[#EFF4F2]">
                            <th class="py-4 px-6">Booking ID</th>
                            <th class="py-4 px-5">Client</th>
                            <th class="py-4 px-5">Event Due</th>
                            <th class="py-4 px-5">Services</th>
                            <th class="py-4 px-5">Booking Status</th>
                            <th class="py-4 px-6">Invoice Status</th>
                        </tr>
                    </thead>
                    <!-- Shimmer Rows during Pagination -->
                    <tbody x-show="bookingTableLoading" class="divide-y divide-[#F1F5F4]" x-cloak>
                        <template x-for="i in Math.min(5, bookingPerPage)" :key="'b-shim-' + i">
                            <tr>
                                <td class="py-4 px-6 whitespace-nowrap">
                                    <div class="h-4 w-20 rounded-md shimmer-mask"></div>
                                </td>
                                <td class="py-4 px-5 whitespace-nowrap">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-lg shimmer-mask shrink-0"></div>
                                        <div class="h-4 w-32 rounded-md shimmer-mask"></div>
                                    </div>
                                </td>
                                <td class="py-4 px-5 whitespace-nowrap">
                                    <div class="h-4 w-24 rounded-md shimmer-mask"></div>
                                </td>
                                <td class="py-4 px-5 whitespace-nowrap">
                                    <div class="h-4 w-28 rounded-md shimmer-mask"></div>
                                </td>
                                <td class="py-4 px-5 whitespace-nowrap">
                                    <div class="h-6 w-20 rounded-full shimmer-mask"></div>
                                </td>
                                <td class="py-4 px-6 whitespace-nowrap">
                                    <div class="h-6 w-16 rounded-full shimmer-mask"></div>
                                </td>
                            </tr>
                        </template>
                    </tbody>

                    <!-- Real Data Rows -->
                    <tbody x-show="!bookingTableLoading" class="divide-y divide-[#F1F5F4]">
                            <template x-for="b in paginatedBookings" :key="b.id">
                                <tr @click="openDetail(b)" class="hover:bg-[#F8FBFA] transition cursor-pointer group">
                                    <td class="py-4 px-6 whitespace-nowrap">
                                        <span class="text-[13px] font-semibold text-[#0284C7] group-hover:underline font-mono" x-text="'#' + b.code"></span>
                                    </td>
                                    <td class="py-4 px-5 whitespace-nowrap">
                                        <div class="flex items-center gap-3">
                                            <img :src="b.avatar" :alt="b.name" class="w-8 h-8 rounded-lg object-cover shrink-0">
                                            <div>
                                                <div class="flex items-center gap-2">
                                                    <span class="text-[13px] font-semibold text-slate-800" x-text="b.name"></span>
                                                    <template x-if="b.job_source && b.job_source !== 'Direct Client'">
                                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-800 border border-amber-200" x-text="b.job_source"></span>
                                                    </template>
                                                </div>
                                                <template x-if="b.tent_color_theme && b.tent_color_theme !== '-'">
                                                    <p class="text-[11px] text-slate-400 mt-0.5 flex items-center gap-1">
                                                        <span class="w-1.5 h-1.5 rounded-full bg-brand-500 inline-block"></span>
                                                        <span x-text="b.tent_color_theme"></span>
                                                    </p>
                                                </template>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-4 px-5 whitespace-nowrap">
                                        <span class="text-[13px] font-medium text-slate-800" x-text="b.date_short"></span>
                                        <span class="text-[11px] font-medium text-slate-400 ml-1.5" x-text="b.time"></span>
                                    </td>
                                    <td class="py-4 px-5 whitespace-nowrap">
                                        <span class="text-[13px] font-medium text-slate-700" x-text="b.service"></span>
                                    </td>
                                    <td class="py-4 px-5 whitespace-nowrap">
                                        <span :class="{
                                                'bg-[#E6F6EF] text-emerald-700': b.bookingStatus === 'Confirmed',
                                                'bg-[#EAF3FF] text-blue-600': b.bookingStatus === 'Inquiry',
                                                'bg-[#F3EAFF] text-purple-600': b.bookingStatus === 'Complete',
                                                'bg-[#FEECEB] text-rose-600': b.bookingStatus === 'Cancelled'
                                              }"
                                              class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-semibold">
                                            <span class="w-1.5 h-1.5 rounded-full"
                                                  :class="{
                                                    'bg-emerald-500': b.bookingStatus === 'Confirmed',
                                                    'bg-blue-500': b.bookingStatus === 'Inquiry',
                                                    'bg-purple-500': b.bookingStatus === 'Complete',
                                                    'bg-rose-500': b.bookingStatus === 'Cancelled'
                                                  }"></span>
                                            <span x-text="b.bookingStatus"></span>
                                        </span>
                                    </td>
                                    <td class="py-4 px-6 whitespace-nowrap">
                                        <span :class="{
                                                'bg-[#E6F6EF] text-emerald-700': b.invoiceStatus === 'Paid',
                                                'bg-[#FEF6E0] text-amber-600': b.invoiceStatus === 'Sent',
                                                'bg-[#F1F5F9] text-slate-500': b.invoiceStatus === 'Draft',
                                                'bg-[#FEECEB] text-rose-600': b.invoiceStatus === 'Overdue' || b.invoiceStatus === 'Cancelled'
                                              }"
                                              class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-semibold">
                                            <span class="w-1.5 h-1.5 rounded-full"
                                                  :class="{
                                                    'bg-emerald-500': b.invoiceStatus === 'Paid',
                                                    'bg-amber-400': b.invoiceStatus === 'Sent',
                                                    'bg-slate-400': b.invoiceStatus === 'Draft',
                                                    'bg-rose-500': b.invoiceStatus === 'Overdue' || b.invoiceStatus === 'Cancelled'
                                                  }"></span>
                                            <span x-text="b.invoiceStatus"></span>
                                        </span>
                                    </td>
                                </tr>
                            </template>

                        <tr x-show="filteredBookings.length === 0">
                            <td colspan="6" class="py-12 text-center text-xs text-slate-400">
                                No bookings match your search or filter.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Table Footer with Interactive Pagination -->
            <div class="px-6 py-4 bg-[#FAFDFC] border-t border-[#EFF4F2] flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs text-slate-500 font-medium">
                <div class="flex items-center gap-3">
                    <span x-show="filteredBookings.length > 0">
                        Showing <strong class="text-slate-900 font-semibold" x-text="bookingFrom"></strong>–<strong class="text-slate-900 font-semibold" x-text="bookingTo"></strong> of <strong class="text-slate-900 font-semibold" x-text="filteredBookings.length"></strong> bookings
                        <span x-show="filteredBookings.length !== allBookings.length" class="text-slate-400 text-[11px]" x-text="'(' + allBookings.length + ' total)'"></span>
                    </span>
                    <span x-show="filteredBookings.length === 0">
                        0 bookings
                    </span>

                    <div class="hidden sm:flex items-center gap-1.5 pl-3 border-l border-slate-200">
                        <span class="text-slate-400 text-[11px]">Per page:</span>
                        <select x-model.number="bookingPerPage"
                                class="bg-white border border-[#E2EAE7] rounded-lg px-2 py-1 text-xs font-semibold text-slate-700 focus:outline-none focus:border-brand-600 cursor-pointer">
                            <option :value="10">10</option>
                            <option :value="20">20</option>
                            <option :value="50">50</option>
                        </select>
                    </div>
                </div>

                <!-- Pagination Buttons (Prev, Numbers, Next) -->
                <div x-show="totalBookingPages > 1" class="flex items-center gap-1">
                    <!-- Prev -->
                    <button type="button"
                            @click="if (bookingPage > 1) { bookingPage--; }"
                            :disabled="bookingPage <= 1"
                            :class="bookingPage <= 1 ? 'opacity-35 cursor-not-allowed text-slate-300' : 'text-slate-600 hover:bg-white hover:text-brand-700 hover:border-slate-300'"
                            class="inline-flex items-center justify-center w-8 h-8 rounded-lg border border-[#E2EAE7] bg-white transition cursor-pointer"
                            title="Previous page">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    </button>

                    <!-- Numbers -->
                    <template x-for="(p, idx) in bookingPaginationRange" :key="'bp-' + idx + '-' + p">
                        <div>
                            <template x-if="p === '...'">
                                <span class="inline-flex items-center justify-center w-8 h-8 text-xs text-slate-400 select-none">…</span>
                            </template>
                            <template x-if="p !== '...'">
                                <button type="button"
                                        @click="bookingPage = p"
                                        :class="bookingPage === p 
                                            ? 'bg-brand-600 text-white font-bold border-brand-600 shadow-2xs' 
                                            : 'bg-white text-slate-600 hover:bg-slate-50 border-[#E2EAE7] hover:border-slate-300'"
                                        class="inline-flex items-center justify-center min-w-[32px] h-8 px-2 rounded-lg border text-xs font-semibold transition cursor-pointer"
                                        x-text="p">
                                </button>
                            </template>
                        </div>
                    </template>

                    <!-- Next -->
                    <button type="button"
                            @click="if (bookingPage < totalBookingPages) { bookingPage++; }"
                            :disabled="bookingPage >= totalBookingPages"
                            :class="bookingPage >= totalBookingPages ? 'opacity-35 cursor-not-allowed text-slate-300' : 'text-slate-600 hover:bg-white hover:text-brand-700 hover:border-slate-300'"
                            class="inline-flex items-center justify-center w-8 h-8 rounded-lg border border-[#E2EAE7] bg-white transition cursor-pointer"
                            title="Next page">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </button>
                </div>
            </div>
        </div>

        <div class="md:hidden space-y-3">
            <template x-for="b in paginatedBookings" :key="b.id">
                <div @click="openDetail(b)" class="bg-white rounded-[24px] p-4 shadow-card border border-[#EDF2F0] space-y-3 cursor-pointer active:scale-[0.99] transition">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold font-mono text-[#0284C7]" x-text="'#' + b.code"></span>
                        <span class="text-[11px] text-slate-400" x-text="b.date_short"></span>
                    </div>
                    <div class="flex items-center gap-3">
                        <img :src="b.avatar" :alt="b.name" class="w-10 h-10 rounded-xl object-cover">
                        <div class="min-w-0 flex-1">
                            <div class="flex items-center gap-1.5 flex-wrap">
                                <p class="text-sm font-bold text-slate-900 truncate" x-text="b.name"></p>
                                <template x-if="b.job_source && b.job_source !== 'Direct Client'">
                                    <span class="px-1.5 py-0.5 rounded-full text-[9px] font-bold bg-amber-50 text-amber-800 border border-amber-200" x-text="b.job_source"></span>
                                </template>
                            </div>
                            <p class="text-xs text-slate-500 truncate" x-text="b.service"></p>
                            <template x-if="b.tent_color_theme && b.tent_color_theme !== '-'">
                                <p class="text-[10px] text-brand-700 font-medium truncate mt-0.5" x-text="'Tema: ' + b.tent_color_theme"></p>
                            </template>
                        </div>
                    </div>
                    <div class="pt-2.5 border-t border-[#F1F5F4] flex items-center justify-between">
                        <span :class="{
                                'bg-[#E6F6EF] text-emerald-700': b.bookingStatus === 'Confirmed',
                                'bg-[#EAF3FF] text-blue-600': b.bookingStatus === 'Inquiry',
                                'bg-[#F3EAFF] text-purple-600': b.bookingStatus === 'Complete',
                                'bg-[#FEECEB] text-rose-600': b.bookingStatus === 'Cancelled'
                              }"
                              class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-semibold">
                            <span x-text="b.bookingStatus"></span>
                        </span>
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-semibold bg-[#F4F7F6] text-slate-700" x-text="b.invoiceStatus"></span>
                    </div>
                </div>
            </template>

            <!-- Mobile Pagination for Bookings -->
            <div x-show="totalBookingPages > 1" class="flex items-center justify-between p-3.5 bg-white rounded-2xl shadow-card border border-[#EDF2F0]">
                <button type="button"
                        @click="if (bookingPage > 1) { bookingPage--; }"
                        :disabled="bookingPage <= 1"
                        :class="bookingPage <= 1 ? 'opacity-35 cursor-not-allowed text-slate-400' : 'text-brand-700 font-bold active:scale-95'"
                        class="px-3 py-1.5 rounded-xl border border-slate-200 text-xs transition cursor-pointer">
                    ← Prev
                </button>
                <span class="text-xs text-slate-500 font-medium">
                    Page <strong class="text-slate-900" x-text="bookingPage"></strong> of <strong class="text-slate-900" x-text="totalBookingPages"></strong>
                </span>
                <button type="button"
                        @click="if (bookingPage < totalBookingPages) { bookingPage++; }"
                        :disabled="bookingPage >= totalBookingPages"
                        :class="bookingPage >= totalBookingPages ? 'opacity-35 cursor-not-allowed text-slate-400' : 'text-brand-700 font-bold active:scale-95'"
                        class="px-3 py-1.5 rounded-xl border border-slate-200 text-xs transition cursor-pointer">
                    Next →
                </button>
            </div>
        </div>
    </div>

