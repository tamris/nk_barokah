<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <title>Reservasi Berhasil - NK Barokah Wedding Organizer</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,600;0,700;0,800;1,400&family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <script src="https://cdn.tailwindcss.com"></script>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        serif: ['"Playfair Display"', 'Georgia', 'serif'],
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                    },
                    colors: {
                        gold: {
                            100: '#F6F1E6',
                            200: '#EBDDC3',
                            300: '#DEC69B',
                            500: '#C59A4D',
                            600: '#A97E33',
                            700: '#835F24',
                        },
                        obsidian: '#191614',
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-[#FAF8F5] text-stone-800 font-sans min-h-screen flex items-center justify-center p-4 selection:bg-gold-200">

    <div class="max-w-lg w-full bg-white rounded-3xl shadow-xl border border-stone-200/90 p-8 sm:p-10 text-center space-y-7 relative overflow-hidden">
        
        <!-- Top Golden Glow Accent -->
        <div class="absolute top-0 left-0 right-0 h-2 bg-gradient-to-r from-gold-300 via-gold-500 to-gold-300"></div>

        <!-- Seal Badge Icon -->
        <div class="w-20 h-20 rounded-full bg-gradient-to-br from-gold-100 to-amber-100 border border-gold-300 flex items-center justify-center mx-auto text-3xl shadow-sm">
            💍
        </div>

        <!-- Header Titles -->
        <div class="space-y-2">
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-amber-50 text-amber-800 border border-amber-200/80 text-[11px] font-mono font-bold tracking-wider">
                <span class="animate-pulse">⏳</span>
                <span>{{ $booking->invoice_number }}</span>
            </span>

            <h1 class="font-serif text-2xl sm:text-3xl font-bold text-obsidian tracking-tight">
                Reservasi Berhasil Diajukan
            </h1>

            <p class="text-xs sm:text-sm text-stone-500 max-w-sm mx-auto leading-relaxed">
                Selamat dan terima kasih, <strong class="text-stone-800 font-semibold">{{ $booking->customer_name }}</strong>. 
                Jadwal hari bahagia Anda untuk tanggal 
                <strong class="text-gold-700">{{ \Carbon\Carbon::parse($booking->event_date)->isoFormat('D MMMM Y') }}</strong> 
                sedang dalam proses verifikasi mutasi oleh tim kami.
            </p>
        </div>

        <!-- Luxury Booking Summary Certificate Card -->
        <div class="bg-[#FCFAF7] border border-gold-200/70 rounded-2xl p-5 text-left text-xs space-y-2.5">
            <div class="flex justify-between items-center pb-2 border-b border-stone-200/60">
                <span class="text-stone-400 uppercase tracking-wider text-[10px]">Paket Terpilih</span>
                <span class="font-serif font-bold text-obsidian">{{ $booking->package_name }}</span>
            </div>

            <div class="flex justify-between items-center">
                <span class="text-stone-500">Tipe Pembayaran:</span>
                <span class="font-bold uppercase tracking-wider text-xs {{ $booking->payment_type === 'lunas' ? 'text-emerald-700' : 'text-amber-700' }}">
                    {{ $booking->payment_type }}
                </span>
            </div>

            <div class="flex justify-between items-center">
                <span class="text-stone-500">Nominal Ditransfer:</span>
                <span class="font-bold text-stone-900 text-sm">Rp {{ number_format($booking->amount_paid, 0, ',', '.') }}</span>
            </div>

            @if($booking->remaining_balance > 0)
                <div class="flex justify-between items-center pt-2 border-t border-stone-200/60">
                    <span class="text-stone-500">Sisa Tagihan Pelunasan:</span>
                    <span class="font-bold text-rose-600">Rp {{ number_format($booking->remaining_balance, 0, ',', '.') }}</span>
                </div>
            @endif
        </div>

        <!-- Action Buttons -->
        <div class="space-y-3 pt-1">
            <!-- WhatsApp CTA Button -->
            <a href="{{ $whatsappUrl }}" target="_blank"
               class="flex items-center justify-center gap-2.5 w-full py-4 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-2xl text-xs sm:text-sm tracking-wide transition duration-200 shadow-md hover:shadow-lg">
                <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981z"/></svg>
                <span>Konfirmasi ke WhatsApp Admin</span>
            </a>

            <!-- Download PDF Button -->
            <a href="{{ route('booking.receipt', $booking->invoice_number) }}" 
               class="flex items-center justify-center gap-2 w-full py-3.5 bg-white border border-stone-300 hover:border-gold-400 hover:bg-cream-50 text-stone-700 font-semibold rounded-2xl text-xs transition duration-200">
                <span>📄 Unduh Tanda Terima Reservasi (PDF)</span>
            </a>
        </div>

        <p class="text-[11px] text-stone-400 leading-relaxed">
            Admin keuangan kami akan memverifikasi mutasi rekening dalam waktu maksimal 1x24 jam. Simpan kuitansi digital ini sebagai bukti pemesanan Anda.
        </p>

    </div>

</body>
</html>
