    <!-- ================================================================== -->
    <!-- MODAL: EDIT BOOKING & RESCHEDULE (OWNER-ONLY)                      -->
    <!-- ================================================================== -->
    <div x-show="editBookingModal.open"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 overflow-y-auto bg-slate-950/60 backdrop-blur-xs flex items-start sm:items-center justify-center p-3 sm:p-4"
         @keydown.escape.window="editBookingModal.openMenu ? (editBookingModal.openMenu = null) : closeEditBookingModal()"
         x-cloak>

        <div class="relative bg-white rounded-[28px] max-w-2xl w-full shadow-2xl border border-[#EDF2F0] my-4 flex flex-col max-h-[92vh] overflow-hidden"
             @click.away="closeEditBookingModal()"
             x-transition:enter="transition ease-out duration-200 transform"
             x-transition:enter-start="opacity-0 scale-95 translate-y-2"
             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
             x-transition:leave="transition ease-in duration-150 transform"
             x-transition:leave-start="opacity-100 scale-100 translate-y-0"
             x-transition:leave-end="opacity-0 scale-95 translate-y-2">

            <!-- Sticky Modal Header -->
            <div class="px-5 sm:px-7 py-4 sm:py-5 border-b border-[#EFF4F2] flex items-start justify-between gap-3 shrink-0">
                <div class="flex items-center gap-3.5">
                    <div class="w-11 h-11 rounded-2xl bg-brand-50 border border-brand-100 text-brand-700 flex items-center justify-center shrink-0 shadow-2xs">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.1" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                    </div>
                    <div>
                        <div class="flex items-center gap-2 flex-wrap">
                            <h3 class="text-base sm:text-lg font-bold text-slate-900 tracking-tight">{{ __('admin.edit_booking.title') }}</h3>
                            <span class="px-2 py-0.5 rounded-full bg-[#EAF3FF] text-[#0284C7] text-[11px] font-mono font-bold"
                                  x-text="'#' + editBookingModal.code"></span>
                        </div>
                        <p class="text-xs text-slate-500 mt-0.5">{{ __('admin.edit_booking.subtitle') }}</p>
                    </div>
                </div>
                <button type="button" @click="closeEditBookingModal()"
                        class="w-8 h-8 rounded-full bg-[#F4F7F6] hover:bg-[#EAEFEA] text-slate-500 hover:text-slate-800 flex items-center justify-center transition cursor-pointer shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <!-- Scrollable Form Body -->
            <form :action="editBookingModal.action" method="POST"
                  @submit="validateEditBookingSubmit($event)"
                  class="flex flex-col flex-1 min-h-0">
                @csrf
                <input type="hidden" name="_method" value="PUT">

                <div class="flex-1 overflow-y-auto px-5 sm:px-7 py-5 space-y-6">

                    <!-- Inline Form Error -->
                    <div x-show="editBookingModal.formError" x-cloak
                         class="p-3.5 rounded-2xl bg-rose-50 border border-rose-200 text-xs font-semibold text-rose-700 flex items-center gap-2">
                        <svg class="w-4 h-4 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        <span x-text="editBookingModal.formError"></span>
                    </div>

                    <!-- SECTION 1: RESCHEDULE EVENT DATES -->
                    <div class="space-y-3.5">
                        <div class="flex items-center justify-between gap-2">
                            <h4 class="text-[11px] font-bold uppercase tracking-wider text-brand-700">{{ __('admin.edit_booking.section_reschedule') }}</h4>
                            <span class="px-2.5 py-0.5 rounded-full bg-[#F4F7F6] text-slate-600 text-[11px] font-semibold">
                                {{ __('admin.manual_booking.duration_label') }}: <strong x-text="editDurationDays"></strong> {{ __('admin.manual_booking.days_suffix') }}
                            </span>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1.5">{{ __('admin.manual_booking.event_start') }} <span class="text-rose-500">*</span></label>
                                <input type="date" name="event_date" x-model="editBookingModal.event_date" @change="syncEditEndDate()" required
                                       class="w-full rounded-xl border border-[#E5ECE9] bg-[#F8FAFA] px-3.5 py-2.5 text-xs sm:text-sm text-slate-800 focus:border-brand-500 focus:bg-white focus:outline-none">
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1.5">{{ __('admin.manual_booking.event_end') }}</label>
                                <input type="date" name="event_end_date" x-model="editBookingModal.event_end_date" :min="editBookingModal.event_date"
                                       class="w-full rounded-xl border border-[#E5ECE9] bg-[#F8FAFA] px-3.5 py-2.5 text-xs sm:text-sm text-slate-800 focus:border-brand-500 focus:bg-white focus:outline-none">
                            </div>
                        </div>

                        <p class="text-[11px] text-slate-500">{{ __('admin.edit_booking.reschedule_hint') }}</p>

                        <!-- Live Conflict Warning (Excludes Current Booking ID) -->
                        <div x-show="editDateConflictCount > 0" x-cloak
                             class="p-3 rounded-xl bg-amber-50 border border-amber-200 text-xs text-amber-800 flex items-start gap-2">
                            <svg class="w-4 h-4 text-amber-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                            <span x-text="'{{ __('admin.edit_booking.date_conflict_warn', ['count' => '__CNT__']) }}'.replace('__CNT__', editDateConflictCount)"></span>
                        </div>
                    </div>

                    <!-- SECTION 2: EVENT TYPE & CORE PACKAGE (UPGRADE / CHANGE) -->
                    <div class="space-y-3.5 pt-4 border-t border-[#EFF4F2]">
                        <h4 class="text-[11px] font-bold uppercase tracking-wider text-brand-700">{{ __('admin.edit_booking.section_package') }}</h4>

                        <div class="grid grid-cols-1 gap-3.5">
                            <!-- Custom Dropdown: Event Type -->
                            <div class="relative" @click.away="if (editBookingModal.openMenu === 'event_type') editBookingModal.openMenu = null">
                                <label class="block text-xs font-semibold text-slate-700 mb-1.5">{{ __('admin.manual_booking.event_type') }} <span class="text-rose-500">*</span></label>
                                <input type="hidden" name="event_type" :value="editBookingModal.event_type">
                                <button type="button" @click="toggleEditMenu('event_type')"
                                        :class="editBookingModal.openMenu === 'event_type' ? 'border-brand-600 ring-2 ring-brand-600/15 bg-white' : 'border-[#E5ECE9] bg-[#F8FAFA] hover:border-brand-300'"
                                        class="w-full px-3.5 py-2.5 rounded-xl border text-xs sm:text-sm text-slate-800 font-medium flex items-center justify-between gap-2 transition cursor-pointer text-left">
                                    <span class="truncate" x-text="{
                                        pernikahan: '{{ __('admin.manual_booking.type_pernikahan') }}',
                                        khitanan: '{{ __('admin.manual_booking.type_khitanan') }}',
                                        tasyakuran: '{{ __('admin.manual_booking.type_tasyakuran') }}',
                                        instansi: '{{ __('admin.manual_booking.type_instansi') }}',
                                        sewa_alat: '{{ __('admin.manual_booking.type_sewa_alat') }}'
                                    }[editBookingModal.event_type] || ''"></span>
                                    <svg class="w-4 h-4 text-slate-400 shrink-0 transition-transform duration-200"
                                         :class="editBookingModal.openMenu === 'event_type' ? 'rotate-180 text-brand-600' : ''"
                                         fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M19 9l-7 7-7-7"/>
                                    </svg>
                                </button>
                                <div x-show="editBookingModal.openMenu === 'event_type'" x-cloak
                                     x-transition:enter="transition ease-out duration-150"
                                     x-transition:enter-start="opacity-0 translate-y-1 scale-98"
                                     x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                                     class="absolute left-0 right-0 top-full mt-1.5 bg-white rounded-2xl shadow-xl border border-[#EAF0EE] py-1.5 z-50">
                                    @php
                                        $editEventTypes = [
                                            'pernikahan' => __('admin.manual_booking.type_pernikahan'),
                                            'khitanan'   => __('admin.manual_booking.type_khitanan'),
                                            'tasyakuran' => __('admin.manual_booking.type_tasyakuran'),
                                            'instansi'   => __('admin.manual_booking.type_instansi'),
                                            'sewa_alat'  => __('admin.manual_booking.type_sewa_alat'),
                                        ];
                                    @endphp
                                    @foreach($editEventTypes as $val => $lbl)
                                        <button type="button"
                                                @click="editBookingModal.event_type = '{{ $val }}'; onEditEventTypeChange(); editBookingModal.openMenu = null"
                                                :class="editBookingModal.event_type === '{{ $val }}' ? 'bg-brand-50 text-brand-700 font-semibold' : 'text-slate-700 hover:bg-[#F6F8F7]'"
                                                class="w-full text-left px-4 py-2 text-xs transition flex items-center justify-between gap-2 cursor-pointer">
                                            <span class="truncate">{{ $lbl }}</span>
                                            <span x-show="editBookingModal.event_type === '{{ $val }}'" class="text-brand-600 font-bold shrink-0">✓</span>
                                        </button>
                                    @endforeach
                                </div>
                            </div>

                            <!-- Custom Rich Dropdown: Package Selection -->
                            <div x-show="editBookingModal.event_type !== 'sewa_alat'" class="relative" @click.away="if (editBookingModal.openMenu === 'package') editBookingModal.openMenu = null">
                                <label class="block text-xs font-semibold text-slate-700 mb-1.5">{{ __('admin.manual_booking.package_label') }} <span class="text-rose-500">*</span></label>
                                <input type="hidden" name="package_id" :value="editBookingModal.event_type === 'sewa_alat' ? '' : editBookingModal.package_id" :disabled="editBookingModal.event_type === 'sewa_alat'">
                                <button type="button" @click="toggleEditMenu('package')"
                                        :class="editBookingModal.openMenu === 'package' ? 'border-brand-600 ring-2 ring-brand-600/15 bg-white' : 'border-[#E5ECE9] bg-[#F8FAFA] hover:border-brand-300'"
                                        class="w-full px-3.5 py-2.5 rounded-xl border text-xs sm:text-sm flex items-center justify-between gap-2 transition cursor-pointer text-left">
                                    <div class="flex items-center gap-2 min-w-0">
                                        <template x-if="selectedEditPackageObj">
                                            <div class="flex items-center gap-2 min-w-0">
                                                <span class="w-2 h-2 rounded-full bg-brand-600 shrink-0"></span>
                                                <span class="font-bold text-slate-900 truncate" x-text="selectedEditPackageObj.name"></span>
                                                <span class="px-2 py-0.5 rounded-md bg-emerald-50 text-emerald-700 text-[11px] font-mono font-bold shrink-0"
                                                      x-text="selectedEditPackageObj.price_formatted"></span>
                                            </div>
                                        </template>
                                        <template x-if="!selectedEditPackageObj">
                                            <span class="text-slate-400 font-medium">{{ __('admin.manual_booking.select_package_ph') }}</span>
                                        </template>
                                    </div>
                                    <svg class="w-4 h-4 text-slate-400 shrink-0 transition-transform duration-200"
                                         :class="editBookingModal.openMenu === 'package' ? 'rotate-180 text-brand-600' : ''"
                                         fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M19 9l-7 7-7-7"/>
                                    </svg>
                                </button>
                                <div x-show="editBookingModal.openMenu === 'package'" x-cloak
                                     x-transition:enter="transition ease-out duration-150"
                                     x-transition:enter-start="opacity-0 translate-y-1 scale-98"
                                     x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                                     class="absolute left-0 right-0 top-full mt-1.5 bg-white rounded-2xl shadow-xl border border-[#EAF0EE] p-1.5 z-50 max-h-60 overflow-y-auto space-y-1">
                                    <template x-for="pkg in activeManualPackages" :key="pkg.id">
                                        <button type="button"
                                                @click="editBookingModal.package_id = String(pkg.id); editBookingModal.openMenu = null"
                                                :class="String(editBookingModal.package_id) === String(pkg.id) ? 'bg-brand-50 text-brand-800 border-brand-200' : 'bg-white hover:bg-[#F6F8F7] text-slate-800 border-transparent'"
                                                class="w-full text-left px-3 py-2.5 rounded-xl border transition flex items-center justify-between gap-2 cursor-pointer">
                                            <div class="min-w-0">
                                                <p class="text-xs font-bold truncate" x-text="pkg.name"></p>
                                                <p class="text-[10px] text-slate-500 truncate mt-0.5" x-text="pkg.description"></p>
                                            </div>
                                            <div class="flex items-center gap-2 shrink-0">
                                                <span class="text-xs font-mono font-bold text-emerald-700" x-text="pkg.price_formatted"></span>
                                                <span x-show="String(editBookingModal.package_id) === String(pkg.id)" class="text-brand-600 font-bold text-xs">✓</span>
                                            </div>
                                        </button>
                                    </template>
                                </div>
                            </div>
                        </div>

                        <p class="text-[11px] text-slate-500">{{ __('admin.edit_booking.recalc_note') }}</p>
                    </div>

                    <!-- SECTION 3: CLIENT & VENUE DETAILS -->
                    <div class="space-y-3.5 pt-4 border-t border-[#EFF4F2]">
                        <h4 class="text-[11px] font-bold uppercase tracking-wider text-brand-700">{{ __('admin.edit_booking.section_client') }}</h4>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1.5">{{ __('admin.manual_booking.customer_name') }} <span class="text-rose-500">*</span></label>
                                <input type="text" name="customer_name" x-model="editBookingModal.customer_name" required
                                       class="w-full rounded-xl border border-[#E5ECE9] bg-[#F8FAFA] px-3.5 py-2.5 text-xs sm:text-sm text-slate-800 uppercase focus:border-brand-500 focus:bg-white focus:outline-none">
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1.5">{{ __('admin.manual_booking.customer_phone') }} <span class="text-rose-500">*</span></label>
                                <input type="tel" name="customer_phone" x-model="editBookingModal.customer_phone" required
                                       class="w-full rounded-xl border border-[#E5ECE9] bg-[#F8FAFA] px-3.5 py-2.5 text-xs sm:text-sm text-slate-800 focus:border-brand-500 focus:bg-white focus:outline-none">
                            </div>

                            <div x-show="editBookingModal.event_type === 'khitanan'" x-cloak class="sm:col-span-2">
                                <label class="block text-xs font-semibold text-slate-700 mb-1.5">{{ __('admin.manual_booking.child_name') }}</label>
                                <input type="text" name="child_name" x-model="editBookingModal.child_name"
                                       class="w-full rounded-xl border border-[#E5ECE9] bg-[#F8FAFA] px-3.5 py-2.5 text-xs sm:text-sm text-slate-800 focus:border-brand-500 focus:bg-white focus:outline-none">
                            </div>

                            <div class="sm:col-span-2">
                                <label class="block text-xs font-semibold text-slate-700 mb-1.5">{{ __('admin.manual_booking.venue_address') }}</label>
                                <textarea name="venue_address" rows="2" x-model="editBookingModal.venue_address"
                                          class="w-full rounded-xl border border-[#E5ECE9] bg-[#F8FAFA] px-3.5 py-2.5 text-xs sm:text-sm text-slate-800 focus:border-brand-500 focus:bg-white focus:outline-none"></textarea>
                            </div>

                            <div class="sm:col-span-2">
                                <label class="block text-xs font-semibold text-slate-700 mb-1.5">{{ __('admin.manual_booking.landmark') }}</label>
                                <input type="text" name="address_landmark" x-model="editBookingModal.address_landmark"
                                       class="w-full rounded-xl border border-[#E5ECE9] bg-[#F8FAFA] px-3.5 py-2.5 text-xs sm:text-sm text-slate-800 focus:border-brand-500 focus:bg-white focus:outline-none">
                            </div>
                        </div>
                    </div>

                </div>

                <!-- Sticky Footer: Live Recalculation Preview + Submit -->
                <div class="px-5 sm:px-7 py-4 border-t border-[#EFF4F2] bg-[#FAFDFC] shrink-0 space-y-3">
                    <div class="grid grid-cols-3 gap-2 text-xs">
                        <div>
                            <p class="text-[10px] uppercase tracking-wider font-semibold text-slate-400">{{ __('admin.manual_booking.summary_total') }}</p>
                            <p class="text-sm font-extrabold text-slate-900" x-text="formatRupiah(editGrandTotal)"></p>
                            <p x-show="editExtraDayCharge > 0" class="text-[10px] text-amber-600 font-medium"
                               x-text="'{{ __('admin.manual_booking.extra_day_note', ['amount' => '__AMT__']) }}'.replace('__AMT__', formatRupiah(editExtraDayCharge))"></p>
                        </div>
                        <div>
                            <p class="text-[10px] uppercase tracking-wider font-semibold text-slate-400">{{ __('admin.manual_booking.summary_paid') }}</p>
                            <p class="text-sm font-bold text-emerald-700" x-text="formatRupiah(editBookingModal.amount_paid)"></p>
                        </div>
                        <div class="text-right">
                            <p class="text-[10px] uppercase tracking-wider font-semibold text-slate-400">{{ __('admin.manual_booking.summary_remaining') }}</p>
                            <p class="text-sm font-bold" :class="editRemaining > 0 ? 'text-amber-700' : 'text-emerald-700'" x-text="formatRupiah(editRemaining)"></p>
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-2.5">
                        <button type="button" @click="closeEditBookingModal()"
                                class="px-5 py-2.5 rounded-full border border-[#DCE5E2] text-slate-700 font-semibold text-xs hover:bg-slate-50 transition cursor-pointer">
                            {{ __('admin.detail.cancel') }}
                        </button>
                        <button type="submit"
                                class="px-6 py-2.5 rounded-full bg-brand-600 hover:bg-brand-700 text-white font-bold text-xs transition shadow-pill cursor-pointer active:scale-95">
                            {{ __('admin.edit_booking.submit_btn') }}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
