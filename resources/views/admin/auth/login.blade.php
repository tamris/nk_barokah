<!DOCTYPE html>
<html lang="en" class="h-full bg-[#F6F8F7]">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <title>Owner Portal Login | NK Barokah</title>

    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Tailwind CSS CDN & Alpine.js -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

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
                        'card': '0 8px 30px -4px rgba(4, 93, 86, 0.08), 0 2px 6px rgba(0, 0, 0, 0.02)',
                        'pill': '0 6px 18px -4px rgba(4, 93, 86, 0.35)',
                    }
                }
            }
        }
    </script>
    <style>
        [x-cloak] { display: none !important; }
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            20%, 60% { transform: translateX(-6px); }
            40%, 80% { transform: translateX(6px); }
        }
        .animate-shake {
            animation: shake 0.4s ease-in-out;
        }
    </style>
</head>
<body class="h-full text-slate-800 antialiased font-sans flex items-center justify-center p-4 bg-[#F6F8F7]"
      x-data="pinLogin()"
      @keydown.window="handleKey($event)"
      @paste.window="handlePaste($event)">

    <div class="max-w-[380px] sm:max-w-[400px] w-full space-y-5">

        <!-- Logo & Branding -->
        <div class="text-center space-y-2.5">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-white shadow-card border border-[#EAF0EE] mx-auto p-2.5">
                <img src="{{ asset('images/logo.png') }}" alt="NK Barokah Logo" class="w-full h-full object-contain">
            </div>
            <div>
                <h1 class="text-xl font-bold text-slate-900 tracking-tight">NK Barokah</h1>
                <p class="text-xs text-slate-400 font-medium">Owner Management Portal</p>
            </div>
        </div>

        <!-- Kartu PIN Utama -->
        <div class="bg-white rounded-[32px] sm:rounded-[36px] border border-[#EDF2F0] shadow-card p-6 sm:p-7 space-y-5">

            <!-- Error Notification -->
            @if ($errors->any())
                <div class="p-3 bg-rose-50 border border-rose-200/80 rounded-2xl text-xs font-semibold text-rose-700 text-center animate-shake">
                    {{ $errors->first() }}
                </div>
            @endif

            <!-- Title -->
            <div class="text-center pt-1">
                <p class="text-xs font-bold text-slate-700 uppercase tracking-wider">Enter 6-Digit PIN</p>
            </div>

            <!-- Form Hidden -->
            <form id="pin-login-form" x-ref="pinForm" action="{{ route('admin.login.post') }}" method="POST">
                @csrf
                <input type="hidden" name="pin" id="pin-input" x-ref="pinInput" value="">
                <input type="hidden" name="remember" value="1">
            </form>

            <!-- 6 Visual Indicator Slots -->
            <div class="flex items-center justify-center gap-2.5 sm:gap-3 py-1 select-none">
                <template x-for="i in 6" :key="i">
                    <div class="w-10 h-12 sm:w-11 sm:h-13 rounded-2xl border flex items-center justify-center transition-all duration-150"
                         :class="pin.length >= i ? 'border-brand-500 bg-brand-50/70 shadow-xs scale-105' : 'border-slate-200/90 bg-[#FAFDFC]'">
                        <span class="w-3 h-3 rounded-full transition-all duration-150"
                              :class="pin.length >= i ? 'bg-brand-600 scale-100' : 'bg-transparent scale-0'">
                        </span>
                    </div>
                </template>
            </div>

            <!-- Modern Circular Keypad -->
            <div class="grid grid-cols-3 gap-2.5 sm:gap-3 max-w-[270px] mx-auto pt-1">
                <template x-for="num in [1, 2, 3, 4, 5, 6, 7, 8, 9]" :key="num">
                    <button type="button"
                            @click="addDigit(num)"
                            class="w-16 h-16 sm:w-18 sm:h-18 rounded-full bg-white hover:bg-brand-50/50 active:bg-brand-100/60 active:scale-90 border border-slate-200/80 text-2xl font-bold text-slate-800 transition-all flex items-center justify-center shadow-xs cursor-pointer select-none mx-auto">
                        <span x-text="num"></span>
                    </button>
                </template>
                <button type="button"
                        @click="clearPin()"
                        class="w-16 h-16 sm:w-18 sm:h-18 rounded-full bg-slate-50/70 hover:bg-slate-100 active:scale-90 text-[11px] font-bold text-slate-400 uppercase tracking-wider transition-all flex items-center justify-center cursor-pointer select-none mx-auto">
                    Clear
                </button>
                <button type="button"
                        @click="addDigit(0)"
                        class="w-16 h-16 sm:w-18 sm:h-18 rounded-full bg-white hover:bg-brand-50/50 active:bg-brand-100/60 active:scale-90 border border-slate-200/80 text-2xl font-bold text-slate-800 transition-all flex items-center justify-center shadow-xs cursor-pointer select-none mx-auto">
                    0
                </button>
                <button type="button"
                        @click="deleteDigit()"
                        class="w-16 h-16 sm:w-18 sm:h-18 rounded-full bg-slate-50/70 hover:bg-rose-50 active:scale-90 hover:text-rose-600 text-slate-500 transition-all flex items-center justify-center cursor-pointer select-none mx-auto">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M12 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2M3 12l6.414-6.414a2 2 0 011.414-.586H19a2 2 0 012 2v10a2 2 0 01-2 2h-7.172a2 2 0 01-1.414-.586L3 12z"/></svg>
                </button>
            </div>

            <!-- Submit Button Indicator -->
            <div class="pt-1">
                <button type="button"
                        @click="submitPin()"
                        :disabled="pin.length !== 6 || isSubmitting"
                        class="w-full py-3.5 rounded-full font-bold text-xs sm:text-sm transition-all duration-200 flex items-center justify-center gap-2"
                        :class="pin.length === 6 && !isSubmitting ? 'bg-brand-600 hover:bg-brand-700 text-white cursor-pointer shadow-pill active:scale-[0.98]' : 'bg-slate-100 text-slate-400 cursor-not-allowed'">
                    <span x-show="!isSubmitting">Sign In to Dashboard</span>
                    <span x-show="isSubmitting" class="inline-flex items-center gap-2">
                        <svg class="animate-spin h-4 w-4 text-white" viewBox="0 0 24 24" fill="none"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg>
                        Verifying PIN...
                    </span>
                </button>
            </div>

        </div>

        <!-- Simple Minimal Footer -->
        <p class="text-center text-[11px] text-slate-400 font-medium">
            © {{ date('Y') }} NK Barokah · Operations Management Portal
        </p>

    </div>

    <script>
        function pinLogin() {
            return {
                pin: '',
                maxDigits: 6,
                isSubmitting: false,
                syncInput() {
                    const el = document.getElementById('pin-input');
                    if (el) el.value = this.pin;
                },
                addDigit(digit) {
                    if (this.isSubmitting) return;
                    const clean = digit.toString().replace(/[^0-9]/g, '');
                    if (!clean) return;
                    if (this.pin.length < this.maxDigits) {
                        this.pin += clean;
                        this.syncInput();
                        if (this.pin.length === this.maxDigits) {
                            this.submitPin();
                        }
                    }
                },
                deleteDigit() {
                    if (this.isSubmitting) return;
                    if (this.pin.length > 0) {
                        this.pin = this.pin.slice(0, -1);
                        this.syncInput();
                    }
                },
                clearPin() {
                    if (this.isSubmitting) return;
                    this.pin = '';
                    this.syncInput();
                },
                handleKey(event) {
                    if (event.target.tagName === 'INPUT' || event.target.tagName === 'TEXTAREA') return;

                    if (event.key >= '0' && event.key <= '9') {
                        event.preventDefault();
                        this.addDigit(event.key);
                    } else if (event.key === 'Backspace') {
                        event.preventDefault();
                        this.deleteDigit();
                    } else if (event.key === 'Escape' || event.key === 'Delete') {
                        event.preventDefault();
                        this.clearPin();
                    } else if (event.key === 'Enter') {
                        event.preventDefault();
                        if (this.pin.length === this.maxDigits) {
                            this.submitPin();
                        }
                    }
                },
                handlePaste(event) {
                    if (event.target.tagName === 'INPUT' || event.target.tagName === 'TEXTAREA') return;
                    const clipboardData = event.clipboardData || window.clipboardData;
                    if (!clipboardData) return;
                    const text = clipboardData.getData('text');
                    const clean = text.replace(/[^0-9]/g, '').slice(0, this.maxDigits);
                    if (clean) {
                        event.preventDefault();
                        this.pin = clean;
                        this.syncInput();
                        if (this.pin.length === this.maxDigits) {
                            this.submitPin();
                        }
                    }
                },
                submitPin() {
                    if (this.isSubmitting) return;
                    if (this.pin.length !== this.maxDigits) return;

                    this.isSubmitting = true;
                    this.syncInput();

                    setTimeout(() => {
                        const form = document.getElementById('pin-login-form');
                        if (form) form.submit();
                    }, 120);
                }
            }
        }
    </script>
</body>
</html>
