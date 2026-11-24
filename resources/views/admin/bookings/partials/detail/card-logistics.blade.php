                <!-- Event Location & Logistics -->
                <div class="bg-white rounded-[28px] p-5 sm:p-7 shadow-card border border-[#EDF2F0] space-y-4"
                     x-data="{
                         editingTransport: false,
                         distanceKm: 0,
                         rawFee: 0,
                         displayFee: '',
                         initTransport() {
                             this.distanceKm = activeDetail?.delivery_distance_km || 0;
                             this.rawFee = activeDetail?.transport_fee || 0;
                             this.displayFee = this.rawFee ? new Intl.NumberFormat('id-ID').format(this.rawFee) : '0';
                             this.editingTransport = false;
                         },
                         formatFeeInput(e) {
                             const clean = e.target.value.replace(/\D/g, '');
                             if (!clean) {
                                 this.rawFee = 0;
                                 this.displayFee = '';
                                 e.target.value = '';
                                 return;
                             }
                             const num = parseInt(clean, 10);
                             this.rawFee = num;
                             this.displayFee = new Intl.NumberFormat('id-ID').format(num);
                             e.target.value = this.displayFee;
                         }
                     }"
                     x-init="$watch('activeDetail', () => initTransport())">

                    <div class="flex items-center justify-between">
                        <h2 class="text-lg font-bold text-slate-900 tracking-tight">Event Location & Logistics</h2>
                        <span class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Field Info</span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
                        <!-- Venue Address -->
                        <div class="p-3.5 rounded-2xl bg-[#F8FAFA] border border-[#EFF4F2] space-y-1.5 sm:col-span-2">
                            <div class="flex items-center justify-between">
                                <span class="text-[10px] uppercase font-bold text-slate-400 tracking-wider">Delivery Address</span>
                                <template x-if="activeDetail?.maps_route_url">
                                    <a :href="activeDetail.maps_route_url" target="_blank"
                                       class="inline-flex items-center gap-1 text-[11px] font-bold text-brand-600 hover:text-brand-700 transition">
                                        <span>Open in Google Maps</span>
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                    </a>
                                </template>
                            </div>
                            <p class="font-bold text-slate-900 text-sm leading-relaxed" x-text="activeDetail?.venue_address || '-'"></p>
                        </div>

                        <!-- Distance -->
                        <div class="p-3.5 rounded-2xl bg-[#F8FAFA] border border-[#EFF4F2] space-y-1">
                            <span class="text-[10px] uppercase font-bold text-slate-400 tracking-wider">Distance</span>
                            <p class="font-bold text-slate-800 text-sm font-mono" x-text="activeDetail?.delivery_distance_km ? `${activeDetail.delivery_distance_km} km` : '-'"></p>
                        </div>

                        <!-- Transport Fee -->
                        <div class="p-3.5 rounded-2xl bg-[#F8FAFA] border border-[#EFF4F2] space-y-1">
                            <div class="flex items-center justify-between">
                                <span class="text-[10px] uppercase font-bold text-slate-400 tracking-wider">Transport Fee</span>
                                <template x-if="activeDetail?.invoiceStatus !== 'Paid' && (activeDetail?.remaining_balance || 0) > 0">
                                    <button type="button" @click="editingTransport = !editingTransport"
                                            class="text-[11px] font-bold text-brand-600 hover:text-brand-700 cursor-pointer">
                                        <span x-text="editingTransport ? 'Cancel' : 'Edit'"></span>
                                    </button>
                                </template>
                            </div>
                            <p class="font-bold text-slate-800 text-sm font-mono"
                               x-text="(activeDetail?.transport_fee || 0) > 0 ? activeDetail.transport_fee_formatted : 'FREE'"></p>
                        </div>

                        <!-- Edit Transport Fee Form (Inline) -->
                        <div x-show="editingTransport && activeDetail?.invoiceStatus !== 'Paid'" x-cloak
                             class="sm:col-span-2 p-3.5 rounded-2xl bg-white border border-slate-200 shadow-sm space-y-2.5">
                            <form :action="activeDetail?.transport_url" method="POST" class="flex flex-col sm:flex-row items-end gap-3 text-xs">
                                @csrf
                                <div class="w-full sm:w-32 space-y-1">
                                    <label class="font-bold text-slate-600">Distance (km)</label>
                                    <input type="number" step="0.1" min="0" max="500" name="delivery_distance_km"
                                           x-model="distanceKm"
                                           class="w-full px-3 py-1.5 rounded-lg border border-slate-200 font-mono font-bold text-slate-900 focus:outline-none focus:border-brand-600">
                                </div>
                                <div class="w-full flex-1 space-y-1">
                                    <label class="font-bold text-slate-600">Transport Fee</label>
                                    <div class="relative">
                                        <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-xs text-slate-400 font-bold pointer-events-none select-none">Rp</span>
                                        <input type="text"
                                               inputmode="numeric"
                                               :value="displayFee"
                                               @input="formatFeeInput($event)"
                                               @blur="if (rawFee) { displayFee = new Intl.NumberFormat('id-ID').format(rawFee); $el.value = displayFee; }"
                                               placeholder="300.000"
                                               class="w-full pl-10 pr-3 py-1.5 rounded-lg border border-slate-200 font-mono font-bold text-slate-900 focus:outline-none focus:border-brand-600">
                                        <input type="hidden" name="transport_fee" :value="rawFee">
                                    </div>
                                </div>
                                <div class="flex items-center gap-2 w-full sm:w-auto">
                                    <button type="submit"
                                            class="px-4 py-2 rounded-lg bg-brand-600 hover:bg-brand-700 text-white font-bold transition cursor-pointer">
                                        Save
                                    </button>
                                    <button type="button" @click="editingTransport = false"
                                            class="px-3 py-2 rounded-lg border border-slate-200 text-slate-600 hover:bg-slate-50 font-medium cursor-pointer">
                                        Cancel
                                    </button>
                                </div>
                            </form>
                        </div>

                        <!-- Landmark -->
                        <div class="p-3.5 rounded-2xl bg-[#F8FAFA] border border-[#EFF4F2] space-y-1">
                            <span class="text-[10px] uppercase font-bold text-slate-400 tracking-wider">Landmark</span>
                            <p class="font-semibold text-slate-800" x-text="activeDetail?.address_landmark || '-'"></p>
                        </div>

                        <!-- Event Time -->
                        <div class="p-3.5 rounded-2xl bg-[#F8FAFA] border border-[#EFF4F2] space-y-1">
                            <span class="text-[10px] uppercase font-bold text-slate-400 tracking-wider">Event Time</span>
                            <p class="font-semibold text-slate-800" x-text="activeDetail?.event_time || '-'"></p>
                        </div>

                        <!-- Site Survey (If Requested) -->
                        <template x-if="activeDetail?.survey_requested">
                            <div class="p-4 rounded-2xl bg-[#F8FAFA] border border-[#EFF4F2] space-y-2.5 sm:col-span-2">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-1.5">
                                        <svg class="w-3.5 h-3.5 text-brand-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                                        </svg>
                                        <span class="text-[10px] uppercase font-bold text-slate-400 tracking-wider">Site Survey</span>
                                    </div>
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase tracking-wider"
                                          :class="activeDetail?.survey_status === 'completed'
                                              ? 'bg-emerald-50 text-emerald-800 border border-emerald-200'
                                              : 'bg-teal-50 text-teal-800 border border-teal-200'">
                                        <span class="w-1.5 h-1.5 rounded-full"
                                              :class="activeDetail?.survey_status === 'completed' ? 'bg-emerald-500' : 'bg-teal-500'"></span>
                                        <span x-text="activeDetail?.survey_status === 'completed' ? 'Survey Selesai' : 'SCHEDULED'"></span>
                                    </span>
                                </div>

                                <div class="flex flex-wrap items-baseline gap-2 pt-0.5">
                                    <span class="font-bold text-slate-900 text-sm" x-text="activeDetail?.survey_date_formatted || '-'"></span>
                                    <span class="text-xs font-medium text-slate-600 bg-white px-2 py-0.5 rounded-md border border-slate-200/80 shadow-2xs"
                                          x-text="activeDetail?.survey_time_slot_label || 'Waktu Fleksibel'"></span>
                                </div>

                                <template x-if="activeDetail?.survey_notes">
                                    <p class="text-xs text-slate-600 bg-white/80 p-2.5 rounded-xl border border-slate-100" x-text="'Survey Notes: ' + activeDetail.survey_notes"></p>
                                </template>

                                <div class="flex flex-wrap items-center gap-2 pt-1 border-t border-[#EFF4F2]">
                                    <!-- Mark Done / Reopen Toggle -->
                                    <form :action="activeDetail?.survey_status_url" method="POST" class="inline">
                                        @csrf
                                        <input type="hidden" name="survey_status" :value="activeDetail?.survey_status === 'completed' ? 'scheduled' : 'completed'">
                                        <button type="submit"
                                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold transition shadow-2xs cursor-pointer border"
                                                :class="activeDetail?.survey_status === 'completed'
                                                    ? 'border-slate-200 bg-white hover:bg-slate-50 text-slate-700'
                                                    : 'border-emerald-300 bg-emerald-50 text-emerald-800 hover:bg-emerald-100'">
                                            <template x-if="activeDetail?.survey_status === 'completed'">
                                                <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                            </template>
                                            <template x-if="activeDetail?.survey_status !== 'completed'">
                                                <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M5 13l4 4L19 7"/></svg>
                                            </template>
                                            <span x-text="activeDetail?.survey_status === 'completed' ? 'Reopen' : 'Mark Done'"></span>
                                        </button>
                                    </form>

                                    <!-- WhatsApp Tim Survey -->
                                    <a :href="'https://wa.me/' + (activeDetail?.phone ? activeDetail.phone.replace(/[^0-9]/g, '').replace(/^0/, '62') : '') + '?text=' + encodeURIComponent('Halo Kak ' + (activeDetail?.name || '') + ', salam dari tim survey NK Barokah Tenda. Mengenai rencana survey lokasi venue pada ' + (activeDetail?.survey_date_formatted || '') + ' (' + (activeDetail?.survey_time_slot_label || '') + ')...')"
                                       target="_blank"
                                       class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-white hover:bg-teal-50 text-teal-800 text-xs font-bold transition border border-teal-200/80 shadow-2xs">
                                        <svg class="w-3.5 h-3.5 fill-current text-teal-600" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981z"/></svg>
                                        <span>Message Client</span>
                                    </a>
                                </div>
                            </div>
                        </template>

                        <!-- Parents' Names -->
                        <div class="p-3.5 rounded-2xl bg-[#F8FAFA] border border-[#EFF4F2] space-y-1">
                            <span class="text-[10px] uppercase font-bold text-slate-400 tracking-wider">Parents' Names</span>
                            <p class="font-semibold text-slate-800" x-text="activeDetail?.parents_name || '-'"></p>
                        </div>

                        <!-- Instagram Account -->
                        <div class="p-3.5 rounded-2xl bg-[#F8FAFA] border border-[#EFF4F2] space-y-1">
                            <span class="text-[10px] uppercase font-bold text-slate-400 tracking-wider">Instagram</span>
                            <p class="font-semibold text-brand-700" x-text="activeDetail?.instagram_account || '-'"></p>
                        </div>
                    </div>
                </div>
