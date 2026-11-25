<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SPK Pemasangan Tenda - #{{ $booking->invoice_number }} - {{ $booking->customer_name }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&family=JetBrains+Mono:wght@500;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            color: #0f172a;
            background-color: #f8fafc;
        }
        .font-mono {
            font-family: 'JetBrains Mono', monospace;
        }
        @media print {
            body {
                background-color: #ffffff;
                color: #000000;
            }
            .no-print {
                display: none !important;
            }
            .print-page {
                box-shadow: none !important;
                border: none !important;
                padding: 0 !important;
                margin: 0 !important;
                max-width: 100% !important;
            }
            @page {
                size: A4;
                margin: 12mm;
            }
        }
    </style>
</head>
<body class="py-6 sm:py-10 px-3 sm:px-6">

    <!-- Top Action Bar (Screen Only) -->
    <div class="max-w-4xl mx-auto mb-6 flex items-center justify-between gap-3 no-print">
        <a href="{{ route('admin.bookings.index', ['booking_id' => $booking->id]) }}"
           class="inline-flex items-center gap-2 px-4 py-2 rounded-full border border-slate-300 bg-white text-xs font-semibold text-slate-700 hover:bg-slate-100 transition shadow-2xs">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Kembali ke Dashboard
        </a>

        <div class="flex items-center gap-2.5">
            <button onclick="window.print()"
                    class="inline-flex items-center gap-2 px-5 py-2.5 rounded-full bg-emerald-700 hover:bg-emerald-800 text-white text-xs font-bold transition shadow-md cursor-pointer active:scale-95">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4H7v4a2 2 0 002 2zM7 9V5a2 2 0 012-2h6a2 2 0 012 2v4"/></svg>
                <span>Cetak SPK Lapangan (Print / PDF)</span>
            </button>
        </div>
    </div>

    <!-- SPK Document Sheet Container -->
    <main class="max-w-4xl mx-auto bg-white border border-slate-200 rounded-3xl p-6 sm:p-10 shadow-lg print-page">

        <!-- Header Kop Surat -->
        <header class="border-b-2 border-slate-900 pb-5">
            <div class="flex items-start justify-between gap-4">
                <div class="flex items-center gap-3.5">
                    <img src="{{ asset('images/logo.png') }}" alt="Logo NK Barokah" class="h-14 sm:h-16 w-auto object-contain">
                    <div>
                        <h1 class="text-xl sm:text-2xl font-black tracking-tight text-slate-900 uppercase">NK BAROKAH</h1>
                        <p class="text-xs font-bold text-slate-700 uppercase tracking-widest">Persewaan Tenda &amp; Perlengkapan Pesta Slawi</p>
                        <p class="text-[11px] text-slate-500 mt-0.5">
                            {{ $settings['warehouse_address'] ?? 'Jl. K.H. Ahmad Dahlan, RT 04 / RW 01, Slawi Kulon, Tegal' }}
                            · CS: {{ $settings['cs_phone'] ?? '0822-2559-6931' }}
                        </p>
                    </div>
                </div>

                <div class="text-right shrink-0">
                    <p class="text-[11px] font-black uppercase tracking-wider text-slate-500">SURAT PERINTAH KERJA (SPK)</p>
                    <span class="inline-block mt-0.5 px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider bg-slate-900 text-white">
                        SPK LOGISTIK LAPANGAN
                    </span>
                    <p class="font-mono text-base font-black text-slate-900 mt-1">#{{ $booking->invoice_number }}</p>
                </div>
            </div>
        </header>

        <!-- Jadwal Waktu Operasional (Highlight H-2 Setup) -->
        <section class="mt-5 grid grid-cols-1 sm:grid-cols-3 gap-3">
            <div class="p-3.5 rounded-2xl bg-emerald-50 border-2 border-emerald-600">
                <span class="text-[10px] font-black uppercase tracking-wider text-emerald-800 block">Jadwal Pasang (H-2 Paten):</span>
                <p class="text-base font-black text-emerald-950 mt-0.5">
                    {{ $booking->formatted_setup_date }}
                </p>
                <p class="text-[11px] font-bold text-emerald-700 mt-0.5">
                    {{ $booking->setup_date ? 'Jadwal Pasang Khusus' : 'Standar SOP H-2 NK Barokah' }}
                </p>
            </div>

            <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200">
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-500 block">Hari Pelaksanaan Acara:</span>
                <p class="text-sm font-black text-slate-900 mt-0.5">
                    {{ $booking->formatted_event_date }}
                </p>
                <p class="text-[11px] text-slate-600 mt-0.5">Durasi: {{ $booking->duration_days ?: 1 }} Hari Acara</p>
            </div>

            <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200">
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-500 block">Status Pembayaran di Lapangan:</span>
                <div class="mt-0.5 flex items-baseline gap-2">
                    @if($booking->remaining_balance <= 0 || strtolower($booking->payment_type) === 'lunas')
                        <span class="px-2.5 py-0.5 rounded-full text-[11px] font-black bg-emerald-100 text-emerald-800">LUNAS (100%)</span>
                        <span class="text-[11px] text-slate-500">Tidak perlu tagih</span>
                    @else
                        <span class="px-2.5 py-0.5 rounded-full text-[11px] font-black bg-amber-100 text-amber-900">DP (ADA SISA)</span>
                        <span class="font-mono text-xs font-bold text-rose-600">Sisa: Rp {{ number_format($booking->remaining_balance, 0, ',', '.') }}</span>
                    @endif
                </div>
                <p class="text-[11px] text-slate-500 mt-1">Total: Rp {{ number_format($booking->grand_total, 0, ',', '.') }}</p>
            </div>
        </section>

        <!-- Informasi Klien, Lokasi & Rekanan -->
        <section class="mt-5 grid grid-cols-1 sm:grid-cols-2 gap-4 p-4 rounded-2xl bg-white border border-slate-200 text-xs">
            <div class="space-y-1.5">
                <h2 class="text-[11px] font-bold uppercase tracking-wider text-slate-400">1. Data Pemesan & Kontak</h2>
                <div class="space-y-1">
                    <div class="flex items-center gap-2 flex-wrap">
                        <p class="text-sm font-black text-slate-900">{{ $booking->customer_name }}</p>
                        <span class="px-2 py-0.5 rounded-full text-[9px] font-black uppercase tracking-wider bg-slate-900 text-white">
                            {{ $booking->event_type_label }}
                        </span>
                    </div>
                    @if($booking->child_name)
                        <p class="text-slate-700 font-bold">Nama Anak: <span class="text-brand-800">{{ $booking->child_name }}</span></p>
                    @endif
                    @if($booking->parents_name && $booking->parents_name !== '-')
                        <p class="text-slate-600 font-medium">Keluarga: <strong>{{ $booking->parents_name }}</strong></p>
                    @endif
                    <p class="font-mono text-sm font-bold text-slate-900 flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                        <span>{{ $booking->customer_phone }}</span>
                    </p>
                    <div class="pt-1">
                        <span class="text-[10px] font-bold uppercase text-slate-400">Sumber Job / Rekanan:</span>
                        <span class="inline-block ml-1 px-2.5 py-0.5 rounded-full text-xs font-black bg-indigo-50 text-indigo-800 border border-indigo-200">
                            {{ $booking->job_source ?: ($booking->vendor_name ?: 'Klien Langsung') }}
                        </span>
                    </div>
                </div>
            </div>

            <div class="space-y-1.5">
                <h2 class="text-[11px] font-bold uppercase tracking-wider text-slate-400">2. Titik Lokasi Pemasangan Tenda</h2>
                <div class="space-y-1">
                    <p class="text-slate-900 font-bold leading-relaxed text-xs">
                        {{ $booking->venue_address ?: 'Alamat belum diisi' }}
                    </p>
                    @if($booking->address_landmark && $booking->address_landmark !== '-')
                        <p class="text-slate-600 font-medium">
                            Patokan: <span class="font-bold text-slate-900">{{ $booking->address_landmark }}</span>
                        </p>
                    @endif
                    @if($booking->delivery_distance_km > 0)
                        <p class="text-slate-500 font-mono text-[11px]">
                            Jarak Tempuh: {{ $booking->delivery_distance_km }} km dari Gudang Slawi Kulon
                        </p>
                    @endif
                </div>
            </div>
        </section>

        <!-- SPESIFIKASI TENDA & WARNA KAIN (CRITICAL FIELD HIGHLIGHT) -->
        <section class="mt-5 p-4 sm:p-5 rounded-2xl bg-amber-50/70 border-2 border-amber-500">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div class="space-y-1">
                    <span class="text-[10px] font-black uppercase tracking-wider text-amber-900">SPESIFIKASI TENDA UTAMA:</span>
                    <h3 class="text-base sm:text-lg font-black text-slate-900">{{ $booking->package_name }}</h3>
                    @if($booking->custom_tent_size)
                        <p class="text-xs font-bold text-slate-800 mt-0.5">
                            Dimensi Lapangan: <span class="font-mono text-emerald-800 bg-white px-2 py-0.5 rounded-md border border-amber-300">{{ $booking->custom_tent_size }}</span>
                        </p>
                    @endif
                </div>

                <div class="sm:text-right p-3 rounded-xl bg-white border border-amber-400 shadow-2xs">
                    <span class="text-[10px] font-black uppercase tracking-wider text-slate-500 block">TEMA WARNA KAIN / PLAFON:</span>
                    <p class="text-base sm:text-lg font-black text-rose-700 uppercase tracking-tight">
                        {{ $booking->tent_color_theme ?: 'SESUAI STANDAR (NATURAL)' }}
                    </p>
                    <span class="text-[10px] font-bold text-slate-400">Pastikan kain bersih & wangi saat dipasang</span>
                </div>
            </div>
        </section>

        <!-- CHECKLIST RINCIAN MUATAN LOGISTIK (Tenda, Kursi, Meja, Blower, Panggung) -->
        <section class="mt-5">
            <h2 class="text-xs font-black uppercase tracking-wider text-slate-800 mb-2 flex items-center justify-between">
                <span>3. Checklist Muatan Peralatan & Add-ons</span>
                <span class="text-[10px] font-normal text-slate-500 lowercase">(centang saat loading di gudang & saat serah terima)</span>
            </h2>

            <div class="border border-slate-300 rounded-2xl overflow-hidden">
                <table class="w-full text-xs text-left">
                    <thead class="bg-slate-100 text-slate-800 font-bold border-b border-slate-300 uppercase text-[10px]">
                        <tr>
                            <th class="py-2.5 px-3 w-10 text-center">No</th>
                            <th class="py-2.5 px-4">Nama Item / Fasilitas</th>
                            <th class="py-2.5 px-4 w-24 text-center">Jumlah</th>
                            <th class="py-2.5 px-3 w-20 text-center">Satuan</th>
                            <th class="py-2.5 px-3 w-20 text-center bg-slate-200/60">Gudang</th>
                            <th class="py-2.5 px-3 w-20 text-center bg-slate-200/60">Pasang</th>
                            <th class="py-2.5 px-3 w-20 text-center bg-slate-200/60">Bongkar</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 text-slate-900 font-medium">
                        <!-- Tenda Utama -->
                        <tr class="bg-slate-50/50">
                            <td class="py-2.5 px-3 text-center font-bold">1</td>
                            <td class="py-2.5 px-4">
                                <span class="font-bold text-slate-900">{{ $booking->package_name }}</span>
                                @if($booking->custom_tent_size)
                                    <span class="text-[11px] text-slate-600 block">Ukuran Modul: {{ $booking->custom_tent_size }}</span>
                                @endif
                            </td>
                            <td class="py-2.5 px-4 text-center font-mono font-bold">1</td>
                            <td class="py-2.5 px-3 text-center text-slate-500">Set</td>
                            <td class="py-2.5 px-3 text-center"><div class="w-4 h-4 mx-auto border border-slate-400 rounded"></div></td>
                            <td class="py-2.5 px-3 text-center"><div class="w-4 h-4 mx-auto border border-slate-400 rounded"></div></td>
                            <td class="py-2.5 px-3 text-center"><div class="w-4 h-4 mx-auto border border-slate-400 rounded"></div></td>
                        </tr>

                        <!-- Addons / Tambahan Lapangan -->
                        @forelse($booking->addons as $idx => $ad)
                            <tr>
                                <td class="py-2.5 px-3 text-center text-slate-400">{{ $idx + 2 }}</td>
                                <td class="py-2.5 px-4">
                                    <span class="font-semibold text-slate-800">{{ $ad->addon_name }}</span>
                                </td>
                                <td class="py-2.5 px-4 text-center font-mono font-bold text-slate-900">{{ $ad->quantity }}</td>
                                <td class="py-2.5 px-3 text-center text-slate-500">{{ $ad->addon->unit ?? 'unit' }}</td>
                                <td class="py-2.5 px-3 text-center"><div class="w-4 h-4 mx-auto border border-slate-400 rounded"></div></td>
                                <td class="py-2.5 px-3 text-center"><div class="w-4 h-4 mx-auto border border-slate-400 rounded"></div></td>
                                <td class="py-2.5 px-3 text-center"><div class="w-4 h-4 mx-auto border border-slate-400 rounded"></div></td>
                            </tr>
                        @empty
                            @if(str_contains(strtolower($booking->package_name), 'decor') || str_contains(strtolower($booking->package_name), 'paket'))
                                <tr>
                                    <td colspan="7" class="py-3 px-4 text-slate-500 italic text-center">
                                        Perlengkapan standar sudah tercakup dalam rincian paket di atas.
                                    </td>
                                </tr>
                            @endif
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        <!-- CATATAN KHUSUS KRU / OPERASIONAL LAPANGAN (FIELD INSTRUCTIONS) -->
        @if($booking->operational_notes || ($booking->survey_requested && $booking->survey_notes))
            <section class="mt-5 p-4 rounded-2xl bg-rose-50 border-2 border-rose-500">
                <div class="flex items-start gap-3">
                    <div class="w-8 h-8 rounded-full bg-rose-600 text-white flex items-center justify-center shrink-0 font-bold text-sm">
                        !
                    </div>
                    <div>
                        <h3 class="text-xs font-black uppercase tracking-wider text-rose-900">CATATAN KHUSUS KRU & OPERASIONAL LAPANGAN:</h3>
                        @if($booking->operational_notes)
                            <p class="text-sm font-black text-rose-950 mt-1 leading-relaxed whitespace-pre-line">
                                {{ $booking->operational_notes }}
                            </p>
                        @endif
                        @if($booking->survey_requested && $booking->survey_notes)
                            <p class="text-xs font-semibold text-rose-800 mt-1">
                                Catatan Hasil Survey: {{ $booking->survey_notes }}
                            </p>
                        @endif
                    </div>
                </div>
            </section>
        @endif

        <!-- Lembar Tanda Tangan / Serah Terima -->
        <section class="mt-8 pt-6 border-t border-slate-300">
            <div class="grid grid-cols-3 gap-6 text-center text-xs">
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Bagian Gudang (Muat)</p>
                    <div class="h-16"></div>
                    <p class="font-bold text-slate-900 border-t border-slate-300 pt-1">( ........................................ )</p>
                    <p class="text-[10px] text-slate-400">Staff Logistik Slawi</p>
                </div>

                <div>
                    <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Mandor / Kru Pasang (Layos)</p>
                    <div class="h-16"></div>
                    <p class="font-bold text-slate-900 border-t border-slate-300 pt-1">( ........................................ )</p>
                    <p class="text-[10px] text-slate-400">Driver & Koordinator Lapangan</p>
                </div>

                <div>
                    <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Tuan Rumah / Penerima</p>
                    <div class="h-16"></div>
                    <p class="font-bold text-slate-900 border-t border-slate-300 pt-1">( {{ $booking->customer_name }} )</p>
                    <p class="text-[10px] text-slate-400">Keluarga / Panitia Acara</p>
                </div>
            </div>
        </section>

        <!-- Footer Nota SPK -->
        <footer class="mt-6 pt-3 border-t border-slate-100 flex items-center justify-between text-[10px] text-slate-400">
            <p>Dicetak otomatis via Sistem Operasional NK Barokah · {{ now()->format('d M Y H:i:s') }}</p>
            <p class="font-mono">Dokumen Resmi Kru Pemasangan Tenda</p>
        </footer>

    </main>

</body>
</html>
