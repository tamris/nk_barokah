    <!-- ============================================================== -->
    <!-- MODAL PROFIL OWNER & LOGOUT                                    -->
    <!-- ============================================================== -->
    <div x-show="profileModalOpen"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         class="fixed inset-0 z-50 flex items-end sm:items-center justify-center p-0 sm:p-4 bg-slate-950/50 backdrop-blur-xs"
         x-cloak>
        <div class="w-full sm:max-w-xs bg-white rounded-t-3xl sm:rounded-3xl border border-[#EAF0EE] p-6 space-y-4 shadow-2xl text-center"
             @click.away="profileModalOpen = false">
            <img src="https://ui-avatars.com/api/?name={{ urlencode($ownerName) }}&background=045D56&color=ffffff&bold=true&size=128"
                 alt="{{ $ownerName }}"
                 class="w-16 h-16 rounded-full mx-auto ring-4 ring-brand-50">
            <div>
                <h3 class="text-sm font-bold text-slate-900">{{ $ownerName }}</h3>
                <p class="text-xs text-slate-400">{{ $ownerEmail }}</p>
                <span class="inline-block mt-1.5 px-3 py-0.5 rounded-full text-[10px] font-bold {{ ($isOwner ?? false) ? 'bg-brand-50 text-brand-700' : 'bg-teal-50 text-teal-800 border border-teal-200' }}">
                    {{ $roleLabel ?? 'NK Barokah Owner' }}
                </span>
            </div>
            <!-- Appearance Quick Selector (Accessible for Owner & Field Coordinator) -->
            <div class="pt-3 border-t border-slate-100 space-y-2 text-left">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Appearance</span>
                    <span class="text-[10px] font-medium text-slate-400"
                          x-text="$store.theme.preference === 'system' ? 'Device (' + ($store.theme.systemDark ? 'Dark' : 'Light') + ')' : ($store.theme.preference === 'dark' ? 'Dark' : 'Light')"></span>
                </div>
                <div role="radiogroup" aria-label="Appearance" class="grid grid-cols-3 gap-1 p-1 rounded-2xl bg-[#F4F7F6] border border-[#E5ECE9]">
                    <button type="button"
                            role="radio"
                            :aria-checked="$store.theme.preference === 'light'"
                            @click="$store.theme.set('light')"
                            :class="$store.theme.preference === 'light' ? 'bg-brand-600 text-white shadow-xs font-bold' : 'text-slate-600 hover:text-slate-900 font-medium'"
                            class="py-1.5 rounded-xl text-[11px] transition flex items-center justify-center gap-1 cursor-pointer">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                        <span>Light</span>
                    </button>
                    <button type="button"
                            role="radio"
                            :aria-checked="$store.theme.preference === 'dark'"
                            @click="$store.theme.set('dark')"
                            :class="$store.theme.preference === 'dark' ? 'bg-brand-600 text-white shadow-xs font-bold' : 'text-slate-600 hover:text-slate-900 font-medium'"
                            class="py-1.5 rounded-xl text-[11px] transition flex items-center justify-center gap-1 cursor-pointer">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg>
                        <span>Dark</span>
                    </button>
                    <button type="button"
                            role="radio"
                            :aria-checked="$store.theme.preference === 'system'"
                            @click="$store.theme.set('system')"
                            :class="$store.theme.preference === 'system' ? 'bg-brand-600 text-white shadow-xs font-bold' : 'text-slate-600 hover:text-slate-900 font-medium'"
                            class="py-1.5 rounded-xl text-[11px] transition flex items-center justify-center gap-1 cursor-pointer">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        <span>System</span>
                    </button>
                </div>
            </div>

            <div class="pt-3 border-t border-slate-100 space-y-2">
                <form action="{{ route('admin.logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full py-2.5 rounded-full bg-rose-50 text-rose-600 hover:bg-rose-100 text-xs font-bold transition cursor-pointer">
                        Sign Out
                    </button>
                </form>
                <button type="button" @click="profileModalOpen = false" class="w-full py-2 text-xs font-medium text-slate-400 hover:text-slate-700 cursor-pointer">
                    Close
                </button>
            </div>
        </div>
    </div>
