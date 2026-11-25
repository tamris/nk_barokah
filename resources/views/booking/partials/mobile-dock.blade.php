    <!-- ============================================================== -->
    <!-- STICKY BOTTOM DOCK (MOBILE-ONLY THUMB NAVIGATION < 768px)      -->
    <!-- ============================================================== -->
    <div class="block md:hidden fixed bottom-0 inset-x-0 z-50 bg-white/95 backdrop-blur-md border-t border-slate-200/90 shadow-dock pb-safe">
        <div class="max-w-2xl mx-auto px-4 py-3 flex items-center justify-between gap-3">
            
            <!-- Left Info / Running Total Indicator -->
            <div class="min-w-0 flex-1">
                <div x-show="step === 1">
                    <p class="text-[10px] uppercase font-bold text-slate-400 tracking-wider">Status Tanggal</p>
                    <p class="text-xs font-bold text-slate-900 truncate"
                       x-text="selectedDate ? formattedSelectedDate : 'Belum pilih tanggal'"></p>
                </div>

                <div x-show="step > 1" x-cloak>
                    <p class="text-[10px] uppercase font-bold text-slate-400 tracking-wider">Total Tagihan</p>
                    <p class="text-sm font-bold font-mono text-brand-700 tracking-tight"
                       x-text="formatRupiah(grandTotal)"></p>
                </div>
            </div>

            <!-- Right Buttons Group -->
            <div class="flex items-center gap-2 shrink-0">
                <button type="button" x-show="step > 1" @click="step--"
                        class="px-3.5 py-2.5 rounded-xl border border-slate-200 hover:bg-slate-50 active:scale-95 text-slate-700 font-semibold text-xs transition cursor-pointer">
                    Kembali
                </button>

                <button type="button" x-show="step < 4" @click="goToStep(step + 1)"
                        :disabled="step === 1 && (!selectedDate || isDateBooked)"
                        class="px-5 py-2.5 rounded-xl bg-brand-600 hover:bg-brand-700 disabled:bg-slate-200 disabled:text-slate-400 active:scale-95 text-white font-bold text-xs tracking-wide transition flex items-center gap-1.5 cursor-pointer shadow-subtle">
                    <span x-text="step === 1 ? 'Pilih Paket' : (step === 2 ? 'Data Pemesan' : 'Pembayaran')"></span>
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                </button>

                <button type="button" x-show="step === 4" @click="submitBookingForm()"
                        :disabled="isDateBooked || !agreedToTerms"
                        class="px-5 py-2.5 rounded-xl bg-brand-600 hover:bg-brand-700 disabled:bg-slate-300 active:scale-95 text-white font-bold text-xs tracking-wide transition flex items-center gap-1.5 cursor-pointer shadow-pill" x-cloak>
                    <span>Konfirmasi Reservasi</span>
                </button>
            </div>

        </div>
    </div>

    <!-- Minimal Clean Footer -->
    <footer class="border-t border-slate-200 bg-white pt-3.5 pb-20 md:py-3.5 text-center text-xs text-slate-400">
        <div class="max-w-2xl mx-auto px-4 flex flex-col sm:flex-row items-center justify-between gap-1 text-[11px]">
            <p>&copy; {{ date('Y') }} NK Barokah Tenda. Hak cipta dilindungi.</p>
            <p class="text-slate-400">Sistem reservasi & penguncian jadwal resmi</p>
        </div>
    </footer>

