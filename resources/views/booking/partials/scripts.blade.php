    <!-- Alpine.js Reservation App Logic -->
    <script>
    function bookingReservation(config) {
        return {
            step: 1,
            packages: config.packages,
            addons: config.addons.map(a => ({ ...a, selectedQty: 0 })),
            bookedDates: config.bookedDates,
            minDpAmount: config.minDpAmount,
            standardDays: config.standardDays || 2,
            extraDayRate: config.extraDayRate || 0.15,
            warehouseLat: config.warehouseLat || -6.9857806,
            warehouseLng: config.warehouseLng || 109.136971,
            warehouseAddress: config.warehouseAddress || 'Jl. K.H. Ahmad Dahlan, RT 04 / RW 01, Slawi Kulon, Kec. Slawi, Kab. Tegal',
            freeDistanceKm: config.freeDistanceKm || 10,
            distanceTierKm: config.distanceTierKm || 10,
            transportFeePerTier: config.transportFeePerTier || 300000,
            deliveryDistanceKm: config.oldDistanceKm || 0,
            selectedDistanceTier: (config.oldDistanceKm > 40 ? 'gt_40' : (config.oldDistanceKm > 30 ? '31_40' : (config.oldDistanceKm > 20 ? '21_30' : (config.oldDistanceKm > 10 ? '11_20' : '0_10')))),
            gpsLoading: false,
            gpsStatus: '',

            // Form inputs
            eventTypeOpen: false,
            eventType: '{{ old("event_type", "pernikahan") }}',
            childName: '{{ old("child_name", "") }}',
            eventTypes: [
                { id: 'pernikahan', label: 'Pernikahan (Akad & Resepsi)' },
                { id: 'khitanan', label: 'Khitanan / Sunatan' },
                { id: 'tasyakuran', label: 'Tasyakuran / Pengajian / Aqiqah' },
                { id: 'instansi', label: 'Acara Instansi / Komunitas / Event' },
                { id: 'sewa_alat', label: 'Sewa Alat Pesta Saja (Tanpa Tenda)' }
            ],

            setEventType(type) {
                this.eventType = type;
                this.eventTypeOpen = false;
                if (type === 'sewa_alat') {
                    const customPkg = this.packages.find(p => 
                        p.base_price == 0 || 
                        (p.name && p.name.toLowerCase().includes('custom')) ||
                        (p.name && p.name.toLowerCase().includes('sewa alat')) ||
                        (p.slug && (p.slug.includes('tanpa-tenda') || p.slug.includes('custom')))
                    );
                    if (customPkg) {
                        this.selectedPackageId = customPkg.id;
                    }
                    this.addonsManualOpen = true;
                    this.surveyDate = '';
                    this.instagramAccount = '';
                    this.parentsName = '';
                } else {
                    const curPkg = this.packages.find(p => p.id == this.selectedPackageId);
                    if (!curPkg || curPkg.base_price == 0) {
                        const firstTentPkg = this.packages.find(p => p.base_price > 0);
                        if (firstTentPkg) {
                            this.selectedPackageId = firstTentPkg.id;
                        }
                    }
                }
            },

            get step1Heading() {
                switch(this.eventType) {
                    case 'sewa_alat': return 'Kapan tanggal penggunaan alat sewa?';
                    case 'khitanan': return 'Kapan tanggal acara khitanan?';
                    case 'tasyakuran': return 'Kapan tanggal tasyakuran / pengajian?';
                    case 'instansi': return 'Kapan jadwal kegiatan instansi?';
                    default: return 'Kapan tanggal pernikahan Anda?';
                }
            },
            get step1Subtitle() {
                return 'Pilih tanggal pelaksanaan yang masih tersedia pada kalender.';
            },

            get step2Badge() {
                return this.eventType === 'sewa_alat' ? 'Langkah 2 dari 4 • Pilih Alat Sewa' : 'Langkah 2 dari 4 • Paket & Layanan';
            },
            get step2Heading() {
                switch(this.eventType) {
                    case 'sewa_alat': return 'Pilih alat pesta yang disewa';
                    case 'khitanan': return 'Pilih paket acara khitanan Anda';
                    case 'tasyakuran': return 'Pilih paket tasyakuran / pengajian';
                    case 'instansi': return 'Pilih paket kegiatan instansi Anda';
                    default: return 'Pilih paket pernikahan Anda';
                }
            },
            get step2Subtitle() {
                if (this.eventType === 'sewa_alat') {
                    return 'Pilih perlengkapan alat pesta lepas sesuai kebutuhan venue acara Anda.';
                }
                return 'Ketuk paket untuk memilih. Anda juga dapat menambahkan perlengkapan tambahan di bawah.';
            },

            get step3Badge() {
                switch(this.eventType) {
                    case 'sewa_alat': return 'Langkah 3 dari 4 • Data Penyewa Alat';
                    case 'instansi': return 'Langkah 3 dari 4 • Data Penanggung Jawab';
                    case 'pernikahan': return 'Langkah 3 dari 4 • Data Calon Pengantin';
                    default: return 'Langkah 3 dari 4 • Data Pemesan Acara';
                }
            },
            get step3Heading() {
                switch(this.eventType) {
                    case 'sewa_alat': return 'Data Pemesan / Penyewa Alat';
                    case 'instansi': return 'Data Instansi & Penanggung Jawab';
                    case 'pernikahan': return 'Data Calon Pengantin';
                    default: return 'Data Pemesan / Tuan Rumah';
                }
            },
            get step3Subtitle() {
                switch(this.eventType) {
                    case 'sewa_alat': return 'Masukkan nama lengkap dan kontak WhatsApp untuk konfirmasi pengiriman alat sewa.';
                    case 'instansi': return 'Masukkan nama instansi/organisasi, PIC, dan kontak WhatsApp untuk koordinasi kegiatan.';
                    case 'pernikahan': return 'Masukkan nama kedua mempelai dan kontak WhatsApp untuk koordinasi dan surat perjanjian resmi.';
                    default: return 'Masukkan nama tuan rumah dan kontak WhatsApp untuk koordinasi acara resmi.';
                }
            },
            get customerNameLabel() {
                switch(this.eventType) {
                    case 'sewa_alat': return 'Nama Lengkap Pemesan';
                    case 'instansi': return 'Nama Instansi / Komunitas & PIC';
                    case 'khitanan': return 'Nama Orang Tua / Tuan Rumah';
                    case 'tasyakuran': return 'Nama Pemesan / Tuan Rumah';
                    default: return 'Nama Calon Pengantin (Kedua Mempelai)';
                }
            },
            get customerNamePlaceholder() {
                switch(this.eventType) {
                    case 'sewa_alat': return 'Cth: Citra Lestari / Bpk. Hendra';
                    case 'instansi': return 'Cth: PT Barokah Abadi (PIC: Budi Prasetyo)';
                    case 'khitanan': return 'Cth: Bpk. H. Ahmad & Ibu Siti';
                    case 'tasyakuran': return 'Cth: Bpk. H. Rahmat / Ibu Hj. Aminah';
                    default: return 'Nama kedua mempelai (cth: Reza & Dian)';
                }
            },
            get customerNameHint() {
                switch(this.eventType) {
                    case 'sewa_alat': return 'Nama pemesan akan tercantum pada invoice dan tanda terima sewa resmi.';
                    case 'instansi': return 'Nama instansi dan PIC akan tercantum pada invoice dan SPK resmi.';
                    case 'pernikahan': return 'Nama kedua mempelai akan tercantum pada invoice dan surat perjanjian resmi.';
                    default: return 'Nama pemesan akan tercantum pada invoice dan surat perjanjian resmi.';
                }
            },
            get venueAddressLabel() {
                switch(this.eventType) {
                    case 'sewa_alat': return 'Alamat Lengkap Pengiriman Alat / Venue';
                    case 'instansi': return 'Alamat Lengkap Lokasi Kegiatan';
                    default: return 'Alamat Lengkap Lokasi Acara (Pemasangan Tenda)';
                }
            },
            get venueAddressPlaceholder() {
                switch(this.eventType) {
                    case 'sewa_alat': return 'Alamat rumah / gedung / hotel tujuan pengantaran alat sewa';
                    case 'instansi': return 'Nama gedung / aula / kantor / alamat lengkap kegiatan';
                    default: return 'Jl. Nama Jalan, No. Rumah, RT/RW, Kelurahan, Kecamatan';
                }
            },
            get eventTypeLabel() {
                const found = this.eventTypes.find(e => e.id === this.eventType);
                return found ? found.label : 'Pernikahan (Akad & Resepsi)';
            },

            customerName: '{{ old("customer_name", "") }}'.toUpperCase(),
            phoneDisplay: '',
            rawPhoneDigits: '',
            customerEmail: '{{ old("customer_email", "") }}'.toLowerCase(),
            venueAddress: '{{ old("venue_address", "") }}',
            addressLandmark: '{{ old("address_landmark", "") }}',
            parentsName: '{{ old("parents_name", "") }}',
            instagramAccount: '{{ old("instagram_account", "") }}',
            eventTime: '{{ old("event_time", "") }}',
            vendorName: '{{ old("vendor_name", "") }}',
            vendorMua: '{{ old("vendor_mua", "") }}',
            vendorDecor: '{{ old("vendor_decor", "") }}',
            vendorPhotoVideo: '{{ old("vendor_photo_video", "") }}',
            vendorMcWo: '{{ old("vendor_mc_wo", "") }}',
            surveyRequested: {{ old('survey_requested') ? 'true' : 'false' }},
            surveyDate: '{{ old("survey_date", "") }}',
            surveyTimeSlot: '{{ old("survey_time_slot", "morning") }}',
            surveyNotes: '{{ old("survey_notes", "") }}',
            tentColorOpen: false,
            tentColorCustomMode: false,
            tentColorPresets: [
                { name: 'Putih + Mocca', dot: 'bg-[#C4A482]' },
                { name: 'Putih + Pink', dot: 'bg-[#F4A7B9]' },
                { name: 'Putih + Sage Green', dot: 'bg-[#8FBC8F]' },
                { name: 'Putih + Biru Navy', dot: 'bg-[#1E3A8A]' },
                { name: 'Full Putih', dot: 'bg-white border border-slate-300' },
                { name: 'Kuning / Adat', dot: 'bg-[#EAB308]' },
                { name: 'Full Hitam', dot: 'bg-[#18181B]' }
            ],
            get selectedTentColorDot() {
                const found = this.tentColorPresets.find(p => p.name === this.tentColorTheme);
                return found ? found.dot : '';
            },
            tentColorTheme: '{{ old("tent_color_theme", "") }}',
            customTentSize: '{{ old("custom_tent_size", "") }}',
            operationalNotes: '{{ old("operational_notes", "") }}',
            get minSurveyDate() {
                const d = new Date();
                d.setDate(d.getDate() + 1);
                return d.toISOString().split('T')[0];
            },
            get maxSurveyDate() {
                if (!this.selectedDate) return '';
                const d = new Date(this.selectedDate);
                d.setDate(d.getDate() - 1);
                return d.toISOString().split('T')[0];
            },
            selectedDate: config.oldEventDate || '',
            selectedEndDate: config.oldEventEndDate || config.oldEventDate || '',
            pickingRange: false,
            hoverDate: null,
            selectedPackageId: config.packages.length > 0 ? config.packages[0].id : null,
            paymentType: '{{ old("payment_type", "dp") }}',
            nominalDp: {{ old('nominal_dp') ? old('nominal_dp') : 'config.minDpAmount' }},
            nominalDpDisplay: '',
            activeAddonCategory: 'all',
            agreedToTerms: false,

            getAddonCategory(name) {
                const n = (name || '').toLowerCase();
                if (n.includes('kursi') || n.includes('meja')) return 'kursi_meja';
                if (n.includes('kipas') || n.includes('blower')) return 'kipas';
                if (n.includes('panggung') || n.includes('flooring') || n.includes('karpet')) return 'panggung';
                if (n.includes('sound')) return 'sound';
                if (n.includes('prasmanan') || n.includes('pondokan')) return 'prasmanan';
                if (n.includes('tenda') || n.includes('lampu') || n.includes('cover')) return 'tenda';
                return 'lainnya';
            },

            // Inline Reactive Form Validation Errors
            formErrors: {
                step1: null,
                step2: null,
                step3: null,
                step4: null,
                customer_name: null,
                customer_phone: null,
                venue_address: null,
                survey_date: null,
                payment_proof: null
            },

            // Calendar state
            calYear: 2026,
            calMonth: 10, // November (0-indexed)
            monthOpen: false,
            yearOpen: false,
            monthNames: [
                'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
            ],

            activeBank: 'bca',
            banks: {
                bca: {
                    name: 'Bank BCA',
                    number: '{{ $settings['bca_account_number'] ?? '3620648107' }}',
                    formatted: '{{ wordwrap($settings['bca_account_number'] ?? '3620648107', 4, ' ', true) }}',
                    holder: '{{ $settings['bca_account_name'] ?? 'EZZU RAKHMAT FIRDAUS' }}'
                },
                bri: {
                    name: 'Bank BRI',
                    number: '{{ $settings['bri_account_number'] ?? '344301026581532' }}',
                    formatted: '{{ wordwrap($settings['bri_account_number'] ?? '344301026581532', 4, ' ', true) }}',
                    holder: '{{ $settings['bri_account_name'] ?? 'EZZU RAKHMAT FIRDAUS' }}'
                }
            },
            copiedBank: false,
            previewUrl: null,
            fileName: '',

            init() {
                const now = new Date();
                if (this.selectedDate) {
                    this.syncCalendarToDate(this.selectedDate);
                } else {
                    this.calYear = now.getFullYear();
                    this.calMonth = now.getMonth();
                }

                if (this.customerName) {
                    this.customerName = this.customerName.toUpperCase();
                }

                const oldPhone = '{{ old("customer_phone", "") }}';
                if (oldPhone) {
                    this.setPhoneValue(oldPhone);
                }

                if (this.nominalDp) {
                    this.nominalDpDisplay = new Intl.NumberFormat('id-ID').format(this.nominalDp);
                }

                if (this.eventType === 'sewa_alat') {
                    this.setEventType('sewa_alat');
                }

                if (this.tentColorTheme && !this.tentColorPresets.find(p => p.name === this.tentColorTheme)) {
                    this.tentColorCustomMode = true;
                }
            },

            formatPhoneNumber(raw) {
                let digits = (raw || '').replace(/\D/g, '');
                if (digits.startsWith('62')) {
                    digits = digits.substring(2);
                }
                digits = digits.replace(/^0+/, '');
                if (digits.length > 13) {
                    digits = digits.substring(0, 13);
                }

                let formatted = '';
                if (digits.length <= 3) {
                    formatted = digits;
                } else if (digits.length <= 7) {
                    formatted = digits.slice(0, 3) + '-' + digits.slice(3);
                } else if (digits.length <= 11) {
                    formatted = digits.slice(0, 3) + '-' + digits.slice(3, 7) + '-' + digits.slice(7);
                } else {
                    formatted = digits.slice(0, 3) + '-' + digits.slice(3, 7) + '-' + digits.slice(7, 11) + '-' + digits.slice(11);
                }

                return { digits, formatted };
            },

            setPhoneValue(val) {
                const { digits, formatted } = this.formatPhoneNumber(val);
                this.rawPhoneDigits = digits;
                this.phoneDisplay = formatted;
            },

            handlePhoneInput(event) {
                const { digits, formatted } = this.formatPhoneNumber(event.target.value);
                this.rawPhoneDigits = digits;
                this.phoneDisplay = formatted;
                event.target.value = formatted;

                this.formErrors.customer_phone = null;
                this.formErrors.step3 = null;
            },

            setNominalDp(amount) {
                this.nominalDp = amount;
                this.nominalDpDisplay = new Intl.NumberFormat('id-ID').format(amount);
                this.formErrors.step4 = null;
            },

            handleNominalDpInput(event) {
                const raw = event.target.value.replace(/\D/g, '');
                if (!raw) {
                    this.nominalDp = 0;
                    this.nominalDpDisplay = '';
                    event.target.value = '';
                    return;
                }
                const num = parseInt(raw, 10);
                this.nominalDp = num;
                const formatted = new Intl.NumberFormat('id-ID').format(num);
                this.nominalDpDisplay = formatted;
                event.target.value = formatted;
                this.formErrors.step4 = null;
            },

            get stepTitle() {
                if (this.step === 1) return 'Jadwal Acara';
                if (this.step === 2) return 'Pilih Paket';
                if (this.step === 3) return 'Data Pemesan';
                return 'Pembayaran';
            },

            get calendarGrid() {
                const year = this.calYear;
                const month = this.calMonth;
                const firstDayIndex = new Date(year, month, 1).getDay();
                const totalDays = new Date(year, month + 1, 0).getDate();
                
                const cells = [];
                const prevMonthTotal = new Date(year, month, 0).getDate();
                for (let i = firstDayIndex - 1; i >= 0; i--) {
                    cells.push({
                        day: prevMonthTotal - i,
                        dateString: null,
                        isCurrentMonth: false,
                        isPast: true,
                        isBooked: false,
                        isToday: false
                    });
                }

                const today = new Date();
                today.setHours(0,0,0,0);

                for (let d = 1; d <= totalDays; d++) {
                    const mStr = String(month + 1).padStart(2, '0');
                    const dStr = String(d).padStart(2, '0');
                    const dateStr = `${year}-${mStr}-${dStr}`;
                    const dayDate = new Date(year, month, d);
                    dayDate.setHours(0,0,0,0);

                    const isPast = dayDate < today;
                    const isBooked = this.bookedDates.includes(dateStr);
                    const isToday = dayDate.getTime() === today.getTime();

                    cells.push({
                        day: d,
                        dateString: dateStr,
                        isCurrentMonth: true,
                        isPast: isPast,
                        isBooked: isBooked,
                        isToday: isToday
                    });
                }
                return cells;
            },

            prevCalMonth() {
                if (this.calMonth === 0) {
                    this.calMonth = 11;
                    this.calYear--;
                } else {
                    this.calMonth--;
                }
            },

            nextCalMonth() {
                if (this.calMonth === 11) {
                    this.calMonth = 0;
                    this.calYear++;
                } else {
                    this.calMonth++;
                }
            },

            goToTodayMonth() {
                const now = new Date();
                this.calYear = now.getFullYear();
                this.calMonth = now.getMonth();
            },

            syncCalendarToDate(dateStr) {
                if (!dateStr) return;
                const parts = dateStr.split('-');
                if (parts.length === 3) {
                    this.calYear = parseInt(parts[0], 10);
                    this.calMonth = parseInt(parts[1], 10) - 1;
                }
            },

            selectDate(dateStr) {
                this.formErrors.step1 = null;
                if (this.bookedDates.includes(dateStr)) return;

                if (!this.selectedDate || (!this.pickingRange && this.selectedEndDate)) {
                    // First click: sets start date
                    this.selectedDate = dateStr;
                    this.selectedEndDate = null;
                    this.hoverDate = null;
                    this.pickingRange = true;
                } else if (this.pickingRange) {
                    // Second click
                    if (dateStr < this.selectedDate) {
                        // Clicked earlier date: reset start to this earlier date
                        this.selectedDate = dateStr;
                        this.selectedEndDate = null;
                        this.hoverDate = null;
                        this.pickingRange = true;
                    } else if (dateStr === this.selectedDate) {
                        // Clicked same date: 1 day duration
                        this.selectedEndDate = dateStr;
                        this.hoverDate = null;
                        this.pickingRange = false;
                    } else {
                        // Clicked later date: check if any date in between is booked
                        if (this.hasBookedDateBetween(this.selectedDate, dateStr)) {
                            this.formErrors.step1 = 'Rentang tanggal yang Anda pilih melewati tanggal yang sudah terisi (PENUH). Silakan pilih rentang tanggal lain.';
                            return;
                        }
                        this.selectedEndDate = dateStr;
                        this.hoverDate = null;
                        this.pickingRange = false;
                    }
                }
            },

            onDateHover(dateStr) {
                if (this.pickingRange && this.selectedDate && dateStr >= this.selectedDate) {
                    this.hoverDate = dateStr;
                }
            },

            hasBookedDateBetween(startStr, endStr) {
                const parts1 = startStr.split('-');
                const parts2 = endStr.split('-');
                const start = new Date(parseInt(parts1[0], 10), parseInt(parts1[1], 10) - 1, parseInt(parts1[2], 10));
                const end = new Date(parseInt(parts2[0], 10), parseInt(parts2[1], 10) - 1, parseInt(parts2[2], 10));
                const curr = new Date(start);

                while (curr <= end) {
                    const y = curr.getFullYear();
                    const m = String(curr.getMonth() + 1).padStart(2, '0');
                    const d = String(curr.getDate()).padStart(2, '0');
                    const dStr = `${y}-${m}-${d}`;
                    if (this.bookedDates.includes(dStr)) {
                        return true;
                    }
                    curr.setDate(curr.getDate() + 1);
                }
                return false;
            },

            resetDateRange() {
                this.selectedDate = '';
                this.selectedEndDate = '';
                this.hoverDate = null;
                this.pickingRange = false;
                this.formErrors.step1 = null;
            },

            get effectiveEndDate() {
                if (this.selectedEndDate) return this.selectedEndDate;
                if (this.pickingRange && this.hoverDate && this.hoverDate > this.selectedDate) {
                    return this.hoverDate;
                }
                return this.selectedDate;
            },

            get durationDays() {
                if (!this.selectedDate) return 0;
                const endStr = this.selectedEndDate || this.selectedDate;
                const parts1 = this.selectedDate.split('-');
                const parts2 = endStr.split('-');
                const d1 = new Date(parseInt(parts1[0], 10), parseInt(parts1[1], 10) - 1, parseInt(parts1[2], 10));
                const d2 = new Date(parseInt(parts2[0], 10), parseInt(parts2[1], 10) - 1, parseInt(parts2[2], 10));
                const diffTime = d2.getTime() - d1.getTime();
                return Math.max(1, Math.round(diffTime / (1000 * 60 * 60 * 24)) + 1);
            },

            get isSingleDateSelection() {
                const end = this.effectiveEndDate;
                return !end || end === this.selectedDate;
            },

            isStartDate(dateStr) {
                return this.selectedDate === dateStr;
            },

            isEndDate(dateStr) {
                const end = this.effectiveEndDate;
                return end === dateStr && end !== this.selectedDate;
            },

            isInRange(dateStr) {
                if (!this.selectedDate || !dateStr) return false;
                const end = this.effectiveEndDate;
                if (!end || end === this.selectedDate) {
                    return this.selectedDate === dateStr;
                }
                return dateStr >= this.selectedDate && dateStr <= end;
            },

            isBetweenDates(dateStr) {
                if (!this.selectedDate || !dateStr) return false;
                const end = this.effectiveEndDate;
                if (!end || end === this.selectedDate) return false;
                return dateStr > this.selectedDate && dateStr < end;
            },

            get isDateBooked() {
                if (!this.selectedDate) return false;
                if (this.bookedDates.includes(this.selectedDate)) return true;
                const endStr = this.selectedEndDate || this.selectedDate;
                if (endStr !== this.selectedDate) {
                    return this.hasBookedDateBetween(this.selectedDate, endStr);
                }
                return false;
            },

            formatSingleDate(dateStr) {
                if (!dateStr) return '';
                const parts = dateStr.split('-');
                if (parts.length !== 3) return dateStr;
                const d = new Date(parseInt(parts[0], 10), parseInt(parts[1], 10) - 1, parseInt(parts[2], 10));
                return d.toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' });
            },

            get formattedSelectedDate() {
                if (!this.selectedDate) return '';
                const endStr = this.selectedEndDate || this.selectedDate;
                const days = this.durationDays;
                const dayLabel = `${days} Hari`;
                if (endStr === this.selectedDate || days === 1) {
                    return `${this.formatSingleDate(this.selectedDate)} (${dayLabel})`;
                }
                const pStart = this.selectedDate.split('-');
                const pEnd = endStr.split('-');
                if (pStart[0] === pEnd[0] && pStart[1] === pEnd[1]) {
                    const dayStart = parseInt(pStart[2], 10);
                    return `${dayStart} – ${this.formatSingleDate(endStr)} (${dayLabel})`;
                }
                return `${this.formatSingleDate(this.selectedDate)} – ${this.formatSingleDate(endStr)} (${dayLabel})`;
            },

            get displayPackages() {
                if (this.eventType === 'sewa_alat') return [];
                return this.packages.filter(p => {
                    const price = parseFloat(p.base_price) || 0;
                    const name = (p.name || '').toLowerCase();
                    const slug = (p.slug || '').toLowerCase();
                    return price > 0 && !name.includes('sewa alat') && !name.includes('custom') && !slug.includes('tanpa-tenda') && !slug.includes('custom');
                });
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

            get isCustomPackage() {
                if (!this.currentPackage) return false;
                const name = (this.currentPackage.name || '').toLowerCase();
                const slug = (this.currentPackage.slug || '').toLowerCase();
                return this.currentPackagePrice === 0 
                    || name.includes('custom') 
                    || name.includes('sewa alat') 
                    || name.includes('tanpa tenda') 
                    || slug.includes('custom') 
                    || slug.includes('tanpa-tenda');
            },

            get effectiveMinDp() {
                if (this.grandTotal <= 0) return this.minDpAmount;
                return Math.min(this.minDpAmount, this.grandTotal);
            },

            addonsManualOpen: null,

            get isAddonsOpen() {
                // Paket Custom SELALU turun / terbuka otomatis!
                if (this.isCustomPackage) return true;
                // Jika user sempat klik toggle manual, ikuti preferensi user
                if (this.addonsManualOpen !== null) return this.addonsManualOpen;
                // Jika user sudah memilih item perlengkapan > 0, biarkan terbuka
                return this.activeAddons.length > 0;
            },

            toggleAddonsAccordion() {
                this.addonsManualOpen = !this.isAddonsOpen;
            },

            onPackageSelect(pkgId) {
                this.selectedPackageId = pkgId;
                if (this.isCustomPackage) {
                    this.addonsManualOpen = true;
                } else if (this.activeAddons.length === 0) {
                    this.addonsManualOpen = false;
                }
            },

            get activeAddons() {
                return this.addons.filter(a => parseInt(a.selectedQty) > 0);
            },

            get addonTotalPrice() {
                return this.activeAddons.reduce((sum, a) => sum + (parseInt(a.selectedQty) * parseFloat(a.unit_price)), 0);
            },

            get extraDays() {
                return Math.max(0, this.durationDays - this.standardDays);
            },

            get additionalDayCharge() {
                if (this.extraDays <= 0 || !this.currentPackagePrice) return 0;
                return this.extraDays * this.extraDayRate * this.currentPackagePrice;
            },

            selectDistanceTier(tier, km) {
                this.selectedDistanceTier = tier;
                this.deliveryDistanceKm = km;
                this.gpsStatus = km === 0 ? 'Bebas ongkir hingga 10 km' : `Estimasi jarak: ${km} km`;
            },

            detectGpsDistance() {
                if (!navigator.geolocation) {
                    this.gpsStatus = 'Browser tidak mendukung GPS';
                    return;
                }
                this.gpsLoading = true;
                this.gpsStatus = 'Mendeteksi titik koordinat HP...';

                navigator.geolocation.getCurrentPosition(
                    (position) => {
                        const lat1 = position.coords.latitude;
                        const lon1 = position.coords.longitude;
                        const lat2 = parseFloat(this.warehouseLat);
                        const lon2 = parseFloat(this.warehouseLng);

                        const R = 6371;
                        const dLat = (lat2 - lat1) * Math.PI / 180;
                        const dLon = (lon2 - lon1) * Math.PI / 180;
                        const a = Math.sin(dLat/2) * Math.sin(dLat/2) +
                                  Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
                                  Math.sin(dLon/2) * Math.sin(dLon/2);
                        const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
                        const rawDistance = R * c;
                        const roadDistance = Math.round(rawDistance * 1.25 * 10) / 10;

                        this.deliveryDistanceKm = roadDistance;
                        if (roadDistance <= 10) {
                            this.selectedDistanceTier = '0_10';
                        } else if (roadDistance <= 20) {
                            this.selectedDistanceTier = '11_20';
                        } else if (roadDistance <= 30) {
                            this.selectedDistanceTier = '21_30';
                        } else if (roadDistance <= 40) {
                            this.selectedDistanceTier = '31_40';
                        } else {
                            this.selectedDistanceTier = 'gt_40';
                        }
                        this.gpsStatus = `Terdeteksi ~${roadDistance} km (via GPS)`;
                        this.gpsLoading = false;
                    },
                    (error) => {
                        this.gpsLoading = false;
                        this.gpsStatus = 'Gagal mendeteksi GPS (izin ditolak atau timeout)';
                    },
                    { enableHighAccuracy: true, timeout: 10000, maximumAge: 60000 }
                );
            },

            get transportFee() {
                const km = parseFloat(this.deliveryDistanceKm) || 0;
                if (km <= this.freeDistanceKm) return 0;
                const extraKm = km - this.freeDistanceKm;
                const tiers = Math.ceil(extraKm / this.distanceTierKm);
                return tiers * this.transportFeePerTier;
            },

            get grandTotal() {
                return this.currentPackagePrice + this.addonTotalPrice + this.additionalDayCharge + this.transportFee;
            },

            get remainingBalance() {
                if (this.paymentType === 'lunas') return 0;
                const diff = this.grandTotal - (parseFloat(this.nominalDp) || 0);
                return diff > 0 ? diff : 0;
            },

            goToStep(nextStep) {
                // Moving back: clear step errors and allow smooth transition
                if (nextStep < this.step) {
                    this.formErrors.step1 = null;
                    this.formErrors.step2 = null;
                    this.formErrors.step3 = null;
                    this.formErrors.step4 = null;
                    this.step = nextStep;
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                    return;
                }

                if (nextStep === 2) {
                    if (this.pickingRange && this.selectedDate && !this.selectedEndDate) {
                        this.selectedEndDate = this.selectedDate;
                        this.pickingRange = false;
                    }
                    if (!this.selectedDate) {
                        this.formErrors.step1 = 'Silakan pilih tanggal acara terlebih dahulu pada kalender.';
                        return;
                    }
                    if (this.isDateBooked) {
                        this.formErrors.step1 = 'Rentang tanggal yang Anda pilih melewati tanggal yang sudah terisi (PENUH). Silakan pilih rentang tanggal lain.';
                        return;
                    }
                    this.formErrors.step1 = null;
                }

                if (nextStep === 3) {
                    if (!this.selectedPackageId) {
                        this.formErrors.step2 = 'Silakan pilih paket atau perlengkapan sewa terlebih dahulu untuk melanjutkan.';
                        return;
                    }
                    if (this.isCustomPackage && this.activeAddons.length === 0) {
                        this.addonsManualOpen = true;
                        this.formErrors.step2 = 'Untuk paket Custom / Sewa Alat Saja, Anda wajib memilih minimal 1 jenis perlengkapan yang disewa pada daftar di bawah.';
                        const el = document.getElementById('addonsSection');
                        if (el) {
                            el.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        }
                        return;
                    }
                    this.formErrors.step2 = null;
                }

                if (nextStep === 4) {
                    let hasErr = false;
                    const name = (this.customerName || '').trim();
                    const phone = (this.rawPhoneDigits || '').trim();
                    const address = (this.venueAddress || '').trim();

                    if (!name) {
                        this.formErrors.customer_name = this.eventType === 'pernikahan' 
                            ? 'Silakan masukkan nama kedua calon pengantin.' 
                            : 'Silakan masukkan nama pemesan / penanggung jawab.';
                        hasErr = true;
                    } else {
                        this.formErrors.customer_name = null;
                    }

                    if (!phone) {
                        this.formErrors.customer_phone = 'Silakan masukkan nomor WhatsApp aktif.';
                        hasErr = true;
                    } else if (phone.length < 8) {
                        this.formErrors.customer_phone = 'Nomor WhatsApp tidak valid (minimal 8 digit).';
                        hasErr = true;
                    } else {
                        this.formErrors.customer_phone = null;
                    }

                    if (!address) {
                        this.formErrors.venue_address = 'Silakan masukkan alamat lengkap lokasi acara.';
                        hasErr = true;
                    } else {
                        this.formErrors.venue_address = null;
                    }

                    if (this.surveyDate) {
                        if (this.selectedDate && this.surveyDate >= this.selectedDate) {
                            this.formErrors.survey_date = 'Tanggal survey harus sebelum tanggal pelaksanaan acara.';
                            hasErr = true;
                        } else {
                            this.formErrors.survey_date = null;
                        }
                    } else {
                        this.formErrors.survey_date = null;
                    }

                    if (hasErr) {
                        this.formErrors.step3 = 'Silakan lengkapi data pemesan dan alamat acara yang wajib di bawah ini.';
                        this.$nextTick(() => {
                            if (!name) {
                                document.querySelector('input[name="customer_name"]')?.focus();
                            } else if (!phone || phone.length < 8) {
                                document.getElementById('phoneInput')?.focus();
                            } else if (!address) {
                                document.querySelector('textarea[name="venue_address"]')?.focus();
                            } else if (this.surveyDate && this.selectedDate && this.surveyDate >= this.selectedDate) {
                                document.querySelector('input[name="survey_date"]')?.focus();
                            }
                        });
                        return;
                    }
                    this.formErrors.step3 = null;
                }

                this.step = nextStep;
                window.scrollTo({ top: 0, behavior: 'smooth' });
            },

            submitBookingForm() {
                const form = document.getElementById('reservationForm');

                if (this.isCustomPackage && this.activeAddons.length === 0) {
                    this.goToStep(2);
                    this.addonsManualOpen = true;
                    this.formErrors.step2 = 'Untuk paket Custom / Sewa Alat Saja, Anda wajib memilih minimal 1 jenis perlengkapan yang disewa.';
                    return;
                }

                if (this.paymentType === 'dp') {
                    const effectiveMinDp = this.effectiveMinDp;
                    if (!this.nominalDp || this.nominalDp < effectiveMinDp) {
                        this.formErrors.step4 = 'Nominal DP minimal adalah ' + this.formatRupiah(effectiveMinDp) + '.';
                        this.$nextTick(() => {
                            document.getElementById('nominalDpInput')?.focus();
                        });
                        return;
                    }
                    if (this.nominalDp > this.grandTotal) {
                        this.formErrors.step4 = 'Nominal DP tidak boleh melebihi Grand Total tagihan (' + this.formatRupiah(this.grandTotal) + ').';
                        this.$nextTick(() => {
                            document.getElementById('nominalDpInput')?.focus();
                        });
                        return;
                    }
                }

                const fileInput = form.querySelector('input[name="payment_proof"]');
                if (!fileInput || !fileInput.files || fileInput.files.length === 0) {
                    this.formErrors.payment_proof = 'Silakan unggah bukti transfer pembayaran sebelum konfirmasi.';
                    this.formErrors.step4 = 'Bukti transfer pembayaran wajib diunggah untuk mengunci reservasi.';
                    this.$nextTick(() => {
                        fileInput?.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    });
                    return;
                }
                this.formErrors.payment_proof = null;

                if (!this.agreedToTerms) {
                    this.formErrors.step4 = 'Harap centang persetujuan Aturan & Ketentuan Reservasi Resmi sebelum konfirmasi.';
                    this.$nextTick(() => {
                        document.getElementById('termsConsentCheckbox')?.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    });
                    return;
                }

                this.formErrors.step4 = null;
                form.submit();
            },

            copyBankAccount() {
                const current = this.banks[this.activeBank];
                if (current) {
                    navigator.clipboard.writeText(current.number);
                    this.copiedBank = true;
                    setTimeout(() => { this.copiedBank = false; }, 2000);
                }
            },

            handleFileUpload(event) {
                const file = event.target.files[0];
                if (!file) {
                    this.previewUrl = null;
                    this.fileName = '';
                    return;
                }
                this.formErrors.payment_proof = null;
                this.formErrors.step4 = null;
                this.fileName = file.name;
                if (file.type.startsWith('image/')) {
                    this.previewUrl = URL.createObjectURL(file);
                } else {
                    this.previewUrl = null;
                }
            },

            formatRupiah(val) {
                return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(val || 0);
            }
        }
    }
    </script>
