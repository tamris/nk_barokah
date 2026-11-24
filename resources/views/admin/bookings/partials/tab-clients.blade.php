    <!-- ================================================================== -->
    <!-- TAB 4: CLIENTS SKELETON SHIMMER (FLUTTER STYLE)                    -->
    <!-- ================================================================== -->
    <div x-show="$store.nav.tab === 'clients' && pageLoading"
         class="space-y-5"
         {{ request('tab') === 'clients' ? '' : 'x-cloak' }}>
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-3">
            <div class="flex flex-wrap items-center gap-2.5 sm:gap-3">
                <div class="h-10 w-64 sm:w-72 rounded-full shimmer-mask"></div>
                <div class="h-10 w-36 rounded-full shimmer-mask"></div>
            </div>
            <div class="h-10 w-32 rounded-full shimmer-mask"></div>
        </div>

        <div class="bg-white rounded-[28px] shadow-card border border-[#EDF2F0] p-6 space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-[#EFF4F2]">
                <div class="h-4 w-32 rounded shimmer-mask"></div>
                <div class="h-4 w-40 rounded shimmer-mask"></div>
                <div class="h-4 w-28 rounded shimmer-mask"></div>
                <div class="h-4 w-24 rounded shimmer-mask"></div>
                <div class="h-4 w-24 rounded shimmer-mask"></div>
            </div>
            <div class="space-y-4">
                <template x-for="i in 5" :key="'shimmer-c-row-' + i">
                    <div class="flex items-center justify-between py-2 border-b border-[#F8FAFA]">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-full shimmer-mask shrink-0"></div>
                            <div class="space-y-1.5">
                                <div class="h-4 w-32 rounded shimmer-mask"></div>
                                <div class="h-3 w-24 rounded shimmer-mask"></div>
                            </div>
                        </div>
                        <div class="h-4 w-28 rounded shimmer-mask"></div>
                        <div class="h-4 w-24 rounded shimmer-mask"></div>
                        <div class="h-4 w-28 rounded shimmer-mask"></div>
                        <div class="h-6 w-20 rounded-full shimmer-mask"></div>
                    </div>
                </template>
            </div>
            <div class="pt-4 flex items-center justify-between border-t border-[#EFF4F2]">
                <div class="h-4 w-36 rounded shimmer-mask"></div>
                <div class="h-8 w-44 rounded-lg shimmer-mask"></div>
            </div>
        </div>
    </div>

    <!-- ================================================================== -->
    <!-- TAB 4: CLIENTS (REAL DIRECTORY)                                    -->
    <!-- ================================================================== -->
    <div x-show="$store.nav.tab === 'clients' && !pageLoading"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 translate-y-1"
         x-transition:enter-end="opacity-100 translate-y-0"
         class="space-y-5"
         x-cloak>

        <div class="flex flex-col md:flex-row md:items-center justify-between gap-3">
            <div class="flex flex-wrap items-center gap-2.5 sm:gap-3">
                <div class="relative flex items-center bg-white rounded-full border border-[#E7EEEC] w-full sm:w-64 lg:w-72">
                    <svg class="w-4 h-4 text-slate-400 absolute left-4 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-5.2-5.2m2.2-4.8a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input type="text" x-model="clientSearch" placeholder="Search Clients..."
                           class="w-full bg-transparent pl-10 pr-4 py-2.5 text-xs text-slate-700 placeholder-slate-400 focus:outline-none">
                </div>

                <div class="relative" @click.away="clientMenuOpen = false">
                    <button type="button" @click="clientMenuOpen = !clientMenuOpen"
                            class="inline-flex items-center justify-between gap-4 bg-white hover:bg-[#FAFDFC] rounded-full border border-[#E7EEEC] px-4 py-2.5 text-xs font-medium text-slate-700 transition min-w-[148px]">
                        <span x-text="clientFilter === 'all' ? 'All Clients' : clientFilter"></span>
                        <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div x-show="clientMenuOpen" class="absolute left-0 mt-2 w-44 bg-white rounded-2xl shadow-xl border border-[#EAF0EE] py-1.5 z-30" x-cloak>
                        <template x-for="st in ['all', 'Active', 'Complete', 'Lead']" :key="st">
                            <button type="button" @click="clientFilter = st; clientMenuOpen = false"
                                    :class="clientFilter === st ? 'bg-brand-50 text-brand-700 font-semibold' : 'text-slate-600 hover:bg-[#F6F8F7]'"
                                    class="w-full text-left px-4 py-2 text-xs transition flex items-center justify-between">
                                <span x-text="st === 'all' ? 'All Clients' : st"></span>
                                <span x-show="clientFilter === st" class="text-brand-600">✓</span>
                            </button>
                        </template>
                    </div>
                </div>
            </div>

            <a href="{{ route('booking.create') }}" target="_blank"
               class="inline-flex items-center justify-center gap-1.5 px-5 py-2.5 rounded-full bg-brand-600 hover:bg-brand-700 text-white text-xs font-semibold transition shadow-pill shrink-0">
                <span>Add Client</span>
            </a>
        </div>

        <div class="hidden md:block bg-white rounded-[28px] shadow-card border border-[#EDF2F0] overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="text-xs font-semibold text-slate-600 border-b border-[#EFF4F2]">
                            <th class="py-4 px-6">Client</th>
                            <th class="py-4 px-5">Contact</th>
                            <th class="py-4 px-5">Latest Package</th>
                            <th class="py-4 px-5">Bookings</th>
                            <th class="py-4 px-5">Total Spent</th>
                            <th class="py-4 px-6">Status</th>
                        </tr>
                    </thead>
                    <!-- Shimmer Rows during Pagination -->
                    <tbody x-show="clientTableLoading" class="divide-y divide-[#F1F5F4]" x-cloak>
                        <template x-for="i in Math.min(5, clientPerPage)" :key="'c-shim-' + i">
                            <tr>
                                <td class="py-4 px-6 whitespace-nowrap">
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-xl shimmer-mask shrink-0"></div>
                                        <div class="space-y-1.5">
                                            <div class="h-4 w-28 rounded shimmer-mask"></div>
                                            <div class="h-3 w-20 rounded shimmer-mask"></div>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-4 px-5 whitespace-nowrap">
                                    <div class="h-4 w-24 rounded shimmer-mask"></div>
                                </td>
                                <td class="py-4 px-5 whitespace-nowrap">
                                    <div class="h-4 w-24 rounded shimmer-mask"></div>
                                </td>
                                <td class="py-4 px-5 whitespace-nowrap">
                                    <div class="h-4 w-16 rounded shimmer-mask"></div>
                                </td>
                                <td class="py-4 px-5 whitespace-nowrap">
                                    <div class="h-4 w-24 rounded shimmer-mask"></div>
                                </td>
                                <td class="py-4 px-6 whitespace-nowrap">
                                    <div class="h-6 w-16 rounded-full shimmer-mask"></div>
                                </td>
                            </tr>
                        </template>
                    </tbody>

                    <!-- Real Data Rows -->
                    <tbody x-show="!clientTableLoading" class="divide-y divide-[#F1F5F4]">
                        <template x-for="c in paginatedClients" :key="c.id">
                            <tr @click="openClientProfile(c)" class="hover:bg-[#F8FBFA] transition cursor-pointer group">
                                <td class="py-4 px-6 whitespace-nowrap">
                                    <div class="flex items-center gap-3">
                                        <img :src="c.avatar" :alt="c.name" class="w-9 h-9 rounded-xl object-cover shrink-0">
                                        <div>
                                            <p class="text-[13px] font-bold text-slate-900 group-hover:text-brand-600 transition" x-text="c.name"></p>
                                            <p class="text-[11px] text-slate-400" x-text="'Event: ' + c.lastEvent"></p>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-4 px-5 whitespace-nowrap">
                                    <p class="text-[12.5px] font-medium text-slate-800" x-text="c.phone"></p>
                                    <p class="text-[11px] text-slate-400" x-text="c.email"></p>
                                </td>
                                <td class="py-4 px-5 whitespace-nowrap text-[13px] font-medium text-slate-700" x-text="c.package"></td>
                                <td class="py-4 px-5 whitespace-nowrap">
                                    <span class="px-2.5 py-1 rounded-full bg-[#F4F7F6] text-slate-700 text-[11px] font-bold" x-text="c.totalBookings + 'x Event'"></span>
                                </td>
                                <td class="py-4 px-5 whitespace-nowrap text-[13px] font-bold text-slate-900" x-text="c.totalSpentFormatted"></td>
                                <td class="py-4 px-6 whitespace-nowrap">
                                    <span :class="{
                                            'bg-[#E6F6EF] text-emerald-700': c.status === 'Active',
                                            'bg-[#F3EAFF] text-purple-600': c.status === 'Complete',
                                            'bg-[#EAF3FF] text-blue-600': c.status === 'Lead'
                                          }"
                                          class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-semibold">
                                        <span class="w-1.5 h-1.5 rounded-full"
                                              :class="{
                                                'bg-emerald-500': c.status === 'Active',
                                                'bg-purple-500': c.status === 'Complete',
                                                'bg-blue-500': c.status === 'Lead'
                                              }"></span>
                                        <span x-text="c.status"></span>
                                    </span>
                                </td>
                            </tr>
                        </template>

                        <tr x-show="filteredClients.length === 0">
                            <td colspan="6" class="py-12 text-center text-xs text-slate-400">
                                No clients match your search or filter.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Table Footer with Interactive Pagination for Clients -->
            <div class="px-6 py-4 bg-[#FAFDFC] border-t border-[#EFF4F2] flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs text-slate-500 font-medium">
                <div class="flex items-center gap-3">
                    <span x-show="filteredClients.length > 0">
                        Showing <strong class="text-slate-900 font-semibold" x-text="clientFrom"></strong>–<strong class="text-slate-900 font-semibold" x-text="clientTo"></strong> of <strong class="text-slate-900 font-semibold" x-text="filteredClients.length"></strong> registered clients
                        <span x-show="filteredClients.length !== clientsList.length" class="text-slate-400 text-[11px]" x-text="'(' + clientsList.length + ' total)'"></span>
                    </span>
                    <span x-show="filteredClients.length === 0">
                        0 clients
                    </span>

                    <div class="hidden sm:flex items-center gap-1.5 pl-3 border-l border-slate-200">
                        <span class="text-slate-400 text-[11px]">Per page:</span>
                        <select x-model.number="clientPerPage"
                                class="bg-white border border-[#E2EAE7] rounded-lg px-2 py-1 text-xs font-semibold text-slate-700 focus:outline-none focus:border-brand-600 cursor-pointer">
                            <option :value="10">10</option>
                            <option :value="20">20</option>
                            <option :value="50">50</option>
                        </select>
                    </div>
                </div>

                <!-- Pagination Buttons -->
                <div x-show="totalClientPages > 1" class="flex items-center gap-1">
                    <button type="button"
                            @click="if (clientPage > 1) { clientPage--; }"
                            :disabled="clientPage <= 1"
                            :class="clientPage <= 1 ? 'opacity-35 cursor-not-allowed text-slate-300' : 'text-slate-600 hover:bg-white hover:text-brand-700 hover:border-slate-300'"
                            class="inline-flex items-center justify-center w-8 h-8 rounded-lg border border-[#E2EAE7] bg-white transition cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    </button>

                    <template x-for="(p, idx) in clientPaginationRange" :key="'cp-' + idx + '-' + p">
                        <div>
                            <template x-if="p === '...'">
                                <span class="inline-flex items-center justify-center w-8 h-8 text-xs text-slate-400 select-none">…</span>
                            </template>
                            <template x-if="p !== '...'">
                                <button type="button"
                                        @click="clientPage = p"
                                        :class="clientPage === p 
                                            ? 'bg-brand-600 text-white font-bold border-brand-600 shadow-2xs' 
                                            : 'bg-white text-slate-600 hover:bg-slate-50 border-[#E2EAE7] hover:border-slate-300'"
                                        class="inline-flex items-center justify-center min-w-[32px] h-8 px-2 rounded-lg border text-xs font-semibold transition cursor-pointer"
                                        x-text="p">
                                </button>
                            </template>
                        </div>
                    </template>

                    <button type="button"
                            @click="if (clientPage < totalClientPages) { clientPage++; }"
                            :disabled="clientPage >= totalClientPages"
                            :class="clientPage >= totalClientPages ? 'opacity-35 cursor-not-allowed text-slate-300' : 'text-slate-600 hover:bg-white hover:text-brand-700 hover:border-slate-300'"
                            class="inline-flex items-center justify-center w-8 h-8 rounded-lg border border-[#E2EAE7] bg-white transition cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </button>
                </div>
            </div>
        </div>

        <div class="md:hidden space-y-3">
            <template x-for="c in paginatedClients" :key="c.id">
                <div @click="openClientProfile(c)" class="bg-white rounded-[24px] p-4 shadow-card border border-[#EDF2F0] space-y-3 cursor-pointer">
                    <div class="flex items-center justify-between gap-3">
                        <div class="flex items-center gap-3 min-w-0">
                            <img :src="c.avatar" :alt="c.name" class="w-10 h-10 rounded-xl object-cover shrink-0">
                            <div class="min-w-0">
                                <p class="text-sm font-bold text-slate-900 truncate" x-text="c.name"></p>
                                <p class="text-xs text-slate-400 truncate" x-text="c.phone"></p>
                            </div>
                        </div>
                        <span class="px-2.5 py-1 rounded-full bg-[#E6F6EF] text-emerald-700 text-[10px] font-bold shrink-0" x-text="c.status"></span>
                    </div>
                    <div class="pt-2.5 border-t border-[#F1F5F4] flex items-center justify-between text-xs">
                        <span class="text-slate-500 truncate" x-text="c.package"></span>
                        <span class="font-bold text-brand-600 shrink-0" x-text="c.totalSpentFormatted"></span>
                    </div>
                </div>
            </template>

            <!-- Mobile Pagination for Clients -->
            <div x-show="totalClientPages > 1" class="flex items-center justify-between p-3.5 bg-white rounded-2xl shadow-card border border-[#EDF2F0]">
                <button type="button"
                        @click="if (clientPage > 1) { clientPage--; }"
                        :disabled="clientPage <= 1"
                        :class="clientPage <= 1 ? 'opacity-35 cursor-not-allowed text-slate-400' : 'text-brand-700 font-bold active:scale-95'"
                        class="px-3 py-1.5 rounded-xl border border-slate-200 text-xs transition cursor-pointer">
                    ← Prev
                </button>
                <span class="text-xs text-slate-500 font-medium">
                    Page <strong class="text-slate-900" x-text="clientPage"></strong> of <strong class="text-slate-900" x-text="totalClientPages"></strong>
                </span>
                <button type="button"
                        @click="if (clientPage < totalClientPages) { clientPage++; }"
                        :disabled="clientPage >= totalClientPages"
                        :class="clientPage >= totalClientPages ? 'opacity-35 cursor-not-allowed text-slate-400' : 'text-brand-700 font-bold active:scale-95'"
                        class="px-3 py-1.5 rounded-xl border border-slate-200 text-xs transition cursor-pointer">
                    Next →
                </button>
            </div>
        </div>
    </div>

