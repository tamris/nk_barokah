<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <title>Reservasi Berhasil Diajukan | NK Barokah</title>

    <!-- Typography: Plus Jakarta Sans & JetBrains Mono -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@500;600;700&display=swap" rel="stylesheet">

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'system-ui', '-apple-system', 'sans-serif'],
                        mono: ['"JetBrains Mono"', 'ui-monospace', 'monospace'],
                    },
                    colors: {
                        brand: {
                            50: '#F0FDF8',
                            100: '#DCFCEE',
                            200: '#BBF7DE',
                            500: '#168579',
                            600: '#045D56', // NK Barokah Primary Emerald
                            700: '#034B45',
                            800: '#023B36',
                            900: '#022E2A',
                        }
                    },
                    boxShadow: {
                        'subtle': '0 1px 2px 0 rgba(0, 0, 0, 0.04)',
                        'card': '0 1px 3px 0 rgba(0, 0, 0, 0.05), 0 20px 25px -5px rgba(0, 0, 0, 0.02)',
                        'pill': '0 4px 14px -2px rgba(4, 93, 86, 0.28)',
                    }
                }
            }
        }
    </script>
    <style>
        .pb-safe { padding-bottom: max(1rem, env(safe-area-inset-bottom)); }
        .pt-safe { padding-top: max(1rem, env(safe-area-inset-top)); }
    </style>
</head>
<body class="min-h-full bg-slate-50 text-slate-900 font-sans antialiased flex items-center justify-center p-4 sm:p-6 pb-safe pt-safe">

    <div class="max-w-md w-full bg-white rounded-3xl border border-slate-200/90 shadow-card p-6 sm:p-8 text-center space-y-6">

        <!-- Minimal Checkmark Icon -->
        <div class="w-14 h-14 rounded-2xl bg-brand-50 border border-brand-200/60 text-brand-600 flex items-center justify-center mx-auto">
            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
        </div>

        <!-- Header Content -->
        <div class="space-y-2">
            <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-slate-100 text-slate-700 text-xs font-mono font-bold tracking-wider">
                <span class="w-1.5 h-1.5 rounded-full bg-brand-600"></span>
                <span>#{{ $booking->invoice_number }}</span>
            </div>

            <h1 class="text-2xl font-extrabold tracking-tight text-slate-900">
                Reservasi Berhasil Diajukan
            </h1>

            <p class="text-xs sm:text-sm text-slate-500 leading-relaxed max-w-xs mx-auto">
                Terima kasih, <strong class="text-slate-900 font-semibold">{{ $booking->customer_name }}</strong>. 
                Permintaan tanggal pernikahan Anda untuk 
                <strong class="text-brand-700 font-semibold">{{ $booking->formatted_event_date }}</strong> 
                telah berhasil dicatat.
            </p>
        </div>

        <!-- Summary Breakdown Card -->
        <div class="bg-slate-50 border border-slate-200/80 rounded-2xl p-4 text-left text-xs space-y-2.5">
            <div class="flex justify-between items-center pb-2 border-b border-slate-200/60">
                <span class="text-slate-400 uppercase tracking-wider text-[10px] font-semibold">Paket Pernikahan</span>
                <span class="font-bold text-slate-900">{{ $booking->package_name }}</span>
            </div>

            <div class="flex justify-between items-center text-slate-600">
                <span>Tipe Pembayaran:</span>
                <span class="font-bold uppercase tracking-wider text-[10px] px-2 py-0.5 rounded-md {{ $booking->payment_type === 'lunas' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                    {{ $booking->payment_type === 'lunas' ? 'Pelunasan (100%)' : 'Uang Muka (DP)' }}
                </span>
            </div>

            <div class="flex justify-between items-center text-slate-600">
                <span>Nominal Ditransfer:</span>
                <span class="font-mono font-extrabold text-brand-700 text-sm">Rp {{ number_format($booking->amount_paid, 0, ',', '.') }}</span>
            </div>

            @if($booking->remaining_balance > 0)
                <div class="flex justify-between items-center pt-2 border-t border-slate-200/60 text-slate-600">
                    <span>Sisa Tagihan:</span>
                    <span class="font-mono font-bold text-slate-900">Rp {{ number_format($booking->remaining_balance, 0, ',', '.') }}</span>
                </div>
            @endif
        </div>

        <!-- Action CTAs -->
        <div class="space-y-2.5 pt-1">
            <!-- WhatsApp Confirmation CTA -->
            <a href="{{ $whatsappUrl }}" target="_blank"
               class="flex items-center justify-center gap-2 w-full py-3.5 bg-brand-600 hover:bg-brand-700 active:scale-[0.99] text-white font-bold rounded-xl text-xs sm:text-sm tracking-wide transition shadow-pill cursor-pointer">
                <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981z"/></svg>
                <span>Konfirmasi via WhatsApp</span>
            </a>

            <!-- Download Official PDF Receipt -->
            <a href="{{ route('booking.receipt', $booking->invoice_number) }}"
               class="flex items-center justify-center gap-2 w-full py-3 bg-white border border-slate-200 hover:bg-slate-50 active:scale-[0.99] text-slate-700 font-semibold rounded-xl text-xs transition cursor-pointer">
                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                <span>Unduh Kwitansi PDF</span>
            </a>
        </div>

        <p class="text-[11px] text-slate-400 leading-relaxed">
            Tim keuangan kami akan memverifikasi bukti transfer dalam waktu 1x24 jam. Jadwal Anda akan resmi terkunci di kalender utama setelah diverifikasi.
        </p>

    </div>

</body>
</html>
