<!DOCTYPE html>
<html lang="id" class="h-full bg-[#F6F8F7]">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <title>Masuk Portal Owner | NK Barokah Wedding Organizer</title>

    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <script src="https://cdn.tailwindcss.com"></script>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'system-ui', '-apple-system', 'sans-serif'],
                    },
                    colors: {
                        brand: {
                            50:  '#EBF7F5',
                            100: '#D3ECE8',
                            400: '#3CA497',
                            500: '#168579',
                            600: '#045D56',
                            700: '#034B45',
                        }
                    },
                    boxShadow: {
                        'card': '0 4px 24px -4px rgba(4, 93, 86, 0.06), 0 1px 4px rgba(0, 0, 0, 0.02)',
                        'pill': '0 6px 16px -4px rgba(4, 93, 86, 0.35)',
                    }
                }
            }
        }
    </script>
</head>
<body class="h-full text-slate-800 antialiased font-sans flex items-center justify-center p-4 bg-[#F6F8F7]">

    <div class="max-w-md w-full space-y-6">

        <!-- Header & Logo Mark -->
        <div class="text-center space-y-2.5">
            <img src="{{ asset('images/logo.png') }}" alt="NK Barokah Logo" class="h-16 w-auto object-contain mx-auto mb-2">
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">NK Barokah</h1>
            <p class="text-xs text-slate-400 font-medium">Wedding Organizer Owner Management Portal</p>
        </div>

        <!-- Kartu Login -->
        <div class="bg-white rounded-[28px] border border-[#EDF2F0] shadow-card p-6 sm:p-8 space-y-5">

            @if ($errors->any())
                <div class="p-3.5 bg-rose-50 border border-rose-200/80 rounded-2xl text-xs font-semibold text-rose-700">
                    {{ $errors->first() }}
                </div>
            @endif

            <form action="{{ route('admin.login.post') }}" method="POST" class="space-y-4 text-xs">
                @csrf

                <div>
                    <label class="block font-semibold text-slate-700 mb-1.5">Email Address</label>
                    <input type="email" name="email" required value="{{ old('email', 'owner@nkbarokah.com') }}"
                           class="w-full px-4 py-3 sm:py-3.5 rounded-2xl border border-[#E5ECE9] bg-[#FAFDFC] focus:bg-white text-xs sm:text-sm text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-brand-600/15 focus:border-brand-500 transition"
                           placeholder="owner@nkbarokah.com">
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 mb-1.5">Password</label>
                    <input type="password" name="password" required value="password"
                           class="w-full px-4 py-3 sm:py-3.5 rounded-2xl border border-[#E5ECE9] bg-[#FAFDFC] focus:bg-white text-xs sm:text-sm text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-brand-600/15 focus:border-brand-500 transition"
                           placeholder="••••••••">
                </div>

                <div class="flex items-center justify-between pt-1">
                    <label class="flex items-center gap-2 cursor-pointer select-none">
                        <input type="checkbox" name="remember" class="rounded border-[#D4DFDC] text-brand-600 focus:ring-brand-500 w-4 h-4">
                        <span class="text-slate-600 font-medium">Keep me signed in</span>
                    </label>
                </div>

                <button type="submit"
                        class="w-full py-3.5 bg-brand-600 hover:bg-brand-700 text-white font-bold rounded-full text-xs sm:text-sm transition duration-150 shadow-pill active:scale-[0.99] mt-2">
                    Sign In to Owner Portal
                </button>
            </form>
        </div>

        <!-- Bantuan Akun Standar -->
        <div class="text-center text-[11px] text-slate-400 bg-white/75 p-3 rounded-full border border-[#EAF0EE]">
            Demo Account: <span class="font-mono text-slate-700 font-semibold">owner@nkbarokah.com</span> · Password: <span class="font-mono text-slate-700 font-semibold">password</span>
        </div>

    </div>

</body>
</html>
