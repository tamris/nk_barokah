    <!-- ================================================================== -->
    <!-- CONFIRMATION MODAL (MODERN REPLACEMENT FOR NATIVE BROWSER ALERT)   -->
    <!-- ================================================================== -->
    <div x-show="confirmModal.open"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/60 backdrop-blur-xs"
         @keydown.escape.window="confirmModal.open = false"
         x-cloak>
        <div class="bg-white rounded-[28px] max-w-md w-full p-6 shadow-2xl border border-[#EAF0EE] space-y-4"
             @click.away="confirmModal.open = false"
             x-transition:enter="transition ease-out duration-200 transform"
             x-transition:enter-start="opacity-0 scale-95 translate-y-2"
             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
             x-transition:leave="transition ease-in duration-150 transform"
             x-transition:leave-start="opacity-100 scale-100 translate-y-0"
             x-transition:leave-end="opacity-0 scale-95 translate-y-2">
            <div class="flex items-start justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl flex items-center justify-center shrink-0 border"
                         :class="confirmModal.iconType === 'warning' ? 'bg-rose-50 text-rose-600 border-rose-100' : 'bg-brand-50 text-brand-600 border-brand-100'">
                        <template x-if="confirmModal.iconType === 'warning'">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                        </template>
                        <template x-if="confirmModal.iconType !== 'warning'">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.4" d="M5 13l4 4L19 7"/>
                            </svg>
                        </template>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-slate-900" x-text="confirmModal.title"></h3>
                        <p class="text-[11px] text-slate-400">Owner Action Confirmation</p>
                    </div>
                </div>
                <button type="button" @click="confirmModal.open = false" class="p-1.5 rounded-full text-slate-400 hover:bg-slate-100">✕</button>
            </div>

            <div class="space-y-3">
                <p class="text-xs sm:text-sm font-semibold text-slate-800 leading-relaxed" x-text="confirmModal.message"></p>
                <div class="p-3.5 bg-[#F6F8F7] rounded-2xl border border-[#E8EFED] text-[11px] text-slate-600 leading-normal" x-show="confirmModal.subtext">
                    <span x-text="confirmModal.subtext"></span>
                </div>
            </div>

            <form :action="confirmModal.actionUrl" method="POST" class="flex items-center gap-2.5 pt-2">
                @csrf
                <template x-if="confirmModal.method === 'DELETE'">
                    <input type="hidden" name="_method" value="DELETE">
                </template>
                <button type="button" @click="confirmModal.open = false"
                        class="flex-1 py-2.5 px-4 rounded-full border border-[#DCE5E2] text-slate-700 font-semibold text-xs hover:bg-slate-50 transition cursor-pointer">
                    Cancel
                </button>
                <button type="submit" :class="confirmModal.btnClass"
                        class="flex-1 py-2.5 px-4 rounded-full font-bold text-xs transition shadow-sm cursor-pointer active:scale-95">
                    <span x-text="confirmModal.btnText"></span>
                </button>
            </form>
        </div>
    </div>

    <!-- ================================================================== -->
    <!-- MODAL: ADD / EDIT WEDDING PACKAGE                                  -->
    <!-- ================================================================== -->
    <div x-show="packageModal.open"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 overflow-y-auto bg-slate-950/60 backdrop-blur-xs flex items-center justify-center p-4"
         @keydown.escape.window="closePackageModal()"
         x-cloak>
        <div class="relative bg-white rounded-[28px] max-w-lg w-full p-6 sm:p-7 shadow-2xl border border-[#EDF2F0] space-y-5 transform transition-all"
             @click.away="closePackageModal()"
             x-transition:enter="transition ease-out duration-200 transform"
             x-transition:enter-start="opacity-0 scale-95 translate-y-2"
             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
             x-transition:leave="transition ease-in duration-150 transform"
             x-transition:leave-start="opacity-100 scale-100 translate-y-0"
             x-transition:leave-end="opacity-0 scale-95 translate-y-2">

            <!-- Modal Header -->
            <div class="flex items-start justify-between gap-3 pb-4 border-b border-[#EFF4F2]">
                <div class="flex items-center gap-3.5">
                    <div class="w-11 h-11 rounded-2xl bg-brand-50 border border-brand-100 text-brand-700 flex items-center justify-center shrink-0 shadow-2xs">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-base sm:text-lg font-bold text-slate-900 tracking-tight"
                            x-text="packageModal.isEdit ? 'Edit Wedding Package' : 'Create New Package'"></h3>
                        <p class="text-xs text-slate-500 mt-0.5"
                           x-text="packageModal.isEdit ? 'Update package specifications and pricing' : 'Add a new package to the official service catalog'"></p>
                    </div>
                </div>
                <button type="button" @click="closePackageModal()"
                        class="w-8 h-8 rounded-full bg-[#F4F7F6] hover:bg-[#EAEFEA] text-slate-500 hover:text-slate-800 flex items-center justify-center transition cursor-pointer shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <!-- Form -->
            <form :action="packageModal.action" method="POST" class="space-y-4">
                @csrf
                <template x-if="packageModal.isEdit">
                    <input type="hidden" name="_method" value="PUT">
                </template>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5 uppercase tracking-wider">Package Name <span class="text-rose-500">*</span></label>
                    <input type="text" name="name" x-model="packageModal.name" required
                           placeholder="e.g. Tenda Set, Paket 1, Diamond Royal"
                           class="w-full px-4 py-2.5 rounded-2xl border border-[#E2EAE7] bg-[#FBFDFD] text-slate-900 placeholder:text-slate-400 text-xs sm:text-sm font-medium focus:outline-none focus:border-brand-500 focus:bg-white focus:ring-2 focus:ring-brand-500/20 transition">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5 uppercase tracking-wider">Base Price (IDR) <span class="text-rose-500">*</span></label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-xs font-bold text-slate-400 select-none pointer-events-none">Rp</span>
                        <input type="text"
                               inputmode="numeric"
                               :value="packageModal.display_price"
                               @input="formatPackagePriceInput($event)"
                               @blur="if (packageModal.base_price !== '' && packageModal.base_price !== null && packageModal.base_price !== undefined) { packageModal.display_price = new Intl.NumberFormat('id-ID').format(packageModal.base_price); $el.value = packageModal.display_price; }"
                               required
                               placeholder="7.000.000"
                               class="w-full pl-11 pr-4 py-2.5 rounded-2xl border border-[#E2EAE7] bg-[#FBFDFD] text-slate-900 placeholder:text-slate-400 text-xs sm:text-sm font-semibold font-mono focus:outline-none focus:border-brand-500 focus:bg-white focus:ring-2 focus:ring-brand-500/20 transition">
                        <input type="hidden" name="base_price" :value="packageModal.base_price">
                    </div>
                    <p class="text-[11px] text-slate-400 mt-1">Standard rate charged for the base duration of this package.</p>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5 uppercase tracking-wider">Description & Inclusions</label>
                    <textarea name="description" x-model="packageModal.description" rows="4"
                              placeholder="Describe tent sizes, chairs, tables, decorations, lighting, stage, etc."
                              class="w-full px-4 py-2.5 rounded-2xl border border-[#E2EAE7] bg-[#FBFDFD] text-slate-900 placeholder:text-slate-400 text-xs sm:text-sm font-medium focus:outline-none focus:border-brand-500 focus:bg-white focus:ring-2 focus:ring-brand-500/20 transition resize-none"></textarea>
                    <p class="text-[11px] text-slate-400 mt-1">Displayed to clients and staff during booking review.</p>
                </div>

                <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-[#EFF4F2]">
                    <button type="button" @click="closePackageModal()"
                            class="px-5 py-2.5 rounded-full border border-[#DCE5E2] text-slate-700 font-semibold text-xs hover:bg-slate-50 transition cursor-pointer">
                        Cancel
                    </button>
                    <button type="submit"
                            class="px-6 py-2.5 rounded-full bg-brand-600 hover:bg-brand-700 text-white font-bold text-xs transition shadow-pill active:scale-95 cursor-pointer">
                        <span x-text="packageModal.isEdit ? 'Save Changes' : 'Create Package'"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ================================================================== -->
    <!-- MODAL: ADD / EDIT ADD-ON FACILITY                                  -->
    <!-- ================================================================== -->
    <div x-show="addonModal.open"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 overflow-y-auto bg-slate-950/60 backdrop-blur-xs flex items-center justify-center p-4"
         @keydown.escape.window="closeAddonModal()"
         x-cloak>
        <div class="relative bg-white rounded-[28px] max-w-lg w-full p-6 sm:p-7 shadow-2xl border border-[#EDF2F0] space-y-5 transform transition-all"
             @click.away="closeAddonModal()"
             x-transition:enter="transition ease-out duration-200 transform"
             x-transition:enter-start="opacity-0 scale-95 translate-y-2"
             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
             x-transition:leave="transition ease-in duration-150 transform"
             x-transition:leave-start="opacity-100 scale-100 translate-y-0"
             x-transition:leave-end="opacity-0 scale-95 translate-y-2">

            <!-- Modal Header -->
            <div class="flex items-start justify-between gap-3 pb-4 border-b border-[#EFF4F2]">
                <div class="flex items-center gap-3.5">
                    <div class="w-11 h-11 rounded-2xl bg-brand-50 border border-brand-100 text-brand-700 flex items-center justify-center shrink-0 shadow-2xs">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-base sm:text-lg font-bold text-slate-900 tracking-tight"
                            x-text="addonModal.isEdit ? 'Edit Add-on Facility' : 'Create New Add-on'"></h3>
                        <p class="text-xs text-slate-500 mt-0.5"
                           x-text="addonModal.isEdit ? 'Update add-on name, unit, and rental rate' : 'Introduce a new extra facility or equipment for events'"></p>
                    </div>
                </div>
                <button type="button" @click="closeAddonModal()"
                        class="w-8 h-8 rounded-full bg-[#F4F7F6] hover:bg-[#EAEFEA] text-slate-500 hover:text-slate-800 flex items-center justify-center transition cursor-pointer shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <!-- Form -->
            <form :action="addonModal.action" method="POST" class="space-y-4">
                @csrf
                <template x-if="addonModal.isEdit">
                    <input type="hidden" name="_method" value="PUT">
                </template>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5 uppercase tracking-wider">Add-on Item Name <span class="text-rose-500">*</span></label>
                    <input type="text" name="name" x-model="addonModal.name" required
                           placeholder="e.g. Kursi Futura + Cover, Panggung Pelaminan"
                           class="w-full px-4 py-2.5 rounded-2xl border border-[#E2EAE7] bg-[#FBFDFD] text-slate-900 placeholder:text-slate-400 text-xs sm:text-sm font-medium focus:outline-none focus:border-brand-500 focus:bg-white focus:ring-2 focus:ring-brand-500/20 transition">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5 uppercase tracking-wider">Unit Measurement <span class="text-rose-500">*</span></label>
                        <input type="text" name="unit" x-model="addonModal.unit" required
                               placeholder="e.g. unit, set, pcs, meter"
                               class="w-full px-4 py-2.5 rounded-2xl border border-[#E2EAE7] bg-[#FBFDFD] text-slate-900 placeholder:text-slate-400 text-xs sm:text-sm font-medium focus:outline-none focus:border-brand-500 focus:bg-white focus:ring-2 focus:ring-brand-500/20 transition">
                        <p class="text-[10px] text-slate-400 mt-1">E.g., "pcs", "set", "unit", "meter".</p>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5 uppercase tracking-wider">Unit Price (IDR) <span class="text-rose-500">*</span></label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-xs font-bold text-slate-400 select-none pointer-events-none">Rp</span>
                            <input type="text"
                                   inputmode="numeric"
                                   :value="addonModal.display_price"
                                   @input="formatAddonPriceInput($event)"
                                   @blur="if (addonModal.unit_price) { addonModal.display_price = new Intl.NumberFormat('id-ID').format(addonModal.unit_price); $el.value = addonModal.display_price; }"
                                   required
                                   placeholder="15.000"
                                   class="w-full pl-11 pr-4 py-2.5 rounded-2xl border border-[#E2EAE7] bg-[#FBFDFD] text-slate-900 placeholder:text-slate-400 text-xs sm:text-sm font-semibold font-mono focus:outline-none focus:border-brand-500 focus:bg-white focus:ring-2 focus:ring-brand-500/20 transition">
                            <input type="hidden" name="unit_price" :value="addonModal.unit_price">
                        </div>
                        <p class="text-[10px] text-slate-400 mt-1">Cost per individual unit.</p>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-[#EFF4F2]">
                    <button type="button" @click="closeAddonModal()"
                            class="px-5 py-2.5 rounded-full border border-[#DCE5E2] text-slate-700 font-semibold text-xs hover:bg-slate-50 transition cursor-pointer">
                        Cancel
                    </button>
                    <button type="submit"
                            class="px-6 py-2.5 rounded-full bg-brand-600 hover:bg-brand-700 text-white font-bold text-xs transition shadow-pill active:scale-95 cursor-pointer">
                        <span x-text="addonModal.isEdit ? 'Save Changes' : 'Create Add-on'"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
