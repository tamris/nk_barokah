    <!-- ================================================================== -->
    <!-- MODAL: ADD MANUAL BOOKING (OWNER-ONLY — WA / WALK-IN ORDERS)       -->
    <!-- ================================================================== -->
    <div x-show="manualBookingModal.open"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 overflow-y-auto bg-slate-950/60 backdrop-blur-xs flex items-start sm:items-center justify-center p-3 sm:p-4"
         @keydown.escape.window="manualBookingModal.openMenu ? (manualBookingModal.openMenu = null) : closeManualBookingModal()"
         x-cloak>

        <div class="relative bg-white rounded-[28px] max-w-3xl w-full shadow-2xl border border-[#EDF2F0] my-4 flex flex-col max-h-[92vh] overflow-hidden"
             @click.away="closeManualBookingModal()"
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
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M12 4v16m8-8H4"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-base sm:text-lg font-bold text-slate-900 tracking-tight">{{ __('admin.manual_booking.title') }}</h3>
                        <p class="text-xs text-slate-500 mt-0.5">{{ __('admin.manual_booking.subtitle') }}</p>
                    </div>
                </div>
                <button type="button" @click="closeManualBookingModal()"
                        class="w-8 h-8 rounded-full bg-[#F4F7F6] hover:bg-[#EAEFEA] text-slate-500 hover:text-slate-800 flex items-center justify-center transition cursor-pointer shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <!-- Scrollable Form Body -->
            <form :action="manualBookingModal.action" method="POST" enctype="multipart/form-data"
                  @submit="validateManualBookingSubmit($event)"
                  class="flex flex-col flex-1 min-h-0">
                @csrf

                <div class="flex-1 overflow-y-auto px-5 sm:px-7 py-5 space-y-6">

                    <!-- Inline Form Error -->
                    <div x-show="manualBookingModal.formError" x-cloak
                         class="p-3.5 rounded-2xl bg-rose-50 border border-rose-200 text-xs font-semibold text-rose-700 flex items-center gap-2">
                        <svg class="w-4 h-4 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        <span x-text="manualBookingModal.formError"></span>
                    </div>

                    <!-- SECTION 1: CLIENT & EVENT TYPE -->
                    <div class="space-y-3.5">
                        <h4 class="text-[11px] font-bold uppercase tracking-wider text-brand-700">{{ __('admin.manual_booking.section_client') }}</h4>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                            <!-- Custom Dropdown: Event Type -->
                            <div class="relative" @click.away="if (manualBookingModal.openMenu === 'event_type') manualBookingModal.openMenu = null">
                                <label class="block text-xs font-semibold text-slate-700 mb-1.5">{{ __('admin.manual_booking.event_type') }} <span class="text-rose-500">*</span></label>
                                <input type="hidden" name="event_type" :value="manualBookingModal.event_type">
                                <button type="button" @click="toggleManualMenu('event_type')"
                                        :class="manualBookingModal.openMenu === 'event_type' ? 'border-brand-600 ring-2 ring-brand-600/15 bg-white' : 'border-[#E5ECE9] bg-[#F8FAFA] hover:border-brand-300'"
                                        class="w-full px-3.5 py-2.5 rounded-xl border text-xs sm:text-sm text-slate-800 font-medium flex items-center justify-between gap-2 transition cursor-pointer text-left">
                                    <span class="truncate" x-text="{
                                        pernikahan: '{{ __('admin.manual_booking.type_pernikahan') }}',
                                        khitanan: '{{ __('admin.manual_booking.type_khitanan') }}',
                                        tasyakuran: '{{ __('admin.manual_booking.type_tasyakuran') }}',
                                        instansi: '{{ __('admin.manual_booking.type_instansi') }}',
                                        sewa_alat: '{{ __('admin.manual_booking.type_sewa_alat') }}'
                                    }[manualBookingModal.event_type] || ''"></span>
                                    <svg class="w-4 h-4 text-slate-400 shrink-0 transition-transform duration-200"
                                         :class="manualBookingModal.openMenu === 'event_type' ? 'rotate-180 text-brand-600' : ''"
                                         fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M19 9l-7 7-7-7"/>
                                    </svg>
                                </button>
                                <div x-show="manualBookingModal.openMenu === 'event_type'" x-cloak
                                     x-transition:enter="transition ease-out duration-150"
                                     x-transition:enter-start="opacity-0 translate-y-1 scale-98"
                                     x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                                     class="absolute left-0 right-0 top-full mt-1.5 bg-white rounded-2xl shadow-xl border border-[#EAF0EE] py-1.5 z-50">
                                    @php
                                        $manualEventTypes = [
                                            'pernikahan' => __('admin.manual_booking.type_pernikahan'),
                                            'khitanan'   => __('admin.manual_booking.type_khitanan'),
                                            'tasyakuran' => __('admin.manual_booking.type_tasyakuran'),
                                            'instansi'   => __('admin.manual_booking.type_instansi'),
                                            'sewa_alat'  => __('admin.manual_booking.type_sewa_alat'),
                                        ];
                                    @endphp
                                    @foreach($manualEventTypes as $val => $lbl)
                                        <button type="button"
                                                @click="manualBookingModal.event_type = '{{ $val }}'; onManualEventTypeChange(); manualBookingModal.openMenu = null"
                                                :class="manualBookingModal.event_type === '{{ $val }}' ? 'bg-brand-50 text-brand-700 font-semibold' : 'text-slate-700 hover:bg-[#F6F8F7]'"
                                                class="w-full text-left px-4 py-2 text-xs transition flex items-center justify-between gap-2 cursor-pointer">
                                            <span class="truncate">{{ $lbl }}</span>
                                            <span x-show="manualBookingModal.event_type === '{{ $val }}'" class="text-brand-600 font-bold shrink-0">✓</span>
                                        </button>
                                    @endforeach
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1.5">{{ __('admin.manual_booking.job_source') }}</label>
                                <input type="text" name="job_source" x-model="manualBookingModal.job_source"
                                       placeholder="{{ __('admin.manual_booking.job_source_ph') }}"
                                       class="w-full rounded-xl border border-[#E5ECE9] bg-[#F8FAFA] px-3.5 py-2.5 text-xs sm:text-sm text-slate-800 placeholder-slate-400 focus:border-brand-500 focus:bg-white focus:outline-none">
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1.5">{{ __('admin.manual_booking.customer_name') }} <span class="text-rose-500">*</span></label>
                                <input type="text" name="customer_name" x-model="manualBookingModal.customer_name" required
                                       placeholder="{{ __('admin.manual_booking.customer_name_ph') }}"
                                       class="w-full rounded-xl border border-[#E5ECE9] bg-[#F8FAFA] px-3.5 py-2.5 text-xs sm:text-sm text-slate-800 placeholder-slate-400 uppercase focus:border-brand-500 focus:bg-white focus:outline-none">
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1.5">{{ __('admin.manual_booking.customer_phone') }} <span class="text-rose-500">*</span></label>
                                <input type="tel" name="customer_phone" x-model="manualBookingModal.customer_phone" required
                                       placeholder="{{ __('admin.manual_booking.customer_phone_ph') }}"
                                       class="w-full rounded-xl border border-[#E5ECE9] bg-[#F8FAFA] px-3.5 py-2.5 text-xs sm:text-sm text-slate-800 placeholder-slate-400 focus:border-brand-500 focus:bg-white focus:outline-none">
                            </div>

                            <div x-show="manualBookingModal.event_type === 'khitanan'" x-cloak class="sm:col-span-2">
                                <label class="block text-xs font-semibold text-slate-700 mb-1.5">{{ __('admin.manual_booking.child_name') }}</label>
                                <input type="text" name="child_name" x-model="manualBookingModal.child_name"
                                       placeholder="{{ __('admin.manual_booking.child_name_ph') }}"
                                       class="w-full rounded-xl border border-[#E5ECE9] bg-[#F8FAFA] px-3.5 py-2.5 text-xs sm:text-sm text-slate-800 placeholder-slate-400 focus:border-brand-500 focus:bg-white focus:outline-none">
                            </div>
                        </div>
                    </div>

                    <!-- SECTION 2: SCHEDULE & LOCATION -->
                    <div class="space-y-3.5 pt-4 border-t border-[#EFF4F2]">
                        <div class="flex items-center justify-between gap-2">
                            <h4 class="text-[11px] font-bold uppercase tracking-wider text-brand-700">{{ __('admin.manual_booking.section_schedule') }}</h4>
                            <span class="px-2.5 py-0.5 rounded-full bg-[#F4F7F6] text-slate-600 text-[11px] font-semibold">
                                {{ __('admin.manual_booking.duration_label') }}: <strong x-text="manualDurationDays"></strong> {{ __('admin.manual_booking.days_suffix') }}
                            </span>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1.5">{{ __('admin.manual_booking.event_start') }} <span class="text-rose-500">*</span></label>
                                <input type="date" name="event_date" x-model="manualBookingModal.event_date" @change="syncManualEndDate()" required
                                       class="w-full rounded-xl border border-[#E5ECE9] bg-[#F8FAFA] px-3.5 py-2.5 text-xs sm:text-sm text-slate-800 focus:border-brand-500 focus:bg-white focus:outline-none">
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1.5">{{ __('admin.manual_booking.event_end') }}</label>
                                <input type="date" name="event_end_date" x-model="manualBookingModal.event_end_date" :min="manualBookingModal.event_date"
                                       class="w-full rounded-xl border border-[#E5ECE9] bg-[#F8FAFA] px-3.5 py-2.5 text-xs sm:text-sm text-slate-800 focus:border-brand-500 focus:bg-white focus:outline-none">
                            </div>
                        </div>

                        <!-- Live Date Conflict Notice (Non-blocking for Owner) -->
                        <div x-show="manualDateConflictCount > 0" x-cloak
                             class="p-3 rounded-xl bg-amber-50 border border-amber-200 text-xs text-amber-800 flex items-start gap-2">
                            <svg class="w-4 h-4 text-amber-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                            <span x-text="'{{ __('admin.manual_booking.date_conflict_warn', ['count' => '__CNT__']) }}'.replace('__CNT__', manualDateConflictCount)"></span>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                            <div class="sm:col-span-2">
                                <label class="block text-xs font-semibold text-slate-700 mb-1.5">{{ __('admin.manual_booking.venue_address') }}</label>
                                <textarea name="venue_address" rows="2" x-model="manualBookingModal.venue_address"
                                          placeholder="{{ __('admin.manual_booking.venue_address_ph') }}"
                                          class="w-full rounded-xl border border-[#E5ECE9] bg-[#F8FAFA] px-3.5 py-2.5 text-xs sm:text-sm text-slate-800 placeholder-slate-400 focus:border-brand-500 focus:bg-white focus:outline-none"></textarea>
                            </div>

                            <div class="sm:col-span-2">
                                <label class="block text-xs font-semibold text-slate-700 mb-1.5">{{ __('admin.manual_booking.landmark') }}</label>
                                <input type="text" name="address_landmark" x-model="manualBookingModal.address_landmark"
                                       placeholder="{{ __('admin.manual_booking.landmark_ph') }}"
                                       class="w-full rounded-xl border border-[#E5ECE9] bg-[#F8FAFA] px-3.5 py-2.5 text-xs sm:text-sm text-slate-800 placeholder-slate-400 focus:border-brand-500 focus:bg-white focus:outline-none">
                            </div>
                        </div>

                        <!-- Optional Site Survey Toggle -->
                        <div x-show="manualBookingModal.event_type !== 'sewa_alat'" class="p-3.5 rounded-2xl bg-[#F8FAFA] border border-[#E8EFED] space-y-3">
                            <label class="inline-flex items-center gap-2.5 cursor-pointer select-none">
                                <input type="checkbox" name="survey_requested" value="1" x-model="manualBookingModal.survey_requested"
                                       class="w-4 h-4 rounded border-slate-300 text-brand-600 focus:ring-brand-500">
                                <span class="text-xs font-semibold text-slate-700">{{ __('admin.manual_booking.request_survey') }}</span>
                            </label>

                            <div x-show="manualBookingModal.survey_requested" x-cloak class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-1">
                                <div>
                                    <label class="block text-[11px] font-semibold text-slate-600 mb-1">{{ __('admin.manual_booking.survey_date') }}</label>
                                    <input type="date" name="survey_date" x-model="manualBookingModal.survey_date" :max="manualBookingModal.event_date"
                                           class="w-full rounded-xl border border-[#E5ECE9] bg-white px-3 py-2 text-xs text-slate-800 focus:border-brand-500 focus:outline-none">
                                </div>

                                <!-- Custom Dropdown: Survey Time Slot -->
                                <div class="relative" @click.away="if (manualBookingModal.openMenu === 'survey_slot') manualBookingModal.openMenu = null">
                                    <label class="block text-[11px] font-semibold text-slate-600 mb-1">{{ __('admin.manual_booking.survey_slot') }}</label>
                                    <input type="hidden" name="survey_time_slot" :value="manualBookingModal.survey_time_slot">
                                    <button type="button" @click="toggleManualMenu('survey_slot')"
                                            :class="manualBookingModal.openMenu === 'survey_slot' ? 'border-brand-600 ring-2 ring-brand-600/15' : 'border-[#E5ECE9] hover:border-brand-300'"
                                            class="w-full px-3 py-2 rounded-xl border bg-white text-xs text-slate-800 font-medium flex items-center justify-between gap-2 transition cursor-pointer text-left">
                                        <span class="truncate" x-text="{
                                            morning: '{{ __('admin.survey_slots.morning') }}',
                                            afternoon: '{{ __('admin.survey_slots.afternoon') }}',
                                            flexible: '{{ __('admin.survey_slots.flexible') }}'
                                        }[manualBookingModal.survey_time_slot] || ''"></span>
                                        <svg class="w-3.5 h-3.5 text-slate-400 shrink-0 transition-transform duration-200"
                                             :class="manualBookingModal.openMenu === 'survey_slot' ? 'rotate-180 text-brand-600' : ''"
                                             fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M19 9l-7 7-7-7"/>
                                        </svg>
                                    </button>
                                    <div x-show="manualBookingModal.openMenu === 'survey_slot'" x-cloak
                                         x-transition:enter="transition ease-out duration-150"
                                         x-transition:enter-start="opacity-0 translate-y-1 scale-98"
                                         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                                         class="absolute left-0 right-0 top-full mt-1.5 bg-white rounded-2xl shadow-xl border border-[#EAF0EE] py-1.5 z-50">
                                        @foreach(['morning' => __('admin.survey_slots.morning'), 'afternoon' => __('admin.survey_slots.afternoon'), 'flexible' => __('admin.survey_slots.flexible')] as $sVal => $sLbl)
                                            <button type="button"
                                                    @click="manualBookingModal.survey_time_slot = '{{ $sVal }}'; manualBookingModal.openMenu = null"
                                                    :class="manualBookingModal.survey_time_slot === '{{ $sVal }}' ? 'bg-brand-50 text-brand-700 font-semibold' : 'text-slate-700 hover:bg-[#F6F8F7]'"
                                                    class="w-full text-left px-3.5 py-2 text-xs transition flex items-center justify-between gap-2 cursor-pointer">
                                                <span class="truncate">{{ $sLbl }}</span>
                                                <span x-show="manualBookingModal.survey_time_slot === '{{ $sVal }}'" class="text-brand-600 font-bold shrink-0">✓</span>
                                            </button>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- SECTION 3: PACKAGE, TENT & ADD-ONS -->
                    <div class="space-y-3.5 pt-4 border-t border-[#EFF4F2]">
                        <h4 class="text-[11px] font-bold uppercase tracking-wider text-brand-700">{{ __('admin.manual_booking.section_package') }}</h4>

                        <div x-show="manualBookingModal.event_type !== 'sewa_alat'" class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                            <!-- Custom Rich Dropdown: Package Selection -->
                            <div class="sm:col-span-2 relative" @click.away="if (manualBookingModal.openMenu === 'package') manualBookingModal.openMenu = null">
                                <label class="block text-xs font-semibold text-slate-700 mb-1.5">{{ __('admin.manual_booking.package_label') }} <span class="text-rose-500">*</span></label>
                                <input type="hidden" name="package_id" :value="manualBookingModal.event_type === 'sewa_alat' ? '' : manualBookingModal.package_id" :disabled="manualBookingModal.event_type === 'sewa_alat'">
                                <button type="button" @click="toggleManualMenu('package')"
                                        :class="manualBookingModal.openMenu === 'package' ? 'border-brand-600 ring-2 ring-brand-600/15 bg-white' : 'border-[#E5ECE9] bg-[#F8FAFA] hover:border-brand-300'"
                                        class="w-full px-3.5 py-2.5 rounded-xl border text-xs sm:text-sm flex items-center justify-between gap-2 transition cursor-pointer text-left">
                                    <div class="flex items-center gap-2 min-w-0">
                                        <template x-if="selectedManualPackageObj">
                                            <div class="flex items-center gap-2 min-w-0">
                                                <span class="w-2 h-2 rounded-full bg-brand-600 shrink-0"></span>
                                                <span class="font-bold text-slate-900 truncate" x-text="selectedManualPackageObj.name"></span>
                                                <span class="px-2 py-0.5 rounded-md bg-emerald-50 text-emerald-700 text-[11px] font-mono font-bold shrink-0"
                                                      x-text="selectedManualPackageObj.price_formatted"></span>
                                            </div>
                                        </template>
                                        <template x-if="!selectedManualPackageObj">
                                            <span class="text-slate-400 font-medium">{{ __('admin.manual_booking.select_package_ph') }}</span>
                                        </template>
                                    </div>
                                    <svg class="w-4 h-4 text-slate-400 shrink-0 transition-transform duration-200"
                                         :class="manualBookingModal.openMenu === 'package' ? 'rotate-180 text-brand-600' : ''"
                                         fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M19 9l-7 7-7-7"/>
                                    </svg>
                                </button>
                                <div x-show="manualBookingModal.openMenu === 'package'" x-cloak
                                     x-transition:enter="transition ease-out duration-150"
                                     x-transition:enter-start="opacity-0 translate-y-1 scale-98"
                                     x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                                     class="absolute left-0 right-0 top-full mt-1.5 bg-white rounded-2xl shadow-xl border border-[#EAF0EE] p-1.5 z-50 max-h-60 overflow-y-auto space-y-1">
                                    <template x-for="pkg in activeManualPackages" :key="pkg.id">
                                        <button type="button"
                                                @click="manualBookingModal.package_id = String(pkg.id); manualBookingModal.openMenu = null"
                                                :class="String(manualBookingModal.package_id) === String(pkg.id) ? 'bg-brand-50 text-brand-800 border-brand-200' : 'bg-white hover:bg-[#F6F8F7] text-slate-800 border-transparent'"
                                                class="w-full text-left px-3 py-2.5 rounded-xl border transition flex items-center justify-between gap-2 cursor-pointer">
                                            <div class="min-w-0">
                                                <p class="text-xs font-bold truncate" x-text="pkg.name"></p>
                                                <p class="text-[10px] text-slate-500 truncate mt-0.5" x-text="pkg.description"></p>
                                            </div>
                                            <div class="flex items-center gap-2 shrink-0">
                                                <span class="text-xs font-mono font-bold text-emerald-700" x-text="pkg.price_formatted"></span>
                                                <span x-show="String(manualBookingModal.package_id) === String(pkg.id)" class="text-brand-600 font-bold text-xs">✓</span>
                                            </div>
                                        </button>
                                    </template>
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1.5">{{ __('admin.manual_booking.tent_color') }}</label>
                                <input type="text" name="tent_color_theme" x-model="manualBookingModal.tent_color_theme"
                                       list="manualTentColors"
                                       placeholder="{{ __('admin.manual_booking.tent_color_ph') }}"
                                       class="w-full rounded-xl border border-[#E5ECE9] bg-[#F8FAFA] px-3.5 py-2.5 text-xs sm:text-sm text-slate-800 placeholder-slate-400 focus:border-brand-500 focus:bg-white focus:outline-none">
                                <datalist id="manualTentColors">
                                    <option value="Putih + Mocca"></option>
                                    <option value="Putih + Pink"></option>
                                    <option value="Putih + Sage Green"></option>
                                    <option value="Putih + Biru Navy"></option>
                                    <option value="Full Putih"></option>
                                    <option value="Kuning / Adat"></option>
                                    <option value="Full Hitam"></option>
                                </datalist>
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1.5">{{ __('admin.manual_booking.tent_size') }}</label>
                                <input type="text" name="custom_tent_size" x-model="manualBookingModal.custom_tent_size"
                                       placeholder="{{ __('admin.manual_booking.tent_size_ph') }}"
                                       class="w-full rounded-xl border border-[#E5ECE9] bg-[#F8FAFA] px-3.5 py-2.5 text-xs sm:text-sm text-slate-800 placeholder-slate-400 focus:border-brand-500 focus:bg-white focus:outline-none">
                            </div>
                        </div>

                        <!-- Dynamic Add-on Rows (Searchable Custom Dropdown per Row) -->
                        <div class="p-4 rounded-2xl bg-[#F8FAFA] border border-[#E8EFED] space-y-3">
                            <div class="flex items-center justify-between gap-2">
                                <div>
                                    <p class="text-xs font-bold text-slate-800">{{ __('admin.manual_booking.addons_label') }}</p>
                                    <p x-show="manualBookingModal.event_type === 'sewa_alat'" class="text-[11px] text-amber-700 font-medium">
                                        {{ __('admin.manual_booking.addons_required_hint') }}
                                    </p>
                                </div>
                                <button type="button" @click="addManualAddonRow()"
                                        class="inline-flex items-center gap-1 px-3 py-1.5 rounded-xl bg-brand-50 hover:bg-brand-100 text-brand-700 text-xs font-bold transition cursor-pointer">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                                    <span>{{ __('admin.manual_booking.add_item_btn') }}</span>
                                </button>
                            </div>

                            <div x-show="manualBookingModal.addons.length === 0" class="text-[11px] text-slate-400 py-1">
                                {{ __('admin.manual_booking.no_addons_yet') }}
                            </div>

                            <div class="space-y-2" x-show="manualBookingModal.addons.length > 0">
                                <template x-for="(row, idx) in manualBookingModal.addons" :key="row.uid">
                                    <div class="flex items-center gap-2">
                                        <!-- Searchable Custom Add-on Dropdown -->
                                        <div class="flex-1 min-w-0 relative" @click.away="if (manualBookingModal.openMenu === 'addon-' + row.uid) manualBookingModal.openMenu = null">
                                            <input type="hidden" :name="'addons[' + idx + '][id]'" :value="row.id">
                                            <button type="button" @click="toggleManualMenu('addon-' + row.uid)"
                                                    :class="manualBookingModal.openMenu === 'addon-' + row.uid ? 'border-brand-600 ring-2 ring-brand-600/15' : 'border-[#E5ECE9] hover:border-brand-300'"
                                                    class="w-full px-3 py-2 rounded-xl border bg-white text-xs flex items-center justify-between gap-2 transition cursor-pointer text-left">
                                                <div class="flex items-center gap-1.5 min-w-0">
                                                    <template x-if="getAddonById(row.id)">
                                                        <div class="flex items-center gap-1.5 min-w-0">
                                                            <span class="font-semibold text-slate-800 truncate" x-text="getAddonById(row.id).name"></span>
                                                            <span class="px-1.5 py-0.5 rounded bg-emerald-50 text-emerald-700 text-[10px] font-mono font-bold shrink-0"
                                                                  x-text="getAddonById(row.id).price_formatted + '/' + getAddonById(row.id).unit"></span>
                                                        </div>
                                                    </template>
                                                    <template x-if="!getAddonById(row.id)">
                                                        <span class="text-slate-400 truncate">{{ __('admin.manual_booking.select_addon_ph') }}</span>
                                                    </template>
                                                </div>
                                                <svg class="w-3.5 h-3.5 text-slate-400 shrink-0 transition-transform duration-200"
                                                     :class="manualBookingModal.openMenu === 'addon-' + row.uid ? 'rotate-180 text-brand-600' : ''"
                                                     fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M19 9l-7 7-7-7"/>
                                                </svg>
                                            </button>

                                            <div x-show="manualBookingModal.openMenu === 'addon-' + row.uid" x-cloak
                                                 x-transition:enter="transition ease-out duration-150"
                                                 x-transition:enter-start="opacity-0 translate-y-1 scale-98"
                                                 x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                                                 class="absolute left-0 right-0 bottom-full mb-1.5 bg-white rounded-2xl shadow-xl border border-[#EAF0EE] p-2 z-50 space-y-1.5">
                                                <div class="relative">
                                                    <svg class="w-3.5 h-3.5 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                                    </svg>
                                                    <input type="text" x-model="manualBookingModal.addonSearch" @click.stop
                                                           placeholder="{{ __('admin.bookings.search_placeholder') }}"
                                                           class="w-full pl-8 pr-3 py-1.5 rounded-xl border border-[#E5ECE9] bg-[#F8FAFA] text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:bg-white focus:border-brand-600">
                                                </div>
                                                <div class="max-h-48 overflow-y-auto space-y-0.5 pr-0.5">
                                                    <template x-for="addon in filteredManualAddons()" :key="addon.id">
                                                        <button type="button"
                                                                @click="row.id = String(addon.id); manualBookingModal.openMenu = null"
                                                                :class="String(row.id) === String(addon.id) ? 'bg-brand-50 text-brand-700 font-semibold' : 'text-slate-700 hover:bg-[#F6F8F7]'"
                                                                class="w-full text-left px-3 py-2 rounded-xl text-xs transition flex items-center justify-between gap-2 cursor-pointer">
                                                            <span class="truncate" x-text="addon.name"></span>
                                                            <span class="flex items-center gap-1.5 shrink-0">
                                                                <span class="text-[11px] font-mono text-emerald-700 font-semibold" x-text="addon.price_formatted + '/' + addon.unit"></span>
                                                                <span x-show="String(row.id) === String(addon.id)" class="text-brand-600 font-bold">✓</span>
                                                            </span>
                                                        </button>
                                                    </template>
                                                </div>
                                            </div>
                                        </div>

                                        <input type="number" min="1" :name="'addons[' + idx + '][qty]'" x-model.number="row.qty"
                                               class="w-20 rounded-xl border border-[#E5ECE9] bg-white px-2.5 py-2 text-xs font-bold text-center text-slate-800 focus:border-brand-500 focus:outline-none">
                                        <button type="button" @click="removeManualAddonRow(idx)"
                                                class="w-8 h-8 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-600 flex items-center justify-center shrink-0 transition cursor-pointer">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                        </button>
                                    </div>
                                </template>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1.5">{{ __('admin.manual_booking.operational_notes') }}</label>
                            <textarea name="operational_notes" rows="2" x-model="manualBookingModal.operational_notes"
                                      placeholder="{{ __('admin.manual_booking.operational_notes_ph') }}"
                                      class="w-full rounded-xl border border-[#E5ECE9] bg-[#F8FAFA] px-3.5 py-2.5 text-xs sm:text-sm text-slate-800 placeholder-slate-400 focus:border-brand-500 focus:bg-white focus:outline-none"></textarea>
                        </div>
                    </div>

                    <!-- SECTION 4: PAYMENT & INITIAL STATUS -->
                    <div class="space-y-3.5 pt-4 border-t border-[#EFF4F2]">
                        <h4 class="text-[11px] font-bold uppercase tracking-wider text-brand-700">{{ __('admin.manual_booking.section_payment') }}</h4>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1.5">{{ __('admin.manual_booking.transport_fee') }}</label>
                                <input type="hidden" name="transport_fee" :value="manualBookingModal.transport_fee">
                                <input type="text" inputmode="numeric" :value="manualBookingModal.transport_display"
                                       @input="formatManualMoney('transport_fee', 'transport_display', $event)"
                                       placeholder="0"
                                       class="w-full rounded-xl border border-[#E5ECE9] bg-[#F8FAFA] px-3.5 py-2.5 text-xs sm:text-sm text-slate-800 placeholder-slate-400 focus:border-brand-500 focus:bg-white focus:outline-none">
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1.5">{{ __('admin.manual_booking.owner_discount') }}</label>
                                <input type="hidden" name="owner_discount" :value="manualBookingModal.owner_discount">
                                <input type="text" inputmode="numeric" :value="manualBookingModal.owner_discount_display"
                                       @input="formatManualMoney('owner_discount', 'owner_discount_display', $event)"
                                       placeholder="0"
                                       class="w-full rounded-xl border border-[#E5ECE9] bg-[#F8FAFA] px-3.5 py-2.5 text-xs sm:text-sm text-slate-800 placeholder-slate-400 focus:border-brand-500 focus:bg-white focus:outline-none">
                            </div>

                            <!-- Custom Dropdown: Payment Type (opens upward so footer never clips it) -->
                            <div class="relative" @click.away="if (manualBookingModal.openMenu === 'payment_type') manualBookingModal.openMenu = null">
                                <label class="block text-xs font-semibold text-slate-700 mb-1.5">{{ __('admin.manual_booking.payment_type') }} <span class="text-rose-500">*</span></label>
                                <input type="hidden" name="payment_type" :value="manualBookingModal.payment_type">
                                <button type="button" @click="toggleManualMenu('payment_type')"
                                        :class="manualBookingModal.openMenu === 'payment_type' ? 'border-brand-600 ring-2 ring-brand-600/15 bg-white' : 'border-[#E5ECE9] bg-[#F8FAFA] hover:border-brand-300'"
                                        class="w-full px-3.5 py-2.5 rounded-xl border text-xs sm:text-sm text-slate-800 font-medium flex items-center justify-between gap-2 transition cursor-pointer text-left">
                                    <span class="truncate" x-text="manualBookingModal.payment_type === 'lunas' ? '{{ __('admin.manual_booking.payment_lunas') }}' : '{{ __('admin.manual_booking.payment_dp') }}'"></span>
                                    <svg class="w-4 h-4 text-slate-400 shrink-0 transition-transform duration-200"
                                         :class="manualBookingModal.openMenu === 'payment_type' ? 'rotate-180 text-brand-600' : ''"
                                         fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M19 9l-7 7-7-7"/>
                                    </svg>
                                </button>
                                <div x-show="manualBookingModal.openMenu === 'payment_type'" x-cloak
                                     x-transition:enter="transition ease-out duration-150"
                                     x-transition:enter-start="opacity-0 translate-y-1 scale-98"
                                     x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                                     class="absolute left-0 right-0 bottom-full mb-1.5 bg-white rounded-2xl shadow-xl border border-[#EAF0EE] py-1.5 z-50">
                                    <button type="button" @click="manualBookingModal.payment_type = 'dp'; manualBookingModal.openMenu = null"
                                            :class="manualBookingModal.payment_type === 'dp' ? 'bg-brand-50 text-brand-700 font-semibold' : 'text-slate-700 hover:bg-[#F6F8F7]'"
                                            class="w-full text-left px-4 py-2 text-xs transition flex items-center justify-between gap-2 cursor-pointer">
                                        <span>{{ __('admin.manual_booking.payment_dp') }}</span>
                                        <span x-show="manualBookingModal.payment_type === 'dp'" class="text-brand-600 font-bold">✓</span>
                                    </button>
                                    <button type="button" @click="manualBookingModal.payment_type = 'lunas'; manualBookingModal.openMenu = null"
                                            :class="manualBookingModal.payment_type === 'lunas' ? 'bg-brand-50 text-brand-700 font-semibold' : 'text-slate-700 hover:bg-[#F6F8F7]'"
                                            class="w-full text-left px-4 py-2 text-xs transition flex items-center justify-between gap-2 cursor-pointer">
                                        <span>{{ __('admin.manual_booking.payment_lunas') }}</span>
                                        <span x-show="manualBookingModal.payment_type === 'lunas'" class="text-brand-600 font-bold">✓</span>
                                    </button>
                                </div>
                            </div>

                            <!-- Custom Dropdown: Initial Booking Status (opens upward) -->
                            <div class="relative" @click.away="if (manualBookingModal.openMenu === 'status') manualBookingModal.openMenu = null">
                                <label class="block text-xs font-semibold text-slate-700 mb-1.5">{{ __('admin.manual_booking.initial_status') }} <span class="text-rose-500">*</span></label>
                                <input type="hidden" name="status" :value="manualBookingModal.status">
                                <button type="button" @click="toggleManualMenu('status')"
                                        :class="manualBookingModal.openMenu === 'status' ? 'border-brand-600 ring-2 ring-brand-600/15 bg-white' : 'border-[#E5ECE9] bg-[#F8FAFA] hover:border-brand-300'"
                                        class="w-full px-3.5 py-2.5 rounded-xl border text-xs sm:text-sm text-slate-800 font-medium flex items-center justify-between gap-2 transition cursor-pointer text-left">
                                    <span class="inline-flex items-center gap-2 truncate">
                                        <span class="w-2 h-2 rounded-full shrink-0"
                                              :class="manualBookingModal.status === 'confirmed' ? 'bg-emerald-500' : 'bg-blue-500'"></span>
                                        <span class="truncate" x-text="manualBookingModal.status === 'confirmed' ? '{{ __('admin.manual_booking.status_confirmed') }}' : '{{ __('admin.manual_booking.status_pending') }}'"></span>
                                    </span>
                                    <svg class="w-4 h-4 text-slate-400 shrink-0 transition-transform duration-200"
                                         :class="manualBookingModal.openMenu === 'status' ? 'rotate-180 text-brand-600' : ''"
                                         fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M19 9l-7 7-7-7"/>
                                    </svg>
                                </button>
                                <div x-show="manualBookingModal.openMenu === 'status'" x-cloak
                                     x-transition:enter="transition ease-out duration-150"
                                     x-transition:enter-start="opacity-0 translate-y-1 scale-98"
                                     x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                                     class="absolute left-0 right-0 bottom-full mb-1.5 bg-white rounded-2xl shadow-xl border border-[#EAF0EE] py-1.5 z-50">
                                    <button type="button" @click="manualBookingModal.status = 'confirmed'; manualBookingModal.openMenu = null"
                                            :class="manualBookingModal.status === 'confirmed' ? 'bg-brand-50 text-brand-700 font-semibold' : 'text-slate-700 hover:bg-[#F6F8F7]'"
                                            class="w-full text-left px-4 py-2 text-xs transition flex items-center justify-between gap-2 cursor-pointer">
                                        <span class="inline-flex items-center gap-2">
                                            <span class="w-2 h-2 rounded-full bg-emerald-500 shrink-0"></span>
                                            <span>{{ __('admin.manual_booking.status_confirmed') }}</span>
                                        </span>
                                        <span x-show="manualBookingModal.status === 'confirmed'" class="text-brand-600 font-bold shrink-0">✓</span>
                                    </button>
                                    <button type="button" @click="manualBookingModal.status = 'pending_verification'; manualBookingModal.openMenu = null"
                                            :class="manualBookingModal.status === 'pending_verification' ? 'bg-brand-50 text-brand-700 font-semibold' : 'text-slate-700 hover:bg-[#F6F8F7]'"
                                            class="w-full text-left px-4 py-2 text-xs transition flex items-center justify-between gap-2 cursor-pointer">
                                        <span class="inline-flex items-center gap-2">
                                            <span class="w-2 h-2 rounded-full bg-blue-500 shrink-0"></span>
                                            <span>{{ __('admin.manual_booking.status_pending') }}</span>
                                        </span>
                                        <span x-show="manualBookingModal.status === 'pending_verification'" class="text-brand-600 font-bold shrink-0">✓</span>
                                    </button>
                                </div>
                            </div>

                            <div x-show="manualBookingModal.payment_type === 'dp'">
                                <label class="block text-xs font-semibold text-slate-700 mb-1.5">{{ __('admin.manual_booking.nominal_dp') }}</label>
                                <input type="hidden" name="nominal_dp" :value="manualBookingModal.nominal_dp">
                                <input type="text" inputmode="numeric" :value="manualBookingModal.nominal_dp_display"
                                       @input="formatManualMoney('nominal_dp', 'nominal_dp_display', $event)"
                                       placeholder="0"
                                       class="w-full rounded-xl border border-[#E5ECE9] bg-[#F8FAFA] px-3.5 py-2.5 text-xs sm:text-sm font-semibold text-slate-800 placeholder-slate-400 focus:border-brand-500 focus:bg-white focus:outline-none">
                                <p class="text-[10px] text-slate-400 mt-1">{{ __('admin.manual_booking.nominal_dp_hint') }}</p>
                            </div>

                            <div :class="manualBookingModal.payment_type === 'lunas' ? 'sm:col-span-2' : ''">
                                <label class="block text-xs font-semibold text-slate-700 mb-1.5">{{ __('admin.manual_booking.payment_proof') }}</label>
                                <input type="file" name="payment_proof" accept=".jpg,.jpeg,.png,.webp,.pdf"
                                       class="w-full rounded-xl border border-[#E5ECE9] bg-[#F8FAFA] px-3 py-2 text-xs text-slate-600 file:mr-2.5 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-brand-50 file:text-brand-700 hover:file:bg-brand-100">
                                <p class="text-[10px] text-slate-400 mt-1">{{ __('admin.manual_booking.payment_proof_hint') }}</p>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- Sticky Footer: Live Totals + Submit -->
                <div class="px-5 sm:px-7 py-4 border-t border-[#EFF4F2] bg-[#FAFDFC] shrink-0 space-y-3">
                    <div class="grid grid-cols-3 gap-2 text-xs">
                        <div>
                            <p class="text-[10px] uppercase tracking-wider font-semibold text-slate-400">{{ __('admin.manual_booking.summary_total') }}</p>
                            <p class="text-sm font-extrabold text-slate-900" x-text="formatRupiah(manualGrandTotal)"></p>
                            <p x-show="manualExtraDayCharge > 0" class="text-[10px] text-amber-600 font-medium"
                               x-text="'{{ __('admin.manual_booking.extra_day_note', ['amount' => '__AMT__']) }}'.replace('__AMT__', formatRupiah(manualExtraDayCharge))"></p>
                        </div>
                        <div>
                            <p class="text-[10px] uppercase tracking-wider font-semibold text-slate-400">{{ __('admin.manual_booking.summary_paid') }}</p>
                            <p class="text-sm font-bold text-emerald-700" x-text="formatRupiah(manualAmountPaid)"></p>
                        </div>
                        <div class="text-right">
                            <p class="text-[10px] uppercase tracking-wider font-semibold text-slate-400">{{ __('admin.manual_booking.summary_remaining') }}</p>
                            <p class="text-sm font-bold" :class="manualRemaining > 0 ? 'text-amber-700' : 'text-emerald-700'" x-text="formatRupiah(manualRemaining)"></p>
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-2.5">
                        <button type="button" @click="closeManualBookingModal()"
                                class="px-5 py-2.5 rounded-full border border-[#DCE5E2] text-slate-700 font-semibold text-xs hover:bg-slate-50 transition cursor-pointer">
                            {{ __('admin.detail.cancel') }}
                        </button>
                        <button type="submit"
                                class="px-6 py-2.5 rounded-full bg-brand-600 hover:bg-brand-700 text-white font-bold text-xs transition shadow-pill cursor-pointer active:scale-95">
                            {{ __('admin.manual_booking.submit_btn') }}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
