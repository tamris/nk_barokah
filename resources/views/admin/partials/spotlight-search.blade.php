                <!-- Global Spotlight Search Command Palette -->
                <div x-data="globalSpotlight()"
                     @open-spotlight.window="$nextTick(() => { $refs.searchInput?.focus(); if (query.trim().length > 0) open = true; })"
                     @keydown.window="handleKeyDown($event)"
                     @click.away="open = false"
                     class="relative hidden md:block">

                    <!-- Search Input Pill -->
                    <div class="flex items-center bg-[#F6F8F7] border border-[#E6ECEA] focus-within:border-brand-500 focus-within:bg-white focus-within:ring-2 focus-within:ring-brand-500/15 rounded-full pl-3.5 pr-1.5 py-1 w-56 lg:w-72 transition-all shadow-2xs">
                        <svg class="w-3.5 h-3.5 text-slate-400 mr-2 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M21 21l-5.2-5.2m2.2-4.8a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        <input type="text"
                               x-ref="searchInput"
                               x-model="query"
                               @input="open = query.trim().length > 0"
                               @focus="if (query.trim().length > 0) open = true"
                               @keydown.enter.prevent="submitFirstResult()"
                               placeholder="Search anything..."
                               class="w-full bg-transparent text-xs text-slate-800 placeholder-slate-400 focus:outline-none font-medium">
                        
                        <!-- Clear Button -->
                        <button type="button"
                                x-show="query"
                                x-cloak
                                @click="query = ''; open = false; $refs.searchInput.focus()"
                                class="text-slate-400 hover:text-slate-600 p-1 mr-1 transition cursor-pointer">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>

                        <!-- Keyboard Shortcut Badge -->
                        <span x-show="!query" class="hidden lg:inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-mono text-slate-400 bg-white border border-slate-200 font-semibold select-none mr-1.5 shadow-2xs">
                            Ctrl+K
                        </span>

                        <!-- Search Button -->
                        <button type="button"
                                @click="$refs.searchInput.focus(); if (query.trim().length > 0) open = true"
                                class="w-7 h-7 rounded-full bg-brand-600 hover:bg-brand-700 text-white flex items-center justify-center shrink-0 transition shadow-2xs cursor-pointer">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.4" d="M21 21l-5.2-5.2m2.2-4.8a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </button>
                    </div>

                    <!-- Floating Spotlight Results Dropdown -->
                    <div x-show="open && query.trim().length > 0"
                         x-cloak
                         x-transition:enter="transition ease-out duration-150"
                         x-transition:enter-start="opacity-0 translate-y-2 scale-98"
                         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                         x-transition:leave="transition ease-in duration-100"
                         x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                         x-transition:leave-end="opacity-0 translate-y-2 scale-98"
                         class="absolute right-0 top-full mt-2.5 w-[380px] sm:w-[460px] bg-white rounded-3xl shadow-2xl border border-[#E2ECE8] p-3.5 sm:p-4 z-50 text-left max-h-[490px] overflow-y-auto space-y-3">
                        
                        <!-- Header -->
                        <div class="flex items-center justify-between pb-2 border-b border-slate-100 px-1">
                            <span class="text-xs font-bold text-slate-800">Search Results</span>
                            <span class="text-[10px] text-slate-400 font-mono" x-text="(filteredBookings.length + ({{ ($isOwner ?? false) ? 'filteredClients.length' : '0' }})) + ' found'"></span>
                        </div>

                        <!-- Bookings & Invoices Section -->
                        <template x-if="filteredBookings.length > 0">
                            <div class="space-y-1">
                                <div class="flex items-center justify-between px-1 pt-1 pb-0.5">
                                    <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">
                                        Bookings &amp; Orders
                                    </span>
                                    <span class="text-[10px] text-slate-400 font-medium" x-text="filteredBookings.length + ' items'"></span>
                                </div>
                                <div class="space-y-1">
                                    <template x-for="b in filteredBookings" :key="b.id">
                                        <button type="button"
                                                @click="selectBooking(b)"
                                                class="w-full text-left p-2.5 rounded-2xl hover:bg-[#F2F8F6] border border-transparent hover:border-brand-200/60 transition group flex items-center justify-between gap-3 cursor-pointer">
                                            <div class="flex items-center gap-2.5 min-w-0">
                                                <div class="w-8 h-8 rounded-xl bg-slate-100 group-hover:bg-brand-600 text-slate-500 group-hover:text-white flex items-center justify-center shrink-0 border border-slate-200/60 group-hover:border-brand-600 transition">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                                    </svg>
                                                </div>
                                                <div class="min-w-0">
                                                    <div class="flex items-center gap-1.5">
                                                        <span class="font-bold text-xs text-slate-900 group-hover:text-brand-700 truncate" x-text="b.name"></span>
                                                        <span class="text-[10px] font-mono text-slate-400 font-medium" x-text="'#' + b.code"></span>
                                                    </div>
                                                    <p class="text-[11px] text-slate-500 truncate mt-0.5">
                                                        <span x-text="b.date_short"></span>
                                                        <span>·</span>
                                                        <span class="font-medium text-slate-700" x-text="b.service"></span>
                                                        <template x-if="b.child_name">
                                                            <span class="text-brand-700 font-semibold" x-text="'(' + b.child_name + ')'"></span>
                                                        </template>
                                                    </p>
                                                </div>
                                            </div>
                                            <div class="text-right shrink-0">
                                                <span class="text-[11px] font-bold text-slate-900 block" x-text="b.grand_total_formatted"></span>
                                                <span class="text-[9px] font-black uppercase px-1.5 py-0.5 rounded-md"
                                                      :class="{
                                                          'bg-emerald-50 text-emerald-700 border border-emerald-200': b.raw_status === 'confirmed',
                                                          'bg-amber-50 text-amber-700 border border-amber-200': b.raw_status === 'pending_verification',
                                                          'bg-slate-100 text-slate-600 border border-slate-200': b.raw_status === 'completed',
                                                          'bg-rose-50 text-rose-700 border border-rose-200': b.raw_status === 'rejected'
                                                      }"
                                                      x-text="b.bookingStatus"></span>
                                            </div>
                                        </button>
                                    </template>
                                </div>
                            </div>
                        </template>

                        @if($isOwner ?? false)
                        <!-- Clients Section (Owner Only) -->
                        <template x-if="filteredClients.length > 0">
                            <div class="space-y-1 pt-1 border-t border-slate-100">
                                <div class="flex items-center justify-between px-1 pt-1 pb-0.5">
                                    <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">
                                        Clients &amp; Contacts
                                    </span>
                                    <span class="text-[10px] text-slate-400 font-medium" x-text="filteredClients.length + ' contacts'"></span>
                                </div>
                                <div class="space-y-1">
                                    <template x-for="c in filteredClients" :key="c.phone + c.name">
                                        <button type="button"
                                                @click="selectClient(c)"
                                                class="w-full text-left p-2.5 rounded-2xl hover:bg-[#F2F8F6] border border-transparent hover:border-brand-200/60 transition group flex items-center justify-between gap-3 cursor-pointer">
                                            <div class="flex items-center gap-2.5 min-w-0">
                                                <div class="w-8 h-8 rounded-xl bg-teal-50 group-hover:bg-brand-600 text-teal-700 group-hover:text-white flex items-center justify-center shrink-0 border border-teal-200/60 group-hover:border-brand-600 transition">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                                    </svg>
                                                </div>
                                                <div class="min-w-0">
                                                    <span class="font-bold text-xs text-slate-900 group-hover:text-brand-700 truncate block" x-text="c.name"></span>
                                                    <p class="text-[11px] font-mono text-slate-500 mt-0.5" x-text="c.phone"></p>
                                                </div>
                                            </div>
                                            <div class="text-right shrink-0">
                                                <span class="text-[10px] font-semibold text-brand-700 block" x-text="c.totalBookings + ' Orders'"></span>
                                                <span class="text-[10px] text-slate-400" x-text="c.totalSpentFormatted"></span>
                                            </div>
                                        </button>
                                    </template>
                                </div>
                            </div>
                        </template>
                        @endif

                        <!-- Empty State -->
                        <template x-if="query && filteredBookings.length === 0 && ({{ ($isOwner ?? false) ? 'filteredClients.length === 0' : 'true' }})">
                            <div class="py-6 text-center space-y-1">
                                <div class="w-9 h-9 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                    </svg>
                                </div>
                                <p class="text-xs font-bold text-slate-800">No results found</p>
                                <p class="text-[11px] text-slate-400" x-text="'No bookings match \'' + query + '\''"></p>
                            </div>
                        </template>

                        <!-- Footer -->
                        <div class="pt-2 border-t border-slate-100 flex items-center justify-between text-[10px] text-slate-400 px-1">
                            <span>Search by name, invoice, phone, or venue</span>
                            <span class="font-mono bg-slate-100 px-1.5 py-0.5 rounded text-[9px]">ESC to close</span>
                        </div>
                    </div>
                </div>
