@extends('admin.layout')

@section('title', 'Detail Reservasi #' . $booking->invoice_number)
@section('page_title', 'Detail Reservasi')

@section('content')
<div class="space-y-4 sm:space-y-6 pb-20 lg:pb-0" 
     x-data="{ 
         imageModal: false,
         confirmModal: {
             open: false,
             title: '',
             message: '',
             subtext: '',
             actionUrl: '',
             btnText: '',
             btnClass: 'bg-emerald-600 hover:bg-emerald-700 text-white',
             iconType: 'check'
         },
         triggerConfirm(title, message, subtext, actionUrl, btnText, btnClass = 'bg-emerald-600 hover:bg-emerald-700 text-white', iconType = 'check') {
             this.confirmModal = {
                 open: true,
                 title: title,
                 message: message,
                 subtext: subtext,
                 actionUrl: actionUrl,
                 btnText: btnText,
                 btnClass: btnClass,
                 iconType: iconType
             };
         }
     }">

    <!-- Navigasi Balik & Status Hero Card -->
    <div class="bg-white rounded-3xl border border-[#ECE8E1] p-4 sm:p-6 shadow-card space-y-4">
        
        <div class="flex items-center justify-between">
            <a href="{{ route('admin.bookings.index') }}" 
               class="inline-flex items-center gap-1.5 text-xs font-bold text-zinc-500 hover:text-coral-600 transition bg-zinc-50 px-3 py-1.5 rounded-xl border border-zinc-200/60">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                <span>Kembali ke Reservasi</span>
            </a>

            <!-- Badge Status -->
            <div>
                @if($booking->status === 'confirmed')
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-extrabold bg-emerald-50 text-emerald-700 border border-emerald-200">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                        <span>Dikonfirmasi & Sah</span>
                    </span>
                @elseif($booking->status === 'pending_verification')
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-extrabold bg-coral-50 text-coral-700 border border-coral-200 animate-pulse">
                        <span class="w-1.5 h-1.5 rounded-full bg-coral-500"></span>
                        <span>Perlu Verifikasi Mutasi</span>
                    </span>
                @elseif($booking->status === 'completed')
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-extrabold bg-emerald-50 text-emerald-700 border border-emerald-200">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                        <span>Acara Selesai</span>
                    </span>
                @else
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-extrabold bg-zinc-100 text-zinc-600 border border-zinc-200">
                        <span class="w-1.5 h-1.5 rounded-full bg-zinc-400"></span>
                        <span>Ditolak</span>
                    </span>
                @endif
            </div>
        </div>

        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pt-1 border-t border-[#F2EFEA]">
            <div>
                <div class="flex items-center gap-2">
                    <span class="text-xl sm:text-2xl font-extrabold text-zinc-900 font-mono tracking-tight">#{{ $booking->invoice_number }}</span>
                    <span class="text-xs text-zinc-400">({{ $booking->created_at->isoFormat('D MMM Y, HH:mm') }} WIB)</span>
                </div>
                <p class="text-xs text-zinc-500 mt-0.5">
                    Calon Pengantin: <strong class="text-zinc-900 font-bold">{{ $booking->customer_name }}</strong> &bull; Paket: <span class="text-coral-600 font-bold">{{ $booking->package_name }}</span>
                </p>
            </div>

            <!-- Tombol Aksi Desktop -->
            <div class="hidden sm:flex items-center gap-2 shrink-0">
                <a href="{{ route('booking.receipt', $booking->invoice_number) }}" target="_blank"
                   class="px-3.5 py-2 rounded-xl border border-[#ECE8E1] bg-white hover:bg-zinc-50 text-zinc-700 text-xs font-bold transition inline-flex items-center gap-1.5 shadow-2xs">
                    <svg class="w-3.5 h-3.5 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    <span>Struk PDF</span>
                </a>

                <a href="{{ $waConfirmUrl }}" target="_blank"
                   class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold transition inline-flex items-center gap-1.5 shadow-2xs">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                    <span>Chat WhatsApp</span>
                </a>

                @if($booking->status === 'pending_verification')
                    <button type="button" 
                            @click="triggerConfirm(
                                'Verifikasi Bukti Pembayaran',
                                'Apakah Anda telah memeriksa mutasi rekening dan memvalidasi pembayaran sebesar Rp {{ number_format($booking->amount_paid, 0, ',', '.') }} dari {{ addslashes($booking->customer_name) }}?',
                                'Setelah diverifikasi, jadwal pernikahan akan resmi Dikonfirmasi & Sah, serta otomatis disinkronkan ke Google Calendar & Sheets.',
                                '{{ route('admin.bookings.confirm', $booking->id) }}',
                                '✓ Ya, Setujui & Konfirmasi',
                                'bg-coral-500 hover:bg-coral-600 text-white',
                                'check'
                            )"
                            class="px-4 py-2 rounded-xl bg-coral-500 hover:bg-coral-600 text-white text-xs font-bold transition shadow-sm shadow-coral-500/25">
                        ✓ Verifikasi Mutasi
                    </button>

                    <button type="button" 
                            @click="triggerConfirm(
                                'Tolak Reservasi Pernikahan',
                                'Apakah Anda yakin ingin menolak reservasi #{{ $booking->invoice_number }} atas nama {{ addslashes($booking->customer_name) }}?',
                                'Status reservasi akan diubah menjadi Ditolak dan pemesanan jadwal akan dibatalkan.',
                                '{{ route('admin.bookings.reject', $booking->id) }}',
                                'Tolak Reservasi',
                                'bg-rose-600 hover:bg-rose-700 text-white',
                                'warning'
                            )"
                            class="px-3.5 py-2 rounded-xl border border-rose-200 text-rose-700 bg-rose-50 hover:bg-rose-100 text-xs font-bold transition">
                        Tolak
                    </button>
                @elseif($booking->status === 'confirmed')
                    @if($booking->remaining_balance > 0)
                        <button type="button" 
                                @click="triggerConfirm(
                                    'Konfirmasi Pelunasan Tagihan',
                                    'Apakah Anda yakin ingin mencatat pelunasan sisa tagihan sebesar Rp {{ number_format($booking->remaining_balance, 0, ',', '.') }} untuk pengantin {{ addslashes($booking->customer_name) }}?',
                                    'Pastikan dana pelunasan telah valid masuk ke rekening bank. Status pembayaran akan langsung diperbarui menjadi LUNAS (100%).',
                                    '{{ route('admin.bookings.settle', $booking->id) }}',
                                    'Ya, Catat Pelunasan',
                                    'bg-emerald-600 hover:bg-emerald-700 text-white',
                                    'check'
                                )"
                                class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold transition shadow-sm inline-flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                            <span>Catat Pelunasan</span>
                        </button>
                    @endif

                    <button type="button" 
                            @click="triggerConfirm(
                                'Konfirmasi Penyelesaian Acara',
                                'Apakah seluruh rangkaian acara pernikahan {{ addslashes($booking->customer_name) }} telah selesai dilaksanakan dengan baik?',
                                'Status acara akan ditandai Selesai dan status pembayaran otomatis dipastikan LUNAS 100%.',
                                '{{ route('admin.bookings.complete', $booking->id) }}',
                                'Ya, Tandai Acara Selesai',
                                'bg-zinc-900 hover:bg-zinc-800 text-white',
                                'check'
                            )"
                            class="px-4 py-2 rounded-xl bg-zinc-900 hover:bg-zinc-800 text-white text-xs font-bold transition shadow-2xs">
                        Tandai Acara Selesai
                    </button>
                @endif
            </div>
        </div>

    </div>

    <!-- Grid Konten: Informasi Lengkap (Responsive 1 Kolom di HP, 3 Kolom di Laptop) -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 sm:gap-6">
        
        <!-- Sisi Kiri: Rincian Klien & Layanan (2 Kolom) -->
        <div class="lg:col-span-2 space-y-4 sm:space-y-6">
            
            <!-- Kartu 1: Data Pengantin & Jadwal Acara -->
            <div class="bg-white rounded-3xl border border-[#ECE8E1] shadow-card p-4 sm:p-6 space-y-4">
                <h3 class="text-xs font-bold text-zinc-900 uppercase tracking-widest pb-3 border-b border-[#F2EFEA] flex items-center justify-between">
                    <span>Informasi Calon Pengantin & Jadwal</span>
                    <span class="text-coral-600 font-mono text-[10px] lowercase">#data-klien</span>
                </h3>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5 sm:gap-4 text-xs">
                    
                    <div class="p-3 bg-[#FAF8F5] rounded-2xl border border-[#ECE8E1]">
                        <span class="text-zinc-400 block mb-0.5">Nama Lengkap Pengantin</span>
                        <span class="font-extrabold text-zinc-900 text-sm block">{{ $booking->customer_name }}</span>
                    </div>

                    <div class="p-3 bg-[#FAF8F5] rounded-2xl border border-[#ECE8E1]">
                        <span class="text-zinc-400 block mb-0.5">Nomor WhatsApp</span>
                        <div class="flex items-center justify-between">
                            <span class="font-mono text-zinc-900 font-extrabold text-sm block">{{ $booking->customer_phone }}</span>
                            <a href="{{ $booking->whatsapp_url }}" target="_blank"
                               class="text-[11px] font-bold text-emerald-700 bg-emerald-100/70 px-2 py-0.5 rounded-lg hover:bg-emerald-200">
                                Chat WA ↗
                            </a>
                        </div>
                    </div>

                    <div class="p-3 bg-[#FAF8F5] rounded-2xl border border-[#ECE8E1]">
                        <span class="text-zinc-400 block mb-0.5">Tanggal Acara Pernikahan</span>
                        <span class="font-extrabold text-zinc-900 text-sm block">
                            {{ $booking->formatted_event_date }}
                        </span>
                        <span class="text-[11px] text-coral-600 font-semibold block mt-0.5">
                            {{ \Carbon\Carbon::parse($booking->event_date)->diffForHumans() }}
                        </span>
                    </div>

                    <div class="p-3 bg-[#FAF8F5] rounded-2xl border border-[#ECE8E1]">
                        <span class="text-zinc-400 block mb-0.5">Alamat Email</span>
                        <span class="text-zinc-800 font-mono block">{{ $booking->customer_email ?? '-' }}</span>
                    </div>

                </div>
            </div>

            <!-- Kartu 2: Rincian Paket & Add-on (Tabel Bersih) -->
            <div class="bg-white rounded-3xl border border-[#ECE8E1] shadow-card p-4 sm:p-6 space-y-4">
                <h3 class="text-xs font-bold text-zinc-900 uppercase tracking-widest pb-3 border-b border-[#F2EFEA]">
                    Rincian Paket & Fasilitas Tambahan
                </h3>
                
                <div class="overflow-x-auto no-scrollbar">
                    <table class="w-full text-left text-xs border-collapse">
                        <thead>
                            <tr class="text-zinc-400 border-b border-[#F2EFEA] text-[10px] uppercase font-bold tracking-wider">
                                <th class="py-2.5">Item Layanan</th>
                                <th class="py-2.5 text-center">Qty</th>
                                <th class="py-2.5 text-right">Harga</th>
                                <th class="py-2.5 text-right">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#F2EFEA]">
                            <tr>
                                <td class="py-3 font-extrabold text-zinc-900">
                                    {{ $booking->package_name }}
                                    <span class="block text-[10px] text-zinc-400 font-normal">Paket Inti Wedding Organizer</span>
                                </td>
                                <td class="py-3 text-center text-zinc-600 font-bold">1</td>
                                <td class="py-3 text-right text-zinc-600 font-mono">Rp {{ number_format($booking->package_price, 0, ',', '.') }}</td>
                                <td class="py-3 text-right font-extrabold text-zinc-900 font-mono">Rp {{ number_format($booking->package_price, 0, ',', '.') }}</td>
                            </tr>
                            @foreach($booking->addons as $addon)
                                <tr>
                                    <td class="py-3 font-semibold text-zinc-800">
                                        {{ $addon->addon_name }}
                                        <span class="block text-[10px] text-coral-600 font-normal">Fasilitas Tambahan</span>
                                    </td>
                                    <td class="py-3 text-center text-zinc-600 font-bold">{{ $addon->quantity }}</td>
                                    <td class="py-3 text-right text-zinc-600 font-mono">Rp {{ number_format($addon->unit_price, 0, ',', '.') }}</td>
                                    <td class="py-3 text-right font-extrabold text-zinc-900 font-mono">Rp {{ number_format($addon->subtotal, 0, ',', '.') }}</td>
                                </tr>
                            @endforeach
                            @if($booking->additional_day_charge > 0)
                                <tr>
                                    <td class="py-3 font-semibold text-amber-800">
                                        Tambahan Durasi Acara ({{ max(1, $booking->duration_days - 2) }} hari ekstra @ 15%)
                                        <span class="block text-[10px] text-amber-600 font-normal">Charge Durasi Tambahan (> 2 hari)</span>
                                    </td>
                                    <td class="py-3 text-center text-amber-800 font-bold">{{ max(1, $booking->duration_days - 2) }} hari</td>
                                    <td class="py-3 text-right text-amber-800 font-mono">Rp {{ number_format($booking->package_price * 0.15, 0, ',', '.') }}</td>
                                    <td class="py-3 text-right font-extrabold text-amber-900 font-mono">Rp {{ number_format($booking->additional_day_charge, 0, ',', '.') }}</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>

                <!-- Box Finansial Ringkasan -->
                <div class="border-t border-[#ECE8E1] pt-4 space-y-2 text-xs bg-[#FAF8F5] p-4 rounded-2xl">
                    <div class="flex justify-between text-zinc-600">
                        <span>Total Nilai Pemesanan (Grand Total):</span>
                        <strong class="text-base font-extrabold text-zinc-900 font-mono">Rp {{ number_format($booking->grand_total, 0, ',', '.') }}</strong>
                    </div>
                    <div class="flex justify-between text-emerald-700">
                        <span>Telah Dibayar ({{ strtoupper($booking->payment_type) }}):</span>
                        <strong class="text-sm font-extrabold font-mono">Rp {{ number_format($booking->amount_paid, 0, ',', '.') }}</strong>
                    </div>
                    @if($booking->remaining_balance > 0)
                        <div class="flex justify-between text-coral-600 pt-1 border-t border-[#ECE8E1]">
                            <span>Sisa Tagihan Pelunasan:</span>
                            <strong class="text-sm font-extrabold font-mono">Rp {{ number_format($booking->remaining_balance, 0, ',', '.') }}</strong>
                        </div>
                    @else
                        <div class="flex justify-between text-emerald-700 pt-1 border-t border-[#ECE8E1]">
                            <span>Status Tagihan:</span>
                            <strong class="font-extrabold">LUNAS PENUH (100%)</strong>
                        </div>
                    @endif
                </div>
            </div>

        </div>

        <!-- Sisi Kanan: Bukti Transfer & Status Cloud (1 Kolom) -->
        <div class="space-y-4 sm:space-y-6">
            
            <!-- Kartu Bukti Pembayaran -->
            <div class="bg-white rounded-3xl border border-[#ECE8E1] shadow-card p-4 sm:p-6 space-y-3.5">
                <h3 class="text-xs font-bold text-zinc-900 uppercase tracking-widest pb-3 border-b border-[#F2EFEA]">
                    Bukti Transfer Rekening
                </h3>
                
                @php
                    $isPdf = strtolower(pathinfo($booking->payment_proof_path, PATHINFO_EXTENSION)) === 'pdf';
                    $fileUrl = asset('storage/' . $booking->payment_proof_path);
                @endphp

                <div class="rounded-2xl border border-[#ECE8E1] overflow-hidden bg-[#FAF8F5] text-center">
                    @if($isPdf)
                        <div class="p-6 space-y-3">
                            <span class="text-3xl block">📄</span>
                            <p class="text-xs font-bold text-zinc-700">Dokumen Bukti Transfer PDF</p>
                            <a href="{{ $fileUrl }}" target="_blank"
                               class="inline-block px-4 py-2 rounded-xl bg-zinc-900 text-white text-xs font-bold hover:bg-zinc-800 transition">
                                Buka Dokumen PDF ↗
                            </a>
                        </div>
                    @else
                        <a href="{{ $fileUrl }}" target="_blank" title="Klik untuk membuka ukuran penuh">
                            <img src="{{ $fileUrl }}" alt="Bukti Transfer" class="w-full object-cover max-h-80 hover:opacity-95 transition">
                        </a>
                        <div class="p-2.5 bg-[#FAF8F5] text-center border-t border-[#ECE8E1]">
                            <a href="{{ $fileUrl }}" target="_blank" class="text-xs font-bold text-coral-600 hover:underline inline-flex items-center gap-1">
                                <span>Buka Resolusi Penuh</span>
                                <span>↗</span>
                            </a>
                        </div>
                    @endif
                </div>

                @if($booking->status === 'pending_verification')
                    <div class="pt-2">
                        <button type="button" 
                                @click="triggerConfirm(
                                    'Verifikasi Bukti Pembayaran',
                                    'Apakah Anda telah memverifikasi mutasi bank dan menerima dana pembayaran sebesar Rp {{ number_format($booking->amount_paid, 0, ',', '.') }} dari {{ addslashes($booking->customer_name) }}?',
                                    'Setelah diverifikasi sah, jadwal pernikahan akan resmi terkunci di sistem dan disinkronkan ke Google Calendar & Sheets.',
                                    '{{ route('admin.bookings.confirm', $booking->id) }}',
                                    '✓ Ya, Setujui Pembayaran',
                                    'bg-coral-500 hover:bg-coral-600 text-white',
                                    'check'
                                )"
                                class="w-full py-3 rounded-2xl bg-coral-500 hover:bg-coral-600 text-white font-extrabold text-xs shadow-md shadow-coral-500/25 active:scale-95 transition">
                            ✓ Verifikasi & Setujui Pembayaran
                        </button>
                    </div>
                @endif
            </div>

            <!-- Kartu Status Sinkronisasi Google -->
            <div class="bg-white rounded-3xl border border-[#ECE8E1] shadow-card p-4 sm:p-6 space-y-3 text-xs">
                <h3 class="text-xs font-bold text-zinc-900 uppercase tracking-widest pb-3 border-b border-[#F2EFEA]">
                    Integrasi Cloud Google
                </h3>

                <div class="flex items-center justify-between">
                    <span class="text-zinc-500">Google Sheets:</span>
                    <span class="font-bold inline-flex items-center gap-1.5 {{ $booking->synced_to_sheets ? 'text-emerald-700' : 'text-coral-600' }}">
                        <span class="w-2 h-2 rounded-full {{ $booking->synced_to_sheets ? 'bg-emerald-500' : 'bg-coral-500' }}"></span>
                        {{ $booking->synced_to_sheets ? 'Tersinkron Otomatis' : 'Menunggu Antrean' }}
                    </span>
                </div>

                <div class="flex items-center justify-between">
                    <span class="text-zinc-500">Google Calendar:</span>
                    <span class="font-bold inline-flex items-center gap-1.5 {{ $booking->google_calendar_event_id ? 'text-emerald-700' : 'text-zinc-400' }}">
                        <span class="w-2 h-2 rounded-full {{ $booking->google_calendar_event_id ? 'bg-emerald-500' : 'bg-zinc-300' }}"></span>
                        {{ $booking->google_calendar_event_id ? 'Jadwal Terdaftar' : 'Belum Ada Jadwal' }}
                    </span>
                </div>
            </div>

        </div>

    </div>

    <!-- ============================================================== -->
    <!-- FLOATING STICKY ACTION BAR KHUSUS HP (JANGKAUAN JEMPOL OWNER)  -->
    <!-- ============================================================== -->
    <div class="fixed bottom-14 inset-x-0 z-30 bg-white/95 backdrop-blur-md p-3 border-t border-[#ECE8E1] flex items-center gap-2 lg:hidden shadow-lg">
        
        <a href="{{ $waConfirmUrl }}" target="_blank"
           class="flex-1 py-3 rounded-2xl bg-emerald-600 text-white font-bold text-xs flex items-center justify-center gap-1.5 active:scale-95 transition shadow-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
            <span>WhatsApp</span>
        </a>

        <a href="{{ route('booking.receipt', $booking->invoice_number) }}" target="_blank"
           class="py-3 px-4 rounded-2xl bg-zinc-100 text-zinc-700 font-bold text-xs active:scale-95 transition">
            Struk
        </a>

        @if($booking->status === 'pending_verification')
            <button type="button" 
                    @click="triggerConfirm(
                        'Verifikasi Bukti Pembayaran',
                        'Apakah Anda telah memverifikasi mutasi bank dan menerima pembayaran sebesar Rp {{ number_format($booking->amount_paid, 0, ',', '.') }} dari {{ addslashes($booking->customer_name) }}?',
                        'Jadwal acara akan resmi Dikonfirmasi & Sah serta disinkronkan ke Google Calendar & Sheets.',
                        '{{ route('admin.bookings.confirm', $booking->id) }}',
                        '✓ Ya, Verifikasi',
                        'bg-coral-500 hover:bg-coral-600 text-white',
                        'check'
                    )"
                    class="flex-1 py-3 rounded-2xl bg-coral-500 text-white font-bold text-xs active:scale-95 transition shadow-md shadow-coral-500/25">
                ✓ Verifikasi
            </button>
        @elseif($booking->status === 'confirmed')
            @if($booking->remaining_balance > 0)
                <button type="button" 
                        @click="triggerConfirm(
                            'Konfirmasi Pelunasan Tagihan',
                            'Apakah Anda yakin ingin mencatat pelunasan sisa tagihan sebesar Rp {{ number_format($booking->remaining_balance, 0, ',', '.') }} untuk calon pengantin {{ addslashes($booking->customer_name) }}?',
                            'Pastikan dana pelunasan telah masuk ke mutasi rekening bank. Status pembayaran akan menjadi LUNAS (100%).',
                            '{{ route('admin.bookings.settle', $booking->id) }}',
                            'Ya, Pelunasan',
                            'bg-emerald-600 hover:bg-emerald-700 text-white',
                            'check'
                        )"
                        class="flex-1 py-3 rounded-2xl bg-emerald-600 text-white font-bold text-xs active:scale-95 transition shadow-sm">
                    ✓ Pelunasan
                </button>
            @endif
            <button type="button" 
                    @click="triggerConfirm(
                        'Konfirmasi Acara Selesai',
                        'Apakah seluruh rangkaian acara pernikahan {{ addslashes($booking->customer_name) }} telah selesai dilaksanakan dengan lancar?',
                        'Status acara akan ditandai Selesai dan tagihan dipastikan LUNAS 100%.',
                        '{{ route('admin.bookings.complete', $booking->id) }}',
                        'Ya, Selesai',
                        'bg-zinc-900 hover:bg-zinc-800 text-white',
                        'check'
                    )"
                    class="flex-1 py-3 rounded-2xl bg-zinc-900 text-white font-bold text-xs active:scale-95 transition">
                Selesai
            </button>
        @endif

    </div>

    <!-- ============================================================== -->
    <!-- MODAL KONFIRMASI ELEGAN (MENGGANTIKAN BROWSER PROMPT NATIVE)   -->
    <!-- ============================================================== -->
    <div x-show="confirmModal.open" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-zinc-950/60 backdrop-blur-xs" 
         x-cloak
         @keydown.escape.window="confirmModal.open = false">
        
        <div class="bg-white rounded-3xl max-w-md w-full p-5 sm:p-6 shadow-2xl border border-[#ECE8E1] space-y-4"
             @click.away="confirmModal.open = false"
             x-transition:enter="transition ease-out duration-200 transform"
             x-transition:enter-start="scale-95 opacity-0"
             x-transition:enter-end="scale-100 opacity-100"
             x-transition:leave="transition ease-in duration-150 transform"
             x-transition:leave-start="scale-100 opacity-100"
             x-transition:leave-end="scale-95 opacity-0">
            
            <div class="flex items-start justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl flex items-center justify-center shrink-0 border"
                         :class="confirmModal.iconType === 'warning' ? 'bg-rose-50 text-rose-600 border-rose-100' : 'bg-emerald-50 text-emerald-600 border-emerald-100'">
                        <template x-if="confirmModal.iconType === 'warning'">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                        </template>
                        <template x-if="confirmModal.iconType !== 'warning'">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                        </template>
                    </div>
                    <div>
                        <h3 class="text-base font-extrabold text-zinc-900" x-text="confirmModal.title"></h3>
                        <p class="text-[11px] text-zinc-400">Konfirmasi Tindakan Reservasi</p>
                    </div>
                </div>
                <button type="button" @click="confirmModal.open = false" class="text-zinc-400 hover:text-zinc-600 p-1.5 rounded-xl hover:bg-zinc-100 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <div class="space-y-3">
                <p class="text-xs sm:text-sm font-semibold text-zinc-800 leading-relaxed" x-text="confirmModal.message"></p>
                <div class="p-3 bg-[#FAF8F5] rounded-2xl border border-[#ECE8E1] text-[11px] text-zinc-600 leading-normal" x-show="confirmModal.subtext">
                    <span x-text="confirmModal.subtext"></span>
                </div>
            </div>

            <form :action="confirmModal.actionUrl" method="POST" class="flex items-center gap-2.5 pt-2">
                @csrf
                <button type="button" @click="confirmModal.open = false" 
                        class="flex-1 py-2.5 px-4 rounded-xl border border-zinc-200 text-zinc-700 font-bold text-xs hover:bg-zinc-50 transition text-center">
                    Batal
                </button>
                <button type="submit" 
                        :class="confirmModal.btnClass"
                        class="flex-1 py-2.5 px-4 rounded-xl font-extrabold text-xs transition shadow-sm text-center">
                    <span x-text="confirmModal.btnText"></span>
                </button>
            </form>

        </div>
    </div>

</div>
@endsection
