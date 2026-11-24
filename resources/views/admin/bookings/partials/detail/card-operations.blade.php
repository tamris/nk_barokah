                <!-- Field Operations & Crew SPK Management -->
                <div class="bg-white rounded-[28px] p-5 sm:p-7 shadow-card border border-[#EDF2F0] space-y-4"
                     x-data="{
                         editingOperations: false,
                         tentColor: '',
                         tentSize: '',
                         opNotes: '',
                         jobSource: '',
                         setupDate: '',
                         initOperations() {
                             this.tentColor = (activeDetail?.tent_color_theme && activeDetail?.tent_color_theme !== '-') ? activeDetail.tent_color_theme : '';
                             this.tentSize = (activeDetail?.custom_tent_size && activeDetail?.custom_tent_size !== '-') ? activeDetail.custom_tent_size : '';
                             this.opNotes = activeDetail?.operational_notes || '';
                             this.jobSource = activeDetail?.job_source || '';
                             this.setupDate = activeDetail?.setup_date_iso || '';
                             this.editingOperations = false;
                         }
                     }"
                     x-init="$watch('activeDetail', () => initOperations())">
                    <div class="flex items-center justify-between pb-3 border-b border-[#EFF4F2]">
                        <div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-full bg-amber-50 text-amber-700 flex items-center justify-center shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                            </div>
                            <div>
                                <h3 class="text-sm font-bold text-slate-900">Field Operations & Crew SPK</h3>
                                
                            </div>
                        </div>

                        <div class="flex items-center gap-2">
                            <a :href="activeDetail?.spk_url || '#'" target="_blank"
                               title="Print SPK / Surat Jalan"
                               class="w-8 h-8 rounded-full bg-emerald-50 hover:bg-emerald-100 text-emerald-700 border border-emerald-200/80 flex items-center justify-center transition shadow-2xs">
                                <svg class="w-4 h-4 text-emerald-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4H7v4a2 2 0 002 2zM7 9V5a2 2 0 012-2h6a2 2 0 012 2v4"/></svg>
                            </a>
                            <button type="button"
                                    @click="editingOperations = !editingOperations"
                                    :title="editingOperations ? 'Cancel' : 'Edit Details'"
                                    class="w-8 h-8 rounded-full border transition flex items-center justify-center cursor-pointer shadow-2xs"
                                    :class="editingOperations
                                        ? 'border-slate-300 bg-slate-100 text-slate-700 hover:bg-slate-200'
                                        : 'border-slate-200 bg-white hover:bg-brand-50 text-slate-600 hover:text-brand-700 hover:border-brand-200'">
                                <template x-if="!editingOperations">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                                    </svg>
                                </template>
                                <template x-if="editingOperations">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </template>
                            </button>
                        </div>
                    </div>

                    <!-- Display Mode Grid -->
                    <div x-show="!editingOperations" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 text-xs">
                        <!-- Tent Color Theme -->
                        <div class="p-3 rounded-2xl bg-[#F8FAFA] border border-[#EFF4F2] space-y-1">
                            <span class="text-[10px] uppercase font-bold text-slate-400 tracking-wider">Tent Color Theme</span>
                            <div class="pt-0.5">
                                <template x-if="activeDetail?.tent_color_theme && activeDetail?.tent_color_theme !== '-'">
                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-black uppercase tracking-tight bg-rose-50 text-rose-700 border border-rose-200"
                                          x-text="activeDetail?.tent_color_theme"></span>
                                </template>
                                <template x-if="!activeDetail?.tent_color_theme || activeDetail?.tent_color_theme === '-'">
                                    <p class="font-bold text-slate-800 text-xs">-</p>
                                </template>
                            </div>
                        </div>

                        <!-- Tent Dimensions / Size -->
                        <div class="p-3 rounded-2xl bg-[#F8FAFA] border border-[#EFF4F2] space-y-1">
                            <span class="text-[10px] uppercase font-bold text-slate-400 tracking-wider">Custom Tent Size / Modul</span>
                            <p class="font-bold text-slate-800 text-xs truncate" x-text="activeDetail?.custom_tent_size || '-'"></p>
                        </div>

                        <!-- Setup Schedule (H-2) -->
                        <div class="p-3 rounded-2xl bg-[#F8FAFA] border border-[#EFF4F2] space-y-1">
                            <span class="text-[10px] uppercase font-bold text-slate-400 tracking-wider">Tent Setup (H-2)</span>
                            <p class="font-bold text-emerald-800 text-xs" x-text="activeDetail?.setup_date_formatted || '-'"></p>
                        </div>

                        <!-- Job Source / Partner -->
                        <div class="p-3 rounded-2xl bg-[#F8FAFA] border border-[#EFF4F2] space-y-1">
                            <span class="text-[10px] uppercase font-bold text-slate-400 tracking-wider">Job Source / Partner</span>
                            <p class="font-bold text-indigo-700 text-xs truncate" x-text="activeDetail?.job_source || 'Direct Client'"></p>
                        </div>

                        <!-- Operational Notes -->
                        <div class="sm:col-span-2 lg:col-span-4 p-3 rounded-2xl bg-amber-50/60 border border-amber-200/80 space-y-1">
                            <span class="text-[10px] uppercase font-black text-amber-900 tracking-wider flex items-center gap-1.5">
                                <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                                Crew & Logistics Instructions
                            </span>
                            <p class="text-xs font-bold text-amber-950 leading-relaxed whitespace-pre-line"
                               x-text="activeDetail?.operational_notes || 'No special crew instructions recorded.'"></p>
                        </div>
                    </div>

                    <!-- Inline Edit Form -->
                    <div x-show="editingOperations" x-cloak class="p-4 sm:p-5 rounded-2xl bg-[#F8FAFA] border border-[#EFF4F2] space-y-4">
                        <form :action="activeDetail?.operations_url" method="POST" class="space-y-4 text-xs">
                            @csrf
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3.5">
                                <div class="space-y-1.5">
                                    <label class="block text-[10px] font-extrabold uppercase tracking-wider text-slate-500">
                                        Tent Color Theme
                                    </label>
                                    <input type="text" name="tent_color_theme" x-model="tentColor"
                                           placeholder="e.g. Putih + Sage Green, Mocca"
                                           class="w-full px-3.5 py-2.5 rounded-2xl border border-[#E2EAE7] bg-white text-xs sm:text-sm font-semibold text-slate-900 placeholder:text-slate-400 focus:outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-500/15 transition shadow-2xs">
                                </div>
                                <div class="space-y-1.5">
                                    <label class="block text-[10px] font-extrabold uppercase tracking-wider text-slate-500">
                                        Custom Tent Size / Layout
                                    </label>
                                    <input type="text" name="custom_tent_size" x-model="tentSize"
                                           placeholder="e.g. 6x16 + 7x7 + 3,5x5"
                                           class="w-full px-3.5 py-2.5 rounded-2xl border border-[#E2EAE7] bg-white text-xs sm:text-sm font-semibold text-slate-900 placeholder:text-slate-400 focus:outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-500/15 transition shadow-2xs">
                                </div>
                                <div class="space-y-1.5">
                                    <label class="block text-[10px] font-extrabold uppercase tracking-wider text-slate-500">
                                        Job Source / Partner
                                    </label>
                                    <input type="text" name="job_source" x-model="jobSource"
                                           placeholder="e.g. Direct Client, Job Nadia"
                                           class="w-full px-3.5 py-2.5 rounded-2xl border border-[#E2EAE7] bg-white text-xs sm:text-sm font-semibold text-slate-900 placeholder:text-slate-400 focus:outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-500/15 transition shadow-2xs">
                                </div>
                                <div class="space-y-1.5">
                                    <label class="block text-[10px] font-extrabold uppercase tracking-wider text-slate-500">
                                        Setup Date (Override H-2)
                                    </label>
                                    <input type="date" name="setup_date" x-model="setupDate"
                                           class="w-full px-3.5 py-2.5 rounded-2xl border border-[#E2EAE7] bg-white font-mono text-xs sm:text-sm font-semibold text-slate-900 focus:outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-500/15 transition shadow-2xs">
                                </div>
                            </div>

                            <div class="space-y-1.5">
                                <label class="block text-[10px] font-extrabold uppercase tracking-wider text-slate-500">
                                    Special Crew & Operational Notes
                                </label>
                                <textarea name="operational_notes" x-model="opNotes" rows="3"
                                          placeholder="e.g. TIM LAYOS BAWA GENSET, Tenda 4x10 nutup jalan setengah, cover dinding dalam rumah"
                                          class="w-full px-3.5 py-2.5 rounded-2xl border border-[#E2EAE7] bg-white text-xs sm:text-sm font-medium text-slate-900 placeholder:text-slate-400 focus:outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-500/15 leading-relaxed resize-none transition shadow-2xs"></textarea>
                            </div>

                            <div class="flex items-center justify-end gap-2.5 pt-2 border-t border-[#EFF4F2]">
                                <button type="button" @click="editingOperations = false"
                                        class="px-5 py-2.5 rounded-full border border-[#DCE5E2] text-slate-700 hover:bg-slate-100 font-bold text-xs cursor-pointer transition">
                                    Cancel
                                </button>
                                <button type="submit"
                                        class="px-6 py-2.5 rounded-full bg-brand-600 hover:bg-brand-700 text-white font-bold text-xs transition shadow-md shadow-brand-600/20 cursor-pointer active:scale-95">
                                    Save Changes
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
