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
                <span class="inline-block mt-1.5 px-3 py-0.5 rounded-full text-[10px] font-bold bg-brand-50 text-brand-700">
                    NK Barokah Owner
                </span>
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
