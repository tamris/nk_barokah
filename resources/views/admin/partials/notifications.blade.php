                <!-- Interactive Notifications & Schedule Bell -->
                <div class="relative" x-data="{ notifTab: '{{ ($isOwner ?? false) ? 'actions' : 'field' }}' }">
                    <button type="button"
                            @click="notifOpen = !notifOpen"
                            title="Notifications &amp; Schedule"
                            class="w-9 h-9 sm:w-10 sm:h-10 rounded-full bg-[#F6F8F7] hover:bg-[#EAF0EE] text-slate-700 flex items-center justify-center transition relative cursor-pointer">
                        <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.9" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                        @if(($isOwner ?? false) && $notifs['urgent_count'] > 0)
                            <span class="absolute -top-1 -right-1 min-w-[18px] h-[18px] px-1 bg-rose-500 text-white text-[10px] font-extrabold rounded-full flex items-center justify-center ring-2 ring-white shadow-sm">
                                {{ $notifs['urgent_count'] > 9 ? '9+' : $notifs['urgent_count'] }}
                            </span>
                        @elseif($notifs['field_count'] > 0)
                            <span class="w-2.5 h-2.5 rounded-full bg-teal-500 ring-2 ring-white absolute top-1.5 right-1.5"></span>
                        @endif
                    </button>

                    <!-- Mobile Backdrop Overlay -->
                    <div x-show="notifOpen"
                         @click="notifOpen = false"
                         x-transition:enter="transition ease-out duration-150"
                         x-transition:enter-start="opacity-0"
                         x-transition:enter-end="opacity-100"
                         class="fixed inset-0 z-40 bg-slate-900/25 backdrop-blur-2xs sm:hidden"
                         x-cloak></div>

                    <!-- Dropdown Pusat Notifikasi & Agenda Lapangan -->
                    <div x-show="notifOpen"
                         @click.away="notifOpen = false"
                         x-transition:enter="transition ease-out duration-150"
                         x-transition:enter-start="opacity-0 scale-95 -translate-y-2"
                         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                         class="fixed inset-x-3.5 top-[68px] max-w-[360px] mx-auto sm:max-w-none sm:mx-0 sm:inset-x-auto sm:absolute sm:right-0 sm:top-full sm:mt-2.5 sm:w-[380px] bg-white rounded-3xl shadow-2xl border border-[#E2ECE8] p-4 sm:p-5 z-50 text-left"
                         x-cloak>
                        
                        <!-- Header Dropdown -->
                        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                            <div>
                                <h3 class="text-xs font-bold text-slate-900 tracking-tight">{{ ($isOwner ?? false) ? 'Notifications & Schedule' : 'Field Schedule' }}</h3>
                                <p class="text-[10px] text-slate-400">Operations control center</p>
                            </div>
                            @if($isOwner ?? false)
                                <span class="text-[10.5px] font-bold px-2.5 py-0.5 rounded-full {{ $notifs['urgent_count'] > 0 ? 'text-rose-700 bg-rose-50 border border-rose-200' : 'text-teal-700 bg-teal-50 border border-teal-200' }}">
                                    {{ $notifs['urgent_count'] > 0 ? $notifs['urgent_count'] . ' Action Required' : 'All Clear' }}
                                </span>
                            @else
                                <span class="text-[10.5px] font-bold px-2.5 py-0.5 rounded-full text-teal-700 bg-teal-50 border border-teal-200">
                                    {{ $notifs['field_count'] }} Active
                                </span>
                            @endif
                        </div>

                        @if($isOwner ?? false)
                        <!-- Pill Switcher (Tab Buttons) -->
                        <div class="grid grid-cols-2 gap-1.5 p-1 bg-slate-100/80 rounded-2xl my-3 text-xs font-semibold">
                            <button type="button"
                                     @click="notifTab = 'actions'"
                                     :class="notifTab === 'actions' ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-500 hover:text-slate-800'"
                                     class="py-1.5 px-2 rounded-xl transition flex items-center justify-center gap-1.5 cursor-pointer">
                                <span>Action Required</span>
                                @if($notifs['urgent_count'] > 0)
                                    <span class="px-1.5 py-0.2 text-[9px] font-extrabold rounded-full"
                                          :class="notifTab === 'actions' ? 'bg-rose-500 text-white' : 'bg-rose-100 text-rose-700'">
                                        {{ $notifs['urgent_count'] }}
                                    </span>
                                @endif
                            </button>
                            <button type="button"
                                     @click="notifTab = 'field'"
                                     :class="notifTab === 'field' ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-500 hover:text-slate-800'"
                                     class="py-1.5 px-2 rounded-xl transition flex items-center justify-center gap-1.5 cursor-pointer">
                                <span>Field Schedule</span>
                                @if($notifs['field_count'] > 0)
                                    <span class="px-1.5 py-0.2 text-[9px] font-extrabold rounded-full"
                                          :class="notifTab === 'field' ? 'bg-teal-600 text-white' : 'bg-teal-100 text-teal-800'">
                                        {{ $notifs['field_count'] }}
                                    </span>
                                @endif
                            </button>
                        </div>

                        <!-- Content Tab: Action Required -->
                        <div x-show="notifTab === 'actions'" class="max-h-[340px] overflow-y-auto space-y-2.5 pr-0.5">
                            @forelse($notifs['action_required'] as $item)
                                <div class="p-3 bg-[#FAFDFC] hover:bg-[#F4F9F7] rounded-2xl border border-slate-100 hover:border-slate-200 transition space-y-2">
                                    <div class="flex items-center justify-between gap-2">
                                        <span class="text-[9px] font-extrabold tracking-wider uppercase px-2 py-0.5 rounded-md border {{ $item['badge_class'] }}">
                                            {{ $item['badge_text'] }}
                                        </span>
                                        <span class="text-[10px] font-mono text-slate-400">#{{ $item['invoice'] }}</span>
                                    </div>
                                    <div>
                                        <h4 class="text-xs font-bold text-slate-900 truncate">{{ $item['customer_name'] }}</h4>
                                        <p class="text-[11px] font-medium text-slate-600 mt-0.5">{{ $item['desc'] }}</p>
                                        <p class="text-[10px] text-slate-400 mt-0.5">{{ $item['meta'] }}</p>
                                    </div>
                                    <div class="pt-1 flex items-center justify-end gap-2">
                                        @if($item['action_type'] === 'whatsapp')
                                            <a href="{{ $item['action_url'] }}" target="_blank"
                                               @click="notifOpen = false"
                                               class="inline-flex items-center gap-1.5 text-[11px] font-bold px-3 py-1 bg-rose-50 text-rose-700 hover:bg-rose-100 border border-rose-200 rounded-xl transition cursor-pointer">
                                                <svg class="w-3.5 h-3.5 fill-current" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981z"/></svg>
                                                <span>{{ $item['action_label'] }}</span>
                                            </a>
                                        @else
                                            <a href="{{ $item['action_url'] }}"
                                               @click.prevent="notifOpen = false; window.dispatchEvent(new CustomEvent('open-booking-detail', { detail: { id: {{ $item['booking_id'] ?? $item['id'] }} } }));"
                                               class="inline-flex items-center gap-1 text-[11px] font-bold px-3 py-1 bg-brand-50 text-brand-700 hover:bg-brand-100 border border-brand-200 rounded-xl transition cursor-pointer">
                                                <span>{{ $item['action_label'] }}</span>
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            @empty
                                <div class="py-8 text-center">
                                    <div class="w-10 h-10 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center mx-auto mb-2">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                    </div>
                                    <p class="text-xs font-bold text-slate-800">All Caught Up!</p>
                                    <p class="text-[11px] text-slate-400 mt-0.5">No pending payment verifications or due settlements.</p>
                                </div>
                            @endforelse
                        </div>
                        @endif

                        <!-- Content Tab: Field Schedule -->
                        <div x-show="notifTab === 'field'" class="max-h-[340px] overflow-y-auto space-y-2.5 pr-0.5 {{ ($isOwner ?? false) ? '' : 'mt-3' }}">
                            @forelse($notifs['field_operations'] as $item)
                                <div class="p-3 bg-[#FAFDFC] hover:bg-[#F4F9F7] rounded-2xl border border-slate-100 hover:border-slate-200 transition space-y-2">
                                    <div class="flex items-center justify-between gap-2">
                                        <span class="text-[9px] font-extrabold tracking-wider uppercase px-2 py-0.5 rounded-md border {{ $item['badge_class'] }}">
                                            {{ $item['badge_text'] }}
                                        </span>
                                        <span class="text-[10px] font-mono text-slate-400">#{{ $item['invoice'] }}</span>
                                    </div>
                                    <div>
                                        <h4 class="text-xs font-bold text-slate-900 truncate">{{ $item['customer_name'] }}</h4>
                                        <p class="text-[11px] font-medium text-slate-600 mt-0.5">{{ $item['desc'] }}</p>
                                        <p class="text-[10px] text-slate-400 mt-0.5">{{ $item['meta'] }}</p>
                                    </div>
                                    <div class="pt-1 flex items-center justify-end gap-2">
                                        @if($item['action_type'] === 'whatsapp')
                                            <a href="{{ $item['action_url'] }}" target="_blank"
                                               @click="notifOpen = false"
                                               class="inline-flex items-center gap-1.5 text-[11px] font-bold px-3 py-1 bg-teal-50 text-teal-800 hover:bg-teal-100 border border-teal-200 rounded-xl transition cursor-pointer">
                                                <svg class="w-3.5 h-3.5 fill-current" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981z"/></svg>
                                                <span>{{ $item['action_label'] }}</span>
                                            </a>
                                        @else
                                            <a href="{{ $item['action_url'] }}"
                                               @click.prevent="notifOpen = false; window.dispatchEvent(new CustomEvent('open-booking-detail', { detail: { id: {{ $item['booking_id'] ?? $item['id'] }} } }));"
                                               class="inline-flex items-center gap-1 text-[11px] font-bold px-3 py-1 bg-teal-50 text-teal-800 hover:bg-teal-100 border border-teal-200 rounded-xl transition cursor-pointer">
                                                <span>{{ $item['action_label'] }}</span>
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            @empty
                                <div class="py-8 text-center">
                                    <div class="w-10 h-10 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-2">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    </div>
                                    <p class="text-xs font-bold text-slate-800">No Upcoming Schedule</p>
                                    <p class="text-[11px] text-slate-400 mt-0.5">No tent loading operations in the next 2 days.</p>
                                </div>
                            @endforelse
                        </div>

                        <!-- Footer Dropdown -->
                        <div class="pt-3 mt-2 border-t border-slate-100 flex items-center justify-between">
                            <span class="text-[10px] text-slate-400">Total: {{ $notifs['total_count'] }} events</span>
                            <a href="{{ route('admin.bookings.index', ['tab' => 'bookings']) }}"
                               @click="notifOpen = false; if (typeof $store !== 'undefined' && $store.nav) $store.nav.setTab('bookings')"
                               class="text-xs font-bold text-brand-600 hover:text-brand-700 flex items-center gap-1 transition">
                                <span>View All Bookings</span>
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                            </a>
                        </div>
                    </div>
                </div>
