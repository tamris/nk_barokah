    <!-- ================================================================== -->
    <!-- TAB 6: INVOICE SKELETON SHIMMER (FLUTTER STYLE)                    -->
    <!-- ================================================================== -->
    <div x-show="$store.nav.tab === 'invoice' && pageLoading"
         class="space-y-5"
         {{ request('tab') === 'invoice' ? '' : 'x-cloak' }}>
        <!-- 3 Financial Summary Cards Shimmer -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="bg-white rounded-[24px] p-5 shadow-card border border-[#EDF2F0] flex items-center gap-4">
                <div class="w-12 h-12 rounded-full shimmer-mask shrink-0"></div>
                <div class="space-y-2">
                    <div class="h-3 w-28 rounded shimmer-mask"></div>
                    <div class="h-6 w-32 rounded shimmer-mask"></div>
                </div>
            </div>
            <div class="bg-white rounded-[24px] p-5 shadow-card border border-[#EDF2F0] flex items-center gap-4">
                <div class="w-12 h-12 rounded-full shimmer-mask shrink-0"></div>
                <div class="space-y-2">
                    <div class="h-3 w-28 rounded shimmer-mask"></div>
                    <div class="h-6 w-32 rounded shimmer-mask"></div>
                </div>
            </div>
            <div class="bg-white rounded-[24px] p-5 shadow-card border border-[#EDF2F0] flex items-center gap-4">
                <div class="w-12 h-12 rounded-full shimmer-mask shrink-0"></div>
                <div class="space-y-2">
                    <div class="h-3 w-28 rounded shimmer-mask"></div>
                    <div class="h-6 w-32 rounded shimmer-mask"></div>
                </div>
            </div>
        </div>

        <!-- Table Skeleton -->
        <div class="bg-white rounded-[28px] shadow-card border border-[#EDF2F0] p-6 space-y-4">
            <div class="h-10 w-72 rounded-full shimmer-mask"></div>
            <div class="flex items-center justify-between pb-3 border-b border-[#EFF4F2]">
                <div class="h-4 w-28 rounded shimmer-mask"></div>
                <div class="h-4 w-36 rounded shimmer-mask"></div>
                <div class="h-4 w-28 rounded shimmer-mask"></div>
                <div class="h-4 w-28 rounded shimmer-mask"></div>
                <div class="h-4 w-24 rounded shimmer-mask"></div>
            </div>
            <div class="space-y-4">
                <template x-for="i in 5" :key="'shimmer-inv-row-' + i">
                    <div class="flex items-center justify-between py-2 border-b border-[#F8FAFA]">
                        <div class="h-4 w-24 rounded shimmer-mask"></div>
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg shimmer-mask shrink-0"></div>
                            <div class="h-4 w-32 rounded shimmer-mask"></div>
                        </div>
                        <div class="h-4 w-28 rounded shimmer-mask"></div>
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
    <!-- TAB 6: INVOICE (3 FINANCIAL SUMMARY CARDS + REAL 6-COLUMN TABLE)   -->
    <!-- ================================================================== -->
    <div x-show="$store.nav.tab === 'invoice' && !pageLoading"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 translate-y-1"
         x-transition:enter-end="opacity-100 translate-y-0"
         class="space-y-5"
         x-cloak>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="bg-white rounded-[24px] p-5 shadow-card border border-[#EDF2F0] flex items-center gap-4">
                <div class="w-12 h-12 rounded-full bg-[#E6F6EF] text-emerald-700 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M5 13l4 4L19 7"/></svg>
                </div>
                <div>
                    <span class="text-xs font-medium text-slate-400 block">Total Collected (Paid)</span>
                    <span class="text-xl font-extrabold text-slate-900 tracking-tight mt-0.5 block">{{ $metrics['total_collected_fmt'] }}</span>
                </div>
            </div>

            <div class="bg-white rounded-[24px] p-5 shadow-card border border-[#EDF2F0] flex items-center gap-4">
                <div class="w-12 h-12 rounded-full bg-[#FEF6E0] text-amber-600 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div>
                    <span class="text-xs font-medium text-slate-400 block">Outstanding Balance</span>
                    <span class="text-xl font-extrabold text-amber-600 tracking-tight mt-0.5 block">{{ $metrics['total_outstanding_fmt'] }}</span>
                </div>
            </div>

            <div class="bg-white rounded-[24px] p-5 shadow-card border border-[#EDF2F0] flex items-center gap-4">
                <div class="w-12 h-12 rounded-full bg-brand-50 text-brand-600 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                </div>
                <div>
                    <span class="text-xs font-medium text-slate-400 block">Total Billed Value</span>
                    <span class="text-xl font-extrabold text-slate-900 tracking-tight mt-0.5 block">{{ $metrics['total_billed_fmt'] }}</span>
                </div>
            </div>
        </div>

        <div class="flex flex-wrap items-center gap-3">
            <div class="relative flex items-center bg-white rounded-full border border-[#E7EEEC] w-full sm:w-72">
                <svg class="w-4 h-4 text-slate-400 absolute left-4 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-5.2-5.2m2.2-4.8a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input type="text" x-model="invoiceSearch" placeholder="Search Invoice ID or Client..."
                       class="w-full bg-transparent pl-10 pr-4 py-2.5 text-xs text-slate-700 placeholder-slate-400 focus:outline-none">
            </div>

            <div class="relative" @click.away="invoiceMenuOpen = false">
                <button type="button" @click="invoiceMenuOpen = !invoiceMenuOpen"
                        class="inline-flex items-center justify-between gap-4 bg-white hover:bg-[#FAFDFC] rounded-full border border-[#E7EEEC] px-4 py-2.5 text-xs font-medium text-slate-700 transition min-w-[148px]">
                    <span x-text="invoiceFilter === 'all' ? 'All Status' : invoiceFilter"></span>
                    <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div x-show="invoiceMenuOpen" class="absolute left-0 mt-2 w-44 bg-white rounded-2xl shadow-xl border border-[#EAF0EE] py-1.5 z-30" x-cloak>
                    <template x-for="st in ['all', 'Paid', 'Sent', 'Draft', 'Overdue']" :key="st">
                        <button type="button" @click="invoiceFilter = st; invoiceMenuOpen = false"
                                :class="invoiceFilter === st ? 'bg-brand-50 text-brand-700 font-semibold' : 'text-slate-600 hover:bg-[#F6F8F7]'"
                                class="w-full text-left px-4 py-2 text-xs transition flex items-center justify-between">
                            <span x-text="st === 'all' ? 'All Status' : st"></span>
                            <span x-show="invoiceFilter === st" class="text-brand-600">✓</span>
                        </button>
                    </template>
                </div>
            </div>
        </div>

        <div class="hidden md:block bg-white rounded-[28px] shadow-card border border-[#EDF2F0] overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="text-xs font-semibold text-slate-600 border-b border-[#EFF4F2]">
                            <th class="py-4 px-6">Invoice ID</th>
                            <th class="py-4 px-5">Client</th>
                            <th class="py-4 px-5">Amount & Balance</th>
                            <th class="py-4 px-5">Event Date</th>
                            <th class="py-4 px-5">Status</th>
                            <th class="py-4 px-6 text-right">Quick Actions</th>
                        </tr>
                    </thead>
                    <!-- Shimmer Rows during Pagination -->
                    <tbody x-show="invoiceTableLoading" class="divide-y divide-[#F1F5F4]" x-cloak>
                        <template x-for="i in Math.min(5, invoicePerPage)" :key="'inv-shim-' + i">
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
                                    <div class="h-4 w-28 rounded-md shimmer-mask"></div>
                                </td>
                                <td class="py-4 px-5 whitespace-nowrap">
                                    <div class="h-4 w-24 rounded-md shimmer-mask"></div>
                                </td>
                                <td class="py-4 px-5 whitespace-nowrap">
                                    <div class="h-6 w-16 rounded-full shimmer-mask"></div>
                                </td>
                                <td class="py-4 px-6 whitespace-nowrap text-right">
                                    <div class="h-6 w-24 rounded-full shimmer-mask ml-auto"></div>
                                </td>
                            </tr>
                        </template>
                    </tbody>

                    <!-- Real Data Rows -->
                    <tbody x-show="!invoiceTableLoading" class="divide-y divide-[#F1F5F4]">
                        <template x-for="inv in paginatedInvoices" :key="inv.id">
                            <tr @click="openDetail(inv)" class="hover:bg-[#F8FBFA] transition cursor-pointer group">
                                <td class="py-4 px-6 whitespace-nowrap">
                                    <span class="text-[13px] font-semibold font-mono text-[#0284C7] group-hover:underline" x-text="'#' + inv.code"></span>
                                </td>
                                <td class="py-4 px-5 whitespace-nowrap">
                                    <div class="flex items-center gap-3">
                                        <img :src="inv.avatar" :alt="inv.name" class="w-8 h-8 rounded-lg object-cover shrink-0">
                                        <div>
                                            <p class="text-[13px] font-medium text-slate-800" x-text="inv.name"></p>
                                            <p class="text-[11px] text-slate-400" x-text="inv.service"></p>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-4 px-5 whitespace-nowrap">
                                    <p class="text-[13px] font-bold text-slate-900" x-text="inv.grand_total_formatted"></p>
                                    <p class="text-[11px] mt-0.5" :class="inv.remaining_balance <= 0 ? 'text-emerald-600 font-semibold' : 'text-amber-600 font-medium'"
                                       x-text="inv.remaining_balance <= 0 ? 'Settled (Paid in Full)' : 'Remaining: ' + inv.remaining_formatted"></p>
                                </td>
                                <td class="py-4 px-5 whitespace-nowrap text-[13px] font-medium text-slate-700" x-text="inv.date_short"></td>
                                <td class="py-4 px-5 whitespace-nowrap">
                                    <span :class="{
                                            'bg-[#E6F6EF] text-emerald-700': inv.invoiceStatus === 'Paid',
                                            'bg-[#FEF6E0] text-amber-600': inv.invoiceStatus === 'Sent',
                                            'bg-[#F1F5F9] text-slate-500': inv.invoiceStatus === 'Draft',
                                            'bg-[#FEECEB] text-rose-600': inv.invoiceStatus === 'Overdue' || inv.invoiceStatus === 'Cancelled'
                                          }"
                                          class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-semibold">
                                        <span class="w-1.5 h-1.5 rounded-full"
                                              :class="{
                                                'bg-emerald-500': inv.invoiceStatus === 'Paid',
                                                'bg-amber-400': inv.invoiceStatus === 'Sent',
                                                'bg-slate-400': inv.invoiceStatus === 'Draft',
                                                'bg-rose-500': inv.invoiceStatus === 'Overdue' || inv.invoiceStatus === 'Cancelled'
                                              }"></span>
                                        <span x-text="inv.invoiceStatus"></span>
                                    </span>
                                </td>
                                <td class="py-4 px-6 whitespace-nowrap text-right" @click.stop>
                                    <div class="inline-flex items-center gap-1.5">
                                        <a :href="inv.receipt_url" target="_blank"
                                           class="px-3 py-1.5 rounded-full border border-[#E5ECE9] bg-white hover:bg-[#F6F8F7] text-[11px] font-semibold text-slate-600 transition">
                                            PDF
                                        </a>
                                        <a :href="inv.whatsapp_url" target="_blank"
                                           class="px-3 py-1.5 rounded-full bg-[#E6F6EF] hover:bg-[#D5F0E4] text-[11px] font-semibold text-emerald-700 transition">
                                            WhatsApp
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        </template>

                        <tr x-show="filteredInvoices.length === 0">
                            <td colspan="6" class="py-12 text-center text-xs text-slate-400">
                                No invoices match your search or filter.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Table Footer with Interactive Pagination for Invoices -->
            <div class="px-6 py-4 bg-[#FAFDFC] border-t border-[#EFF4F2] flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs text-slate-500 font-medium">
                <div class="flex items-center gap-3">
                    <span x-show="filteredInvoices.length > 0">
                        Showing <strong class="text-slate-900 font-semibold" x-text="invoiceFrom"></strong>–<strong class="text-slate-900 font-semibold" x-text="invoiceTo"></strong> of <strong class="text-slate-900 font-semibold" x-text="filteredInvoices.length"></strong> invoices
                        <span x-show="filteredInvoices.length !== allBookings.length" class="text-slate-400 text-[11px]" x-text="'(' + allBookings.length + ' total)'"></span>
                    </span>
                    <span x-show="filteredInvoices.length === 0">
                        0 invoices
                    </span>

                    <div class="hidden sm:flex items-center gap-1.5 pl-3 border-l border-slate-200">
                        <span class="text-slate-400 text-[11px]">Per page:</span>
                        <select x-model.number="invoicePerPage"
                                class="bg-white border border-[#E2EAE7] rounded-lg px-2 py-1 text-xs font-semibold text-slate-700 focus:outline-none focus:border-brand-600 cursor-pointer">
                            <option :value="10">10</option>
                            <option :value="20">20</option>
                            <option :value="50">50</option>
                        </select>
                    </div>
                </div>

                <!-- Pagination Buttons for Invoices -->
                <div x-show="totalInvoicePages > 1" class="flex items-center gap-1">
                    <button type="button"
                            @click="if (invoicePage > 1) { invoicePage--; }"
                            :disabled="invoicePage <= 1"
                            :class="invoicePage <= 1 ? 'opacity-35 cursor-not-allowed text-slate-300' : 'text-slate-600 hover:bg-white hover:text-brand-700 hover:border-slate-300'"
                            class="inline-flex items-center justify-center w-8 h-8 rounded-lg border border-[#E2EAE7] bg-white transition cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    </button>

                    <template x-for="(p, idx) in invoicePaginationRange" :key="'ip-' + idx + '-' + p">
                        <div>
                            <template x-if="p === '...'">
                                <span class="inline-flex items-center justify-center w-8 h-8 text-xs text-slate-400 select-none">…</span>
                            </template>
                            <template x-if="p !== '...'">
                                <button type="button"
                                        @click="invoicePage = p"
                                        :class="invoicePage === p 
                                            ? 'bg-brand-600 text-white font-bold border-brand-600 shadow-2xs' 
                                            : 'bg-white text-slate-600 hover:bg-slate-50 border-[#E2EAE7] hover:border-slate-300'"
                                        class="inline-flex items-center justify-center min-w-[32px] h-8 px-2 rounded-lg border text-xs font-semibold transition cursor-pointer"
                                        x-text="p">
                                </button>
                            </template>
                        </div>
                    </template>

                    <button type="button"
                            @click="if (invoicePage < totalInvoicePages) { invoicePage++; }"
                            :disabled="invoicePage >= totalInvoicePages"
                            :class="invoicePage >= totalInvoicePages ? 'opacity-35 cursor-not-allowed text-slate-300' : 'text-slate-600 hover:bg-white hover:text-brand-700 hover:border-slate-300'"
                            class="inline-flex items-center justify-center w-8 h-8 rounded-lg border border-[#E2EAE7] bg-white transition cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </button>
                </div>
            </div>
        </div>

        <div class="md:hidden space-y-3">
            <template x-for="inv in paginatedInvoices" :key="inv.id">
                <div @click="openDetail(inv)" class="bg-white rounded-[24px] p-4 shadow-card border border-[#EDF2F0] space-y-3 cursor-pointer">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold font-mono text-[#0284C7]" x-text="'#' + inv.code"></span>
                        <span class="text-[11px] text-slate-400" x-text="inv.date_short"></span>
                    </div>
                    <div class="flex items-center gap-3">
                        <img :src="inv.avatar" :alt="inv.name" class="w-10 h-10 rounded-xl object-cover">
                        <div class="min-w-0">
                            <p class="text-sm font-bold text-slate-900 truncate" x-text="inv.name"></p>
                            <p class="text-xs text-slate-500 truncate" x-text="inv.service"></p>
                        </div>
                    </div>
                    <div class="pt-2.5 border-t border-[#F1F5F4] flex items-center justify-between">
                        <div>
                            <p class="text-xs font-bold text-slate-900" x-text="inv.grand_total_formatted"></p>
                            <p class="text-[10px]" :class="inv.remaining_balance <= 0 ? 'text-emerald-600 font-semibold' : 'text-amber-600'"
                               x-text="inv.remaining_balance <= 0 ? 'Paid 100%' : 'Due: ' + inv.remaining_formatted"></p>
                        </div>
                        <span class="px-2.5 py-1 rounded-full text-[11px] font-semibold bg-[#F4F7F6] text-slate-700" x-text="inv.invoiceStatus"></span>
                    </div>
                </div>
            </template>

            <!-- Mobile Pagination for Invoices -->
            <div x-show="totalInvoicePages > 1" class="flex items-center justify-between p-3.5 bg-white rounded-2xl shadow-card border border-[#EDF2F0]">
                <button type="button"
                        @click="if (invoicePage > 1) { invoicePage--; }"
                        :disabled="invoicePage <= 1"
                        :class="invoicePage <= 1 ? 'opacity-35 cursor-not-allowed text-slate-400' : 'text-brand-700 font-bold active:scale-95'"
                        class="px-3 py-1.5 rounded-xl border border-slate-200 text-xs transition cursor-pointer">
                    ← Prev
                </button>
                <span class="text-xs text-slate-500 font-medium">
                    Page <strong class="text-slate-900" x-text="invoicePage"></strong> of <strong class="text-slate-900" x-text="totalInvoicePages"></strong>
                </span>
                <button type="button"
                        @click="if (invoicePage < totalInvoicePages) { invoicePage++; }"
                        :disabled="invoicePage >= totalInvoicePages"
                        :class="invoicePage >= totalInvoicePages ? 'opacity-35 cursor-not-allowed text-slate-400' : 'text-brand-700 font-bold active:scale-95'"
                        class="px-3 py-1.5 rounded-xl border border-slate-200 text-xs transition cursor-pointer">
                    Next →
                </button>
            </div>
        </div>
    </div>

