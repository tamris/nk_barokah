<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <title>Reservasi Jadwal Pernikahan - NK Barokah Wedding Organizer</title>
    
    <!-- Google Fonts: Playfair Display (Serif) & Plus Jakarta Sans (Sans) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,600;0,700;0,800;1,400;1,600&family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

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
                            50: '#FBF9F4',
                            100: '#F6F1E6',
                            200: '#EBDDC3',
                            300: '#DEC69B',
                            400: '#D1AF73',
                            500: '#C59A4D',
                            600: '#A97E33',
                            700: '#835F24',
                            800: '#5F4419',
                            900: '#3D2B0F',
                        },
                        cream: {
                            50: '#FDFBF7',
                            100: '#FAF6EE',
                            200: '#F3EBD8',
                        },
                        obsidian: '#191614',
                    }
                }
            }
        }
    </script>

    <style>
        [x-cloak] { display: none !important; }
        .gold-gradient-text {
            background: linear-gradient(135deg, #A97E33 0%, #DEC69B 50%, #A97E33 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .gold-border-gradient {
            border-image: linear-gradient(to right, #DEC69B, #A97E33, #DEC69B) 1;
        }
        .luxury-card-shadow {
            box-shadow: 0 10px 40px -10px rgba(25, 22, 20, 0.06), 0 2px 10px -2px rgba(25, 22, 20, 0.03);
        }
    </style>
</head>
<body class="bg-[#FAF8F5] text-stone-800 font-sans antialiased min-h-screen selection:bg-gold-200 selection:text-stone-900">

    <!-- Decorative Top Accent Bar -->
    <div class="h-1.5 w-full bg-gradient-to-r from-gold-300 via-gold-500 to-gold-300"></div>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-10 sm:py-16" 
         x-data="bookingApp({
            packages: {{ json_encode($packages) }},
            addons: {{ json_encode($addons) }},
            bookedDates: {{ json_encode($bookedDates) }},
            minDpAmount: {{ config('wo.min_dp_amount', 1000000) }}
         })" 
         x-cloak>

        <!-- Brand Header Section -->
        <header class="text-center mb-12 sm:mb-16 space-y-3">
            <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-cream-100 border border-gold-200/80 text-[11px] font-semibold tracking-widest text-gold-700 uppercase">
                <span class="text-gold-500">✦</span>
                <span>NK Barokah Wedding Planner & Organizer</span>
                <span class="text-gold-500">✦</span>
            </div>

            <h1 class="font-serif text-3xl sm:text-5xl font-normal text-obsidian tracking-tight leading-tight">
                Kunci Tanggal & <span class="italic font-serif font-light text-gold-600">Momen Bahagia</span> Anda
            </h1>
            
            <p class="text-stone-600 text-xs sm:text-sm max-w-lg mx-auto font-light leading-relaxed">
                Lengkapi reservasi digital untuk mengunci tim wedding organizer kami. Kuitansi resmi dan slot jadwal akan langsung diterbitkan secara instan.
            </p>

            <div class="w-16 h-0.5 bg-gradient-to-r from-transparent via-gold-400 to-transparent mx-auto mt-4"></div>
        </header>

        <!-- Error Notification Banner -->
        @if ($errors->any())
            <div class="mb-8 p-5 bg-rose-50/80 border border-rose-200 rounded-2xl shadow-sm text-xs text-rose-800">
                <div class="flex items-center gap-2 font-bold mb-2">
                    <span class="w-5 h-5 rounded-full bg-rose-200 text-rose-800 flex items-center justify-center text-[10px]">✕</span>
                    <span>Mohon periksa data yang belum sesuai:</span>
                </div>
                <ul class="list-disc list-inside space-y-1 text-rose-700 pl-2">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Main Booking Form -->
        <form action="{{ route('booking.store') }}" method="POST" enctype="multipart/form-data" 
              class="space-y-10 bg-white/90 backdrop-blur-sm rounded-3xl border border-stone-200/80 p-6 sm:p-12 luxury-card-shadow">
            @csrf

            <!-- STEP 1: IDENTITAS MEMPELAI -->
            <section class="space-y-6">
                <div class="flex items-center gap-3 border-b border-stone-100 pb-4">
                    <span class="w-7 h-7 rounded-full bg-gold-100 text-gold-800 font-serif font-bold text-xs flex items-center justify-center border border-gold-200">
                        1
                    </span>
                    <div>
                        <h2 class="font-serif text-lg font-bold text-obsidian">Identitas Calon Mempelai</h2>
                        <p class="text-[11px] text-stone-500">Nama resmi yang akan dicantumkan pada kuitansi dan rundown acara.</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-semibold text-stone-700 mb-1.5 uppercase tracking-wider">
                            Nama Lengkap Mempelai / Klien <span class="text-gold-600">*</span>
                        </label>
                        <input type="text" name="customer_name" required value="{{ old('customer_name') }}"
                               class="w-full px-4 py-3 rounded-xl bg-cream-50 border border-stone-200 text-sm focus:bg-white focus:ring-2 focus:ring-gold-400 focus:border-transparent focus:outline-none transition"
                               placeholder="Contoh: Muhammad Reza Pratama & Dian Sastrowardoyo">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-stone-700 mb-1.5 uppercase tracking-wider">
                            Nomor WhatsApp Aktif <span class="text-gold-600">*</span>
                        </label>
                        <div class="relative">
                            <span class="absolute left-4 top-3 text-xs text-stone-400 font-mono">📱</span>
                            <input type="tel" name="customer_phone" required value="{{ old('customer_phone') }}"
                                   class="w-full pl-10 pr-4 py-3 rounded-xl bg-cream-50 border border-stone-200 text-sm focus:bg-white focus:ring-2 focus:ring-gold-400 focus:border-transparent focus:outline-none transition"
                                   placeholder="081234567890">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-stone-700 mb-1.5 uppercase tracking-wider">
                            Alamat Email (Opsional)
                        </label>
                        <div class="relative">
                            <span class="absolute left-4 top-3 text-xs text-stone-400">✉️</span>
                            <input type="email" name="customer_email" value="{{ old('customer_email') }}"
                                   class="w-full pl-10 pr-4 py-3 rounded-xl bg-cream-50 border border-stone-200 text-sm focus:bg-white focus:ring-2 focus:ring-gold-400 focus:border-transparent focus:outline-none transition"
                                   placeholder="mempelai@email.com">
                        </div>
                    </div>

                    <div class="sm:col-span-2">
                        <label class="block text-xs font-semibold text-stone-700 mb-1.5 uppercase tracking-wider">
                            Tanggal Acara Pernikahan <span class="text-gold-600">*</span>
                        </label>
                        <div class="relative">
                            <input type="date" name="event_date" required min="{{ date('Y-m-d') }}"
                                   x-model="selectedDate" value="{{ old('event_date') }}"
                                   class="w-full px-4 py-3 rounded-xl bg-cream-50 border border-stone-200 text-sm focus:bg-white focus:ring-2 focus:ring-gold-400 focus:border-transparent focus:outline-none transition">
                        </div>

                        <!-- Date Overbooked Alert -->
                        <div x-show="isDateBooked" class="mt-2.5 p-3 rounded-xl bg-rose-50 border border-rose-200 flex items-center gap-2 text-xs text-rose-700 font-medium">
                            <span>⚠️</span>
                            <span>Maaf, jadwal pada tanggal tersebut sudah terisi penuh oleh klien lain. Silakan pilih tanggal lain yang masih tersedia.</span>
                        </div>
                    </div>
                </div>
            </section>

            <!-- STEP 2: PAKET & ADD-ON -->
            <section class="space-y-6">
                <div class="flex items-center gap-3 border-b border-stone-100 pb-4">
                    <span class="w-7 h-7 rounded-full bg-gold-100 text-gold-800 font-serif font-bold text-xs flex items-center justify-center border border-gold-200">
                        2
                    </span>
                    <div>
                        <h2 class="font-serif text-lg font-bold text-obsidian">Pilihan Paket & Tambahan Layanan</h2>
                        <p class="text-[11px] text-stone-500">Pilih paket utama dan tambahkan fasilitas ekstra sesuai kebutuhan resepsi Anda.</p>
                    </div>
                </div>

                <!-- Package Selection Grid -->
                <div class="space-y-3">
                    <label class="block text-xs font-semibold text-stone-700 uppercase tracking-wider">
                        Pilih Paket Utama <span class="text-gold-600">*</span>
                    </label>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <template x-for="pkg in packages" :key="pkg.id">
                            <label class="relative flex flex-col justify-between p-5 rounded-2xl border cursor-pointer transition duration-200"
                                   :class="selectedPackageId == pkg.id 
                                       ? 'border-gold-500 bg-cream-50/70 ring-2 ring-gold-400 shadow-md' 
                                       : 'border-stone-200 hover:border-gold-300 hover:bg-cream-50/30'">
                                <input type="radio" name="package_id" :value="pkg.id" class="sr-only" x-model="selectedPackageId">
                                
                                <div>
                                    <div class="flex items-center justify-between gap-1 mb-2">
                                        <h3 class="font-serif font-bold text-base text-obsidian" x-text="pkg.name"></h3>
                                        <div class="w-4 h-4 rounded-full border flex items-center justify-center transition"
                                             :class="selectedPackageId == pkg.id ? 'border-gold-600 bg-gold-500' : 'border-stone-300'">
                                            <div class="w-1.5 h-1.5 rounded-full bg-white" x-show="selectedPackageId == pkg.id"></div>
                                        </div>
                                    </div>
                                    <span class="font-serif font-bold text-base text-gold-700" x-text="formatRupiah(pkg.base_price)"></span>
                                    <p class="text-xs text-stone-500 mt-2.5 leading-relaxed line-clamp-3" x-text="pkg.description"></p>
                                </div>

                                <div class="mt-4 pt-3 border-t border-stone-200/60 flex items-center justify-between text-[11px]">
                                    <span class="text-stone-400">Status</span>
                                    <span class="font-medium text-emerald-700">Tersedia ✓</span>
                                </div>
                            </label>
                        </template>
                    </div>
                </div>

                <!-- Multi Add-ons Section -->
                <div x-show="addons.length > 0" class="pt-4 space-y-3">
                    <label class="block text-xs font-semibold text-stone-700 uppercase tracking-wider">
                        Fasilitas Tambahan (Add-ons Opsional)
                    </label>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <template x-for="(addon, idx) in addons" :key="addon.id">
                            <div class="flex items-center justify-between p-4 rounded-2xl border border-stone-200/90 bg-cream-50/40 hover:bg-cream-50 transition">
                                <div class="pr-2">
                                    <h4 class="text-xs font-bold text-obsidian" x-text="addon.name"></h4>
                                    <span class="text-[11px] font-medium text-gold-700" x-text="formatRupiah(addon.unit_price) + ' / ' + addon.unit"></span>
                                </div>

                                <div class="flex items-center gap-2">
                                    <input type="hidden" :name="`addons[${idx}][id]`" :value="addon.id">
                                    
                                    <button type="button" @click="addon.selectedQty = Math.max(0, (parseInt(addon.selectedQty) || 0) - 1)"
                                            class="w-7 h-7 rounded-lg bg-white border border-stone-300 text-stone-600 font-bold hover:bg-stone-100 flex items-center justify-center text-xs transition">
                                        -
                                    </button>

                                    <input type="number" :name="`addons[${idx}][qty]`" min="0" max="1000" 
                                           x-model.number="addon.selectedQty"
                                           class="w-14 px-2 py-1.5 rounded-lg border border-stone-300 bg-white text-center text-xs font-bold text-stone-800 focus:outline-none focus:ring-1 focus:ring-gold-500"
                                           placeholder="0">

                                    <button type="button" @click="addon.selectedQty = (parseInt(addon.selectedQty) || 0) + 1"
                                            class="w-7 h-7 rounded-lg bg-white border border-stone-300 text-stone-600 font-bold hover:bg-stone-100 flex items-center justify-center text-xs transition">
                                        +
                                    </button>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </section>

            <!-- ORDER SUMMARY CARD (ELEGANT RECEIPT PREVIEW) -->
            <section class="bg-gradient-to-br from-cream-100/80 via-cream-50 to-cream-100/40 border border-gold-200/80 rounded-2xl p-6 space-y-4">
                <div class="flex items-center justify-between border-b border-gold-200/60 pb-3">
                    <span class="font-serif font-bold text-xs uppercase tracking-widest text-gold-800">Rincian Perhitungan Biaya</span>
                    <span class="text-[10px] text-stone-400 uppercase font-mono tracking-wider">Automated Quotation</span>
                </div>

                <div class="space-y-2 text-xs text-stone-600">
                    <div class="flex justify-between items-center">
                        <span>Paket: <strong class="text-stone-800" x-text="currentPackageName"></strong></span>
                        <span class="font-medium text-stone-900" x-text="formatRupiah(currentPackagePrice)"></span>
                    </div>

                    <template x-for="addon in activeAddons" :key="addon.id">
                        <div class="flex justify-between items-center text-stone-600">
                            <span>Add-on: <span x-text="addon.name + ' (' + addon.selectedQty + ' ' + addon.unit + ')'"></span></span>
                            <span class="font-medium text-stone-900" x-text="formatRupiah(addon.selectedQty * addon.unit_price)"></span>
                        </div>
                    </template>
                </div>

                <div class="border-t border-gold-200/60 pt-3 flex justify-between items-baseline">
                    <span class="font-serif font-bold text-sm text-obsidian">Total Estimasi Biaya</span>
                    <span class="font-serif font-bold text-xl text-gold-700" x-text="formatRupiah(grandTotal)"></span>
                </div>
            </section>

            <!-- STEP 3: PEMBAYARAN & BUKTI TRANSFER -->
            <section class="space-y-6">
                <div class="flex items-center gap-3 border-b border-stone-100 pb-4">
                    <span class="w-7 h-7 rounded-full bg-gold-100 text-gold-800 font-serif font-bold text-xs flex items-center justify-center border border-gold-200">
                        3
                    </span>
                    <div>
                        <h2 class="font-serif text-lg font-bold text-obsidian">Metode Pembayaran & Bukti Transfer</h2>
                        <p class="text-[11px] text-stone-500">Pilih opsi pembayaran uang muka (DP) atau pelunasan 100%.</p>
                    </div>
                </div>

                <!-- Payment Type Selector -->
                <div class="grid grid-cols-2 gap-4">
                    <label class="relative p-4 rounded-2xl border text-center cursor-pointer transition flex flex-col items-center justify-center"
                           :class="paymentType === 'dp' 
                               ? 'border-gold-500 bg-cream-50/70 ring-2 ring-gold-400 font-bold shadow-sm' 
                               : 'border-stone-200 hover:border-gold-300'">
                        <input type="radio" name="payment_type" value="dp" class="sr-only" x-model="paymentType">
                        <span class="text-xs text-obsidian font-serif font-bold block">Uang Muka (DP)</span>
                        <span class="text-[10px] text-stone-500 mt-0.5">Kunci tanggal dengan DP minimal</span>
                    </label>

                    <label class="relative p-4 rounded-2xl border text-center cursor-pointer transition flex flex-col items-center justify-center"
                           :class="paymentType === 'lunas' 
                               ? 'border-gold-500 bg-cream-50/70 ring-2 ring-gold-400 font-bold shadow-sm' 
                               : 'border-stone-200 hover:border-gold-300'">
                        <input type="radio" name="payment_type" value="lunas" class="sr-only" x-model="paymentType">
                        <span class="text-xs text-obsidian font-serif font-bold block">Pelunasan Penuh (100%)</span>
                        <span class="text-[10px] text-stone-500 mt-0.5">Selesaikan seluruh pembayaran</span>
                    </label>
                </div>

                <!-- DP Nominal Input Box -->
                <div x-show="paymentType === 'dp'" class="p-5 bg-cream-50/60 rounded-2xl border border-stone-200 space-y-2">
                    <label class="block text-xs font-semibold text-stone-700 uppercase tracking-wider">
                        Nominal DP yang Ditransfer <span class="text-gold-600">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute left-4 top-3 text-xs font-bold text-gold-700">Rp</span>
                        <input type="number" name="nominal_dp" x-model.number="nominalDp"
                               class="w-full pl-11 pr-4 py-3 rounded-xl bg-white border border-stone-200 text-sm font-semibold text-stone-900 focus:ring-2 focus:ring-gold-400 focus:outline-none transition"
                               placeholder="5000000">
                    </div>
                    <div class="flex justify-between items-center text-xs text-stone-500 pt-1">
                        <span>Sisa pelunasan nanti: <strong class="text-stone-800" x-text="formatRupiah(remainingBalance)"></strong></span>
                        <span class="text-[11px] text-gold-700">Minimal DP: <span x-text="formatRupiah(minDpAmount)"></span></span>
                    </div>
                </div>

                <!-- LUXURY METALLIC BANK CARD -->
                <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-stone-900 via-stone-800 to-obsidian p-6 text-white shadow-xl">
                    <div class="absolute -right-8 -top-8 w-32 h-32 rounded-full bg-gold-400/10 blur-xl"></div>
                    <div class="flex items-center justify-between mb-4">
                        <span class="text-xs font-mono font-bold tracking-widest text-gold-300 uppercase">Rekening Resmi Pembayaran</span>
                        <span class="text-[10px] px-2.5 py-0.5 rounded-full bg-gold-400/20 text-gold-300 border border-gold-400/30">Bank BCA</span>
                    </div>
                    <div class="space-y-1">
                        <span class="text-[11px] text-stone-400 uppercase tracking-wider">Nomor Rekening:</span>
                        <p class="font-mono text-xl sm:text-2xl font-bold tracking-widest text-gold-200">8831 294 819</p>
                    </div>
                    <div class="mt-4 pt-3 border-t border-stone-700/60 flex items-center justify-between text-xs text-stone-300">
                        <span>Atas Nama: <strong class="text-white">NK Barokah Wedding Organizer</strong></span>
                        <span class="text-[10px] text-stone-400">Verifikasi 1x24 Jam</span>
                    </div>
                </div>

                <!-- FILE UPLOAD DROPZONE WITH PREVIEW -->
                <div class="space-y-2" x-data="{ previewUrl: null }">
                    <label class="block text-xs font-semibold text-stone-700 uppercase tracking-wider">
                        Unggah Struk / Bukti Transfer <span class="text-gold-600">*</span>
                    </label>

                    <div class="relative border-2 border-dashed border-stone-300 rounded-2xl p-6 text-center hover:border-gold-400 bg-cream-50/30 transition">
                        <input type="file" name="payment_proof" required accept="image/*,application/pdf"
                               @change="const file = $event.target.files[0]; if (file && file.type.startsWith('image/')) { previewUrl = URL.createObjectURL(file); } else { previewUrl = null; }"
                               class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">

                        <div class="space-y-2">
                            <span class="text-3xl block">📎</span>
                            <div class="text-xs text-stone-600 font-medium">
                                <span class="text-gold-700 font-bold underline">Pilih Berkas</span> atau tarik file bukti transfer ke sini
                            </div>
                            <p class="text-[11px] text-stone-400">Mendukung format JPG, PNG, atau PDF (Maksimal 3MB)</p>
                        </div>

                        <!-- Image Preview -->
                        <template x-if="previewUrl">
                            <div class="mt-4 pt-4 border-t border-stone-200/60">
                                <span class="text-[11px] text-stone-500 block mb-2 font-medium">Preview Struk Transfer:</span>
                                <img :src="previewUrl" alt="Preview Bukti" class="max-h-48 mx-auto rounded-xl border border-stone-300 shadow-sm object-cover">
                            </div>
                        </template>
                    </div>
                </div>
            </section>

            <!-- SUBMIT BUTTON -->
            <div class="pt-4">
                <button type="submit" :disabled="isDateBooked"
                        class="w-full py-4 rounded-2xl bg-gradient-to-r from-obsidian via-stone-900 to-obsidian hover:from-gold-700 hover:to-gold-600 disabled:from-stone-300 disabled:to-stone-400 text-white font-serif font-bold text-sm sm:text-base tracking-wide transition duration-300 shadow-lg hover:shadow-xl active:scale-[0.99] flex items-center justify-center gap-2">
                    <span>Konfirmasi Reservasi & Dapatkan Kuitansi Resmi</span>
                    <span class="text-gold-300">✦</span>
                </button>
                <p class="text-[11px] text-center text-stone-400 mt-3">
                    Dengan menekan tombol di atas, data pesanan dan jadwal Anda akan langsung terdaftar di sistem kami.
                </p>
            </div>

        </form>

        <!-- Footer Branding -->
        <footer class="mt-12 text-center text-xs text-stone-400 space-y-1">
            <p>&copy; {{ date('Y') }} NK Barokah Wedding Organizer & Event Management.</p>
            <p class="text-[11px]">Yogyakarta & Solo | Layanan Reservasi Digital Sah</p>
        </footer>

    </div>

