            <!-- ============================================================== -->
            <!-- STEP 2: WEDDING PACKAGE & ADD-ONS                              -->
            <!-- ============================================================== -->
            <div x-show="step === 2"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 translate-y-2"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 class="space-y-4 sm:space-y-5" x-cloak>

                <!-- Step Title -->
                <div class="space-y-1">
                    <span class="text-[10px] sm:text-[11px] font-bold uppercase tracking-[0.12em] text-brand-600" x-text="step2Badge"></span>
                    <h1 class="text-xl sm:text-2xl font-extrabold tracking-tight text-slate-900 leading-tight" x-text="step2Heading"></h1>
                    <p class="text-xs sm:text-sm text-slate-500 leading-relaxed" x-text="step2Subtitle"></p>
                </div>

                <!-- Step 2 Inline Error Alert -->
                <div x-show="formErrors.step2" x-cloak x-transition
                     class="p-3.5 rounded-xl bg-rose-50 border border-rose-200 text-xs text-rose-700 flex items-center gap-2.5 shadow-subtle">
                    <svg class="w-4 h-4 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    <span class="font-medium" x-text="formErrors.step2"></span>
                </div>

                <!-- Hidden input package_id specifically when eventType is sewa_alat -->
                <input type="hidden" name="package_id" :value="selectedPackageId" x-show="eventType === 'sewa_alat'" :disabled="eventType !== 'sewa_alat'">

                <!-- Package Cards (Hanya Tampil Jika BUKAN Sewa Alat Saja) -->
                <div x-show="eventType !== 'sewa_alat'" class="space-y-2.5 sm:space-y-3">
                    <template x-for="pkg in displayPackages" :key="pkg.id">
                        <label @click="onPackageSelect(pkg.id)"
                               class="block p-3.5 sm:p-4 rounded-2xl border transition select-none cursor-pointer relative active:scale-[0.99]"
                               :class="selectedPackageId == pkg.id 
                                   ? 'bg-white border-brand-600 ring-2 ring-brand-600/10 shadow-card' 
                                   : 'bg-white border-slate-200/90 hover:border-slate-300 shadow-2xs'">
                            
                            <input type="radio" name="package_id" :value="pkg.id" x-model="selectedPackageId" @change="onPackageSelect(pkg.id)" :disabled="eventType === 'sewa_alat'" class="sr-only">

                            <!-- Top Row: Radio, Title + Badges, and Price -->
                            <div class="flex items-center justify-between gap-2.5">
                                <div class="flex items-center gap-2.5 min-w-0">
                                    <div class="w-4 h-4 sm:w-5 sm:h-5 rounded-full border-2 flex items-center justify-center shrink-0 transition"
                                         :class="selectedPackageId == pkg.id ? 'border-brand-600 bg-brand-600' : 'border-slate-300 bg-white'">
                                        <span class="w-1.5 h-1.5 rounded-full bg-white" x-show="selectedPackageId == pkg.id"></span>
                                    </div>
                                    <div class="flex items-center gap-1.5 flex-wrap min-w-0">
                                        <h3 class="font-bold text-[13px] sm:text-sm text-slate-900 tracking-tight" x-text="pkg.name"></h3>
                                        <template x-if="pkg.slug === 'paket-1' || pkg.slug === 'tenda-decor-6m'">
                                            <span class="px-1.5 py-0.2 rounded-full bg-brand-50 text-brand-700 text-[9px] font-bold tracking-wide uppercase border border-brand-200/60 shrink-0">
                                                Favorit
                                            </span>
                                        </template>
                                    </div>
                                </div>

                                <div class="text-right shrink-0">
                                    <span class="text-xs sm:text-sm font-extrabold font-mono text-slate-900 tracking-tight"
                                          x-text="formatRupiah(pkg.base_price)"></span>
                                </div>
                            </div>

                            <!-- Bottom Row: Description text justified, compact, full-width -->
                            <div class="mt-2 pt-2 border-t border-slate-100">
                                <p class="text-xs text-slate-600 leading-snug text-justify" x-text="pkg.description"></p>
                            </div>
                        </label>
                    </template>
                </div>

                <!-- Add-ons Selection (Katalog Alat Sewa Langsung Terbuka untuk Sewa Alat Saja / Accordion untuk Paket Tenda) -->
                <div id="addonsSection" x-show="addons.length > 0" 
                     class="bg-white rounded-2xl border border-slate-200/90 shadow-card overflow-hidden transition-all duration-300">
                    
                    <!-- Mode 1: Static Header untuk Sewa Alat Saja (Tanpa tombol toggle/collapse) -->
                    <template x-if="eventType === 'sewa_alat'">
                        <div class="p-3.5 sm:p-4 border-b border-slate-100 bg-[#FAFDFC] flex items-center justify-between gap-3">
                            <div class="flex items-center gap-2.5 min-w-0 flex-1">
                                <div class="w-8 h-8 rounded-xl bg-brand-50 text-brand-700 border border-brand-200/60 flex items-center justify-center shrink-0">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012 2v2M7 7h10"/></svg>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <h4 class="text-xs sm:text-sm font-bold text-slate-900 tracking-tight">Katalog Perlengkapan Sewa</h4>
                                        <template x-if="activeAddons.length > 0">
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 shrink-0">
                                                <span x-text="`${activeAddons.length} item dipilih`"></span>
                                            </span>
                                        </template>
                                        <template x-if="activeAddons.length === 0">
                                            <!-- <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-800 border border-amber-200 shrink-0">
                                                Wajib min. 1 item
                                            </span> -->
                                        </template>
                                    </div>
                                    <p class="text-[11px] text-slate-500 mt-0.5 truncate"
                                       x-text="activeAddons.length > 0 ? `${activeAddons.length} jenis alat telah dipilih. Anda dapat menambah atau mengurangi unit di bawah.` : 'Tentukan jumlah unit alat pesta yang ingin disewa di bawah ini (min. 1 item).'"></p>
                                </div>
                            </div>
                        </div>
                    </template>

                    <!-- Mode 2: Accordion Header untuk Acara dengan Paket Tenda -->
                    <template x-if="eventType !== 'sewa_alat'">
                        <button type="button" 
                                @click="toggleAddonsAccordion()"
                                class="w-full p-3.5 sm:p-4 flex items-center justify-between text-left cursor-pointer select-none hover:bg-slate-50/70 transition"
                                :class="isAddonsOpen ? 'border-b border-slate-100 bg-[#FAFDFC]' : ''">
                            <div class="flex items-center gap-2.5 min-w-0 flex-1">
                                <div class="w-8 h-8 rounded-xl flex items-center justify-center shrink-0 border transition"
                                     :class="activeAddons.length > 0 
                                         ? 'bg-brand-50 text-brand-700 border-brand-200/60' 
                                         : (isCustomPackage ? 'bg-amber-50 text-amber-700 border-amber-200/60' : 'bg-slate-100 text-slate-600 border-slate-200')">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <h4 class="text-xs sm:text-sm font-bold text-slate-900 tracking-tight"
                                            x-text="isCustomPackage ? 'Pilih Perlengkapan Sewa' : 'Perlengkapan Tambahan'"></h4>
                                        
                                        <template x-if="activeAddons.length > 0">
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 shrink-0">
                                                <span x-text="`${activeAddons.length} item dipilih`"></span>
                                            </span>
                                        </template>
                                        <template x-if="activeAddons.length === 0 && isCustomPackage">
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-800 border border-amber-200 shrink-0">
                                                Wajib diisi
                                            </span>
                                        </template>
                                        <template x-if="activeAddons.length === 0 && !isCustomPackage">
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-medium bg-slate-100 text-slate-500 border border-slate-200 shrink-0">
                                                Opsional
                                            </span>
                                        </template>
                                    </div>
                                    <p class="text-[11px] text-slate-500 mt-0.5 truncate"
                                       x-text="isCustomPackage 
                                           ? (activeAddons.length > 0 ? 'Item sewa telah dipilih, ketuk untuk ubah' : 'Pilih jenis dan jumlah unit alat pesta di bawah') 
                                           : (activeAddons.length > 0 ? `${activeAddons.length} item tambahan dipilih, ketuk untuk ubah` : 'Kursi, meja, blower, panggung, sound, dll (ketuk untuk buka)')"></p>
                                </div>
                            </div>

                            <div class="flex items-center shrink-0 pl-2">
                                <div class="w-6 h-6 rounded-lg bg-slate-100 flex items-center justify-center text-slate-500 transition"
                                     :class="isAddonsOpen ? 'bg-brand-50 text-brand-700' : ''">
                                    <svg class="w-3.5 h-3.5 transition-transform duration-200" :class="{ 'rotate-180': isAddonsOpen }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/>
                                    </svg>
                                </div>
                            </div>
                        </button>
                    </template>

                    <!-- Expandable Add-ons Content (Always Open for sewa_alat) -->
                    <div x-show="eventType === 'sewa_alat' || isAddonsOpen" x-cloak 
                         class="p-3.5 sm:p-5 space-y-3"
                         :class="eventType !== 'sewa_alat' ? 'border-t border-slate-100' : ''">

                        <!-- Category Filter Tabs for 20 Add-ons -->
                        <div class="flex items-center gap-1.5 overflow-x-auto pb-1 text-xs no-scrollbar">
                            <button type="button" @click="activeAddonCategory = 'all'"
                                    class="px-2.5 py-1 rounded-lg text-[11px] font-bold transition whitespace-nowrap cursor-pointer active:scale-95"
                                    :class="activeAddonCategory === 'all' ? 'bg-brand-600 text-white shadow-xs' : 'bg-slate-100 hover:bg-slate-200 text-slate-600'">
                                Semua
                            </button>
                            <button type="button" @click="activeAddonCategory = 'kursi_meja'"
                                    class="px-2.5 py-1 rounded-lg text-[11px] font-bold transition whitespace-nowrap cursor-pointer active:scale-95"
                                    :class="activeAddonCategory === 'kursi_meja' ? 'bg-brand-600 text-white shadow-xs' : 'bg-slate-100 hover:bg-slate-200 text-slate-600'">
                                Kursi & Meja
                            </button>
                            <button type="button" @click="activeAddonCategory = 'kipas'"
                                    class="px-2.5 py-1 rounded-lg text-[11px] font-bold transition whitespace-nowrap cursor-pointer active:scale-95"
                                    :class="activeAddonCategory === 'kipas' ? 'bg-brand-600 text-white shadow-xs' : 'bg-slate-100 hover:bg-slate-200 text-slate-600'">
                                Kipas & Blower
                            </button>
                            <button type="button" @click="activeAddonCategory = 'panggung'"
                                    class="px-2.5 py-1 rounded-lg text-[11px] font-bold transition whitespace-nowrap cursor-pointer active:scale-95"
                                    :class="activeAddonCategory === 'panggung' ? 'bg-brand-600 text-white shadow-xs' : 'bg-slate-100 hover:bg-slate-200 text-slate-600'">
                                Panggung & Lantai
                            </button>
                            <button type="button" @click="activeAddonCategory = 'sound'"
                                    class="px-2.5 py-1 rounded-lg text-[11px] font-bold transition whitespace-nowrap cursor-pointer active:scale-95"
                                    :class="activeAddonCategory === 'sound' ? 'bg-brand-600 text-white shadow-xs' : 'bg-slate-100 hover:bg-slate-200 text-slate-600'">
                                Sound & Genset
                            </button>
                            <button type="button" @click="activeAddonCategory = 'prasmanan'"
                                    class="px-2.5 py-1 rounded-lg text-[11px] font-bold transition whitespace-nowrap cursor-pointer active:scale-95"
                                    :class="activeAddonCategory === 'prasmanan' ? 'bg-brand-600 text-white shadow-xs' : 'bg-slate-100 hover:bg-slate-200 text-slate-600'">
                                Prasmanan
                            </button>
                            <button type="button" @click="activeAddonCategory = 'tenda'"
                                    class="px-2.5 py-1 rounded-lg text-[11px] font-bold transition whitespace-nowrap cursor-pointer active:scale-95"
                                    :class="activeAddonCategory === 'tenda' ? 'bg-brand-600 text-white shadow-xs' : 'bg-slate-100 hover:bg-slate-200 text-slate-600'">
                                Tenda & Lampu
                            </button>
                        </div>

                        <!-- 2-Column Responsive Compact Grid for Add-ons -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                            <template x-for="(addon, idx) in addons" :key="addon.id">
                                <div x-show="activeAddonCategory === 'all' || getAddonCategory(addon.name) === activeAddonCategory"
                                     class="py-2 px-3 rounded-xl border transition flex items-center justify-between gap-2.5 text-xs select-none"
                                     :class="addon.selectedQty > 0 
                                         ? 'bg-brand-50/50 border-brand-300 ring-1 ring-brand-400/20 shadow-2xs' 
                                         : 'bg-[#F9FBFA] hover:bg-slate-100/70 border-slate-200/70'">
                                    
                                    <div class="min-w-0 flex-1">
                                        <p class="font-bold text-slate-800 tracking-tight truncate text-xs" x-text="addon.name"></p>
                                        <div class="flex items-center gap-1.5 text-[11px] mt-0.5">
                                            <span class="text-slate-500 font-mono font-medium"
                                                  x-text="`${formatRupiah(addon.unit_price)} / ${addon.unit}`"></span>
                                        </div>
                                    </div>

                                    <div class="flex items-center gap-1 shrink-0 bg-white rounded-lg border border-slate-200 p-0.5 shadow-2xs">
                                        <input type="hidden" :name="`addons[${idx}][id]`" :value="addon.id">
                                        
                                        <button type="button"
                                                @click="addon.selectedQty = Math.max(0, (parseInt(addon.selectedQty) || 0) - 1)"
                                                :disabled="!addon.selectedQty || addon.selectedQty <= 0"
                                                class="w-6 h-6 rounded flex items-center justify-center transition"
                                                :class="!addon.selectedQty || addon.selectedQty <= 0 
                                                    ? 'text-slate-300 cursor-not-allowed bg-transparent' 
                                                    : 'text-slate-700 hover:bg-slate-100 active:scale-90 cursor-pointer bg-slate-50'">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M20 12H4"/></svg>
                                        </button>

                                        <input type="number" :name="`addons[${idx}][qty]`" min="0" max="1000"
                                               x-model.number="addon.selectedQty"
                                               class="w-8 text-center bg-transparent font-mono font-extrabold text-xs text-slate-900 focus:outline-none p-0" placeholder="0">

                                        <button type="button"
                                                @click="addon.selectedQty = (parseInt(addon.selectedQty) || 0) + 1"
                                                class="w-6 h-6 rounded flex items-center justify-center transition active:scale-90 cursor-pointer text-slate-700 hover:bg-brand-50 hover:text-brand-700 bg-slate-50">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                                        </button>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>

                <!-- DESKTOP & TABLET INLINE ACTION FOOTER (CARD CONTAINER) -->
                <div x-show="step === 2" class="hidden md:flex items-center justify-between gap-4 bg-white rounded-2xl border border-slate-200/90 p-4 sm:p-5 shadow-card">
                    <button type="button" @click="goToStep(1)"
                            class="px-4 py-2.5 rounded-xl border border-slate-200 hover:bg-slate-50 text-slate-700 font-semibold text-xs transition flex items-center gap-1.5 cursor-pointer active:scale-95">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
                        <span>Kembali</span>
                    </button>
                    <div class="flex items-center gap-4 sm:gap-5">
                        <div class="text-right">
                            <span class="text-[10px] uppercase font-bold text-slate-400 tracking-wider block">Estimasi Total</span>
                            <span class="text-base sm:text-lg font-extrabold font-mono text-brand-700" x-text="formatRupiah(grandTotal)"></span>
                        </div>
                        <button type="button" @click="goToStep(3)"
                                class="px-6 py-2.5 sm:py-3 rounded-xl bg-brand-600 hover:bg-brand-700 active:scale-95 text-white font-bold text-xs tracking-wide transition flex items-center gap-2 cursor-pointer shadow-pill">
                            <span>Lanjut ke Data Pemesan</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                        </button>
                    </div>
                </div>

            </div>
