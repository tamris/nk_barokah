<!DOCTYPE html>
<html lang="id" class="h-full bg-[#F8F7F4]">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <title>Masuk Portal Owner | NK Barokah Wedding Organizer</title>
    
    <!-- Google Fonts: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <script src="https://cdn.tailwindcss.com"></script>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'system-ui', '-apple-system', 'sans-serif'],
                    },
                    colors: {
                        canvas: '#F8F7F4',
                        coral: {
                            50: '#FDF6F3',
                            100: '#FCEBE4',
                            200: '#F9D4C5',
                            300: '#F5B79F',
                            400: '#EE8865',
                            500: '#E8592A', // Signature Terracotta
                            600: '#CF471D',
                            700: '#A93614',
                        }
                    },
                    boxShadow: {
                        'card': '0 2px 10px rgba(0, 0, 0, 0.03), 0 10px 25px -5px rgba(0, 0, 0, 0.04)',
                    }
                }
            }
        }
    </script>
</head>
<body class="h-full text-zinc-800 antialiased font-sans flex items-center justify-center p-4 bg-[#F8F7F4]">

    <div class="max-w-md w-full space-y-6">
        
        <!-- Header & Logo Mark Oranye Terakota -->
        <div class="text-center space-y-2">
            <div class="w-14 h-14 rounded-3xl bg-gradient-to-br from-coral-500 to-coral-600 text-white flex items-center justify-center shadow-lg shadow-coral-500/25 mx-auto">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                </svg>
            </div>
            <h1 class="text-xl sm:text-2xl font-extrabold text-zinc-900 tracking-tight">NK Barokah</h1>
            <p class="text-xs text-zinc-400 font-medium">Portal Manajemen Reservasi & Penjualan Owner</p>
        </div>

        <!-- Kartu Login -->
        <div class="bg-white rounded-3xl border border-[#ECE8E1] shadow-card p-6 sm:p-8 space-y-5">
            
            @if ($errors->any())
                <div class="p-3.5 bg-rose-50 border border-rose-200 rounded-2xl text-xs font-semibold text-rose-700">
                    {{ $errors->first() }}
                </div>
            @endif

            <form action="{{ route('admin.login.post') }}" method="POST" class="space-y-4 text-xs">
                @csrf

                <div>
                    <label class="block font-bold text-zinc-700 mb-1.5">Alamat Email</label>
                    <input type="email" name="email" required value="{{ old('email', 'owner@nkbarokah.com') }}"
                           class="w-full px-4 py-3 sm:py-3.5 rounded-2xl border border-[#ECE8E1] text-xs sm:text-sm text-zinc-900 placeholder-zinc-400 focus:outline-none focus:ring-2 focus:ring-coral-500/20 focus:border-coral-500 transition shadow-2xs"
                           placeholder="owner@nkbarokah.com">
                </div>

                <div>
                    <label class="block font-bold text-zinc-700 mb-1.5">Kata Sandi</label>
                    <input type="password" name="password" required value="password"
                           class="w-full px-4 py-3 sm:py-3.5 rounded-2xl border border-[#ECE8E1] text-xs sm:text-sm text-zinc-900 placeholder-zinc-400 focus:outline-none focus:ring-2 focus:ring-coral-500/20 focus:border-coral-500 transition shadow-2xs"
                           placeholder="••••••••">
                </div>

                <div class="flex items-center justify-between pt-1">
                    <label class="flex items-center gap-2 cursor-pointer select-none">
                        <input type="checkbox" name="remember" class="rounded border-[#D5D1C9] text-coral-500 focus:ring-coral-400 w-4 h-4">
                        <span class="text-zinc-600 font-medium">Ingat sesi saya di HP</span>
                    </label>
                </div>

                <button type="submit" 
                        class="w-full py-3.5 bg-coral-500 hover:bg-coral-600 text-white font-extrabold rounded-2xl text-xs sm:text-sm transition duration-150 shadow-md shadow-coral-500/25 active:scale-95 mt-2">
                    Masuk ke Portal Owner
                </button>
            </form>
        </div>

        <!-- Bantuan Akun Standar -->
        <div class="text-center text-[11px] text-zinc-400 bg-white/60 p-3 rounded-2xl border border-[#ECE8E1]">
            Akun Standar: <span class="font-mono text-zinc-800 font-bold">owner@nkbarokah.com</span> / Sandi: <span class="font-mono text-zinc-800 font-bold">password</span>
        </div>

    </div>

</body>
</html>
