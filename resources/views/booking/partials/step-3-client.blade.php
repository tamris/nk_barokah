            <!-- ============================================================== -->
            <!-- STEP 3: COUPLE INFORMATION                                     -->
            <!-- ============================================================== -->
            <div x-show="step === 3"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 translate-y-2"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 class="space-y-4 sm:space-y-5" x-cloak>

                <div class="space-y-1">
                    <span class="text-[10px] sm:text-[11px] font-bold uppercase tracking-[0.12em] text-brand-600" x-text="step3Badge"></span>
                    <h1 class="text-xl sm:text-2xl font-extrabold tracking-tight text-slate-900 leading-tight" x-text="step3Heading"></h1>
                    <p class="text-xs sm:text-sm text-slate-500 leading-relaxed" x-text="step3Subtitle"></p>
                </div>

                <!-- Input Fields Card -->
                <div class="bg-white rounded-2xl border border-slate-200/90 p-4 sm:p-6 shadow-card space-y-4">
                    
                    <!-- Step 3 Inline Error Alert -->
                    <div x-show="formErrors.step3" x-cloak x-transition
                         class="p-3.5 rounded-xl bg-rose-50 border border-rose-200 text-xs text-rose-700 flex items-center gap-2.5 shadow-subtle">
                        <svg class="w-4 h-4 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        <span class="font-medium" x-text="formErrors.step3"></span>
                    </div>

                    <div class="space-y-1.5">
                        <label class="block text-xs font-semibold text-slate-700 tracking-tight">
                            <span x-text="customerNameLabel"></span> <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" name="customer_name" required x-model="customerName"
                               @input="customerName = $event.target.value.toUpperCase(); formErrors.customer_name = null; formErrors.step3 = null"
                               autocapitalize="characters" autocomplete="name"
                               :placeholder="customerNamePlaceholder"
                               class="w-full px-4 py-3 rounded-xl border text-base sm:text-sm uppercase placeholder:normal-case placeholder:text-slate-400 focus:outline-none transition"
                               :class="formErrors.customer_name 
                                   ? 'border-rose-400 ring-2 ring-rose-100 bg-rose-50/20 text-slate-900' 
                                   : 'border-slate-200 focus:border-brand-600 focus:ring-2 focus:ring-brand-600/15'">
                        <p x-show="formErrors.customer_name" x-cloak class="text-xs font-semibold text-rose-600 flex items-center gap-1.5 pt-0.5">
                            <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                            <span x-text="formErrors.customer_name"></span>
                        </p>
                        <p x-show="!formErrors.customer_name" class="text-[11px] text-slate-400" x-text="customerNameHint"></p>
                    </div>

                    <!-- Optional Child Name for Khitanan -->
                    <div x-show="eventType === 'khitanan'" x-cloak class="space-y-1.5">
                        <label class="block text-xs font-semibold text-slate-700 tracking-tight">
                            Nama Anak yang Dikhitan <span class="text-slate-400 font-normal">(Opsional)</span>
                        </label>
                        <input type="text" name="child_name" x-model="childName"
                               placeholder="Cth: Muhammad Fikri Pratama"
                               class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-base sm:text-sm placeholder:text-slate-400 focus:outline-none focus:border-brand-600 focus:ring-2 focus:ring-brand-600/15 transition">
                    </div>

                    <div class="space-y-1.5">
                        <label class="block text-xs font-semibold text-slate-700 tracking-tight">
                            Nomor WhatsApp Aktif <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative flex items-center">
                            <span class="absolute left-4 text-xs font-bold text-slate-400 select-none">+62</span>
                            <input id="phoneInput" type="tel" required
                                   :value="phoneDisplay"
                                   @input="handlePhoneInput($event)"
                                   inputmode="numeric"
                                   placeholder="857-1234-5678"
                                   class="w-full pl-14 pr-4 py-3 rounded-xl border text-base sm:text-sm placeholder:text-slate-400 focus:outline-none transition font-mono"
                                   :class="formErrors.customer_phone 
                                       ? 'border-rose-400 ring-2 ring-rose-100 bg-rose-50/20 text-slate-900' 
                                       : 'border-slate-200 focus:border-brand-600 focus:ring-2 focus:ring-brand-600/15'">
                            <input type="hidden" name="customer_phone" :value="rawPhoneDigits ? ('0' + rawPhoneDigits) : ''">
                        </div>
                        <p x-show="formErrors.customer_phone" x-cloak class="text-xs font-semibold text-rose-600 flex items-center gap-1.5 pt-0.5">
                            <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                            <span x-text="formErrors.customer_phone"></span>
                        </p>
                        <p x-show="!formErrors.customer_phone" class="text-[11px] text-slate-400">Kwitansi pembayaran dan konfirmasi jadwal akan dikirimkan ke nomor ini.</p>
                    </div>

                    <div class="space-y-1.5">
                        <label class="block text-xs font-semibold text-slate-700 tracking-tight">
                            Alamat Email <span class="text-slate-400 font-normal">(Opsional)</span>
                        </label>
                        <input type="email" name="customer_email" x-model="customerEmail"
                               @input="customerEmail = $event.target.value.toLowerCase()"
                               inputmode="email" autocomplete="email"
                               autocapitalize="none" autocorrect="off" spellcheck="false"
                               placeholder="email@contoh.com"
                               class="w-full px-4 py-3 rounded-xl border border-slate-200 text-base sm:text-sm lowercase placeholder:normal-case placeholder:text-slate-400 focus:outline-none focus:border-brand-600 focus:ring-2 focus:ring-brand-600/15 transition">
                    </div>

                    <!-- Alamat Lengkap Lokasi Acara (Venue) -->
                    <div class="space-y-1.5 pt-1">
                        <label class="block text-xs font-semibold text-slate-700 tracking-tight">
                            <span x-text="venueAddressLabel"></span> <span class="text-rose-500">*</span>
                        </label>
                        <textarea name="venue_address" required x-model="venueAddress"
                                  @input="formErrors.venue_address = null; formErrors.step3 = null"
                                  rows="2"
                                  :placeholder="venueAddressPlaceholder"
                                  class="w-full px-4 py-2.5 rounded-xl border text-base sm:text-sm placeholder:text-slate-400 focus:outline-none transition resize-none leading-relaxed"
                                  :class="formErrors.venue_address 
                                      ? 'border-rose-400 ring-2 ring-rose-100 bg-rose-50/20 text-slate-900' 
                                      : 'border-slate-200 focus:border-brand-600 focus:ring-2 focus:ring-brand-600/15'"></textarea>
                        <p x-show="formErrors.venue_address" x-cloak class="text-xs font-semibold text-rose-600 flex items-center gap-1.5 pt-0.5">
                            <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                            <span x-text="formErrors.venue_address"></span>
                        </p>
                        <p x-show="!formErrors.venue_address" class="text-[11px] text-slate-400"
                           x-text="eventType === 'sewa_alat' 
                               ? 'Digunakan oleh kru operasional untuk pengantaran dan penjemputan alat sewa.' 
                               : 'Digunakan oleh kru operasional untuk survey lokasi dan pengiriman muatan.'"></p>
                    </div>

                    <!-- Jarak & Wilayah Pengiriman Muatan Tenda -->
                    <div class="space-y-2 pt-1">
                        <div class="flex items-center justify-between">
                            <label class="block text-xs font-semibold text-slate-700 tracking-tight">
                                <span x-text="eventType === 'sewa_alat' ? 'Jarak Lokasi Pengantaran dari Slawi' : 'Jarak Lokasi Acara dari Slawi'"></span> <span class="text-rose-500">*</span>
                            </label>
                            <span class="text-[10px] text-slate-400 font-medium">
                                Pilih estimasi jarak tempuh
                            </span>
                        </div>

                        <input type="hidden" name="delivery_distance_km" :value="deliveryDistanceKm">

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-xs">
                            <!-- Tier 1: <= 10 km (FREE) -->
                            <label class="relative p-3 rounded-xl border transition cursor-pointer flex items-center justify-between gap-2.5"
                                   :class="selectedDistanceTier === '0_10' 
                                       ? 'border-brand-600 bg-brand-50/40 ring-1 ring-brand-600' 
                                       : 'border-slate-200 hover:border-slate-300 bg-white'">
                                <div class="flex items-center gap-2.5 min-w-0">
                                    <input type="radio" name="_distance_tier" value="0_10" 
                                           x-model="selectedDistanceTier"
                                           @change="selectDistanceTier('0_10', 0)"
                                           class="text-brand-600 focus:ring-brand-600 shrink-0">
                                    <div class="min-w-0">
                                        <p class="font-bold text-slate-900 leading-tight">&le; 10 km</p>
                                        <p class="text-[10px] text-slate-500 leading-tight mt-0.5 truncate">Slawi, Lebaksiu, Adiwerna, Pangkah</p>
                                    </div>
                                </div>
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-emerald-100 text-emerald-800 shrink-0">
                                    Gratis Ongkir
                                </span>
                            </label>

                            <!-- Tier 2: 11 - 20 km -->
                            <label class="relative p-3 rounded-xl border transition cursor-pointer flex items-center justify-between gap-2.5"
                                   :class="selectedDistanceTier === '11_20' 
                                       ? 'border-brand-600 bg-brand-50/40 ring-1 ring-brand-600' 
                                       : 'border-slate-200 hover:border-slate-300 bg-white'">
                                <div class="flex items-center gap-2.5 min-w-0">
                                    <input type="radio" name="_distance_tier" value="11_20" 
                                           x-model="selectedDistanceTier"
                                           @change="selectDistanceTier('11_20', 15)"
                                           class="text-brand-600 focus:ring-brand-600 shrink-0">
                                    <div class="min-w-0">
                                        <p class="font-bold text-slate-900 leading-tight">11 – 20 km</p>
                                        <p class="text-[10px] text-slate-500 leading-tight mt-0.5 truncate">Kota Tegal, Balapulang, Jatibarang</p>
                                    </div>
                                </div>
                            </label>

                            <!-- Tier 3: 21 - 30 km -->
                            <label class="relative p-3 rounded-xl border transition cursor-pointer flex items-center justify-between gap-2.5"
                                   :class="selectedDistanceTier === '21_30' 
                                       ? 'border-brand-600 bg-brand-50/40 ring-1 ring-brand-600' 
                                       : 'border-slate-200 hover:border-slate-300 bg-white'">
                                <div class="flex items-center gap-2.5 min-w-0">
                                    <input type="radio" name="_distance_tier" value="21_30" 
                                           x-model="selectedDistanceTier"
                                           @change="selectDistanceTier('21_30', 25)"
                                           class="text-brand-600 focus:ring-brand-600 shrink-0">
                                    <div class="min-w-0">
                                        <p class="font-bold text-slate-900 leading-tight">21 – 30 km</p>
                                        <p class="text-[10px] text-slate-500 leading-tight mt-0.5 truncate">Brebes Kota, Suradadi, Margasari</p>
                                    </div>
                                </div>
                            </label>

                            <!-- Tier 4: 31 - 40 km -->
                            <label class="relative p-3 rounded-xl border transition cursor-pointer flex items-center justify-between gap-2.5"
                                   :class="selectedDistanceTier === '31_40' 
                                       ? 'border-brand-600 bg-brand-50/40 ring-1 ring-brand-600' 
                                       : 'border-slate-200 hover:border-slate-300 bg-white'">
                                <div class="flex items-center gap-2.5 min-w-0">
                                    <input type="radio" name="_distance_tier" value="31_40" 
                                           x-model="selectedDistanceTier"
                                           @change="selectDistanceTier('31_40', 35)"
                                           class="text-brand-600 focus:ring-brand-600 shrink-0">
                                    <div class="min-w-0">
                                        <p class="font-bold text-slate-900 leading-tight">31 – 40 km</p>
                                        <p class="text-[10px] text-slate-500 leading-tight mt-0.5 truncate">Bumiayu, Pemalang Kota, Tanjung</p>
                                    </div>
                                </div>
                            </label>

                            <!-- Tier 5: > 40 km -->
                            <label class="relative p-3 rounded-xl border transition cursor-pointer flex items-center justify-between gap-2.5 sm:col-span-2"
                                   :class="selectedDistanceTier === 'gt_40' 
                                       ? 'border-brand-600 bg-brand-50/40 ring-1 ring-brand-600' 
                                       : 'border-slate-200 hover:border-slate-300 bg-white'">
                                <div class="flex items-center gap-2.5 min-w-0">
                                    <input type="radio" name="_distance_tier" value="gt_40" 
                                           x-model="selectedDistanceTier"
                                           @change="selectDistanceTier('gt_40', 45)"
                                           class="text-brand-600 focus:ring-brand-600 shrink-0">
                                    <div class="min-w-0">
                                        <p class="font-bold text-slate-900 leading-tight">&gt; 40 km</p>
                                        <p class="text-[10px] text-slate-500 leading-tight mt-0.5">Wilayah Lainnya / Luar Kota (Comal, Losari, Pekalongan, dll)</p>
                                    </div>
                                </div>
                            </label>
                        </div>

                        <!-- GPS Button & Status -->
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-1.5 pt-1 text-[11px]">
                            <button type="button" @click="detectGpsDistance()" 
                                    :disabled="gpsLoading"
                                    class="inline-flex items-center gap-1.5 text-brand-700 hover:text-brand-800 font-semibold cursor-pointer transition">
                                <template x-if="!gpsLoading">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                </template>
                                <template x-if="gpsLoading">
                                    <svg class="w-3.5 h-3.5 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                </template>
                                <span x-text="gpsLoading ? 'Mendeteksi titik...' : 'Cek Jarak Otomatis via Lokasi Anda'"></span>
                            </button>
                            <span class="text-slate-400" x-text="gpsStatus"></span>
                        </div>
                    </div>

                    <!-- Grid: Ancer-ancer Rumah & Jam Acara -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="space-y-1.5">
                            <label class="block text-xs font-semibold text-slate-700 tracking-tight">
                                Patokan / Ancer-ancer Rumah <span class="text-slate-400 font-normal">(Opsional)</span>
                            </label>
                            <input type="text" name="address_landmark" x-model="addressLandmark"
                                   placeholder="Dekat masjid, gapura, atau warna pagar"
                                   class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-base sm:text-sm placeholder:text-slate-400 focus:outline-none focus:border-brand-600 focus:ring-2 focus:ring-brand-600/15 transition">
                        </div>
                        <div class="space-y-1.5">
                            <label class="block text-xs font-semibold text-slate-700 tracking-tight">
                                <span x-text="eventType === 'sewa_alat' ? 'Jam Pengantaran / Digunakan' : 'Jam Acara'"></span> <span class="text-slate-400 font-normal">(Opsional)</span>
                            </label>
                            <input type="text" name="event_time" x-model="eventTime"
                                   :placeholder="eventType === 'sewa_alat' ? 'Cth: Pagi jam 07:00 / Siang jam 13:00' : '08:00 - Selesai'"
                                   class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-base sm:text-sm placeholder:text-slate-400 focus:outline-none focus:border-brand-600 focus:ring-2 focus:ring-brand-600/15 transition">
                        </div>
                    </div>

                    <!-- Grid: Request Tanggal Survey & Pilihan Waktu (Hanya untuk Paket Tenda / Acara Selain Sewa Alat Lepas) -->
                    <div x-show="eventType !== 'sewa_alat'" x-cloak class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <input type="hidden" name="survey_requested" :value="(eventType !== 'sewa_alat' && surveyDate) ? '1' : '0'">
                        <div class="space-y-1.5">
                            <label class="block text-xs font-semibold text-slate-700 tracking-tight">
                                Request Tanggal Survey Tempat <span class="text-slate-400 font-normal">(Opsional)</span>
                            </label>
                            <input type="date" name="survey_date" x-model="surveyDate"
                                   :min="minSurveyDate" :max="maxSurveyDate"
                                   :disabled="eventType === 'sewa_alat'"
                                   class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-base sm:text-sm focus:outline-none focus:border-brand-600 focus:ring-2 focus:ring-brand-600/15 transition bg-white text-slate-800">
                        </div>
                        <div class="space-y-1.5">
                            <label class="block text-xs font-semibold text-slate-700 tracking-tight">
                                Waktu Survey <span class="text-slate-400 font-normal">(Opsional)</span>
                            </label>
                            <div class="relative">
                                <select name="survey_time_slot" x-model="surveyTimeSlot"
                                        :disabled="eventType === 'sewa_alat'"
                                        class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-base sm:text-sm text-slate-700 focus:outline-none focus:border-brand-600 focus:ring-2 focus:ring-brand-600/15 transition bg-white appearance-none cursor-pointer">
                                    <option value="flexible">Fleksibel (Sesuai Janji Temu)</option>
                                    <option value="morning">Pagi (09:00 – 12:00 WIB)</option>
                                    <option value="afternoon">Siang – Sore (13:00 – 17:00 WIB)</option>
                                </select>
                                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3.5 text-slate-400">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Grid: Nama Orang Tua & Akun Instagram (Hanya untuk Acara Selain Sewa Alat) -->
                    <div x-show="eventType !== 'sewa_alat'" x-cloak class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div x-show="eventType === 'pernikahan'" class="space-y-1.5">
                            <label class="block text-xs font-semibold text-slate-700 tracking-tight">
                                Nama Orang Tua Mempelai <span class="text-slate-400 font-normal">(Opsional)</span>
                            </label>
                            <input type="text" name="parents_name" x-model="parentsName"
                                   placeholder="Bpk. Rahmat & Ibu Siti"
                                   :disabled="eventType === 'sewa_alat'"
                                   class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-base sm:text-sm placeholder:text-slate-400 focus:outline-none focus:border-brand-600 focus:ring-2 focus:ring-brand-600/15 transition">
                        </div>
                        <div class="space-y-1.5" :class="eventType !== 'pernikahan' ? 'sm:col-span-2' : ''">
                            <label class="block text-xs font-semibold text-slate-700 tracking-tight">
                                Akun Instagram <span class="text-slate-400 font-normal">(Opsional)</span>
                            </label>
                            <input type="text" name="instagram_account" x-model="instagramAccount"
                                   placeholder="@akun_instagram"
                                   :disabled="eventType === 'sewa_alat'"
                                   class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-base sm:text-sm placeholder:text-slate-400 focus:outline-none focus:border-brand-600 focus:ring-2 focus:ring-brand-600/15 transition">
                        </div>
                    </div>

                </div>

                <!-- Bagian Vendor (Relevan untuk Pernikahan & Khitanan) -->
                <div x-show="eventType === 'pernikahan' || eventType === 'khitanan'" x-cloak class="space-y-4">
                    <!-- Vendor Rekanan / Rujukan (Opsional) -->
                    <div class="bg-white rounded-2xl border border-slate-200/90 p-4 sm:p-5 shadow-card space-y-2.5">
                        <div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-xl bg-slate-100 text-slate-600 flex items-center justify-center font-bold text-xs shrink-0 border border-slate-200">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                            </div>
                            <div>
                                <h4 class="text-xs sm:text-sm font-bold text-slate-900 tracking-tight">Vendor <span class="text-slate-400 font-normal text-xs">(Opsional)</span></h4>
                            </div>
                        </div>
                        <input type="text" name="vendor_name" x-model="vendorName"
                               placeholder="Nama vendor rekanan (cth: MUA Lestari / WO Mas Dimas)"
                               class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-base sm:text-sm placeholder:text-slate-400 focus:outline-none focus:border-brand-600 focus:ring-2 focus:ring-brand-600/15 transition">
                    </div>

                    <!-- Accordion Informasi Vendor Lainnya (Opsional) -->
                    <div class="bg-white rounded-2xl border border-slate-200/90 p-4 sm:p-5 shadow-card" x-data="{ vendorsOpen: Boolean(vendorMua || vendorDecor || vendorPhotoVideo || vendorMcWo) }">
                        <button type="button" @click="vendorsOpen = !vendorsOpen"
                                class="w-full flex items-center justify-between text-left cursor-pointer select-none">
                            <div class="flex items-center gap-2.5">
                                <div class="w-8 h-8 rounded-xl bg-slate-100 text-slate-600 flex items-center justify-center font-bold text-xs shrink-0 border border-slate-200">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                </div>
                                <div>
                                    <h4 class="text-xs sm:text-sm font-bold text-slate-900 tracking-tight">Rincian Vendor Acara Lainnya <span class="text-slate-400 font-normal text-xs">(Opsional)</span></h4>
                                </div>
                            </div>
                            <svg class="w-4 h-4 text-slate-400 transition-transform duration-200" :class="{ 'rotate-180': vendorsOpen }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/></svg>
                        </button>

                        <div x-show="vendorsOpen" x-cloak x-transition class="mt-4 pt-4 border-t border-slate-100 space-y-3.5">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                                <div class="space-y-1">
                                    <label class="text-[11px] font-semibold text-slate-700">MUA / Tata Rias</label>
                                    <input type="text" name="vendor_mua" x-model="vendorMua" placeholder="Nama MUA"
                                           class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs placeholder:text-slate-400 focus:outline-none focus:border-brand-600 focus:ring-1 focus:ring-brand-600 transition">
                                </div>
                                <div class="space-y-1">
                                    <label class="text-[11px] font-semibold text-slate-700">Vendor Dekorasi</label>
                                    <input type="text" name="vendor_decor" x-model="vendorDecor" placeholder="Nama Vendor Dekorasi (jika dari luar)"
                                           class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs placeholder:text-slate-400 focus:outline-none focus:border-brand-600 focus:ring-1 focus:ring-brand-600 transition">
                                </div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                                <div class="space-y-1">
                                    <label class="text-[11px] font-semibold text-slate-700">Fotografer / Videografer</label>
                                    <input type="text" name="vendor_photo_video" x-model="vendorPhotoVideo" placeholder="Nama Vendor Dokumentasi"
                                           class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs placeholder:text-slate-400 focus:outline-none focus:border-brand-600 focus:ring-1 focus:ring-brand-600 transition">
                                </div>
                                <div class="space-y-1">
                                    <label class="text-[11px] font-semibold text-slate-700">MC / Wedding Organizer Luar</label>
                                    <input type="text" name="vendor_mc_wo" x-model="vendorMcWo" placeholder="Nama MC atau WO"
                                           class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs placeholder:text-slate-400 focus:outline-none focus:border-brand-600 focus:ring-1 focus:ring-brand-600 transition">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- TEMA WARNA TENDA (OPSIONAL) - HANYA UNTUK PAKET TENDA (BUKAN SEWA ALAT LEPAS) -->
                <div x-show="!isCustomPackage && eventType !== 'sewa_alat'" x-cloak
                     class="bg-white rounded-2xl border border-slate-200/90 p-4 sm:p-5 shadow-card space-y-3.5">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-xl bg-brand-50 text-brand-700 flex items-center justify-center font-bold text-xs shrink-0 border border-brand-200/70">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"/></svg>
                        </div>
                        <div>
                            <h4 class="text-xs sm:text-sm font-bold text-slate-900 tracking-tight">Tema Warna Tenda <span class="text-slate-400 font-normal text-xs">(Opsional)</span></h4>
                            <p class="text-[11px] text-slate-500">Pilih palet warna kain plafon tenda atau ketik kombinasi custom.</p>
                        </div>
                    </div>

                    <!-- Dropdown Tema Warna Tenda (Clean & Space-Saving) -->
                    <div class="space-y-2">
                        <!-- Hidden input for standard form submission -->
                        <input type="hidden" name="tent_color_theme" :value="tentColorTheme">

                        <div class="relative w-full" @click.away="tentColorOpen = false">
                            <div role="button" tabindex="0"
                                 @click="tentColorOpen = !tentColorOpen"
                                 @keydown.enter.prevent="tentColorOpen = !tentColorOpen"
                                 @keydown.space.prevent="tentColorOpen = !tentColorOpen"
                                 class="w-full px-3.5 sm:px-4 py-2.5 sm:py-3 rounded-xl border transition flex items-center justify-between cursor-pointer select-none active:scale-[0.99]"
                                 :class="tentColorOpen 
                                     ? 'bg-white border-brand-600 ring-2 ring-brand-600/15 text-slate-900 shadow-subtle' 
                                     : 'bg-white hover:bg-slate-50/80 border-slate-200 text-slate-800 shadow-2xs'">
                                <div class="flex items-center gap-2.5 min-w-0">
                                    <template x-if="selectedTentColorDot">
                                        <span class="w-3 h-3 rounded-full shrink-0 shadow-2xs border border-slate-200" :class="selectedTentColorDot"></span>
                                    </template>
                                    <template x-if="!selectedTentColorDot && tentColorTheme">
                                        <span class="w-3 h-3 rounded-full bg-brand-600 shrink-0"></span>
                                    </template>
                                    <span class="truncate text-xs sm:text-sm font-semibold"
                                          :class="tentColorTheme ? 'text-slate-900 font-bold' : 'text-slate-400 font-normal'"
                                          x-text="tentColorTheme || 'Pilih tema warna tenda (opsional)'"></span>
                                </div>

                                <div class="flex items-center gap-1.5 shrink-0 pl-2 text-slate-400">
                                    <button type="button" x-show="tentColorTheme" 
                                            @click.stop="tentColorTheme = ''; tentColorCustomMode = false"
                                            class="p-0.5 hover:text-slate-600 text-slate-400 cursor-pointer" title="Hapus pilihan">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                    </button>
                                    <svg class="w-4 h-4 transition-transform duration-200"
                                         :class="{ 'rotate-180 text-brand-600': tentColorOpen }"
                                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/>
                                    </svg>
                                </div>
                            </div>

                            <!-- Custom Popover Menu Options -->
                            <div x-show="tentColorOpen" x-cloak
                                 x-transition:enter="transition ease-out duration-150"
                                 x-transition:enter-start="opacity-0 scale-[0.98] -translate-y-1"
                                 x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                                 x-transition:leave="transition ease-in duration-100"
                                 x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                                 x-transition:leave-end="opacity-0 scale-[0.98] -translate-y-1"
                                 class="absolute left-0 right-0 top-full mt-1.5 w-full bg-white rounded-2xl shadow-xl border border-slate-200/90 p-1.5 z-30 space-y-0.5 max-h-60 overflow-y-auto">
                                
                                <!-- Opsi: Tanpa Preferensi Khusus -->
                                <button type="button"
                                        @click="tentColorTheme = ''; tentColorCustomMode = false; tentColorOpen = false"
                                        :class="!tentColorTheme 
                                            ? 'bg-brand-50 text-brand-800 font-bold border border-brand-200/70' 
                                            : 'text-slate-700 hover:bg-slate-50 hover:text-slate-900 border border-transparent font-medium'"
                                        class="w-full text-left px-3.5 py-2 text-xs rounded-xl transition flex items-center justify-between cursor-pointer group">
                                    <span class="text-slate-500 italic">Tanpa preferensi khusus (konsultasi nanti)</span>
                                    <template x-if="!tentColorTheme">
                                        <svg class="w-4 h-4 text-brand-600 shrink-0 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                        </svg>
                                    </template>
                                </button>

                                <div class="border-t border-slate-100 my-1"></div>

                                <!-- Preset Color Options -->
                                <template x-for="item in tentColorPresets" :key="item.name">
                                    <button type="button"
                                            @click="tentColorTheme = item.name; tentColorCustomMode = false; tentColorOpen = false"
                                            :class="tentColorTheme === item.name 
                                                ? 'bg-brand-50 text-brand-800 font-bold border border-brand-200/70' 
                                                : 'text-slate-700 hover:bg-slate-50 hover:text-slate-900 border border-transparent font-medium'"
                                            class="w-full text-left px-3.5 py-2.5 text-xs sm:text-sm rounded-xl transition flex items-center justify-between cursor-pointer group">
                                        <div class="flex items-center gap-2.5 min-w-0">
                                            <span class="w-3 h-3 rounded-full shrink-0 shadow-2xs border border-slate-200" :class="item.dot"></span>
                                            <span class="truncate" x-text="item.name"></span>
                                        </div>
                                        <template x-if="tentColorTheme === item.name">
                                            <svg class="w-4 h-4 text-brand-600 shrink-0 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                            </svg>
                                        </template>
                                    </button>
                                </template>

                                <div class="border-t border-slate-100 my-1"></div>

                                <!-- Opsi: Custom Warna Lainnya -->
                                <button type="button"
                                        @click="tentColorCustomMode = true; tentColorOpen = false; $nextTick(() => document.getElementById('customColorInput')?.focus())"
                                        :class="tentColorCustomMode 
                                            ? 'bg-brand-50 text-brand-800 font-bold border border-brand-200/70' 
                                            : 'text-slate-700 hover:bg-slate-50 hover:text-slate-900 border border-transparent font-medium'"
                                        class="w-full text-left px-3.5 py-2 text-xs rounded-xl transition flex items-center justify-between cursor-pointer group">
                                    <div class="flex items-center gap-2.5 min-w-0">
                                        <svg class="w-3.5 h-3.5 text-slate-400 group-hover:text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                        <span class="truncate font-semibold text-brand-700">Kombinasi Warna Lainnya...</span>
                                    </div>
                                    <template x-if="tentColorCustomMode">
                                        <svg class="w-4 h-4 text-brand-600 shrink-0 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                        </svg>
                                    </template>
                                </button>
                            </div>
                        </div>

                        <!-- Custom Color Input Field (Shown if Custom Mode is chosen or custom value typed) -->
                        <div x-show="tentColorCustomMode" x-cloak x-transition
                             class="space-y-1 pt-1">
                            <div class="relative">
                                <input id="customColorInput" type="text" x-model="tentColorTheme"
                                       placeholder="Ketik kombinasi warna custom (cth: Putih + Lilac, Putih + Terakota)"
                                       class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs placeholder:text-slate-400 focus:outline-none focus:border-brand-600 focus:ring-1 focus:ring-brand-600 transition bg-[#F9FBFA] focus:bg-white pr-8">
                                <button type="button" x-show="tentColorTheme" @click="tentColorTheme = ''; tentColorCustomMode = false" 
                                        class="absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 text-xs p-1 cursor-pointer">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Modifikasi / Custom Ukuran Tenda (Opsional) -->
                    <div class="pt-2 border-t border-slate-100 space-y-1">
                        <label class="block text-[11px] font-semibold text-slate-700 tracking-tight">
                            Permintaan Khusus Dimensi Tenda <span class="text-slate-400 font-normal">(Opsional)</span>
                        </label>
                        <input type="text" name="custom_tent_size" x-model="customTentSize"
                               placeholder="Cth: Sambung lorong 6x16m + modul VIP 7x7m"
                               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs placeholder:text-slate-400 focus:outline-none focus:border-brand-600 focus:ring-1 focus:ring-brand-600 transition bg-[#F9FBFA] focus:bg-white">
                    </div>
                </div>

                <!-- DESKTOP & TABLET INLINE ACTION FOOTER (CARD CONTAINER) -->
                <div x-show="step === 3" class="hidden md:flex items-center justify-between gap-4 bg-white rounded-2xl border border-slate-200/90 p-4 sm:p-5 shadow-card">
                    <button type="button" @click="goToStep(2)"
                            class="px-4 py-2.5 rounded-xl border border-slate-200 hover:bg-slate-50 text-slate-700 font-semibold text-xs transition flex items-center gap-1.5 cursor-pointer active:scale-95">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
                        <span>Kembali</span>
                    </button>
                    <button type="button" @click="goToStep(4)"
                            class="px-6 py-2.5 sm:py-3 rounded-xl bg-brand-600 hover:bg-brand-700 active:scale-95 text-white font-bold text-xs tracking-wide transition flex items-center gap-2 cursor-pointer shadow-pill">
                        <span>Lanjut ke Pembayaran</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </button>
                </div>

            </div>
