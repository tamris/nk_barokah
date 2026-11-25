    <!-- ================================================================== -->
    <!-- TAB 5: SERVICES (REAL PACKAGES & ADD-ONS FROM MASTER DATA)         -->
    <!-- ================================================================== -->
    <div x-show="$store.nav.tab === 'services'"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 translate-y-1"
         x-transition:enter-end="opacity-100 translate-y-0"
         class="space-y-5"
         x-cloak>

        <div class="flex flex-col md:flex-row md:items-center justify-between gap-3">
            <div class="flex flex-wrap items-center gap-3">
                <div class="inline-flex items-center gap-1 bg-white p-1 rounded-full border border-[#E7EEEC]">
                    <button type="button" @click="serviceSubTab = 'packages'"
                            :class="serviceSubTab === 'packages' ? 'bg-brand-600 text-white shadow-sm' : 'text-slate-600 hover:text-slate-900'"
                            class="px-4 py-2 rounded-full text-xs font-semibold transition">
                        {{ __('admin.services.tab_packages') }} (<span x-text="packagesList.length"></span>)
                    </button>
                    <button type="button" @click="serviceSubTab = 'addons'"
                            :class="serviceSubTab === 'addons' ? 'bg-brand-600 text-white shadow-sm' : 'text-slate-600 hover:text-slate-900'"
                            class="px-4 py-2 rounded-full text-xs font-semibold transition">
                        {{ __('admin.services.tab_addons') }} (<span x-text="addonsList.length"></span>)
                    </button>
                </div>

                <div class="relative flex items-center bg-white rounded-full border border-[#E7EEEC] w-full sm:w-60">
                    <svg class="w-4 h-4 text-slate-400 absolute left-4 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-5.2-5.2m2.2-4.8a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input type="text" x-model="serviceSearch" placeholder="{{ __('admin.services.search_placeholder') }}"
                           class="w-full bg-transparent pl-10 pr-4 py-2.5 text-xs text-slate-700 placeholder-slate-400 focus:outline-none">
                </div>
            </div>

            <button type="button"
                    @click="serviceSubTab === 'packages' ? openPackageModal() : openAddonModal()"
                    class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-full bg-brand-600 hover:bg-brand-700 text-white text-xs font-semibold transition shadow-pill shrink-0 cursor-pointer active:scale-95">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M12 4v16m8-8H4"/></svg>
                <span x-text="serviceSubTab === 'packages' ? '{{ __('admin.services.add_package') }}' : '{{ __('admin.services.add_addon') }}'"></span>
            </button>
        </div>

        <div x-show="serviceSubTab === 'packages'" class="grid grid-cols-1 lg:grid-cols-3 gap-5">
            <template x-for="pkg in packagesList.filter(p => !serviceSearch || p.name.toLowerCase().includes(serviceSearch.toLowerCase()))" :key="pkg.id">
                <div class="bg-white rounded-[28px] p-6 shadow-card border border-[#EDF2F0] flex flex-col justify-between space-y-5 hover:border-brand-300 transition">
                    <div class="space-y-4">
                        <div class="flex items-center justify-between gap-2">
                            <span class="px-3 py-1 rounded-full text-[11px] font-bold bg-brand-50 text-brand-600" x-text="'Booked ' + pkg.booked_count + 'x'"></span>
                            <form :action="pkg.toggle_url" method="POST">
                                @csrf
                                @method('PATCH')
                                <button type="submit"
                                        :class="pkg.is_active ? 'bg-[#E6F6EF] text-emerald-700' : 'bg-slate-100 text-slate-500'"
                                        class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold transition cursor-pointer">
                                    <span class="w-1.5 h-1.5 rounded-full" :class="pkg.is_active ? 'bg-emerald-500' : 'bg-slate-400'"></span>
                                    <span x-text="pkg.is_active ? 'Active' : 'Inactive'"></span>
                                </button>
                            </form>
                        </div>

                        <div>
                            <h3 class="text-[17px] font-bold text-slate-900" x-text="pkg.name"></h3>
                            <p class="text-2xl font-extrabold text-brand-600 tracking-tight mt-2" x-text="pkg.price_formatted"></p>
                        </div>

                        <p class="text-xs text-slate-500 leading-relaxed pt-2 border-t border-[#EFF4F2]" x-text="pkg.description"></p>
                    </div>

                    <button type="button"
                            @click="openPackageModal(pkg)"
                            class="w-full py-2.5 rounded-full border border-[#E5ECE9] hover:bg-[#F6F8F7] hover:border-brand-300 hover:text-brand-700 text-xs font-semibold text-slate-700 text-center transition cursor-pointer active:scale-[0.99]">
                        Edit Package
                    </button>
                </div>
            </template>
        </div>

        <div x-show="serviceSubTab === 'addons'" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4" x-cloak>
            <template x-for="addon in addonsList.filter(a => !serviceSearch || a.name.toLowerCase().includes(serviceSearch.toLowerCase()))" :key="addon.id">
                <div class="bg-white rounded-[24px] p-5 shadow-card border border-[#EDF2F0] hover:border-brand-300 transition flex flex-col justify-between gap-4">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <span class="text-[10px] font-bold uppercase tracking-wider text-brand-600 bg-brand-50 px-2.5 py-0.5 rounded-full" x-text="'Unit: ' + addon.unit"></span>
                            <h3 class="text-sm font-bold text-slate-900 mt-2" x-text="addon.name"></h3>
                        </div>
                        <form :action="addon.toggle_url" method="POST">
                            @csrf
                            @method('PATCH')
                            <button type="submit"
                                    :class="addon.is_active ? 'bg-[#E6F6EF] text-emerald-700' : 'bg-slate-100 text-slate-500'"
                                    class="px-2.5 py-1 rounded-full text-[10px] font-bold transition cursor-pointer">
                                <span x-text="addon.is_active ? 'Active' : 'Hidden'"></span>
                            </button>
                        </form>
                    </div>
                    <div class="pt-3 border-t border-[#EFF4F2] flex items-baseline justify-between">
                        <div>
                            <span class="text-base font-extrabold text-slate-900" x-text="addon.price_formatted"></span>
                            <span class="text-[11px] text-slate-400" x-text="' / ' + addon.unit"></span>
                        </div>
                        <button type="button"
                                @click="openAddonModal(addon)"
                                class="text-[11px] font-semibold text-brand-600 hover:underline cursor-pointer">
                            Edit ›
                        </button>
                    </div>
                </div>
            </template>
        </div>
    </div>

