    <!-- ================================================================== -->
    <!-- FULL-PAGE VIEW: CLIENT PROFILE                                     -->
    <!-- ================================================================== -->
    <div x-show="$store.nav.tab === 'client_profile' && activeClient !== null"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 translate-y-1"
         x-transition:enter-end="opacity-100 translate-y-0"
         class="space-y-5"
         x-cloak>

        <button type="button" @click="$store.nav.setTab('clients')" class="inline-flex items-center gap-2 group cursor-pointer">
            <span class="w-8 h-8 rounded-full bg-white border border-[#E5ECE9] text-slate-600 group-hover:text-brand-600 flex items-center justify-center shadow-2xs transition">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.4" d="M15 19l-7-7 7-7"/></svg>
            </span>
            <span class="px-4 py-1.5 rounded-full bg-[#ECEFEF] group-hover:bg-[#E2E8E7] text-xs font-semibold text-slate-700 transition">
                Back to Clients
            </span>
        </button>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 items-start">
            <div class="lg:col-span-4 bg-white rounded-[28px] p-6 shadow-card border border-[#EDF2F0] space-y-5">
                <div class="flex items-center gap-4">
                    <img :src="activeClient?.avatar" :alt="activeClient?.name" class="w-16 h-16 rounded-2xl object-cover shrink-0">
                    <div>
                        <h2 class="text-lg font-bold text-slate-900" x-text="activeClient?.name"></h2>
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-[#E6F6EF] text-emerald-700 mt-1">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                            <span x-text="activeClient?.status"></span>
                        </span>
                    </div>
                </div>

                <div class="space-y-2.5 pt-4 border-t border-[#EFF4F2] text-xs">
                    <div class="flex justify-between"><span class="text-slate-400">WhatsApp</span><span class="font-semibold text-slate-800" x-text="activeClient?.phone"></span></div>
                    <div class="flex justify-between"><span class="text-slate-400">Email</span><span class="font-semibold text-slate-800" x-text="activeClient?.email"></span></div>
                    <div class="flex justify-between"><span class="text-slate-400">Total Spent</span><span class="font-bold text-brand-600" x-text="activeClient?.totalSpentFormatted"></span></div>
                </div>

                <a :href="activeClient?.whatsapp_url || '#'" target="_blank"
                   class="block w-full py-2.5 rounded-full bg-brand-600 hover:bg-brand-700 text-white text-xs font-semibold text-center transition shadow-pill">
                    Chat via WhatsApp
                </a>
            </div>

            <div class="lg:col-span-8 bg-white rounded-[28px] p-6 shadow-card border border-[#EDF2F0] space-y-4">
                <h3 class="text-lg font-bold text-slate-900">Booking & Invoice History</h3>
                <div class="space-y-3">
                    <template x-for="b in allBookings.filter(i => i.name === activeClient?.name || i.phone === activeClient?.phone)" :key="b.id">
                        <div @click="openDetail(b)" class="p-4 rounded-2xl bg-[#F8FAFA] hover:bg-[#F1F6F5] border border-[#EFF4F2] flex flex-col sm:flex-row sm:items-center justify-between gap-3 transition cursor-pointer">
                            <div>
                                <div class="flex items-center gap-2">
                                    <span class="text-xs font-bold font-mono text-[#0284C7]" x-text="'#' + b.code"></span>
                                    <span class="text-sm font-bold text-slate-900" x-text="b.service"></span>
                                </div>
                                <p class="text-xs text-slate-400 mt-0.5" x-text="b.date"></p>
                            </div>
                            <div class="flex items-center gap-3">
                                <span class="text-sm font-bold text-slate-900" x-text="b.grand_total_formatted"></span>
                                <span class="px-2.5 py-1 rounded-full text-[11px] font-semibold bg-[#E6F6EF] text-emerald-700" x-text="b.bookingStatus"></span>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </div>

