    <!-- ============================================================== -->
    <!-- MOBILE FLOATING BOTTOM NAVIGATION BAR (KHUSUS HP)              -->
    <!-- ============================================================== -->
    <nav class="fixed bottom-4 sm:bottom-6 inset-x-4 max-w-[370px] sm:max-w-md mx-auto z-40 lg:hidden bg-white/95 backdrop-blur-xl rounded-full h-[62px] px-2.5 shadow-[0_12px_36px_rgba(0,0,0,0.12)] border border-[#DFE7E4] flex items-center justify-between">
        @php
            $mobileTabs = [
                ['key' => 'dashboard', 'label' => 'Dashboard', 'icon' => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6'],
                ['key' => 'bookings',  'label' => 'Bookings',  'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2'],
                ['key' => 'calendar',  'label' => 'Calendar',  'icon' => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z'],
            ];
            if ($isOwner ?? false) {
                $mobileTabs[] = ['key' => 'invoice', 'label' => 'Invoice', 'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'];
            }
        @endphp
        @foreach($mobileTabs as $mTab)
            @if($isDashboardPage)
                <button type="button"
                        @click="$store.nav.setTab('{{ $mTab['key'] }}')"
                        :class="$store.nav.tab === '{{ $mTab['key'] }}' 
                            ? 'bg-brand-600 text-white shadow-md font-bold px-4 h-11 rounded-full' 
                            : 'text-slate-400 hover:text-slate-700 w-11 h-11 rounded-full hover:bg-slate-50'"
                        class="flex items-center justify-center gap-2 text-xs transition-all active:scale-95 cursor-pointer shrink-0">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $mTab['icon'] }}"/>
                    </svg>
                    <span x-show="$store.nav.tab === '{{ $mTab['key'] }}'"
                          x-cloak
                          x-transition:enter="transition ease-out duration-150"
                          x-transition:enter-start="opacity-0 scale-95"
                          x-transition:enter-end="opacity-100 scale-100"
                          class="whitespace-nowrap">{{ $mTab['label'] }}</span>
                </button>
            @else
                <a href="{{ route('admin.bookings.index') }}"
                   class="flex items-center justify-center w-11 h-11 rounded-full text-slate-400 hover:text-slate-700 hover:bg-slate-50 transition-all">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $mTab['icon'] }}"/>
                    </svg>
                </a>
            @endif
        @endforeach
    </nav>
