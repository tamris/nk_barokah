@extends('admin.layout')

@section('title', 'Katalog Paket & Layanan')
@section('page_title', 'Paket & Layanan WO')

@section('content')
<div class="space-y-6 sm:space-y-8" x-data="{ 
    showPackageModal: false, 
    showAddonModal: false, 
    editPackageData: null, 
    editAddonData: null 
}">

    <!-- ============================================================== -->
    <!-- 1. MASTER PAKET UTAMA PERNIKAHAN                               -->
    <!-- ============================================================== -->
    <div class="space-y-3.5 sm:space-y-4">
        <div class="flex flex-col xs:flex-row xs:items-center justify-between gap-3">
            <div>
                <h2 class="text-xs font-extrabold text-zinc-900 uppercase tracking-widest flex items-center gap-2">
                    <span>Paket Utama Pernikahan</span>
                    <span class="w-1.5 h-1.5 rounded-full bg-coral-500"></span>
                </h2>
                <p class="text-[11px] sm:text-xs text-zinc-400 mt-0.5">Paket inti yang menjadi fondasi penawaran calon pengantin.</p>
            </div>
            
            <button @click="showPackageModal = true; editPackageData = null"
                    class="self-start xs:self-auto px-3.5 sm:px-4 py-2 sm:py-2.5 rounded-xl bg-coral-500 hover:bg-coral-600 text-white text-xs font-bold transition inline-flex items-center gap-1.5 shadow-sm shadow-coral-500/25 active:scale-95">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
                <span>Tambah Paket Baru</span>
            </button>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3.5 sm:gap-5">
            @foreach($packages as $pkg)
                <div class="bg-white rounded-3xl border {{ $pkg->is_active ? 'border-[#ECE8E1] shadow-card' : 'border-[#ECE8E1] bg-[#FAF8F5]/80 opacity-70' }} p-5 sm:p-6 space-y-4 flex flex-col justify-between transition hover:border-coral-300">
                    <div>
                        <div class="flex items-start justify-between gap-2">
                            <h3 class="font-extrabold text-sm sm:text-base text-zinc-900 leading-snug">{{ $pkg->name }}</h3>
                            <span class="shrink-0 px-2.5 py-0.5 rounded-full text-[10px] font-bold {{ $pkg->is_active ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-zinc-100 text-zinc-500 border border-zinc-200' }}">
                                {{ $pkg->is_active ? 'Aktif' : 'Nonaktif' }}
                            </span>
                        </div>
                        
                        <div class="mt-2.5">
                            <span class="text-xl sm:text-2xl font-extrabold text-zinc-900 font-mono tracking-tight">
                                Rp {{ number_format($pkg->base_price, 0, ',', '.') }}
                            </span>
                        </div>

                        <p class="text-xs text-zinc-500 mt-2.5 line-clamp-3 leading-relaxed">
                            {{ $pkg->description ?? 'Belum ada rincian fasilitas tambahan.' }}
                        </p>
                    </div>

                    <div class="pt-4 border-t border-[#F2EFEA] flex items-center justify-between gap-2 text-xs">
                        <button @click="editPackageData = {{ json_encode($pkg) }}; showPackageModal = true"
                                class="font-bold text-zinc-700 hover:text-coral-600 transition px-2 py-1 rounded-lg hover:bg-zinc-50">
                            Ubah Data
                        </button>

                        <form action="{{ route('admin.packages.toggle', $pkg->id) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="font-bold px-2 py-1 rounded-lg {{ $pkg->is_active ? 'text-zinc-400 hover:text-rose-600 hover:bg-rose-50' : 'text-emerald-600 hover:text-emerald-700 hover:bg-emerald-50' }} transition">
                                {{ $pkg->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- ============================================================== -->
    <!-- 2. MASTER FASILITAS TAMBAHAN (ADD-ONS)                          -->
    <!-- RESPONSIVE: CARD LIST DI HP, TABEL DI DESKTOP                  -->
    <!-- ============================================================== -->
    <div class="space-y-3.5 sm:space-y-4 pt-4 sm:pt-6 border-t border-[#ECE8E1]">
        <div class="flex flex-col xs:flex-row xs:items-center justify-between gap-3">
            <div>
                <h2 class="text-xs font-extrabold text-zinc-900 uppercase tracking-widest flex items-center gap-2">
                    <span>Fasilitas Tambahan (Add-ons)</span>
                    <span class="w-1.5 h-1.5 rounded-full bg-coral-500"></span>
                </h2>
                <p class="text-[11px] sm:text-xs text-zinc-400 mt-0.5">Pilihan fasilitas atau perlengkapan ekstra per unit/item.</p>
            </div>
            
            <button @click="showAddonModal = true; editAddonData = null"
                    class="self-start xs:self-auto px-3.5 sm:px-4 py-2 sm:py-2.5 rounded-xl bg-coral-500 hover:bg-coral-600 text-white text-xs font-bold transition inline-flex items-center gap-1.5 shadow-sm shadow-coral-500/25 active:scale-95">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
                <span>Tambah Fasilitas Baru</span>
            </button>
        </div>

        <!-- 2A. LIST VIEW DI HP (RAMAH SMARTPHONE) -->
        <div class="block lg:hidden space-y-3">
            @forelse($addons as $addon)
                <div class="bg-white rounded-2xl border border-[#ECE8E1] p-4 space-y-3 shadow-card">
                    <div class="flex items-start justify-between gap-2">
                        <div>
                            <h3 class="font-bold text-zinc-900 text-xs sm:text-sm leading-snug">{{ $addon->name }}</h3>
                            <span class="text-[11px] text-zinc-400 font-mono">Satuan: {{ $addon->unit }}</span>
                        </div>
                        <span class="shrink-0 px-2 py-0.5 rounded-full text-[10px] font-bold {{ $addon->is_active ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-zinc-100 text-zinc-500 border border-zinc-200' }}">
                            {{ $addon->is_active ? 'Aktif' : 'Nonaktif' }}
                        </span>
                    </div>

                    <div class="flex items-baseline justify-between pt-1 border-t border-[#F2EFEA]">
                        <div>
                            <span class="text-[10px] text-zinc-400 block">Harga Satuan</span>
                            <span class="font-mono font-extrabold text-zinc-900 text-sm">
                                Rp {{ number_format($addon->unit_price, 0, ',', '.') }}
                            </span>
                        </div>

                        <div class="flex items-center gap-2">
                            <button @click="editAddonData = {{ json_encode($addon) }}; showAddonModal = true"
                                    class="px-2.5 py-1.5 rounded-lg bg-zinc-100 hover:bg-zinc-200 text-xs font-bold text-zinc-700 transition">
                                Ubah
                            </button>

                            <form action="{{ route('admin.addons.toggle', $addon->id) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="px-2.5 py-1.5 rounded-lg text-xs font-bold {{ $addon->is_active ? 'bg-rose-50 text-rose-700 hover:bg-rose-100' : 'bg-emerald-50 text-emerald-700 hover:bg-emerald-100' }} transition">
                                    {{ $addon->is_active ? 'Matikan' : 'Aktifkan' }}
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                <div class="bg-white rounded-2xl border border-[#ECE8E1] p-8 text-center text-zinc-400 text-xs">
                    Belum ada fasilitas tambahan yang dibuat.
                </div>
            @endforelse
        </div>

        <!-- 2B. TABEL VIEW DI DESKTOP -->
        <div class="hidden lg:block bg-white rounded-3xl border border-[#ECE8E1] shadow-card overflow-hidden">
            <table class="w-full text-left text-xs border-collapse">
                <thead>
                    <tr class="border-b border-[#ECE8E1] text-[11px] font-bold uppercase text-zinc-400 tracking-wider bg-[#FAF8F5]">
                        <th class="py-4 px-6">Nama Fasilitas</th>
                        <th class="py-4 px-6">Satuan Unit</th>
                        <th class="py-4 px-6">Harga Satuan</th>
                        <th class="py-4 px-6">Status Layanan</th>
                        <th class="py-4 px-6 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#F2EFEA]">
                    @foreach($addons as $addon)
                        <tr class="hover:bg-[#FAF8F5]/80 transition">
                            <td class="py-4 px-6 font-bold text-zinc-900">{{ $addon->name }}</td>
                            <td class="py-4 px-6 text-zinc-500 font-mono text-[11px]">{{ $addon->unit }}</td>
                            <td class="py-4 px-6 font-bold text-zinc-900 font-mono">Rp {{ number_format($addon->unit_price, 0, ',', '.') }}</td>
                            <td class="py-4 px-6">
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold {{ $addon->is_active ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-zinc-100 text-zinc-500 border border-zinc-200' }}">
                                    {{ $addon->is_active ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </td>
                            <td class="py-4 px-6 text-right space-x-3">
                                <button @click="editAddonData = {{ json_encode($addon) }}; showAddonModal = true"
                                        class="text-xs font-bold text-zinc-700 hover:text-coral-600 transition">
                                    Ubah
                                </button>
                                <form action="{{ route('admin.addons.toggle', $addon->id) }}" method="POST" class="inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="text-xs font-bold {{ $addon->is_active ? 'text-zinc-400 hover:text-rose-600' : 'text-emerald-600 hover:text-emerald-700' }} transition">
                                        {{ $addon->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- ============================================================== -->
    <!-- MODAL FORM PAKET (RESPONSIVE BOTTOM SHEET DI HP)               -->
    <!-- ============================================================== -->
    <div x-show="showPackageModal" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex items-end sm:items-center justify-center p-0 sm:p-4 bg-zinc-950/70 backdrop-blur-xs" 
         x-cloak>
        
        <div class="relative bg-white rounded-t-3xl sm:rounded-3xl max-w-md w-full p-6 sm:p-7 shadow-2xl border border-[#ECE8E1] space-y-5" 
             @click.away="showPackageModal = false">
            
            <!-- Handle Drag di HP -->
            <div class="w-12 h-1 bg-zinc-200 rounded-full mx-auto -mt-1 mb-2 sm:hidden"></div>

            <!-- Tombol Close -->
            <button @click="showPackageModal = false" 
                    class="absolute top-5 right-5 w-7 h-7 rounded-full bg-zinc-100 hover:bg-zinc-200 text-zinc-700 flex items-center justify-center font-bold text-xs transition"
                    title="Tutup">
                ✕
            </button>

            <div>
                <h3 class="text-base font-extrabold text-zinc-900 tracking-tight" x-text="editPackageData ? 'Ubah Paket Pernikahan' : 'Tambah Paket Baru'"></h3>
                <p class="text-xs text-zinc-400 mt-0.5">Kelola tarif dasar dan fasilitas utama paket WO.</p>
            </div>
            
            <form :action="editPackageData ? '{{ url('admin/packages') }}/' + editPackageData.id : '{{ route('admin.packages.store') }}'" method="POST" class="space-y-4 text-xs">
                @csrf
                <template x-if="editPackageData">
                    <input type="hidden" name="_method" value="PUT">
                </template>

                <div>
                    <label class="block font-bold text-zinc-700 mb-1.5">Nama Paket</label>
                    <input type="text" name="name" required :value="editPackageData ? editPackageData.name : ''"
                           class="w-full px-3.5 py-3 rounded-2xl border border-[#ECE8E1] text-xs focus:ring-2 focus:ring-coral-500/20 focus:border-coral-500 focus:outline-none"
                           placeholder="Contoh: Paket Platinum Luxury">
                </div>

                <div>
                    <label class="block font-bold text-zinc-700 mb-1.5">Harga Dasar (Rp)</label>
                    <input type="number" name="base_price" required :value="editPackageData ? editPackageData.base_price : ''"
                           class="w-full px-3.5 py-3 rounded-2xl border border-[#ECE8E1] text-xs focus:ring-2 focus:ring-coral-500/20 focus:border-coral-500 focus:outline-none"
                           placeholder="25000000">
                </div>

                <div>
                    <label class="block font-bold text-zinc-700 mb-1.5">Deskripsi Fasilitas</label>
                    <textarea name="description" rows="3" :value="editPackageData ? editPackageData.description : ''"
                              class="w-full px-3.5 py-3 rounded-2xl border border-[#ECE8E1] text-xs focus:ring-2 focus:ring-coral-500/20 focus:border-coral-500 focus:outline-none"
                              placeholder="Rincian fasilitas dan layanan yang didapatkan pengantin..."></textarea>
                </div>

                <div class="flex items-center justify-end gap-2 pt-3 border-t border-[#F2EFEA]">
                    <button type="button" @click="showPackageModal = false" class="px-4 py-2.5 rounded-xl text-xs font-semibold text-zinc-500 hover:bg-zinc-100">Batal</button>
                    <button type="submit" class="px-5 py-2.5 rounded-xl text-xs font-bold bg-coral-500 hover:bg-coral-600 text-white transition shadow-sm shadow-coral-500/25">Simpan Paket</button>
                </div>
            </form>
        </div>
    </div>

    <!-- ============================================================== -->
    <!-- MODAL FORM ADDON (RESPONSIVE BOTTOM SHEET DI HP)                -->
    <!-- ============================================================== -->
    <div x-show="showAddonModal" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex items-end sm:items-center justify-center p-0 sm:p-4 bg-zinc-950/70 backdrop-blur-xs" 
         x-cloak>
        
        <div class="relative bg-white rounded-t-3xl sm:rounded-3xl max-w-md w-full p-6 sm:p-7 shadow-2xl border border-[#ECE8E1] space-y-5" 
             @click.away="showAddonModal = false">
            
            <!-- Handle Drag di HP -->
            <div class="w-12 h-1 bg-zinc-200 rounded-full mx-auto -mt-1 mb-2 sm:hidden"></div>

            <!-- Tombol Close -->
            <button @click="showAddonModal = false" 
                    class="absolute top-5 right-5 w-7 h-7 rounded-full bg-zinc-100 hover:bg-zinc-200 text-zinc-700 flex items-center justify-center font-bold text-xs transition"
                    title="Tutup">
                ✕
            </button>

            <div>
                <h3 class="text-base font-extrabold text-zinc-900 tracking-tight" x-text="editAddonData ? 'Ubah Fasilitas Tambahan' : 'Tambah Fasilitas Baru'"></h3>
                <p class="text-xs text-zinc-400 mt-0.5">Kelola item fasilitas tambahan per unit.</p>
            </div>
            
            <form :action="editAddonData ? '{{ url('admin/addons') }}/' + editAddonData.id : '{{ route('admin.addons.store') }}'" method="POST" class="space-y-4 text-xs">
                @csrf
                <template x-if="editAddonData">
                    <input type="hidden" name="_method" value="PUT">
                </template>

                <div>
                    <label class="block font-bold text-zinc-700 mb-1.5">Nama Fasilitas Tambahan</label>
                    <input type="text" name="name" required :value="editAddonData ? editAddonData.name : ''"
                           class="w-full px-3.5 py-3 rounded-2xl border border-[#ECE8E1] text-xs focus:ring-2 focus:ring-coral-500/20 focus:border-coral-500 focus:outline-none"
                           placeholder="Contoh: Panggung Hiburan Musik Akustik">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-bold text-zinc-700 mb-1.5">Satuan Unit</label>
                        <input type="text" name="unit" required :value="editAddonData ? editAddonData.unit : 'pcs'"
                               class="w-full px-3.5 py-3 rounded-2xl border border-[#ECE8E1] text-xs focus:ring-2 focus:ring-coral-500/20 focus:border-coral-500 focus:outline-none"
                               placeholder="pcs, set, sesi">
                    </div>
                    <div>
                        <label class="block font-bold text-zinc-700 mb-1.5">Harga Satuan (Rp)</label>
                        <input type="number" name="unit_price" required :value="editAddonData ? editAddonData.unit_price : ''"
                               class="w-full px-3.5 py-3 rounded-2xl border border-[#ECE8E1] text-xs focus:ring-2 focus:ring-coral-500/20 focus:border-coral-500 focus:outline-none"
                               placeholder="1500000">
                    </div>
                </div>

                <div class="flex items-center justify-end gap-2 pt-3 border-t border-[#F2EFEA]">
                    <button type="button" @click="showAddonModal = false" class="px-4 py-2.5 rounded-xl text-xs font-semibold text-zinc-500 hover:bg-zinc-100">Batal</button>
                    <button type="submit" class="px-5 py-2.5 rounded-xl text-xs font-bold bg-coral-500 hover:bg-coral-600 text-white transition shadow-sm shadow-coral-500/25">Simpan Fasilitas</button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