<script>
function bookingApp(config) {
    return {
        packages: config.packages,
        addons: config.addons.map(a => ({ ...a, selectedQty: 0 })),
        bookedDates: config.bookedDates,
        minDpAmount: config.minDpAmount,
        selectedPackageId: config.packages.length > 0 ? config.packages[0].id : null,
        selectedDate: '{{ old("event_date", "") }}',
        paymentType: '{{ old("payment_type", "dp") }}',
        nominalDp: {{ old('nominal_dp') ? old('nominal_dp') : 'config.minDpAmount' }},

        get isDateBooked() {
            return this.selectedDate && this.bookedDates.includes(this.selectedDate);
        },
        get currentPackage() {
            return this.packages.find(p => p.id == this.selectedPackageId);
        },
        get currentPackageName() {
            return this.currentPackage ? this.currentPackage.name : '-';
        },
        get currentPackagePrice() {
            return this.currentPackage ? parseFloat(this.currentPackage.base_price) : 0;
        },
        get activeAddons() {
            return this.addons.filter(a => parseInt(a.selectedQty) > 0);
        },
        get addonTotalPrice() {
            return this.activeAddons.reduce((sum, a) => sum + (parseInt(a.selectedQty) * parseFloat(a.unit_price)), 0);
        },
        get grandTotal() {
            return this.currentPackagePrice + this.addonTotalPrice;
        },
        get remainingBalance() {
            if (this.paymentType === 'lunas') return 0;
            const diff = this.grandTotal - (parseFloat(this.nominalDp) || 0);
            return diff > 0 ? diff : 0;
        },
        formatRupiah(val) {
            return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(val || 0);
        }
    }
}
</script>
</body>
</html>
