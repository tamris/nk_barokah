            <!-- ============================================================== -->
            <!-- STEP 4: REVIEW, DOWN PAYMENT & RECEIPT UPLOAD                 -->
            <!-- ============================================================== -->
            <div x-show="step === 4"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 translate-y-2"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 class="space-y-4 sm:space-y-5" x-cloak>

                <div class="space-y-1">
                    <span class="text-[10px] sm:text-[11px] font-bold uppercase tracking-[0.12em] text-brand-600">Langkah 4 dari 4 • Konfirmasi & Pembayaran</span>
                    <h1 class="text-xl sm:text-2xl font-extrabold tracking-tight text-slate-900 leading-tight">Periksa & Kunci Reservasi Anda</h1>
                    <p class="text-xs sm:text-sm text-slate-500 leading-relaxed">
                        Pastikan rincian pesanan sudah sesuai dan unggah bukti transfer pembayaran.
                    </p>
                </div>

                <!-- Quotation Breakdown Card -->
                <div class="bg-white rounded-2xl border border-slate-200/90 shadow-card overflow-hidden">
                    <!-- Card Header -->
                    <div class="px-4 sm:px-5 pt-4 sm:pt-5 pb-3.5 border-b border-slate-100 space-y-3">
                        <div class="flex items-center justify-between gap-2">
                            <h3 class="text-[11px] sm:text-xs font-bold text-slate-900 uppercase tracking-wider">Rincian Reservasi</h3>
                            <span class="text-[11px] font-medium text-slate-400">Ringkasan Pesanan</span>
                        </div>

                        <!-- Structured Event & Schedule Meta Box -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5 p-3 rounded-xl bg-[#F8FAFA] border border-slate-200/70">
                            <div class="flex items-start gap-2.5 min-w-0">
                                <div class="w-7 h-7 rounded-lg bg-brand-50 text-brand-700 flex items-center justify-center shrink-0 border border-brand-200/60 mt-0.5">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                </div>
                                <div class="min-w-0">
                                    <span class="text-[10px] font-semibold uppercase tracking-wider text-slate-400 block leading-none">Tanggal Acara</span>
                                    <p class="text-xs font-bold text-slate-900 mt-1 leading-snug" x-text="formattedSelectedDate"></p>
                                </div>
                            </div>

                            <div class="flex items-start gap-2.5 min-w-0 pt-2 sm:pt-0 border-t sm:border-t-0 sm:border-l border-slate-200/70 sm:pl-3">
                                <div class="w-7 h-7 rounded-lg bg-slate-100 text-slate-600 flex items-center justify-center shrink-0 border border-slate-200/80 mt-0.5">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                                </div>
                                <div class="min-w-0">
                                    <span class="text-[10px] font-semibold uppercase tracking-wider text-slate-400 block leading-none">Jenis Acara</span>
                                    <p class="text-xs font-bold text-slate-900 mt-1 leading-snug" x-text="eventTypeLabel"></p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Itemized Pricing Breakdown -->
                    <div class="px-4 sm:px-5 py-3.5 space-y-2.5 text-xs">
                        <!-- Core Package -->
                        <div class="flex items-baseline justify-between gap-3">
                            <div class="min-w-0">
                                <p class="font-bold text-slate-900 truncate" x-text="currentPackageName"></p>
                                <p class="text-[11px] text-slate-400">Paket Utama</p>
                            </div>
                            <span class="font-mono font-bold text-slate-900 whitespace-nowrap shrink-0 tabular-nums" x-text="formatRupiah(currentPackagePrice)"></span>
                        </div>

                        <!-- Selected Add-ons -->
                        <template x-if="activeAddons.length > 0">
                            <div class="pt-2 border-t border-slate-100 space-y-2">
                                <template x-for="ad in activeAddons" :key="ad.id">
                                    <div class="flex items-baseline justify-between gap-3 text-slate-600">
                                        <div class="flex items-center gap-1.5 min-w-0">
                                            <span class="truncate font-medium text-slate-700" x-text="ad.name"></span>
                                            <span class="px-1.5 py-0.5 rounded bg-slate-100 text-[10px] font-mono font-semibold text-slate-600 shrink-0" x-text="`${ad.selectedQty}x`"></span>
                                        </div>
                                        <span class="font-mono font-semibold text-slate-800 whitespace-nowrap shrink-0 tabular-nums" x-text="formatRupiah(ad.selectedQty * ad.unit_price)"></span>
                                    </div>
                                </template>
                            </div>
                        </template>

                        <!-- Extra Day Charge if > 2 days -->
                        <template x-if="extraDays > 0">
                            <div class="pt-2 border-t border-slate-100 flex items-baseline justify-between gap-3">
                                <div class="min-w-0">
                                    <p class="font-semibold text-amber-900">Tambahan Durasi</p>
                                    <p class="text-[11px] text-amber-700" x-text="`${extraDays} hari ekstra · +15%/hari`"></p>
                                </div>
                                <span class="font-mono font-bold text-amber-800 whitespace-nowrap shrink-0 tabular-nums" x-text="`+ ${formatRupiah(additionalDayCharge)}`"></span>
                            </div>
                        </template>

                        <!-- Biaya Transport Muatan Tenda -->
                        <template x-if="transportFee > 0">
                            <div class="pt-2 border-t border-slate-100 flex items-baseline justify-between gap-3">
                                <div class="min-w-0">
                                    <p class="font-semibold text-slate-800">Biaya Transport Muatan Tenda</p>
                                    <p class="text-[11px] text-slate-400" x-text="`Estimasi jarak tempuh ${deliveryDistanceKm} km`"></p>
                                </div>
                                <span class="font-mono font-bold text-slate-900 whitespace-nowrap shrink-0 tabular-nums" x-text="`+ ${formatRupiah(transportFee)}`"></span>
                            </div>
                        </template>

                        <!-- Optional Operational Notes (Survey & Tent Config) -->
                        <template x-if="surveyDate || tentColorTheme || customTentSize">
                            <div class="pt-2.5 border-t border-slate-100 space-y-2">
                                <template x-if="surveyDate">
                                    <div class="flex items-center justify-between gap-3 px-3 py-2 rounded-xl bg-[#F8FAFA] border border-slate-200/70">
                                        <div class="min-w-0">
                                            <p class="text-[11px] font-semibold text-slate-800">Request Survey Lokasi</p>
                                            <p class="text-[10.5px] text-slate-500 mt-0.5" x-text="`${surveyDate} · ${surveyTimeSlot === 'morning' ? 'Pagi (09:00 - 12:00)' : (surveyTimeSlot === 'afternoon' ? 'Siang - Sore (13:00 - 17:00)' : 'Fleksibel')}`"></p>
                                        </div>
                                        <span class="text-[10px] font-semibold text-brand-700 bg-brand-50 border border-brand-200/60 px-2 py-0.5 rounded-full whitespace-nowrap shrink-0">Gratis</span>
                                    </div>
                                </template>

                                <template x-if="tentColorTheme || customTentSize">
                                    <div class="flex items-center justify-between gap-3 px-3 py-2 rounded-xl bg-[#F8FAFA] border border-slate-200/70">
                                        <div class="min-w-0">
                                            <p class="text-[11px] font-semibold text-slate-800">Konfigurasi Tenda</p>
                                            <p class="text-[10.5px] text-slate-500 mt-0.5 truncate"
                                               x-text="[tentColorTheme ? 'Tema: ' + tentColorTheme : '', customTentSize ? 'Ukuran: ' + customTentSize : ''].filter(Boolean).join(' · ')"></p>
                                        </div>
                                        <span class="text-[10px] font-semibold text-slate-600 bg-white border border-slate-200 px-2 py-0.5 rounded-full whitespace-nowrap shrink-0">Tercatat</span>
                                    </div>
                                </template>
                            </div>
                        </template>
                    </div>

                    <!-- Total Tagihan Footer -->
                    <div class="px-4 sm:px-5 py-3.5 bg-[#F8FAFA] border-t border-dashed border-slate-200 flex items-center justify-between gap-3">
                        <div class="min-w-0">
                            <span class="text-xs font-bold text-slate-900 uppercase tracking-wider block">Total Tagihan</span>
                            <span class="text-[11px] text-slate-400">Termasuk seluruh layanan & fasilitas sewa</span>
                        </div>
                        <span class="text-base sm:text-lg font-mono font-extrabold text-brand-700 tracking-tight whitespace-nowrap shrink-0 tabular-nums" x-text="formatRupiah(grandTotal)"></span>
                    </div>
                </div>

                <!-- Payment Options -->
                <div class="bg-white rounded-2xl border border-slate-200/90 p-4 sm:p-5 shadow-card space-y-4">
                    <h3 class="text-xs font-bold text-slate-900 uppercase tracking-wider">Metode Pembayaran</h3>

                    <!-- Toggle DP vs Lunas -->
                    <div class="grid grid-cols-2 gap-2.5">
                        <label class="p-3 rounded-xl border text-center cursor-pointer transition select-none active:scale-[0.98]"
                               :class="paymentType === 'dp' 
                                   ? 'border-brand-600 bg-brand-50/50 text-brand-900 font-semibold ring-1 ring-brand-600/20' 
                                   : 'border-slate-200 hover:border-slate-300 text-slate-700'">
                            <input type="radio" name="payment_type" value="dp" class="sr-only" x-model="paymentType">
                            <span class="text-xs block font-bold">Uang Muka (DP)</span>
                            <span class="text-[10px] text-slate-500 mt-0.5 block">Kunci tanggal dengan DP minimal</span>
                        </label>

                        <label class="p-3 rounded-xl border text-center cursor-pointer transition select-none active:scale-[0.98]"
                               :class="paymentType === 'lunas' 
                                   ? 'border-brand-600 bg-brand-50/50 text-brand-900 font-semibold ring-1 ring-brand-600/20' 
                                   : 'border-slate-200 hover:border-slate-300 text-slate-700'">
                            <input type="radio" name="payment_type" value="lunas" class="sr-only" x-model="paymentType">
                            <span class="text-xs block font-bold">Pelunasan (100%)</span>
                            <span class="text-[10px] text-slate-500 mt-0.5 block">Bayar lunas seluruh tagihan</span>
                        </label>
                    </div>

                    <!-- DP Amount Input -->
                    <div x-show="paymentType === 'dp'" class="p-3.5 rounded-xl bg-slate-50 border border-slate-200 space-y-2.5" x-cloak>
                        <div class="flex items-center justify-between text-xs">
                            <label class="font-semibold text-slate-700">Nominal Transfer DP</label>
                            <span class="text-slate-500 text-[11px]">Min: <span class="font-mono font-bold text-slate-800" x-text="formatRupiah(effectiveMinDp)"></span></span>
                        </div>
                        <div class="relative flex items-center">
                            <span class="absolute left-4 text-xs font-bold text-slate-400 select-none">Rp</span>
                            <input id="nominalDpInput" type="text" inputmode="numeric"
                                   :value="nominalDpDisplay"
                                   @input="handleNominalDpInput($event)"
                                   placeholder="1.000.000"
                                   class="w-full pl-12 pr-4 py-2.5 rounded-xl border border-slate-200 bg-white text-base sm:text-sm font-mono font-bold text-slate-900 focus:outline-none focus:border-brand-600 focus:ring-2 focus:ring-brand-600/15 transition">
                            <input type="hidden" name="nominal_dp" :value="nominalDp">
                        </div>

                        <!-- Quick DP Preset Pills -->
                        <div class="flex items-center gap-2 pt-0.5">
                            <button type="button" @click="setNominalDp(effectiveMinDp)"
                                     class="px-2.5 py-1 rounded-lg text-[11px] font-semibold transition border border-slate-200 hover:bg-white text-slate-600 active:scale-95 cursor-pointer"
                                     :class="nominalDp === effectiveMinDp ? 'bg-brand-50 border-brand-300 text-brand-800 font-bold ring-1 ring-brand-500/20' : 'bg-white/60'">
                                <span x-text="`Min DP (${formatRupiah(effectiveMinDp)})`">Min DP (Rp 1.000.000)</span>
                            </button>
                            <button type="button" @click="setNominalDp(Math.round(grandTotal * 0.5))"
                                     class="px-2.5 py-1 rounded-lg text-[11px] font-semibold transition border border-slate-200 hover:bg-white text-slate-600 active:scale-95 cursor-pointer"
                                     :class="nominalDp === Math.round(grandTotal * 0.5) ? 'bg-brand-50 border-brand-300 text-brand-800 font-bold ring-1 ring-brand-500/20' : 'bg-white/60'">
                                50% DP
                            </button>
                        </div>

                        <div class="flex justify-between items-center text-xs text-slate-500 pt-1.5 border-t border-slate-200/60">
                            <span>Sisa tagihan yang harus dibayar:</span>
                            <strong class="font-mono font-bold text-slate-900" x-text="formatRupiah(remainingBalance)"></strong>
                        </div>
                    </div>

                    <!-- Step 4 Inline Error Alert -->
                    <div x-show="formErrors.step4" x-cloak x-transition
                         class="p-3.5 rounded-xl bg-rose-50 border border-rose-200 text-xs text-rose-700 flex items-center gap-2.5 shadow-subtle">
                        <svg class="w-4 h-4 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        <span class="font-medium" x-text="formErrors.step4"></span>
                    </div>

                    <!-- Destination Bank Section (Authentic ATM / Debit Card UI) -->
                    <div class="space-y-3">
                        <div class="flex flex-wrap items-center justify-between gap-2">
                            <span class="text-xs font-bold text-slate-700 uppercase tracking-wider">
                                Rekening Resmi Transfer
                            </span>
                            <!-- Bank Switcher Tabs -->
                            <div class="inline-flex p-1 bg-slate-100 rounded-xl border border-slate-200 shadow-subtle shrink-0">
                                <button type="button" @click="activeBank = 'bca'"
                                        class="px-3 py-1.5 rounded-lg text-xs font-bold transition flex items-center gap-1.5 whitespace-nowrap cursor-pointer active:scale-95"
                                        :class="activeBank === 'bca' 
                                            ? 'bg-blue-700 text-white shadow-xs' 
                                            : 'text-slate-600 hover:text-slate-900'">
                                    <span class="w-2 h-2 rounded-full shrink-0" :class="activeBank === 'bca' ? 'bg-white' : 'bg-blue-600'"></span>
                                    <span>Bank BCA</span>
                                </button>
                                <button type="button" @click="activeBank = 'bri'"
                                        class="px-3 py-1.5 rounded-lg text-xs font-bold transition flex items-center gap-1.5 whitespace-nowrap cursor-pointer active:scale-95"
                                        :class="activeBank === 'bri' 
                                            ? 'bg-[#00529C] text-white shadow-xs' 
                                            : 'text-slate-600 hover:text-slate-900'">
                                    <span class="w-2 h-2 rounded-full shrink-0" :class="activeBank === 'bri' ? 'bg-white' : 'bg-sky-600'"></span>
                                    <span>Bank BRI</span>
                                </button>
                            </div>
                        </div>

                        <!-- Realistic ATM / Debit Bank Card -->
                        <div class="w-full rounded-2xl sm:rounded-3xl p-5 sm:p-6 text-white relative overflow-hidden shadow-xl transition-all duration-300 min-h-[210px] flex flex-col justify-between select-none"
                             :class="activeBank === 'bca' 
                                 ? 'bg-gradient-to-br from-[#003B73] via-[#00519E] to-[#001D38] border border-blue-400/30' 
                                 : 'bg-gradient-to-br from-[#00529C] via-[#00396B] to-[#01182D] border border-sky-400/30'">
                            
                            <!-- Ambient Decorative Elements (Bank Holographic Waves) -->
                            <div class="absolute -right-10 -bottom-10 w-48 h-48 rounded-full bg-white/5 blur-2xl pointer-events-none"></div>
                            <div class="absolute -left-10 -top-10 w-40 h-40 rounded-full bg-cyan-400/10 blur-xl pointer-events-none"></div>
                            <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_top_right,_var(--tw-gradient-stops))] from-white/10 via-transparent to-transparent pointer-events-none"></div>

                            <!-- Card Top Bar: Chip, Contactless & Bank Brand Logo -->
                            <div class="flex items-center justify-between relative z-10">
                                <div class="flex items-center gap-3">
                                    <!-- Metallic Gold EMV Chip -->
                                    <div class="w-11 h-8 rounded-md bg-gradient-to-br from-amber-200 via-amber-300 to-amber-500 shadow border border-amber-400/80 p-0.5 relative overflow-hidden shrink-0">
                                        <div class="w-full h-full border border-amber-700/30 rounded-[3px] grid grid-cols-2 gap-0.5 p-0.5 opacity-80">
                                            <div class="border-r border-b border-amber-700/40 rounded-tl"></div>
                                            <div class="border-b border-amber-700/40 rounded-tr"></div>
                                            <div class="border-r border-amber-700/40 rounded-bl"></div>
                                            <div class="rounded-br"></div>
                                        </div>
                                    </div>
                                    <!-- Contactless NFC Wave Icon -->
                                    <svg class="w-5 h-5 text-white/70 rotate-90" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M8.111 16.404a5.5 5.5 0 017.778 0M12 20h.01m-7.08-7.071c3.904-3.905 10.236-3.905 14.141 0M1.394 9.393c5.857-5.857 15.355-5.857 21.213 0"/>
                                    </svg>
                                </div>

                                <!-- Bank Brand Logo Badge -->
                                <div class="flex items-center gap-2 bg-white/10 backdrop-blur-md px-3.5 py-1.5 rounded-xl border border-white/20 shadow-xs">
                                    <span class="font-extrabold tracking-tight text-base text-white"
                                          x-text="activeBank === 'bca' ? 'BCA' : 'BRI'"></span>
                                    <span class="text-[9px] font-mono tracking-widest text-white/80 uppercase px-1.5 py-0.5 rounded bg-white/15"
                                          x-text="activeBank === 'bca' ? 'PASPOR' : 'BRITAMA'"></span>
                                </div>
                            </div>

                            <!-- Card Middle: Embossed Account Number & Copy Button -->
                            <div class="my-4 relative z-10 space-y-1">
                                <div class="flex items-center justify-between">
                                    <span class="text-[9px] uppercase font-bold tracking-widest text-white/60">Nomor Rekening Resmi</span>
                                    <span class="text-[10px] font-medium text-white/70" x-text="activeBank === 'bca' ? 'BCA Virtual / Kantor Cabang' : 'BRI Kantor Unit / Cabang'"></span>
                                </div>
                                <div class="flex items-center justify-between gap-3 pt-0.5">
                                    <p class="font-mono text-xl sm:text-2xl md:text-[26px] font-extrabold tracking-widest text-white drop-shadow-md select-all"
                                       x-text="banks[activeBank].formatted"></p>
                                    <button type="button" @click="copyBankAccount()"
                                            class="px-3.5 py-1.5 rounded-xl bg-white/20 hover:bg-white/30 active:scale-95 text-xs text-white transition font-bold flex items-center gap-1.5 border border-white/30 shadow-sm cursor-pointer backdrop-blur-md shrink-0">
                                        <svg x-show="!copiedBank" class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                                        <span x-text="copiedBank ? 'Tersalin!' : 'Salin'"></span>
                                    </button>
                                </div>
                            </div>

                            <!-- Card Bottom: Cardholder Name & GPN / Debit Badge -->
                            <div class="pt-3 border-t border-white/15 flex items-center justify-between text-xs relative z-10">
                                <div>
                                    <span class="text-[8px] uppercase font-bold tracking-wider text-white/50 block">Atas Nama Pemilik Rekening</span>
                                    <p class="font-mono font-bold tracking-wider text-white text-xs sm:text-sm uppercase" x-text="banks[activeBank].holder"></p>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="px-2 py-0.5 rounded bg-white/15 font-mono text-[9px] font-bold tracking-wider text-white/90 uppercase border border-white/20">
                                        DEBIT
                                    </span>
                                    <span class="px-2 py-0.5 rounded bg-white text-slate-900 font-extrabold text-[9px] tracking-wider uppercase shadow-xs">
                                        GPN
                                    </span>
                                </div>
                            </div>
                        </div>

                        <p class="text-[11px] text-slate-500 text-center leading-relaxed">
                            Silakan lakukan transfer ke rekening <strong class="text-slate-800" x-text="banks[activeBank].name"></strong> di atas a.n. <strong class="text-slate-800" x-text="banks[activeBank].holder"></strong>, lalu unggah bukti transfer di bawah.
                        </p>
                    </div>

                    <!-- Payment Proof Upload Dropzone -->
                    <div class="space-y-1.5">
                        <label class="block text-xs font-semibold text-slate-700 tracking-tight">
                            Unggah Bukti Transfer <span class="text-rose-500">*</span>
                        </label>
                        
                        <div class="border-2 border-dashed rounded-2xl p-5 text-center transition cursor-pointer relative group"
                             :class="formErrors.payment_proof 
                                 ? 'border-rose-400 bg-rose-50/40' 
                                 : 'border-slate-200 hover:border-brand-600 bg-slate-50/70 active:bg-slate-100'">
                            <input type="file" name="payment_proof" required accept="image/*,application/pdf"
                                   @change="handleFileUpload($event)"
                                   class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                            
                            <div class="space-y-1.5">
                                <div class="w-10 h-10 rounded-full bg-slate-100 group-hover:bg-brand-50 text-slate-500 group-hover:text-brand-600 mx-auto flex items-center justify-center transition">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                                </div>
                                <p class="text-xs font-semibold text-slate-800">
                                    <span class="text-brand-600 underline">Ketuk untuk foto atau unggah berkas</span>
                                </p>
                                <p class="text-[11px] text-slate-400">JPG, PNG, atau PDF maksimal 3MB</p>
                            </div>

                            <template x-if="previewUrl">
                                <div class="mt-3 pt-3 border-t border-slate-200">
                                    <img :src="previewUrl" alt="Bukti transfer" class="max-h-28 mx-auto rounded-lg border border-slate-200 object-cover shadow-sm">
                                </div>
                            </template>
                            <template x-if="!previewUrl && fileName">
                                <p class="text-xs font-mono font-semibold text-slate-800 mt-2" x-text="fileName"></p>
                            </template>
                        </div>
                        <p x-show="formErrors.payment_proof" x-cloak class="text-xs font-semibold text-rose-600 flex items-center gap-1.5 pt-0.5">
                            <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                            <span x-text="formErrors.payment_proof"></span>
                        </p>
                    </div>

                </div>

                <!-- Unified Booking Rules & Consent Card -->
                <div class="bg-white rounded-2xl border border-slate-200/90 shadow-card divide-y divide-slate-100 overflow-hidden" x-data="{ rulesOpen: false }">
                    <!-- Rules Accordion Header -->
                    <button type="button" @click="rulesOpen = !rulesOpen"
                            class="w-full p-4 sm:p-5 flex items-center justify-between text-left cursor-pointer select-none hover:bg-slate-50/60 transition">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-xl bg-amber-50 text-amber-700 flex items-center justify-center font-bold text-xs shrink-0 border border-amber-200/60">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            </div>
                            <div>
                                <h4 class="text-xs sm:text-sm font-bold text-slate-900 tracking-tight">Aturan & Ketentuan Reservasi Resmi</h4>
                                <p class="text-[11px] text-slate-400">Ringkasan S&K resmi NK Barokah 2026</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-1.5 text-xs font-semibold text-brand-600">
                            <span class="text-[11px] hidden sm:inline" x-text="rulesOpen ? 'Tutup' : 'Lihat'"></span>
                            <svg class="w-4 h-4 text-slate-400 transition-transform duration-200" :class="{ 'rotate-180': rulesOpen }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/></svg>
                        </div>
                    </button>

                    <!-- Rules Content (Expandable) -->
                    <div x-show="rulesOpen" x-cloak x-transition class="p-4 sm:p-5 bg-slate-50/50 text-xs text-slate-600 space-y-2">
                        <ul class="list-disc pl-4 space-y-1.5 leading-relaxed text-[11px] sm:text-xs text-slate-600">
                            <li><strong>Keep Tanggal:</strong> Wajib DP minimal Rp 1.000.000 via transfer atau langsung ke galeri (sertakan bukti transfer).</li>
                            <li><strong>Durasi Acara:</strong> Pemakaian paket tenda berlaku maksimal <strong>2 hari acara</strong> (Loading tenda H-2, loading dekor H-1 sore/malam, bongkar H+1). Acara lebih dari 2 hari dikenakan <strong>charge 15% per hari</strong>.</li>
                            <li><strong>Survey Lokasi:</strong> Survey lokasi dilakukan setelah pembayaran DP. Biaya transport diinfokan jika ada (belum termasuk transport vendor).</li>
                            <li><strong>Penataan:</strong> Penataan meja & kursi dilakukan oleh pihak klien agar memahami tata letak yang diinginkan. Penataan alat prasmanan & karpet dilakukan oleh crew kami.</li>
                            <li><strong>Kebijakan Pembatalan:</strong> Pembatalan sepihak DP hangus. Reschedule dapat dilakukan apabila tanggal pengganti masih tersedia (available).</li>
                            <li><strong>Kelistrikan:</strong> Disediakan & ditangani crew untuk seputar tenda; diluar area tenda dikenakan Rp 100.000/titik. Saluran listrik dari klien.</li>
                            <li><strong>Alat Prasmanan:</strong> Pengembalian alat prasmanan dalam kondisi bersih & dicuci.</li>
                        </ul>
                    </div>

                    <!-- Consent Checkbox Row -->
                    <label id="termsConsentCheckbox"
                           class="flex items-center gap-3 p-4 sm:p-4.5 cursor-pointer transition select-none group"
                           :class="agreedToTerms ? 'bg-brand-50/40 text-brand-900' : 'bg-white hover:bg-slate-50/60 text-slate-700'">
                        <input type="checkbox" x-model="agreedToTerms" required
                               class="w-4 h-4 rounded text-brand-600 focus:ring-brand-500 border-slate-300 cursor-pointer shrink-0">
                        <span class="text-xs leading-snug">
                            Saya telah membaca dan menyetujui seluruh <strong class="text-slate-900 group-hover:text-brand-700 transition">Aturan & Ketentuan Reservasi Resmi</strong> di atas.
                        </span>
                    </label>
                </div>

                <!-- DESKTOP & TABLET INLINE ACTION FOOTER (CARD CONTAINER) -->
                <div x-show="step === 4" class="hidden md:flex items-center justify-between gap-4 bg-white rounded-2xl border border-slate-200/90 p-4 sm:p-5 shadow-card">
                    <button type="button" @click="goToStep(3)"
                            class="px-4 py-2.5 rounded-xl border border-slate-200 hover:bg-slate-50 text-slate-700 font-semibold text-xs transition flex items-center gap-1.5 cursor-pointer active:scale-95">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
                        <span>Kembali</span>
                    </button>
                    <button type="button" @click="submitBookingForm()" :disabled="isDateBooked || !agreedToTerms"
                            class="px-7 py-3 rounded-xl bg-brand-600 hover:bg-brand-700 disabled:bg-slate-300 active:scale-95 text-white font-bold text-xs tracking-wide transition flex items-center gap-2 cursor-pointer shadow-pill">
                        <span>Konfirmasi Reservasi</span>
                    </button>
                </div>

            </div>
