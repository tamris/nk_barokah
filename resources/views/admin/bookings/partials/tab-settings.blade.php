    <!-- ================================================================== -->
    <!-- TAB 7: SETTINGS & SYSTEM CONFIGURATION                             -->
    <!-- ================================================================== -->
    <div x-show="$store.nav.tab === 'settings'"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 translate-y-1"
         x-transition:enter-end="opacity-100 translate-y-0"
         class="space-y-6"
         x-cloak>

        <!-- Header Panel -->
        <div class="bg-white rounded-[28px] p-6 sm:p-7 shadow-card border border-[#EDF2F0] flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-2xl bg-brand-50 border border-brand-100 flex items-center justify-center text-brand-600 shrink-0 shadow-xs">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.9" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.9" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
                <div>
                    <div class="flex items-center gap-2.5 flex-wrap">
                        <h1 class="text-xl sm:text-2xl font-bold tracking-tight text-slate-900">{{ __('admin.settings.title') }}</h1>
                    </div>
                </div>
            </div>

            <a href="{{ route('booking.create') }}" target="_blank"
               class="inline-flex items-center gap-2 px-5 py-2.5 rounded-full bg-[#F6F8F7] hover:bg-[#EAF0EE] text-slate-700 text-xs font-semibold transition border border-[#E3ECE9] shrink-0">
                <span>{{ __('admin.settings.open_client_form') }}</span>
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
            </a>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

            <!-- APPEARANCE & THEME PREFERENCE (Col Span 7) -->
            <div class="lg:col-span-7 bg-white rounded-[28px] p-6 sm:p-7 shadow-card border border-[#EDF2F0] space-y-5">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-4 border-b border-[#F0F4F2]">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-2xl bg-brand-50 text-brand-600 flex items-center justify-center border border-brand-100 shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.9" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-base font-bold text-slate-900">{{ __('admin.settings.appearance_title') }}</h2>
                            <p class="text-xs text-slate-400">{{ __('admin.settings.appearance_desc') }}</p>
                        </div>
                    </div>
                    <span class="inline-flex items-center gap-1.5 self-start sm:self-center px-3 py-1 rounded-full text-[11px] font-semibold bg-[#F6F8F7] border border-[#E5ECE9] text-slate-600 shrink-0">
                        <span class="w-2 h-2 rounded-full"
                              :class="$store.theme.resolved === 'dark' ? 'bg-teal-400' : 'bg-amber-400'"></span>
                        <span x-text="$store.theme.preference === 'system'
                            ? '{{ __('admin.js.theme.system') }} (' + ($store.theme.systemDark ? '{{ __('admin.js.theme.dark') }}' : '{{ __('admin.js.theme.light') }}') + ')'
                            : ($store.theme.preference === 'dark' ? '{{ __('admin.js.theme.dark_mode') }}' : '{{ __('admin.js.theme.light_mode') }}')"></span>
                    </span>
                </div>

                <div role="radiogroup" aria-label="{{ __('admin.settings.appearance_title') }}" class="grid grid-cols-1 sm:grid-cols-3 gap-3.5">
                    <!-- Option 1: Light -->
                    <button type="button"
                            role="radio"
                            :aria-checked="$store.theme.preference === 'light'"
                            @click="$store.theme.set('light')"
                            :class="$store.theme.preference === 'light'
                                ? 'border-brand-600 ring-2 ring-brand-500/20 bg-brand-50/40'
                                : 'border-[#E5ECE9] bg-[#FAFDFC] hover:border-brand-300'"
                            class="group relative text-left rounded-2xl border p-3.5 transition-all cursor-pointer space-y-3 focus:outline-none">
                        <!-- Mini Light UI Wireframe -->
                        <div class="h-14 rounded-xl border border-[#DCE5E2] overflow-hidden p-2 flex flex-col justify-between" style="background-color: #F6F8F7;">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-1.5">
                                    <span class="w-2.5 h-2.5 rounded-full" style="background-color: #045D56;"></span>
                                    <span class="w-8 h-1.5 rounded-full" style="background-color: #CBD5E1;"></span>
                                </div>
                                <span class="w-3.5 h-1.5 rounded-full" style="background-color: #E2E8F0;"></span>
                            </div>
                            <div class="grid grid-cols-3 gap-1.5">
                                <div class="h-5 rounded-md border" style="background-color: #FFFFFF; border-color: #E2EAE7;"></div>
                                <div class="h-5 rounded-md border col-span-2" style="background-color: #FFFFFF; border-color: #E2EAE7;"></div>
                            </div>
                        </div>

                        <div class="flex items-start justify-between gap-2">
                            <div class="min-w-0">
                                <p class="text-xs font-bold text-slate-900">{{ __('admin.settings.theme_light') }}</p>
                                <p class="text-[11px] text-slate-400 mt-0.5 leading-snug">{{ __('admin.settings.theme_light_desc') }}</p>
                            </div>
                            <span class="w-4 h-4 rounded-full flex items-center justify-center shrink-0 border transition mt-0.5"
                                  :class="$store.theme.preference === 'light' ? 'bg-brand-600 border-brand-600 text-white' : 'border-slate-300 text-transparent'">
                                <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                            </span>
                        </div>
                    </button>

                    <!-- Option 2: Dark -->
                    <button type="button"
                            role="radio"
                            :aria-checked="$store.theme.preference === 'dark'"
                            @click="$store.theme.set('dark')"
                            :class="$store.theme.preference === 'dark'
                                ? 'border-brand-600 ring-2 ring-brand-500/20 bg-brand-50/40'
                                : 'border-[#E5ECE9] bg-[#FAFDFC] hover:border-brand-300'"
                            class="group relative text-left rounded-2xl border p-3.5 transition-all cursor-pointer space-y-3 focus:outline-none">
                        <!-- Mini Dark UI Wireframe -->
                        <div class="h-14 rounded-xl border overflow-hidden p-2 flex flex-col justify-between" style="background-color: #0D0E10; border-color: #26292E;">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-1.5">
                                    <span class="w-2.5 h-2.5 rounded-full" style="background-color: #168579;"></span>
                                    <span class="w-8 h-1.5 rounded-full" style="background-color: #32363D;"></span>
                                </div>
                                <span class="w-3.5 h-1.5 rounded-full" style="background-color: #22262A;"></span>
                            </div>
                            <div class="grid grid-cols-3 gap-1.5">
                                <div class="h-5 rounded-md border" style="background-color: #151719; border-color: #26292E;"></div>
                                <div class="h-5 rounded-md border col-span-2" style="background-color: #151719; border-color: #26292E;"></div>
                            </div>
                        </div>

                        <div class="flex items-start justify-between gap-2">
                            <div class="min-w-0">
                                <p class="text-xs font-bold text-slate-900">{{ __('admin.settings.theme_dark') }}</p>
                                <p class="text-[11px] text-slate-400 mt-0.5 leading-snug">{{ __('admin.settings.theme_dark_desc') }}</p>
                            </div>
                            <span class="w-4 h-4 rounded-full flex items-center justify-center shrink-0 border transition mt-0.5"
                                  :class="$store.theme.preference === 'dark' ? 'bg-brand-600 border-brand-600 text-white' : 'border-slate-300 text-transparent'">
                                <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                            </span>
                        </div>
                    </button>

                    <!-- Option 3: System -->
                    <button type="button"
                            role="radio"
                            :aria-checked="$store.theme.preference === 'system'"
                            @click="$store.theme.set('system')"
                            :class="$store.theme.preference === 'system'
                                ? 'border-brand-600 ring-2 ring-brand-500/20 bg-brand-50/40'
                                : 'border-[#E5ECE9] bg-[#FAFDFC] hover:border-brand-300'"
                            class="group relative text-left rounded-2xl border p-3.5 transition-all cursor-pointer space-y-3 focus:outline-none">
                        <!-- Mini Split System UI Wireframe -->
                        <div class="h-14 rounded-xl border border-[#DCE5E2] overflow-hidden grid grid-cols-2">
                            <div class="p-2 flex flex-col justify-between" style="background-color: #F6F8F7;">
                                <span class="w-5 h-1.5 rounded-full" style="background-color: #CBD5E1;"></span>
                                <div class="h-5 rounded-md border" style="background-color: #FFFFFF; border-color: #E2EAE7;"></div>
                            </div>
                            <div class="p-2 flex flex-col justify-between" style="background-color: #0D0E10;">
                                <span class="w-5 h-1.5 rounded-full ml-auto" style="background-color: #32363D;"></span>
                                <div class="h-5 rounded-md border" style="background-color: #151719; border-color: #26292E;"></div>
                            </div>
                        </div>

                        <div class="flex items-start justify-between gap-2">
                            <div class="min-w-0">
                                <p class="text-xs font-bold text-slate-900">{{ __('admin.settings.theme_system') }}</p>
                                <p class="text-[11px] text-slate-400 mt-0.5 leading-snug"
                                   x-text="'{{ __('admin.js.theme.follows', ['mode' => '__MODE__']) }}'.replace('__MODE__', $store.theme.systemDark ? '{{ __('admin.js.theme.dark') }}' : '{{ __('admin.js.theme.light') }}')"></p>
                            </div>
                            <span class="w-4 h-4 rounded-full flex items-center justify-center shrink-0 border transition mt-0.5"
                                  :class="$store.theme.preference === 'system' ? 'bg-brand-600 border-brand-600 text-white' : 'border-slate-300 text-transparent'">
                                <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                            </span>
                        </div>
                    </button>
                </div>
            </div>

            <!-- LANGUAGE PREFERENCE (Col Span 5) -->
            @php $activeLocale = app()->getLocale(); @endphp
            <div class="lg:col-span-5 bg-white rounded-[28px] p-6 sm:p-7 shadow-card border border-[#EDF2F0] space-y-5 h-full flex flex-col justify-between">
                <div class="space-y-5">
                    <div class="flex items-center justify-between gap-3 pb-4 border-b border-[#F0F4F2]">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-2xl bg-sky-50 text-sky-600 flex items-center justify-center border border-sky-100 shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.9" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129"/>
                                </svg>
                            </div>
                            <div>
                                <h2 class="text-base font-bold text-slate-900">{{ __('admin.settings.language_title') }}</h2>
                                <p class="text-xs text-slate-400">{{ __('admin.settings.language_desc') }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-3">
                        <!-- Option: English (Default) -->
                        <form action="{{ route('admin.locale.update') }}" method="POST">
                            @csrf
                            <input type="hidden" name="locale" value="en">
                            <button type="submit"
                                    class="w-full text-left rounded-2xl border p-4 transition-all cursor-pointer flex items-center justify-between gap-3 {{ $activeLocale === 'en' ? 'border-brand-600 ring-2 ring-brand-500/20 bg-brand-50/40' : 'border-[#E5ECE9] bg-[#FAFDFC] hover:border-brand-300' }}">
                                <div class="flex items-center gap-3 min-w-0">
                                    <span class="w-10 h-10 rounded-xl bg-white border border-[#E5ECE9] flex items-center justify-center font-mono text-xs font-extrabold text-brand-700 shrink-0 shadow-2xs">
                                        EN
                                    </span>
                                    <div class="min-w-0">
                                        <div class="flex items-center gap-2">
                                            <p class="text-xs font-bold text-slate-900">{{ __('admin.settings.lang_en') }}</p>
                                            <span class="px-2 py-0.5 rounded-full text-[9px] font-bold bg-[#F6F8F7] border border-[#E5ECE9] text-slate-500">{{ __('admin.settings.default_badge') }}</span>
                                        </div>
                                        <p class="text-[11px] text-slate-400 mt-0.5 truncate">{{ __('admin.settings.lang_en_desc') }}</p>
                                    </div>
                                </div>
                                <span class="w-5 h-5 rounded-full flex items-center justify-center shrink-0 border transition {{ $activeLocale === 'en' ? 'bg-brand-600 border-brand-600 text-white' : 'border-slate-300 text-transparent' }}">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                </span>
                            </button>
                        </form>

                        <!-- Option: Bahasa Indonesia -->
                        <form action="{{ route('admin.locale.update') }}" method="POST">
                            @csrf
                            <input type="hidden" name="locale" value="id">
                            <button type="submit"
                                    class="w-full text-left rounded-2xl border p-4 transition-all cursor-pointer flex items-center justify-between gap-3 {{ $activeLocale === 'id' ? 'border-brand-600 ring-2 ring-brand-500/20 bg-brand-50/40' : 'border-[#E5ECE9] bg-[#FAFDFC] hover:border-brand-300' }}">
                                <div class="flex items-center gap-3 min-w-0">
                                    <span class="w-10 h-10 rounded-xl bg-white border border-[#E5ECE9] flex items-center justify-center font-mono text-xs font-extrabold text-brand-700 shrink-0 shadow-2xs">
                                        ID
                                    </span>
                                    <div class="min-w-0">
                                        <p class="text-xs font-bold text-slate-900">{{ __('admin.settings.lang_id') }}</p>
                                        <p class="text-[11px] text-slate-400 mt-0.5 truncate">{{ __('admin.settings.lang_id_desc') }}</p>
                                    </div>
                                </div>
                                <span class="w-5 h-5 rounded-full flex items-center justify-center shrink-0 border transition {{ $activeLocale === 'id' ? 'bg-brand-600 border-brand-600 text-white' : 'border-slate-300 text-transparent' }}">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                </span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- FORM 1: OPERATIONAL RULES & EVENT POLICIES (Col Span 7) -->
            <div class="lg:col-span-7 bg-white rounded-[28px] p-6 sm:p-7 shadow-card border border-[#EDF2F0] space-y-6">
                <div class="flex items-center justify-between pb-4 border-b border-[#F0F4F2]">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center font-bold text-sm border border-amber-100">
                            ⚖
                        </div>
                        <div>
                            <h2 class="text-base font-bold text-slate-900">{{ __('admin.settings.rules_title') }}</h2>
                            <p class="text-xs text-slate-400">{{ __('admin.settings.rules_desc') }}</p>
                        </div>
                    </div>
                </div>

                <form action="{{ route('admin.settings.update_rules') }}" method="POST" class="space-y-4">
                    @csrf

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <!-- Standard Package Duration -->
                        <div class="space-y-1.5">
                            <label class="block text-xs font-bold text-slate-700">
                                Standard Package Duration (Days)
                            </label>
                            <div class="relative">
                                <input type="number" name="standard_package_max_days" min="1" max="14" required
                                       value="{{ $settings['standard_package_max_days'] ?? 2 }}"
                                       class="w-full pl-4 pr-16 py-2.5 rounded-xl border border-slate-200 text-sm font-semibold text-slate-900 focus:outline-none focus:border-brand-600 focus:ring-2 focus:ring-brand-600/15 transition [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none">
                                <span class="absolute right-3.5 top-1/2 -translate-y-1/2 text-xs text-slate-400 font-medium pointer-events-none">Days</span>
                            </div>
                            <p class="text-[11px] text-slate-400">Event duration included in base package pricing.</p>
                        </div>

                        <!-- Extra Day Charge Rate -->
                        <div class="space-y-1.5">
                            <label class="block text-xs font-bold text-slate-700">
                                Extra Day Charge Rate (%)
                            </label>
                            <div class="relative">
                                <input type="number" step="0.5" name="extra_day_charge_rate" min="0" max="100" required
                                       value="{{ $settings['extra_day_charge_rate'] ?? 15 }}"
                                       class="w-full pl-4 pr-16 py-2.5 rounded-xl border border-slate-200 text-sm font-semibold text-slate-900 focus:outline-none focus:border-brand-600 focus:ring-2 focus:ring-brand-600/15 transition [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none">
                                <span class="absolute right-3.5 top-1/2 -translate-y-1/2 text-xs text-slate-400 font-medium pointer-events-none">% / day</span>
                            </div>
                            <p class="text-[11px] text-slate-400">Calculated from base package price per additional day.</p>
                        </div>

                        <!-- Minimum Down Payment -->
                        <div x-data="{
                            rawAmount: {{ (int) ($settings['min_dp_amount'] ?? 1000000) }},
                            displayAmount: '{{ number_format((int) ($settings['min_dp_amount'] ?? 1000000), 0, ',', '.') }}',
                            formatInput(e) {
                                const clean = e.target.value.replace(/\D/g, '');
                                if (!clean) {
                                    this.rawAmount = 0;
                                    this.displayAmount = '';
                                    e.target.value = '';
                                    return;
                                }
                                const num = parseInt(clean, 10);
                                this.rawAmount = num;
                                this.displayAmount = new Intl.NumberFormat('id-ID').format(num);
                                e.target.value = this.displayAmount;
                            }
                        }" class="space-y-1.5">
                            <label class="block text-xs font-bold text-slate-700">
                                Minimum Down Payment (DP)
                            </label>
                            <div class="relative">
                                <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-xs text-slate-400 font-bold pointer-events-none select-none">Rp</span>
                                <input type="text"
                                       inputmode="numeric"
                                       :value="displayAmount"
                                       value="{{ number_format((int) ($settings['min_dp_amount'] ?? 1000000), 0, ',', '.') }}"
                                       @input="formatInput($event)"
                                       @blur="if (rawAmount) { displayAmount = new Intl.NumberFormat('id-ID').format(rawAmount); $el.value = displayAmount; }"
                                       placeholder="1.000.000"
                                       class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-slate-200 text-sm font-semibold font-mono text-slate-900 focus:outline-none focus:border-brand-600 focus:ring-2 focus:ring-brand-600/15 transition">
                                <input type="hidden" name="min_dp_amount" :value="rawAmount" value="{{ (int) ($settings['min_dp_amount'] ?? 1000000) }}">
                            </div>
                            <p class="text-[11px] text-slate-400">Minimum deposit required to confirm and lock a schedule.</p>
                        </div>

                        <!-- Daily Event Capacity (Anti-Overbooking) -->
                        <div class="space-y-1.5">
                            <label class="block text-xs font-bold text-slate-700">
                                Daily Event Capacity
                            </label>
                            <div class="relative">
                                <input type="number" name="max_events_per_day" min="1" max="10" required
                                       value="{{ $settings['max_events_per_day'] ?? 1 }}"
                                       class="w-full pl-4 pr-16 py-2.5 rounded-xl border border-slate-200 text-sm font-semibold text-slate-900 focus:outline-none focus:border-brand-600 focus:ring-2 focus:ring-brand-600/15 transition [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none">
                                <span class="absolute right-3.5 top-1/2 -translate-y-1/2 text-xs text-slate-400 font-medium pointer-events-none">Events</span>
                            </div>
                            <p class="text-[11px] text-slate-400">Maximum booking quota allowed on the same calendar date.</p>
                        </div>
                    </div>

                    <!-- Customer Service WhatsApp -->
                    <div class="space-y-1.5 pt-1">
                        <label class="block text-xs font-bold text-slate-700">
                            Customer Service WhatsApp Number
                        </label>
                        <input type="text" name="admin_whatsapp" required
                               value="{{ $settings['admin_whatsapp'] ?? '6282255969313' }}"
                               placeholder="6282255969313"
                               class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm font-mono font-semibold text-slate-900 focus:outline-none focus:border-brand-600 focus:ring-2 focus:ring-brand-600/15 transition">
                        <p class="text-[11px] text-slate-400">International format without symbols or hyphens (e.g. 6282255969313).</p>
                    </div>

                    <!-- Logistics Basecamp & Delivery Tiers -->
                    <div class="pt-3 border-t border-slate-100 space-y-3">
                        <div class="flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-brand-600"></span>
                            <span class="text-xs font-bold text-slate-800 uppercase tracking-wider">Logistics Basecamp & Transport Distance Tiers</span>
                        </div>

                        <!-- Warehouse Address -->
                        <div class="space-y-1.5">
                            <label class="block text-xs font-bold text-slate-700">
                                Slawi Warehouse & Fleet Departure Address
                            </label>
                            <input type="text" name="warehouse_address" required
                                   value="{{ $settings['warehouse_address'] ?? 'Jl. K.H. Ahmad Dahlan, RT 04 / RW 01, Slawi Kulon, Kec. Slawi, Kab. Tegal' }}"
                                   class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm font-semibold text-slate-900 focus:outline-none focus:border-brand-600 focus:ring-2 focus:ring-brand-600/15 transition">
                            <p class="text-[11px] text-slate-400">Origin point for delivery routing & distance calculations.</p>
                        </div>

                        <!-- Warehouse Coordinates -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div class="space-y-1.5">
                                <label class="block text-xs font-bold text-slate-700">Basecamp Latitude</label>
                                <input type="text" name="warehouse_lat" required
                                       value="{{ $settings['warehouse_lat'] ?? '-6.9857806' }}"
                                       class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm font-mono font-semibold text-slate-900 focus:outline-none focus:border-brand-600">
                            </div>
                            <div class="space-y-1.5">
                                <label class="block text-xs font-bold text-slate-700">Basecamp Longitude</label>
                                <input type="text" name="warehouse_lng" required
                                       value="{{ $settings['warehouse_lng'] ?? '109.136971' }}"
                                       class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm font-mono font-semibold text-slate-900 focus:outline-none focus:border-brand-600">
                            </div>
                        </div>

                        <!-- Free Distance, Tier Distance & Fee -->
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                            <div class="space-y-1.5">
                                <label class="block text-xs font-bold text-slate-700">Free Delivery (≤ km)</label>
                                <div class="relative">
                                    <input type="number" min="0" step="1" name="free_distance_km" required
                                           value="{{ $settings['free_distance_km'] ?? 10 }}"
                                           class="w-full pl-4 pr-12 py-2.5 rounded-xl border border-slate-200 text-sm font-semibold text-slate-900 focus:outline-none focus:border-brand-600">
                                    <span class="absolute right-3.5 top-1/2 -translate-y-1/2 text-xs text-slate-400 font-medium pointer-events-none">km</span>
                                </div>
                            </div>
                            <div class="space-y-1.5">
                                <label class="block text-xs font-bold text-slate-700">Distance Tier Step</label>
                                <div class="relative">
                                    <input type="number" min="1" step="1" name="distance_tier_km" required
                                           value="{{ $settings['distance_tier_km'] ?? 10 }}"
                                           class="w-full pl-4 pr-12 py-2.5 rounded-xl border border-slate-200 text-sm font-semibold text-slate-900 focus:outline-none focus:border-brand-600">
                                    <span class="absolute right-3.5 top-1/2 -translate-y-1/2 text-xs text-slate-400 font-medium pointer-events-none">km</span>
                                </div>
                            </div>
                            <div class="space-y-1.5" x-data="{
                                rawTierFee: {{ (int) ($settings['transport_fee_per_tier'] ?? 300000) }},
                                displayTierFee: '{{ number_format((int) ($settings['transport_fee_per_tier'] ?? 300000), 0, ',', '.') }}',
                                formatTierFee(e) {
                                    const clean = e.target.value.replace(/\D/g, '');
                                    if (!clean) {
                                        this.rawTierFee = 0;
                                        this.displayTierFee = '';
                                        e.target.value = '';
                                        return;
                                    }
                                    const num = parseInt(clean, 10);
                                    this.rawTierFee = num;
                                    this.displayTierFee = new Intl.NumberFormat('id-ID').format(num);
                                    e.target.value = this.displayTierFee;
                                }
                            }">
                                <label class="block text-xs font-bold text-slate-700">Fee per Distance Tier</label>
                                <div class="relative">
                                    <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-xs text-slate-400 font-bold pointer-events-none select-none">Rp</span>
                                    <input type="text"
                                           inputmode="numeric"
                                           :value="displayTierFee"
                                           value="{{ number_format((int) ($settings['transport_fee_per_tier'] ?? 300000), 0, ',', '.') }}"
                                           @input="formatTierFee($event)"
                                           @blur="if (rawTierFee) { displayTierFee = new Intl.NumberFormat('id-ID').format(rawTierFee); $el.value = displayTierFee; }"
                                           placeholder="300.000"
                                           class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-slate-200 text-sm font-mono font-semibold text-slate-900 focus:outline-none focus:border-brand-600 focus:ring-2 focus:ring-brand-600/15 transition">
                                    <input type="hidden" name="transport_fee_per_tier" :value="rawTierFee" value="{{ (int) ($settings['transport_fee_per_tier'] ?? 300000) }}">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="pt-3 border-t border-[#F0F4F2] flex items-center justify-end">
                        <button type="submit"
                                class="inline-flex items-center gap-2 px-6 py-2.5 rounded-full bg-brand-600 hover:bg-brand-700 text-white text-xs font-bold transition shadow-pill cursor-pointer">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M5 13l4 4L19 7"/></svg>
                            <span>{{ __('admin.settings.save_policy') }}</span>
                        </button>
                    </div>
                </form>
            </div>

            <!-- FORM 2: OFFICIAL PAYMENT BANK ACCOUNTS (Col Span 5) -->
            <div class="lg:col-span-5 bg-white rounded-[28px] p-6 sm:p-7 shadow-card border border-[#EDF2F0] space-y-6">
                <div class="flex items-center justify-between pb-4 border-b border-[#F0F4F2]">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold text-sm border border-indigo-100">
                            💳
                        </div>
                        <div>
                            <h2 class="text-base font-bold text-slate-900">{{ __('admin.settings.banks_title') }}</h2>
                            <p class="text-xs text-slate-400">{{ __('admin.settings.banks_desc') }}</p>
                        </div>
                    </div>
                </div>

                <form action="{{ route('admin.settings.update_banks') }}" method="POST" class="space-y-4">
                    @csrf

                    <!-- Bank BCA Section -->
                    <div class="p-4 rounded-2xl bg-[#F8FAFA] border border-[#EFF4F2] space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-[#003B73] uppercase tracking-wider">Bank BCA (Primary)</span>
                            <span class="w-2 h-2 rounded-full bg-blue-600"></span>
                        </div>
                        <div class="space-y-2">
                            <div>
                                <label class="block text-[11px] font-semibold text-slate-600 mb-1">Account Number</label>
                                <input type="text" name="bca_account_number" required
                                       value="{{ $settings['bca_account_number'] ?? '3620648107' }}"
                                       class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs font-mono font-bold text-slate-900 focus:outline-none focus:border-brand-600 focus:ring-2 focus:ring-brand-600/15 bg-white transition">
                            </div>
                            <div>
                                <label class="block text-[11px] font-semibold text-slate-600 mb-1">Account Holder Name (Owner)</label>
                                <input type="text" name="bca_account_name" required
                                       value="{{ $settings['bca_account_name'] ?? 'EZZU RAKHMAT FIRDAUS' }}"
                                       class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs font-semibold text-slate-900 uppercase focus:outline-none focus:border-brand-600 focus:ring-2 focus:ring-brand-600/15 bg-white transition">
                            </div>
                        </div>
                    </div>

                    <!-- Bank BRI Section -->
                    <div class="p-4 rounded-2xl bg-[#F8FAFA] border border-[#EFF4F2] space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-[#004D99] uppercase tracking-wider">Bank BRI (Alternative)</span>
                            <span class="w-2 h-2 rounded-full bg-sky-600"></span>
                        </div>
                        <div class="space-y-2">
                            <div>
                                <label class="block text-[11px] font-semibold text-slate-600 mb-1">Account Number</label>
                                <input type="text" name="bri_account_number" required
                                       value="{{ $settings['bri_account_number'] ?? '344301026581532' }}"
                                       class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs font-mono font-bold text-slate-900 focus:outline-none focus:border-brand-600 focus:ring-2 focus:ring-brand-600/15 bg-white transition">
                            </div>
                            <div>
                                <label class="block text-[11px] font-semibold text-slate-600 mb-1">Account Holder Name (Owner)</label>
                                <input type="text" name="bri_account_name" required
                                       value="{{ $settings['bri_account_name'] ?? 'EZZU RAKHMAT FIRDAUS' }}"
                                       class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs font-semibold text-slate-900 uppercase focus:outline-none focus:border-brand-600 focus:ring-2 focus:ring-brand-600/15 bg-white transition">
                            </div>
                        </div>
                    </div>

                    <div class="pt-2 border-t border-[#F0F4F2] flex items-center justify-end">
                        <button type="submit"
                                class="inline-flex items-center gap-2 px-6 py-2.5 rounded-full bg-brand-600 hover:bg-brand-700 text-white text-xs font-bold transition shadow-pill cursor-pointer">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M5 13l4 4L19 7"/></svg>
                            <span>{{ __('admin.settings.save_banks') }}</span>
                        </button>
                    </div>
                </form>
            </div>

            <!-- FORM 3: GOOGLE CLOUD INTEGRATION (SHEETS & CALENDAR) (Col Span 12) -->
            <div class="lg:col-span-12 bg-white rounded-[28px] p-6 sm:p-7 shadow-card border border-[#EDF2F0] space-y-6">
                <div class="flex items-center justify-between pb-4 border-b border-[#F0F4F2]">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-2xl bg-sky-50 text-sky-600 flex items-center justify-center font-bold text-sm border border-sky-100">
                            G
                        </div>
                        <div>
                            <h2 class="text-base font-bold text-slate-900">{{ __('admin.settings.google_title') }}</h2>
                            <p class="text-xs text-slate-400">{{ __('admin.settings.google_desc') }}</p>
                        </div>
                    </div>
                    <span class="px-3 py-1 rounded-full text-xs font-bold text-emerald-700 bg-emerald-50 border border-emerald-200">{{ __('admin.settings.connected') }}</span>
                </div>

                <form action="{{ route('admin.settings.update_google') }}" method="POST" class="space-y-5">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <!-- Google Spreadsheet ID -->
                        <div class="space-y-1.5">
                            <div class="flex items-center justify-between">
                                <label class="block text-xs font-bold text-slate-700">
                                    Google Spreadsheet ID (Recap Database)
                                </label>
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold text-emerald-700 bg-emerald-50">Auto Sync</span>
                            </div>
                            <input type="text" name="google_sheet_id"
                                   value="{{ $settings['google_sheet_id'] ?? '' }}"
                                   placeholder="Example: 1RzvZTREK-1dNA99yYV8K59..."
                                   class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs sm:text-sm font-mono font-semibold text-slate-900 focus:outline-none focus:border-brand-600 focus:ring-2 focus:ring-brand-600/15 transition">
                            <p class="text-[11px] text-slate-400">Spreadsheet ID from Google Sheets URL for real-time booking records.</p>
                        </div>

                        <!-- Google Calendar ID -->
                        <div class="space-y-1.5">
                            <div class="flex items-center justify-between">
                                <label class="block text-xs font-bold text-slate-700">
                                    Google Calendar ID (Event Schedule)
                                </label>
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold text-sky-700 bg-sky-50">Auto Event</span>
                            </div>
                            <input type="text" name="google_calendar_id"
                                   value="{{ $settings['google_calendar_id'] ?? '' }}"
                                   placeholder="Example: primary or your-calendar@gmail.com"
                                   class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs sm:text-sm font-mono font-semibold text-slate-900 focus:outline-none focus:border-brand-600 focus:ring-2 focus:ring-brand-600/15 transition">
                            <p class="text-[11px] text-slate-400">Google Calendar ID (usually primary or the Google account email address).</p>
                        </div>
                    </div>

                    <div class="pt-3 border-t border-[#F0F4F2] flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                        <p class="text-xs text-slate-500">
                            Updates to Google IDs apply immediately to newly confirmed bookings and manual synchronization.
                        </p>
                        <button type="submit"
                                class="inline-flex items-center justify-center gap-2 px-6 py-2.5 rounded-full bg-brand-600 hover:bg-brand-700 text-white text-xs font-bold transition shadow-pill cursor-pointer shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M5 13l4 4L19 7"/></svg>
                            <span>{{ __('admin.settings.save_google') }}</span>
                        </button>
                    </div>
                </form>

                <!-- Box Sinkronisasi Ulang Semua Data -->
                <div class="p-5 rounded-2xl bg-gradient-to-br from-brand-50/50 to-brand-100/30 border border-brand-200/70 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div class="space-y-0.5">
                        <h3 class="text-sm font-bold text-slate-900">{{ __('admin.settings.resync_title') }}</h3>
                        <p class="text-xs text-slate-600 leading-relaxed">
                            {{ __('admin.settings.resync_desc') }}
                        </p>
                    </div>

                    <form action="{{ route('admin.settings.sync_all') }}" method="POST" class="shrink-0">
                        @csrf
                        <button type="submit"
                                class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-full bg-white hover:bg-slate-50 border border-brand-200 text-brand-700 hover:text-brand-800 text-xs font-bold transition shadow-2xs cursor-pointer">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                            <span>{{ __('admin.settings.sync_now') }}</span>
                        </button>
                    </form>
                </div>
            </div>

            <!-- FORM 4: SECURITY & ROLE PIN ACCESS (OWNER + FIELD COORDINATOR) -->
            <div class="lg:col-span-12 grid grid-cols-1 lg:grid-cols-2 gap-6 items-stretch">

                <!-- 4A: OWNER PIN ACCESS -->
                <div class="bg-white rounded-[28px] p-6 sm:p-7 shadow-card border border-[#EDF2F0] flex flex-col justify-between space-y-5">
                    <div class="space-y-5">
                        <div class="flex items-center justify-between pb-4 border-b border-[#F0F4F2]">
                            <div class="space-y-0.5">
                                <div class="flex items-center gap-2">
                                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                                    <h2 class="text-base font-bold text-slate-900 tracking-tight">{{ __('admin.settings.owner_pin_title') }}</h2>
                                </div>
                                <p class="text-xs text-slate-400">{{ __('admin.settings.owner_pin_desc') }}</p>
                            </div>
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-brand-50 text-brand-700 border border-brand-200">Owner</span>
                        </div>

                        <form action="{{ route('admin.settings.update_pin') }}" method="POST" class="space-y-4">
                            @csrf
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">New Owner PIN</label>
                                    <input type="password" name="pin" required maxlength="6" pattern="[0-9]{6}" inputmode="numeric"
                                           placeholder="••••••"
                                           class="w-full px-4 py-2.5 rounded-2xl border border-[#E5ECE9] bg-[#FAFDFC] focus:bg-white text-sm font-mono font-bold tracking-widest text-slate-900 focus:outline-none focus:ring-2 focus:ring-brand-600/15 focus:border-brand-500 transition">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Confirm Owner PIN</label>
                                    <input type="password" name="pin_confirmation" required maxlength="6" pattern="[0-9]{6}" inputmode="numeric"
                                           placeholder="••••••"
                                           class="w-full px-4 py-2.5 rounded-2xl border border-[#E5ECE9] bg-[#FAFDFC] focus:bg-white text-sm font-mono font-bold tracking-widest text-slate-900 focus:outline-none focus:ring-2 focus:ring-brand-600/15 focus:border-brand-500 transition">
                                </div>
                            </div>
                            <div class="pt-2 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                                <p class="text-[11px] text-slate-400">Default PIN: <span class="font-mono font-bold text-brand-700">123456</span></p>
                                <button type="submit"
                                        class="inline-flex items-center justify-center gap-2 px-6 py-2.5 rounded-full bg-brand-600 hover:bg-brand-700 text-white text-xs font-bold transition shadow-xs cursor-pointer">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M5 13l4 4L19 7"/></svg>
                                    <span>{{ __('admin.settings.update_owner_pin') }}</span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- 4B: FIELD COORDINATOR ACCESS (1 SHARED ACCOUNT) -->
                @php
                    $staffActive = $staffAccount ? (bool) $staffAccount->is_active : true;
                    $staffDisplayName = $staffAccount?->name ?? 'Field Coordinator';
                @endphp
                <div class="bg-white rounded-[28px] p-6 sm:p-7 shadow-card border border-[#EDF2F0] space-y-5"
                     x-data="{ staffEnabled: {{ $staffActive ? 'true' : 'false' }} }">
                    <div class="flex items-center justify-between pb-4 border-b border-[#F0F4F2]">
                        <div class="space-y-0.5">
                            <div class="flex items-center gap-2">
                                <span class="w-2.5 h-2.5 rounded-full" :class="staffEnabled ? 'bg-teal-500' : 'bg-slate-300'"></span>
                                <h2 class="text-base font-bold text-slate-900 tracking-tight">{{ __('admin.settings.staff_access_title') }}</h2>
                            </div>
                            <p class="text-xs text-slate-400">{{ __('admin.settings.staff_access_desc') }}</p>
                        </div>
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold border"
                              :class="staffEnabled ? 'bg-teal-50 text-teal-800 border-teal-200' : 'bg-slate-100 text-slate-500 border-slate-200'"
                              x-text="staffEnabled ? 'Active' : 'Disabled'"></span>
                    </div>

                    <form action="{{ route('admin.settings.update_staff') }}" method="POST" class="space-y-4">
                        @csrf
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 items-end">
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1.5">Account Display Name</label>
                                <input type="text" name="staff_name" required maxlength="100"
                                       value="{{ $staffDisplayName }}"
                                       placeholder="Field Coordinator"
                                       class="w-full px-4 py-2.5 rounded-2xl border border-[#E5ECE9] bg-[#FAFDFC] focus:bg-white text-xs sm:text-sm font-semibold text-slate-900 focus:outline-none focus:ring-2 focus:ring-brand-600/15 focus:border-brand-500 transition">
                            </div>

                            <label class="flex items-center justify-between gap-3 px-4 py-2.5 rounded-2xl border border-[#E5ECE9] bg-[#FAFDFC] cursor-pointer select-none">
                                <span class="text-xs font-semibold text-slate-700">Allow Login Access</span>
                                <input type="hidden" name="staff_is_active" :value="staffEnabled ? 1 : 0">
                                <button type="button"
                                        @click="staffEnabled = !staffEnabled"
                                        :class="staffEnabled ? 'bg-brand-600' : 'bg-slate-300'"
                                        class="relative inline-flex h-5 w-9 shrink-0 rounded-full transition-colors duration-200 cursor-pointer">
                                    <span :class="staffEnabled ? 'translate-x-4' : 'translate-x-0.5'"
                                          class="inline-block h-4 w-4 transform rounded-full bg-white shadow transition duration-200 mt-0.5"></span>
                                </button>
                            </label>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                                    {{ $staffAccount ? 'New Staff PIN (Leave blank to keep)' : 'Set 6-Digit Staff PIN' }}
                                </label>
                                <input type="password" name="staff_pin" {{ $staffAccount ? '' : 'required' }} maxlength="6" pattern="[0-9]{6}" inputmode="numeric"
                                       placeholder="••••••"
                                       class="w-full px-4 py-2.5 rounded-2xl border border-[#E5ECE9] bg-[#FAFDFC] focus:bg-white text-sm font-mono font-bold tracking-widest text-slate-900 focus:outline-none focus:ring-2 focus:ring-brand-600/15 focus:border-brand-500 transition">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1.5">Confirm Staff PIN</label>
                                <input type="password" name="staff_pin_confirmation" {{ $staffAccount ? '' : 'required' }} maxlength="6" pattern="[0-9]{6}" inputmode="numeric"
                                       placeholder="••••••"
                                       class="w-full px-4 py-2.5 rounded-2xl border border-[#E5ECE9] bg-[#FAFDFC] focus:bg-white text-sm font-mono font-bold tracking-widest text-slate-900 focus:outline-none focus:ring-2 focus:ring-brand-600/15 focus:border-brand-500 transition">
                            </div>
                        </div>

                        <div class="pt-2 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                            <p class="text-[11px] text-slate-400">Default Staff PIN: <span class="font-mono font-bold text-teal-700">112233</span></p>
                            <button type="submit"
                                    class="inline-flex items-center justify-center gap-2 px-6 py-2.5 rounded-full bg-brand-600 hover:bg-brand-700 text-white text-xs font-bold transition shadow-xs cursor-pointer">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M5 13l4 4L19 7"/></svg>
                                <span>{{ __('admin.settings.save_staff_access') }}</span>
                            </button>
                        </div>
                    </form>
                </div>

            </div>

        </div>

    </div>

