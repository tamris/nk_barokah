            <!-- RIGHT COLUMN (4 COLS: TIMELINE + STATUS + REAL ACTIONS) -->
            <div class="lg:col-span-4 space-y-5">

                <!-- Timeline -->
                <div class="bg-white rounded-[28px] p-5 sm:p-6 shadow-card border border-[#EDF2F0] space-y-4">
                    <h2 class="text-lg font-bold text-slate-900 tracking-tight">Timeline</h2>
                    <div class="space-y-4 relative pl-4 before:content-[''] before:absolute before:left-[5px] before:top-2 before:bottom-2 before:w-px before:bg-[#E8EFED]">
                        <div class="relative">
                            <span class="w-2.5 h-2.5 rounded-full bg-purple-500 ring-4 ring-white absolute -left-4 top-1"></span>
                            <p class="text-xs font-bold text-slate-800">Booking Created</p>
                            <p class="text-[11px] text-slate-400 mt-0.5" x-text="activeDetail?.created_at_label"></p>
                        </div>
                        <div class="relative">
                            <span class="w-2.5 h-2.5 rounded-full ring-4 ring-white absolute -left-4 top-1"
                                  :class="activeDetail?.raw_status === 'confirmed' || activeDetail?.raw_status === 'completed' ? 'bg-emerald-500' : 'bg-slate-300'"></span>
                            <p class="text-xs font-bold text-slate-800" x-text="'Status: ' + (activeDetail?.bookingStatus || '')"></p>
                            <p class="text-[11px] text-slate-400 mt-0.5" x-text="activeDetail?.updated_at_label"></p>
                        </div>
                        <div class="relative">
                            <span class="w-2.5 h-2.5 rounded-full ring-4 ring-white absolute -left-4 top-1"
                                  :class="activeDetail?.google_calendar_synced ? 'bg-brand-600' : 'bg-slate-300'"></span>
                            <p class="text-xs font-bold text-slate-800" x-text="activeDetail?.google_calendar_synced ? 'Synced to Google Calendar' : 'Pending Calendar Sync'"></p>
                            <p class="text-[11px] text-slate-400 mt-0.5" x-text="activeDetail?.date"></p>
                        </div>
                    </div>
                </div>

                <!-- Status -->
                <div class="bg-white rounded-[28px] p-5 sm:p-6 shadow-card border border-[#EDF2F0] space-y-4">
                    <h2 class="text-lg font-bold text-slate-900 tracking-tight">Status</h2>
                    <div class="space-y-3 text-xs">
                        <div class="flex items-center justify-between">
                            <span class="text-slate-500 font-medium">Booking Status</span>
                            <span class="px-3 py-1 rounded-full text-[11px] font-semibold bg-[#E6F6EF] text-emerald-700" x-text="activeDetail?.bookingStatus"></span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-slate-500 font-medium">Invoice Status</span>
                            <span class="px-3 py-1 rounded-full text-[11px] font-semibold bg-[#FEF6E0] text-amber-700" x-text="activeDetail?.invoiceStatus"></span>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="bg-white rounded-[28px] p-5 sm:p-6 shadow-card border border-[#EDF2F0] space-y-2.5">
                    <h2 class="text-lg font-bold text-slate-900 tracking-tight mb-3">Actions</h2>

                    <a :href="activeDetail?.whatsapp_url || '#'" target="_blank"
                       class="block w-full py-2.5 px-4 rounded-full bg-brand-600 hover:bg-brand-700 text-white text-xs font-semibold text-center transition shadow-pill">
                        Chat WhatsApp Client
                    </a>

                    <template x-if="activeDetail?.raw_status === 'pending_verification'">
                        <button type="button"
                                @click="triggerConfirm(
                                    'Verify Payment Proof',
                                    'Have you verified the bank transfer of ' + activeDetail.amount_paid_formatted + ' from ' + activeDetail.name + '?',
                                    'Once confirmed, the event date will be locked and automatically synced to Google Calendar & Sheets.',
                                    activeDetail.confirm_url,
                                    'Yes, Verify & Confirm',
                                    'bg-brand-600 hover:bg-brand-700 text-white',
                                    'check'
                                )"
                                class="w-full py-2.5 px-4 rounded-full bg-[#E6F6EF] hover:bg-[#D5F0E4] text-emerald-700 text-xs font-semibold transition">
                            ✓ Verify & Confirm Payment
                        </button>
                    </template>

                    <template x-if="activeDetail?.raw_status === 'confirmed' && (activeDetail?.remaining_balance || 0) > 0">
                        <button type="button"
                                @click="triggerConfirm(
                                    'Record Full Settlement',
                                    'Are you sure you want to record full settlement of ' + activeDetail.remaining_formatted + ' for ' + activeDetail.name + '?',
                                    'Please ensure the remaining balance has cleared in your bank account. Invoice status will update to PAID (100%).',
                                    activeDetail.settle_url,
                                    'Yes, Mark as Paid',
                                    'bg-emerald-600 hover:bg-emerald-700 text-white',
                                    'check'
                                )"
                                class="w-full py-2.5 px-4 rounded-full bg-[#E6F6EF] hover:bg-[#D5F0E4] text-emerald-700 text-xs font-semibold transition">
                            ✓ Record Settlement (Paid 100%)
                        </button>
                    </template>

                    <template x-if="activeDetail?.raw_status === 'confirmed'">
                        <button type="button"
                                @click="triggerConfirm(
                                    'Mark Event as Complete',
                                    'Confirm that all wedding event series for ' + activeDetail.name + ' have been successfully completed?',
                                    'Booking status will change to Complete and payment will be automatically recorded as PAID 100%.',
                                    activeDetail.complete_url,
                                    'Yes, Mark Complete',
                                    'bg-slate-900 hover:bg-slate-800 text-white',
                                    'check'
                                )"
                                class="w-full py-2.5 px-4 rounded-full bg-slate-900 hover:bg-slate-800 text-white text-xs font-semibold transition">
                            Mark Event Complete
                        </button>
                    </template>

                    <a :href="activeDetail?.spk_url || '#'" target="_blank"
                       class="inline-flex items-center justify-center gap-2 w-full py-2.5 px-4 rounded-full bg-emerald-50 hover:bg-emerald-100 text-emerald-800 border border-emerald-200 text-xs font-bold text-center transition shadow-2xs">
                        <svg class="w-4 h-4 text-emerald-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4H7v4a2 2 0 002 2zM7 9V5a2 2 0 012-2h6a2 2 0 012 2v4"/></svg>
                        <span>Print Crew SPK / Surat Jalan</span>
                    </a>

                    <a :href="activeDetail?.receipt_url || '#'" target="_blank"
                       class="block w-full py-2.5 px-4 rounded-full border border-[#E5ECE9] hover:bg-[#F6F8F7] text-slate-700 text-xs font-semibold text-center transition">
                        Download Receipt PDF
                    </a>

                    <template x-if="activeDetail?.raw_status === 'pending_verification'">
                        <button type="button"
                                @click="triggerConfirm(
                                    'Reject Booking',
                                    'Are you sure you want to reject booking #' + activeDetail.code + ' for ' + activeDetail.name + '?',
                                    'This booking will be marked as Cancelled and removed from your active schedule.',
                                    activeDetail.reject_url,
                                    'Yes, Reject Booking',
                                    'bg-rose-600 hover:bg-rose-700 text-white',
                                    'warning'
                                )"
                                class="w-full py-2.5 px-4 rounded-full bg-rose-50 hover:bg-rose-100 text-rose-600 text-xs font-semibold transition">
                            Reject Booking
                        </button>
                    </template>
                </div>

            </div>
