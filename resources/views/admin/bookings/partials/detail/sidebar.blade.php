            <!-- RIGHT COLUMN (4 COLS: TIMELINE + STATUS + REAL ACTIONS) -->
            <div class="lg:col-span-4 space-y-5">

                <!-- Timeline -->
                <div class="bg-white rounded-[28px] p-5 sm:p-6 shadow-card border border-[#EDF2F0] space-y-4">
                    <h2 class="text-lg font-bold text-slate-900 tracking-tight">{{ __('admin.detail.timeline') }}</h2>
                    <div class="space-y-4 relative pl-4 before:content-[''] before:absolute before:left-[5px] before:top-2 before:bottom-2 before:w-px before:bg-[#E8EFED]">
                        <!-- 1. Booking Created -->
                        <div class="relative">
                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 ring-4 ring-white absolute -left-4 top-1"></span>
                            <p class="text-xs font-bold text-slate-800">{{ __('admin.detail.booking_created') }}</p>
                            <p class="text-[11px] text-slate-400 mt-0.5" x-text="activeDetail?.created_at_label"></p>
                        </div>

                        <!-- 2. Site Survey (if requested) -->
                        <template x-if="activeDetail?.survey_requested">
                            <div class="relative">
                                <span class="w-2.5 h-2.5 rounded-full ring-4 ring-white absolute -left-4 top-1"
                                      :class="activeDetail?.survey_status === 'completed' ? 'bg-emerald-500' : 'bg-amber-400'"></span>
                                <div class="flex items-center gap-2">
                                    <p class="text-xs font-bold text-slate-800">{{ __('admin.detail.site_survey') }}</p>
                                    <span x-show="activeDetail?.survey_status === 'completed'"
                                          class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-[#E6F6EF] text-emerald-700">{{ __('admin.bookings.survey_done') }}</span>
                                    <span x-show="activeDetail?.survey_status !== 'completed'"
                                          class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-amber-50 text-amber-700">{{ __('admin.bookings.survey_scheduled') }}</span>
                                </div>
                                <p class="text-[11px] text-slate-400 mt-0.5"
                                   x-text="(activeDetail?.survey_date_formatted || '-') + (activeDetail?.survey_time_slot_label ? ' · ' + activeDetail.survey_time_slot_label : '')"></p>
                            </div>
                        </template>

                        <!-- Cancelled state -->
                        <template x-if="activeDetail?.raw_status === 'rejected'">
                            <div class="relative">
                                <span class="w-2.5 h-2.5 rounded-full bg-rose-500 ring-4 ring-white absolute -left-4 top-1"></span>
                                <p class="text-xs font-bold text-rose-700">{{ __('admin.detail.booking_cancelled') }}</p>
                                <p class="text-[11px] text-slate-400 mt-0.5" x-text="activeDetail?.updated_at_label"></p>
                            </div>
                        </template>

                        <!-- Operational schedule milestones (when not cancelled) -->
                        <template x-if="activeDetail?.raw_status !== 'rejected'">
                            <div class="space-y-4">
                                <!-- 3. Tent Setup -->
                                <div class="relative">
                                    <span class="w-2.5 h-2.5 rounded-full ring-4 ring-white absolute -left-4 top-1"
                                          :class="activeDetail?.raw_status === 'completed' || (activeDetail?.setup_date_iso && activeDetail.setup_date_iso < todayIso)
                                            ? 'bg-emerald-500'
                                            : (activeDetail?.setup_date_iso === todayIso ? 'bg-sky-500' : 'bg-slate-300')"></span>
                                    <div class="flex items-center gap-2">
                                        <p class="text-xs font-bold text-slate-800">{{ __('admin.detail.tent_setup') }}</p>
                                        <span x-show="activeDetail?.setup_date_iso === todayIso"
                                              class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-sky-50 text-sky-700">{{ __('admin.calendar.today') }}</span>
                                    </div>
                                    <p class="text-[11px] text-slate-400 mt-0.5" x-text="activeDetail?.setup_date_formatted || '-'"></p>
                                </div>

                                <!-- 4. Event Day -->
                                <div class="relative">
                                    <span class="w-2.5 h-2.5 rounded-full ring-4 ring-white absolute -left-4 top-1"
                                          :class="activeDetail?.raw_status === 'completed' || (activeDetail?.event_end_date_iso && activeDetail.event_end_date_iso < todayIso)
                                            ? 'bg-emerald-500'
                                            : (activeDetail?.event_date_iso && todayIso >= activeDetail.event_date_iso && todayIso <= activeDetail.event_end_date_iso ? 'bg-brand-600' : 'bg-slate-300')"></span>
                                    <div class="flex items-center gap-2">
                                        <p class="text-xs font-bold text-slate-800">{{ __('admin.detail.event_day') }}</p>
                                        <span x-show="activeDetail?.event_date_iso && todayIso >= activeDetail.event_date_iso && todayIso <= activeDetail.event_end_date_iso"
                                              class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-[#E6F6EF] text-brand-700">{{ __('admin.relative_date.ongoing') }}</span>
                                    </div>
                                    <p class="text-[11px] text-slate-400 mt-0.5" x-text="activeDetail?.date || '-'"></p>
                                </div>

                                <!-- 5. Teardown -->
                                <div class="relative">
                                    <span class="w-2.5 h-2.5 rounded-full ring-4 ring-white absolute -left-4 top-1"
                                          :class="activeDetail?.raw_status === 'completed' || (activeDetail?.teardown_date_iso && activeDetail.teardown_date_iso < todayIso)
                                            ? 'bg-emerald-500'
                                            : (activeDetail?.teardown_date_iso === todayIso ? 'bg-amber-500' : 'bg-slate-300')"></span>
                                    <div class="flex items-center gap-2">
                                        <p class="text-xs font-bold text-slate-800">{{ __('admin.detail.teardown') }}</p>
                                        <span x-show="activeDetail?.teardown_date_iso === todayIso"
                                              class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-amber-50 text-amber-700">{{ __('admin.calendar.today') }}</span>
                                    </div>
                                    <p class="text-[11px] text-slate-400 mt-0.5" x-text="activeDetail?.teardown_date_formatted || '-'"></p>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                <!-- Status -->
                <div class="bg-white rounded-[28px] p-5 sm:p-6 shadow-card border border-[#EDF2F0] space-y-4">
                    <h2 class="text-lg font-bold text-slate-900 tracking-tight">{{ __('admin.detail.status_card') }}</h2>
                    <div class="space-y-3 text-xs">
                        <div class="flex items-center justify-between">
                            <span class="text-slate-500 font-medium">{{ __('admin.detail.booking_status') }}</span>
                            <span :class="{
                                    'bg-[#E6F6EF] text-emerald-700': activeDetail?.bookingStatus === 'Confirmed',
                                    'bg-[#EAF3FF] text-blue-600': activeDetail?.bookingStatus === 'Inquiry',
                                    'bg-[#F3EAFF] text-purple-600': activeDetail?.bookingStatus === 'Complete',
                                    'bg-[#FEECEB] text-rose-600': activeDetail?.bookingStatus === 'Cancelled'
                                  }"
                                  class="px-3 py-1 rounded-full text-[11px] font-semibold"
                                  x-text="tStatus(activeDetail?.bookingStatus)"></span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-slate-500 font-medium">{{ __('admin.detail.invoice_status') }}</span>
                            <span :class="{
                                    'bg-[#E6F6EF] text-emerald-700': activeDetail?.invoiceStatus === 'Paid',
                                    'bg-[#FEF6E0] text-amber-700': activeDetail?.invoiceStatus === 'Sent',
                                    'bg-[#F1F5F9] text-slate-500': activeDetail?.invoiceStatus === 'Draft',
                                    'bg-[#FEECEB] text-rose-600': activeDetail?.invoiceStatus === 'Overdue' || activeDetail?.invoiceStatus === 'Cancelled'
                                  }"
                                  class="px-3 py-1 rounded-full text-[11px] font-semibold"
                                  x-text="tInvoiceStatus(activeDetail?.invoiceStatus)"></span>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="bg-white rounded-[28px] p-5 sm:p-6 shadow-card border border-[#EDF2F0] space-y-2.5">
                    <h2 class="text-lg font-bold text-slate-900 tracking-tight mb-3">{{ __('admin.detail.actions_card') }}</h2>

                    <a :href="activeDetail?.whatsapp_url || '#'" target="_blank"
                       class="block w-full py-2.5 px-4 rounded-full bg-brand-600 hover:bg-brand-700 text-white text-xs font-semibold text-center transition shadow-pill">
                        {{ __('admin.detail.chat_wa_client') }}
                    </a>

                    @if($isOwner ?? false)
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
                            ✓ {{ __('admin.detail.verify_confirm') }}
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
                            ✓ {{ __('admin.detail.record_settlement') }}
                        </button>
                    </template>

                    <template x-if="activeDetail?.raw_status === 'confirmed'">
                        <button type="button"
                                @click="triggerConfirm(
                                    'Mark Event as Complete',
                                    'Confirm that all event series for ' + activeDetail.name + ' have been successfully completed?',
                                    'Booking status will change to Complete and payment will be automatically recorded as PAID 100%.',
                                    activeDetail.complete_url,
                                    'Yes, Mark Complete',
                                    'bg-slate-900 hover:bg-slate-800 text-white',
                                    'check'
                                )"
                                class="w-full py-2.5 px-4 rounded-full bg-slate-900 hover:bg-slate-800 text-white text-xs font-semibold transition">
                            {{ __('admin.detail.mark_complete') }}
                        </button>
                    </template>
                    @endif

                    <a :href="activeDetail?.spk_url || '#'" target="_blank"
                       class="inline-flex items-center justify-center gap-2 w-full py-2.5 px-4 rounded-full bg-emerald-50 hover:bg-emerald-100 text-emerald-800 border border-emerald-200 text-xs font-bold text-center transition shadow-2xs">
                        <svg class="w-4 h-4 text-emerald-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4H7v4a2 2 0 002 2zM7 9V5a2 2 0 012-2h6a2 2 0 012 2v4"/></svg>
                        <span>{{ __('admin.detail.print_spk') }}</span>
                    </a>

                    @if($isOwner ?? false)
                    <a :href="activeDetail?.receipt_url || '#'" target="_blank"
                       class="block w-full py-2.5 px-4 rounded-full border border-[#E5ECE9] hover:bg-[#F6F8F7] text-slate-700 text-xs font-semibold text-center transition">
                        {{ __('admin.detail.download_receipt') }}
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
                            {{ __('admin.detail.reject_booking') }}
                        </button>
                    </template>
                    @endif
                </div>

            </div>
