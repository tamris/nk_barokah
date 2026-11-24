                <!-- Client Information -->
                <div class="bg-white rounded-[28px] p-5 sm:p-7 shadow-card border border-[#EDF2F0] space-y-4">
                    <div class="flex items-center justify-between gap-2">
                        <h2 class="text-base sm:text-lg font-bold text-slate-900 tracking-tight">Client Information</h2>
                        <button type="button"
                                @click="openClientProfile(activeDetail)"
                                class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full bg-[#F4F7F6] hover:bg-brand-50 hover:text-brand-700 text-xs font-semibold text-slate-700 transition border border-transparent hover:border-brand-200 cursor-pointer">
                            <span>View Profile</span>
                            <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </button>
                    </div>

                    <div class="flex items-start gap-3.5 sm:gap-4">
                        <img :src="activeDetail?.avatar" :alt="activeDetail?.name" class="w-13 h-13 sm:w-14 sm:h-14 rounded-2xl object-cover shrink-0 ring-1 ring-slate-100 shadow-2xs">
                        <div class="min-w-0 flex-1 space-y-1.5">
                            <div class="flex flex-wrap items-center gap-2">
                                <h3 class="text-sm sm:text-base font-bold text-slate-900 leading-snug" x-text="activeDetail?.name"></h3>
                                <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-extrabold uppercase tracking-wider bg-slate-100 text-slate-700 border border-slate-200"
                                      x-text="activeDetail?.event_type_label || 'Pernikahan'"></span>
                            </div>

                            <template x-if="activeDetail?.child_name">
                                <p class="text-xs text-brand-700 font-semibold" x-text="'Nama Anak: ' + activeDetail.child_name"></p>
                            </template>

                            <div class="flex flex-wrap items-center gap-x-4 gap-y-1 text-xs text-slate-500 pt-0.5">
                                <template x-if="activeDetail?.phone">
                                    <div class="inline-flex items-center gap-1.5 font-mono text-slate-700 font-medium">
                                        <svg class="w-3.5 h-3.5 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                        </svg>
                                        <span x-text="activeDetail.phone"></span>
                                    </div>
                                </template>

                                <template x-if="activeDetail?.email">
                                    <div class="inline-flex items-center gap-1.5 text-slate-500 truncate">
                                        <svg class="w-3.5 h-3.5 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                        </svg>
                                        <span class="truncate" x-text="activeDetail.email"></span>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>
