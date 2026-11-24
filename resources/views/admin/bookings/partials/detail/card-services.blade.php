                <!-- Service Details -->
                <div class="bg-white rounded-[28px] p-5 sm:p-7 shadow-card border border-[#EDF2F0] space-y-4"
                     x-data="{
                         addingAddon: false,
                         addonMode: 'catalog',
                         selectedAddonId: '',
                         addonName: '',
                         addonQty: 1,
                         rawPrice: 0,
                         displayPrice: '',
                         editingOwnerDiscount: false,
                         rawOwnerDiscount: 0,
                         displayOwnerDiscount: '',
                         catalogDropdownOpen: false,
                         catalogSearch: '',
                         get filteredAddons() {
                             if (!this.catalogSearch) return (addonsList || []);
                             const q = this.catalogSearch.toLowerCase();
                             return (addonsList || []).filter(item => 
                                 (item.name || '').toLowerCase().includes(q) || 
                                 (item.unit || '').toLowerCase().includes(q) ||
                                 (item.price_formatted || '').toLowerCase().includes(q)
                             );
                         },
                         selectCatalogItem(item) {
                             this.selectedAddonId = item.id;
                             this.addonName = item.name;
                             this.rawPrice = item.price || 0;
                             this.displayPrice = new Intl.NumberFormat('id-ID').format(this.rawPrice);
                             this.catalogDropdownOpen = false;
                             this.catalogSearch = '';
                         },
                         resetForm() {
                             this.addingAddon = false;
                             this.addonMode = 'catalog';
                             this.selectedAddonId = '';
                             this.addonName = '';
                             this.addonQty = 1;
                             this.rawPrice = 0;
                             this.displayPrice = '';
                             this.catalogDropdownOpen = false;
                             this.catalogSearch = '';
                         },
                         formatPriceInput(e) {
                             const clean = e.target.value.replace(/\D/g, '');
                             if (!clean) {
                                 this.rawPrice = 0;
                                 this.displayPrice = '';
                                 e.target.value = '';
                                 return;
                             }
                             this.rawPrice = parseFloat(clean) || 0;
                             this.displayPrice = new Intl.NumberFormat('id-ID').format(this.rawPrice);
                             e.target.value = this.displayPrice;
                         }
                     }"
                     x-init="$watch('activeDetail', () => { resetForm(); })">
                    
                    <div class="flex items-center justify-between">
                        <h2 class="text-lg font-bold text-slate-900 tracking-tight">Service Details</h2>
                        <template x-if="activeDetail?.raw_status !== 'completed' && activeDetail?.bookingStatus !== 'Complete' && activeDetail?.raw_status !== 'rejected'">
                            <button type="button" @click="addingAddon = !addingAddon"
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-brand-50 hover:bg-brand-100 text-brand-700 text-xs font-bold transition cursor-pointer active:scale-95">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                                </svg>
                                <span x-text="addingAddon ? 'Cancel' : 'Add Item'"></span>
                            </button>
                        </template>
                    </div>

                    <!-- Inline Add Item Form -->
                    <div x-show="addingAddon && activeDetail?.raw_status !== 'completed' && activeDetail?.bookingStatus !== 'Complete'" x-cloak
                         class="p-4 rounded-2xl bg-[#F8FAFA] border border-brand-200 shadow-xs space-y-3">
                        <div class="flex items-center justify-between pb-2 border-b border-slate-200/60">
                            <span class="text-xs font-bold text-slate-800 uppercase tracking-wider">Add New Item / Service</span>
                            <button type="button" @click="resetForm()" class="text-slate-400 hover:text-slate-600 text-xs font-bold cursor-pointer">
                                Close
                            </button>
                        </div>

                        <form :action="'/admin/bookings/' + activeDetail?.id + '/addons'" method="POST" class="space-y-3 text-xs">
                            @csrf
                            
                            <!-- Mode Toggle: Catalog vs Custom -->
                            <div class="grid grid-cols-2 gap-2 p-1 rounded-xl bg-slate-200/60">
                                <button type="button" @click="addonMode = 'catalog'"
                                        :class="addonMode === 'catalog' ? 'bg-white shadow-2xs font-bold text-brand-700' : 'text-slate-500 font-semibold'"
                                        class="py-1.5 rounded-lg text-center transition cursor-pointer">
                                    Catalog Add-on
                                </button>
                                <button type="button" @click="addonMode = 'custom'; selectedAddonId = ''; catalogDropdownOpen = false;"
                                        :class="addonMode === 'custom' ? 'bg-white shadow-2xs font-bold text-brand-700' : 'text-slate-500 font-semibold'"
                                        class="py-1.5 rounded-lg text-center transition cursor-pointer">
                                    Custom Field Item
                                </button>
                            </div>

                            <!-- Catalog Selection (Rich Custom Dropdown) -->
                            <div x-show="addonMode === 'catalog'" class="space-y-1 relative" @click.away="catalogDropdownOpen = false">
                                <label class="font-bold text-slate-700">Select Service</label>
                                
                                <!-- Custom Trigger Button -->
                                <button type="button" @click="catalogDropdownOpen = !catalogDropdownOpen"
                                        class="w-full px-3.5 py-2.5 rounded-xl border bg-white text-xs flex items-center justify-between transition cursor-pointer text-left shadow-2xs"
                                        :class="catalogDropdownOpen ? 'border-brand-600 ring-2 ring-brand-600/15' : 'border-slate-200 hover:border-brand-300'">
                                    <div class="flex items-center gap-2 min-w-0 pr-2">
                                        <template x-if="selectedAddonId">
                                            <div class="flex items-center gap-2 truncate">
                                                <span class="w-2 h-2 rounded-full bg-brand-600 shrink-0"></span>
                                                <span class="font-bold text-slate-900 truncate" x-text="addonName"></span>
                                                <span class="px-2 py-0.5 rounded-md bg-emerald-50 text-emerald-700 text-[11px] font-mono font-bold shrink-0"
                                                      x-text="'Rp ' + displayPrice"></span>
                                            </div>
                                        </template>
                                        <template x-if="!selectedAddonId">
                                            <span class="text-slate-400 font-medium">-- Select from Add-on Catalog --</span>
                                        </template>
                                    </div>
                                    <svg class="w-4 h-4 text-slate-400 shrink-0 transition-transform duration-200"
                                         :class="catalogDropdownOpen ? 'rotate-180 text-brand-600' : ''"
                                         fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </button>

                                <input type="hidden" name="addon_id" :value="addonMode === 'catalog' ? selectedAddonId : ''">
                                <input type="hidden" name="addon_name" :value="addonName">

                                <!-- Rich Dropdown Panel -->
                                <div x-show="catalogDropdownOpen" x-cloak
                                     x-transition:enter="transition ease-out duration-150"
                                     x-transition:enter-start="opacity-0 translate-y-1 scale-98"
                                     x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                                     x-transition:leave="transition ease-in duration-100"
                                     x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                                     x-transition:leave-end="opacity-0 translate-y-1 scale-98"
                                     class="absolute left-0 right-0 top-full mt-1.5 bg-white rounded-2xl shadow-xl border border-[#EDF2F0] p-2 z-50 space-y-1.5">
                                    
                                    <!-- Quick Search Input inside Dropdown -->
                                    <div class="relative px-1 pt-1">
                                        <svg class="w-3.5 h-3.5 absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                        </svg>
                                        <input type="text" x-model="catalogSearch" @click.stop
                                               placeholder="Search items (stage, chair, tent, blower...)"
                                               class="w-full pl-8 pr-3 py-1.5 rounded-xl border border-slate-200 bg-[#F8FAFA] text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:bg-white focus:border-brand-600 transition">
                                    </div>

                                    <!-- Scrollable Option Items -->
                                    <div class="max-h-56 overflow-y-auto space-y-1 pt-1 pr-0.5 [scrollbar-width:thin] [scrollbar-color:#CBD5E1_transparent]">
                                        <template x-for="item in filteredAddons" :key="item.id">
                                            <div @click="selectCatalogItem(item)"
                                                 class="p-2 rounded-xl border transition flex items-center justify-between cursor-pointer group"
                                                 :class="String(selectedAddonId) === String(item.id) ? 'bg-brand-50 border-brand-200 text-brand-900' : 'bg-white hover:bg-[#F8FAFA] border-transparent hover:border-slate-100 text-slate-800'">
                                                <div class="flex items-center gap-2.5 min-w-0 pr-2">
                                                    <div class="w-7 h-7 rounded-lg flex items-center justify-center shrink-0"
                                                         :class="String(selectedAddonId) === String(item.id) ? 'bg-brand-600 text-white' : 'bg-[#EBF2F0] text-brand-700 group-hover:bg-brand-100'">
                                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                                        </svg>
                                                    </div>
                                                    <div class="min-w-0">
                                                        <p class="font-bold text-xs truncate" x-text="item.name"></p>
                                                        <p class="text-[10px] text-slate-400" x-text="'Unit: ' + (item.unit || 'unit')"></p>
                                                    </div>
                                                </div>
                                                <div class="flex items-center gap-1.5 shrink-0">
                                                    <span class="font-mono font-bold text-xs"
                                                          :class="String(selectedAddonId) === String(item.id) ? 'text-brand-700' : 'text-slate-700'"
                                                          x-text="item.price_formatted"></span>
                                                    <template x-if="String(selectedAddonId) === String(item.id)">
                                                        <svg class="w-4 h-4 text-brand-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                                                        </svg>
                                                    </template>
                                                </div>
                                            </div>
                                        </template>

                                        <template x-if="filteredAddons.length === 0">
                                            <div class="py-4 text-center text-slate-400 text-xs">
                                                No add-on items match your search
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </div>

                            <!-- Custom Item Name -->
                            <div x-show="addonMode === 'custom'" class="space-y-1">
                                <label class="font-bold text-slate-700">Custom Item Name</label>
                                <input type="text" name="addon_name" x-model="addonName"
                                       placeholder="e.g. Extra Tent Length 3x6m"
                                       class="w-full px-3 py-2 rounded-xl border border-slate-200 bg-white text-xs font-semibold text-slate-800 focus:outline-none focus:border-brand-600">
                            </div>

                            <!-- Quantity & Price Grid -->
                            <div class="grid grid-cols-2 gap-3">
                                <div class="space-y-1">
                                    <label class="font-bold text-slate-700">Quantity (Qty)</label>
                                    <input type="number" min="1" max="9999" name="quantity" x-model.number="addonQty"
                                           class="w-full px-3 py-2 rounded-xl border border-slate-200 bg-white text-xs font-mono font-bold text-slate-800 focus:outline-none focus:border-brand-600">
                                </div>
                                <div class="space-y-1">
                                    <label class="font-bold text-slate-700">Unit Price</label>
                                    <div class="relative">
                                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-xs font-bold text-slate-400 select-none">Rp</span>
                                        <input type="text" inputmode="numeric" :value="displayPrice" @input="formatPriceInput($event)"
                                               placeholder="100.000"
                                               class="w-full pl-9 pr-3 py-2 rounded-xl border border-slate-200 bg-white text-xs font-mono font-bold text-slate-800 focus:outline-none focus:border-brand-600">
                                        <input type="hidden" name="unit_price" :value="rawPrice">
                                    </div>
                                </div>
                            </div>

                            <!-- Subtotal Live Preview -->
                            <div class="p-2.5 rounded-xl bg-white border border-[#EFF4F2] flex items-center justify-between text-xs">
                                <span class="text-slate-500 font-medium">Item Subtotal:</span>
                                <span class="font-mono font-bold text-emerald-700"
                                      x-text="'Rp ' + new Intl.NumberFormat('id-ID').format((addonQty || 1) * (rawPrice || 0))"></span>
                            </div>

                            <!-- Action Buttons -->
                            <div class="flex items-center gap-2 pt-1">
                                <button type="submit"
                                        :disabled="!addonName || !rawPrice"
                                        class="flex-1 py-2 rounded-xl bg-brand-600 hover:bg-brand-700 disabled:opacity-50 text-white font-bold text-xs tracking-wide transition shadow-2xs cursor-pointer active:scale-95">
                                    Add to Booking
                                </button>
                                <button type="button" @click="resetForm()"
                                        class="px-4 py-2 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-100 font-medium text-xs cursor-pointer transition">
                                    Cancel
                                </button>
                            </div>
                        </form>
                    </div>

                    <div class="p-4 rounded-2xl bg-[#F8FAFA] border border-[#EFF4F2] space-y-3 text-xs">
                        <div class="flex items-center justify-between gap-3">
                            <div>
                                <p class="text-sm font-bold text-slate-900" x-text="activeDetail?.service"></p>
                                <p class="text-[11px] text-slate-400">Core Wedding Organizer Package</p>
                            </div>
                            <span class="text-sm font-bold text-slate-900 shrink-0" x-text="activeDetail?.package_price_formatted"></span>
                        </div>

                        <template x-for="(ad, aIdx) in (activeDetail?.addons || [])" :key="aIdx">
                            <div class="flex items-center justify-between gap-3 pt-2 border-t border-[#EBF1EF]">
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-1.5 flex-wrap">
                                        <p class="font-semibold text-slate-800 truncate" x-text="ad.name + ' (x' + ad.quantity + ')'"></p>
                                        <template x-if="ad.added_by_role === 'staff'">
                                            <span class="px-1.5 py-0.5 rounded-md text-[9px] font-bold bg-teal-50 text-teal-800 border border-teal-200">Field Staff</span>
                                        </template>
                                    </div>
                                    <p class="text-[11px] text-slate-400" x-text="ad.unit_price + ' / unit'"></p>
                                </div>
                                <div class="flex items-center gap-2 shrink-0">
                                    <span class="font-bold text-slate-800" x-text="ad.subtotal"></span>
                                    @if($isOwner ?? false)
                                    <template x-if="activeDetail?.raw_status !== 'completed' && activeDetail?.bookingStatus !== 'Complete' && activeDetail?.raw_status !== 'rejected'">
                                        <button type="button"
                                                @click="triggerConfirm(
                                                    'Delete Add-on Item',
                                                    'Remove item \'' + ad.name + '\' (' + ad.subtotal + ') from this booking?',
                                                    'Total invoice and remaining balance will be automatically recalculated.',
                                                    '/admin/bookings/' + activeDetail.id + '/addons/' + ad.id,
                                                    'Yes, Remove Item',
                                                    'bg-rose-600 hover:bg-rose-700 text-white',
                                                    'warning',
                                                    'DELETE'
                                                )"
                                                title="Remove item"
                                                class="p-1.5 rounded-lg hover:bg-rose-50 text-slate-300 hover:text-rose-600 transition cursor-pointer active:scale-95">
                                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </template>
                                    @endif
                                </div>
                            </div>
                        </template>

                        <template x-if="(activeDetail?.additional_day_charge || 0) > 0">
                            <div class="flex items-center justify-between gap-3 pt-2 border-t border-[#EBF1EF] text-amber-800">
                                <div>
                                    <p class="font-semibold" x-text="'Extra Duration (' + (activeDetail.duration_days - 2) + ' extra days @ 15%)'"></p>
                                    <p class="text-[11px] text-amber-600">Additional Day Surcharge</p>
                                </div>
                                <span class="font-bold shrink-0" x-text="'+ ' + activeDetail.additional_day_charge_formatted"></span>
                            </div>
                        </template>

                        <!-- Tent Delivery & Transport Fee -->
                        <template x-if="(activeDetail?.transport_fee || 0) > 0">
                            <div class="flex items-center justify-between gap-3 pt-2 border-t border-[#EBF1EF] text-slate-800">
                                <div>
                                    <p class="font-semibold" x-text="'Tent Transport & Delivery (' + (activeDetail.delivery_distance_km || 0) + ' km)'"></p>
                                    <p class="text-[11px] text-slate-400">Logistics fleet &amp; tent cargo delivery</p>
                                </div>
                                <span class="font-bold shrink-0 text-emerald-800" x-text="'+ ' + activeDetail.transport_fee_formatted"></span>
                            </div>
                        </template>

                        <!-- Special Owner Discount -->
                        <div class="p-3.5 rounded-2xl bg-[#F8FAFA] border border-[#EFF4F2] space-y-1">
                            <div class="flex items-center justify-between">
                                <span class="text-[10px] uppercase font-bold text-slate-400 tracking-wider">Special Owner Discount</span>
                                @if($isOwner ?? false)
                                <template x-if="activeDetail?.invoiceStatus !== 'Paid' && (activeDetail?.remaining_balance || 0) > 0">
                                    <button type="button"
                                            @click="editingOwnerDiscount = !editingOwnerDiscount; if(editingOwnerDiscount) { rawOwnerDiscount = activeDetail?.owner_discount || 0; displayOwnerDiscount = rawOwnerDiscount > 0 ? new Intl.NumberFormat('id-ID').format(rawOwnerDiscount) : ''; }"
                                            class="text-[11px] font-bold text-brand-600 hover:text-brand-700 cursor-pointer">
                                        <span x-text="editingOwnerDiscount ? 'Cancel' : 'Edit'"></span>
                                    </button>
                                </template>
                                @endif
                            </div>
                            <div>
                                <template x-if="(activeDetail?.owner_discount || 0) > 0">
                                    <p class="font-bold text-emerald-700 text-sm font-mono">
                                        -<span x-text="activeDetail?.owner_discount_formatted"></span>
                                    </p>
                                </template>
                                <template x-if="!(activeDetail?.owner_discount > 0)">
                                    <p class="text-slate-400 text-xs font-medium">No discount applied</p>
                                </template>
                            </div>
                        </div>

                        @if($isOwner ?? false)
                        <!-- Inline Edit Special Owner Discount Form -->
                        <div x-show="editingOwnerDiscount && activeDetail?.invoiceStatus !== 'Paid'" x-cloak
                             class="p-3.5 rounded-2xl bg-white border border-slate-200 shadow-sm space-y-2.5">
                            <form :action="activeDetail?.owner_discount_url" method="POST" class="flex flex-col sm:flex-row items-end gap-3 text-xs">
                                @csrf
                                <div class="w-full flex-1 space-y-1">
                                    <label class="font-bold text-slate-600">Discount Amount (Rp)</label>
                                    <div class="relative">
                                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-xs text-slate-400 font-bold pointer-events-none select-none">Rp</span>
                                        <input type="text"
                                               inputmode="numeric"
                                               :value="displayOwnerDiscount"
                                               @input="
                                                    let val = $event.target.value.replace(/\D/g, '');
                                                    rawOwnerDiscount = val ? parseInt(val, 10) : 0;
                                                    displayOwnerDiscount = rawOwnerDiscount > 0 ? new Intl.NumberFormat('id-ID').format(rawOwnerDiscount) : '';
                                                    $event.target.value = displayOwnerDiscount;
                                               "
                                               placeholder="0"
                                               class="w-full pl-9 pr-3 py-1.5 rounded-lg border border-slate-200 font-mono font-bold text-slate-900 focus:outline-none focus:border-brand-600">
                                        <input type="hidden" name="owner_discount" :value="rawOwnerDiscount">
                                    </div>
                                </div>
                                <div class="flex items-center gap-2 w-full sm:w-auto">
                                    <button type="submit"
                                            class="px-4 py-2 rounded-lg bg-brand-600 hover:bg-brand-700 text-white font-bold transition shadow-2xs cursor-pointer active:scale-95">
                                        Save
                                    </button>
                                    <button type="button" @click="editingOwnerDiscount = false"
                                            class="px-3 py-2 rounded-lg border border-slate-200 text-slate-600 hover:bg-slate-50 font-medium cursor-pointer transition">
                                        Cancel
                                    </button>
                                </div>
                            </form>
                        </div>
                        @endif

                        <div class="pt-3 border-t border-[#DFE8E5] space-y-1.5">
                            <template x-if="(activeDetail?.discount_amount || 0) > 0">
                                <div class="flex justify-between text-brand-700 font-semibold">
                                    <span>Vendor Partner Discount (<span x-text="(activeDetail?.discount_percent || 0) + '%'"></span>)</span>
                                    <span x-text="'- ' + activeDetail?.discount_amount_formatted"></span>
                                </div>
                            </template>
                            <template x-if="(activeDetail?.owner_discount || 0) > 0">
                                <div class="flex justify-between text-amber-700 font-semibold">
                                    <span>Special Owner Discount</span>
                                    <span x-text="'- ' + activeDetail?.owner_discount_formatted"></span>
                                </div>
                            </template>
                            <div class="flex justify-between">
                                <span class="text-slate-500">Grand Total</span>
                                <span class="font-extrabold text-slate-900 text-sm" x-text="activeDetail?.grand_total_formatted"></span>
                            </div>
                            <div class="flex justify-between text-emerald-700">
                                <span x-text="'Amount Paid (' + (activeDetail?.payment_type || 'DP') + ')'"></span>
                                <span class="font-bold" x-text="activeDetail?.amount_paid_formatted"></span>
                            </div>
                            <div class="flex justify-between" :class="(activeDetail?.remaining_balance || 0) > 0 ? 'text-rose-600 font-bold' : 'text-emerald-700 font-bold'">
                                <span>Remaining Balance</span>
                                <span x-text="(activeDetail?.remaining_balance || 0) > 0 ? activeDetail?.remaining_formatted : 'PAID (100%)'"></span>
                            </div>
                        </div>
                    </div>
                </div>
