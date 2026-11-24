                <!-- Vendor Partners & Discount Management -->
                <div class="bg-white rounded-[28px] p-5 sm:p-7 shadow-card border border-[#EDF2F0] space-y-4"
                     x-data="{
                         editingDiscount: false,
                         pct: '',
                         initDiscount() {
                             this.pct = (activeDetail?.discount_percent > 0) ? activeDetail.discount_percent : '';
                             this.editingDiscount = false;
                         },
                         get subtotal() {
                             return Number(activeDetail?.subtotal_before_discount || (Number(activeDetail?.grand_total || 0) + Number(activeDetail?.discount_amount || 0)));
                         },
                         get nominal() {
                             const p = parseFloat(this.pct) || 0;
                             return Math.round((Math.min(100, Math.max(0, p)) / 100) * this.subtotal);
                         },
                         handleInput(e) {
                             const raw = e.target.value;
                             if (raw === '') {
                                 this.pct = '';
                                 return;
                             }
                             let n = parseFloat(raw);
                             if (isNaN(n) || n < 0) {
                                 this.pct = 0;
                                 e.target.value = 0;
                             } else if (n > 100) {
                                 this.pct = 100;
                                 e.target.value = 100;
                             } else {
                                 this.pct = raw;
                             }
                         }
                     }"
                     x-init="$watch('activeDetail', () => { initDiscount(); })">
                    <div class="flex items-center justify-between">
                        <h2 class="text-lg font-bold text-slate-900 tracking-tight">Vendor Partners</h2>
                        <template x-if="activeDetail?.vendor_name">
                            <span class="px-3 py-1 rounded-full bg-brand-50 border border-brand-200 text-brand-700 text-xs font-bold" x-text="activeDetail.vendor_name"></span>
                        </template>
                    </div>

                    <!-- External Vendors Grid -->
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-xs">
                        <div class="p-3.5 rounded-2xl bg-[#F8FAFA] border border-[#EFF4F2] space-y-1">
                            <span class="text-[10px] uppercase font-bold text-slate-400 tracking-wider">MUA</span>
                            <p class="font-semibold text-slate-800 truncate" x-text="activeDetail?.vendor_mua || '-'"></p>
                        </div>
                        <div class="p-3.5 rounded-2xl bg-[#F8FAFA] border border-[#EFF4F2] space-y-1">
                            <span class="text-[10px] uppercase font-bold text-slate-400 tracking-wider">Decoration</span>
                            <p class="font-semibold text-slate-800 truncate" x-text="activeDetail?.vendor_decor || '-'"></p>
                        </div>
                        <div class="p-3.5 rounded-2xl bg-[#F8FAFA] border border-[#EFF4F2] space-y-1">
                            <span class="text-[10px] uppercase font-bold text-slate-400 tracking-wider">Photo / Video</span>
                            <p class="font-semibold text-slate-800 truncate" x-text="activeDetail?.vendor_photo_video || '-'"></p>
                        </div>
                        <div class="p-3.5 rounded-2xl bg-[#F8FAFA] border border-[#EFF4F2] space-y-1">
                            <span class="text-[10px] uppercase font-bold text-slate-400 tracking-wider">MC / WO</span>
                            <p class="font-semibold text-slate-800 truncate" x-text="activeDetail?.vendor_mc_wo || '-'"></p>
                        </div>
                    </div>

                    <!-- Partner Discount Display Card -->
                    <div class="p-3.5 rounded-2xl bg-[#F8FAFA] border border-[#EFF4F2] space-y-1">
                        <div class="flex items-center justify-between">
                            <span class="text-[10px] uppercase font-bold text-slate-400 tracking-wider">Partner Discount</span>
                            @if($isOwner ?? false)
                            <template x-if="activeDetail?.invoiceStatus !== 'Paid' && (activeDetail?.remaining_balance || 0) > 0">
                                <button type="button" @click="editingDiscount = !editingDiscount; if(editingDiscount) { pct = (activeDetail?.discount_percent > 0) ? activeDetail.discount_percent : ''; }"
                                        class="text-[11px] font-bold text-brand-600 hover:text-brand-700 cursor-pointer">
                                    <span x-text="editingDiscount ? 'Cancel' : 'Edit'"></span>
                                </button>
                            </template>
                            @endif
                        </div>
                        <div>
                            <template x-if="(activeDetail?.discount_amount || 0) > 0">
                                <p class="font-bold text-emerald-700 text-sm font-mono">
                                    -<span x-text="activeDetail?.discount_amount_formatted"></span>
                                    <span class="text-slate-400 font-sans font-normal text-xs ml-1" x-text="'(' + (activeDetail?.discount_percent || 0) + '%)'"></span>
                                </p>
                            </template>
                            <template x-if="!(activeDetail?.discount_amount > 0)">
                                <p class="text-slate-400 text-xs font-medium">No discount applied</p>
                            </template>
                        </div>
                    </div>

                    @if($isOwner ?? false)
                    <!-- Inline Edit Partner Discount Form -->
                    <div x-show="editingDiscount && activeDetail?.invoiceStatus !== 'Paid'" x-cloak
                         class="p-3.5 rounded-2xl bg-white border border-slate-200 shadow-sm space-y-2.5">
                        <form :action="activeDetail?.discount_url" method="POST" class="flex flex-col sm:flex-row items-end gap-3 text-xs">
                            @csrf
                            <div class="w-full sm:w-36 space-y-1">
                                <label class="font-bold text-slate-600">Discount (%)</label>
                                <div class="relative">
                                    <input type="number" name="discount_percent" min="0" max="100" step="1"
                                           :value="pct"
                                           @input="handleInput($event)"
                                           @focus="$event.target.select()"
                                           placeholder="0"
                                           class="w-full pl-3 pr-7 py-1.5 rounded-lg border border-slate-200 font-mono font-bold text-slate-900 focus:outline-none focus:border-brand-600">
                                    <span class="absolute right-2.5 top-1/2 -translate-y-1/2 text-xs font-bold text-slate-400 select-none">%</span>
                                </div>
                            </div>
                            <div class="w-full flex-1 space-y-1">
                                <label class="font-bold text-slate-600">Deduction Preview</label>
                                <div class="py-1.5 px-3 rounded-lg bg-[#F8FAFA] border border-[#EFF4F2] font-mono font-bold text-emerald-700 min-h-[34px] flex items-center">
                                    <span x-text="nominal > 0 ? '-Rp ' + new Intl.NumberFormat('id-ID').format(nominal) : 'Rp 0'"></span>
                                </div>
                            </div>
                            <div class="flex items-center gap-2 w-full sm:w-auto">
                                <button type="submit"
                                        class="px-4 py-2 rounded-lg bg-brand-600 hover:bg-brand-700 text-white font-bold transition shadow-2xs cursor-pointer active:scale-95">
                                    Save
                                </button>
                                <button type="button" @click="editingDiscount = false"
                                        class="px-3 py-2 rounded-lg border border-slate-200 text-slate-600 hover:bg-slate-50 font-medium cursor-pointer transition">
                                    Cancel
                                </button>
                            </div>
                        </form>
                    </div>
                    @endif
                </div>
