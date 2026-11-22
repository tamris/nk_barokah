@extends('admin.layout')

@section('title', 'Dashboard')

@section('content')
@php
    $ownerName = Auth::user()->name ?? 'Owner NK Barokah';
    $hour = now()->hour;
    $greeting = $hour < 12 ? 'Good Morning' : ($hour < 17 ? 'Good Afternoon' : 'Good Evening');
@endphp

<div @open-booking-detail.window="const target = allBookings.find(b => String(b.id) === String($event.detail.id)); if (target) openDetail(target);"
     x-data="{
        allBookings: @js($bookingsPayload),
        clientsList: @js($clientsPayload),
        packagesList: @js($packagesPayload),
        addonsList: @js($addonsPayload),

        selectedDateIso: '{{ now()->format('Y-m-d') }}',
        scheduleQuery: '',
        stripOffset: 0,
        stripAnimating: false,
        stripTimer: null,

        bookingSearch: '',
        bookingFilter: 'all',
        bookingServiceFilter: 'all',
        statusMenuOpen: false,
        serviceMenuOpen: false,
        bookingPage: 1,
        bookingPerPage: 10,

        currentRealYear: {{ now()->year }},
        currentRealMonth: {{ now()->month }},
        currentRealDay: {{ now()->day }},
        calYear: {{ $initialCalYear }},
        calMonth: {{ $initialCalMonth }},
        calendarView: 'Monthly View',
        calendarViewOpen: false,
        selectedDayModal: null,
        maxEventsPerDay: {{ (int) ($settings['max_events_per_day'] ?? 1) }},
        openDayModal(cell) {
            if (!cell || cell.blank) return;
            this.selectedDayModal = cell;
        },
        closeDayModal() {
            this.selectedDayModal = null;
        },
        monthNames: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
        monthShort: ['JAN', 'FEB', 'MAR', 'APR', 'MAY', 'JUN', 'JUL', 'AUG', 'SEP', 'OCT', 'NOV', 'DEC'],

        clientSearch: '',
        clientFilter: 'all',
        clientMenuOpen: false,
        activeClient: null,
        clientPage: 1,
        clientPerPage: 10,

        serviceSubTab: 'packages',
        serviceSearch: '',

        invoiceSearch: '',
        invoiceFilter: 'all',
        invoiceMenuOpen: false,
        invoicePage: 1,
        invoicePerPage: 10,

        previousTab: 'dashboard',
        detailLoading: false,
        pageLoading: true,

        triggerPageLoading(duration = 650) {
            this.pageLoading = true;
            setTimeout(() => {
                this.pageLoading = false;
            }, duration);
        },

        bookingTableLoading: false,
        _bTimer: null,
        triggerBookingPagination() {
            this.bookingTableLoading = true;
            clearTimeout(this._bTimer);
            this._bTimer = setTimeout(() => {
                this.bookingTableLoading = false;
            }, 260);
        },

        clientTableLoading: false,
        _cTimer: null,
        triggerClientPagination() {
            this.clientTableLoading = true;
            clearTimeout(this._cTimer);
            this._cTimer = setTimeout(() => {
                this.clientTableLoading = false;
            }, 260);
        },

        invoiceTableLoading: false,
        _iTimer: null,
        triggerInvoicePagination() {
            this.invoiceTableLoading = true;
            clearTimeout(this._iTimer);
            this._iTimer = setTimeout(() => {
                this.invoiceTableLoading = false;
            }, 260);
        },

        init() {
            setTimeout(() => {
                this.pageLoading = false;
            }, 600);

            this.$watch('bookingSearch', () => { this.bookingPage = 1; });
            this.$watch('bookingFilter', () => { this.bookingPage = 1; });
            this.$watch('bookingServiceFilter', () => { this.bookingPage = 1; });
            this.$watch('bookingPerPage', () => { this.bookingPage = 1; this.triggerBookingPagination(); });
            this.$watch('bookingPage', () => { this.triggerBookingPagination(); });

            this.$watch('clientSearch', () => { this.clientPage = 1; });
            this.$watch('clientFilter', () => { this.clientPage = 1; });
            this.$watch('clientPerPage', () => { this.clientPage = 1; this.triggerClientPagination(); });
            this.$watch('clientPage', () => { this.triggerClientPagination(); });

            this.$watch('invoiceSearch', () => { this.invoicePage = 1; });
            this.$watch('invoiceFilter', () => { this.invoicePage = 1; });
            this.$watch('invoicePerPage', () => { this.invoicePage = 1; this.triggerInvoicePagination(); });
            this.$watch('invoicePage', () => { this.triggerInvoicePagination(); });

            // Auto open booking detail if query parameter is present (e.g. ?booking_id=12)
            const urlParams = new URLSearchParams(window.location.search);
            const targetId = urlParams.get('booking_id') || urlParams.get('id');
            if (targetId) {
                this.pageLoading = false;
                const found = this.allBookings.find(b => String(b.id) === String(targetId) || b.code === targetId);
                if (found) {
                    this.$nextTick(() => {
                        this.openDetail(found);
                    });
                }
                // Bersihkan parameter booking_id dari URL agar saat refresh (F5) tidak membuka ulang
                try {
                    const cleanUrl = new URL(window.location);
                    cleanUrl.searchParams.delete('booking_id');
                    cleanUrl.searchParams.delete('id');
                    window.history.replaceState({}, '', cleanUrl);
                } catch (e) {}
            }
        },
        activeDetail: null,
        confirmModal: {
            open: false,
            title: '',
            message: '',
            subtext: '',
            actionUrl: '',
            btnText: '',
            btnClass: 'bg-brand-600 hover:bg-brand-700 text-white',
            iconType: 'check'
        },

        parseIsoDate(iso) {
            const [y, m, d] = (iso || '{{ now()->format('Y-m-d') }}').split('-').map(Number);
            return new Date(y, (m || 1) - 1, d || 1);
        },

        toLocalIso(dateObj) {
            const y = dateObj.getFullYear();
            const m = String(dateObj.getMonth() + 1).padStart(2, '0');
            const d = String(dateObj.getDate()).padStart(2, '0');
            return `${y}-${m}-${d}`;
        },

        get scheduleMonthLabel() {
            const d = this.parseIsoDate(this.selectedDateIso);
            return `${this.monthNames[d.getMonth()]} ${d.getFullYear()}`;
        },

        get selectedDatePretty() {
            const d = this.parseIsoDate(this.selectedDateIso);
            return `${this.monthShort[d.getMonth()]} ${d.getDate()}, ${d.getFullYear()}`;
        },

        shiftScheduleDay(delta) {
            if (this.stripAnimating) return;
            this.stripAnimating = true;
            this.stripOffset = -delta;
            clearTimeout(this.stripTimer);
            this.stripTimer = setTimeout(() => {
                const d = this.parseIsoDate(this.selectedDateIso);
                d.setDate(d.getDate() + delta);
                this.stripAnimating = false;
                this.stripOffset = 0;
                this.selectedDateIso = this.toLocalIso(d);
            }, 240);
        },

        shiftScheduleMonth(delta) {
            const d = this.parseIsoDate(this.selectedDateIso);
            const targetMonth = d.getMonth() + delta;
            const targetYear = d.getFullYear() + Math.floor(targetMonth / 12);
            const normMonth = ((targetMonth % 12) + 12) % 12;
            const maxDay = new Date(targetYear, normMonth + 1, 0).getDate();
            const clampedDay = Math.min(d.getDate(), maxDay);
            this.selectedDateIso = this.toLocalIso(new Date(targetYear, normMonth, clampedDay));
        },

        selectScheduleDate(iso, offsetFromCenter) {
            if (offsetFromCenter === 0) return;
            if (Math.abs(offsetFromCenter) === 1) {
                this.shiftScheduleDay(offsetFromCenter);
                return;
            }
            this.selectedDateIso = iso;
        },

        get scheduleDaysStrip() {
            const base = this.parseIsoDate(this.selectedDateIso);
            const dayNames = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
            const list = [];
            // 7 slots (-3..+3) so sliding -1 or +1 reveals the incoming day seamlessly with zero flicker
            for (let i = -3; i <= 3; i++) {
                const d = new Date(base.getFullYear(), base.getMonth(), base.getDate() + i);
                const iso = this.toLocalIso(d);
                const hasEvent = this.allBookings.some(b => {
                    if (b.raw_status === 'rejected') return false;
                    const start = b.event_date_iso;
                    const end = b.event_end_date_iso || start;
                    return iso >= start && iso <= end;
                });
                list.push({ day: dayNames[d.getDay()], num: d.getDate(), iso, hasEvent, offset: i });
            }
            return list;
        },

        get stripTrackStyle() {
            // Base position hides slot -3 (-14.2857% of 7-slot track = 1 slot). Shifting adds stripOffset * 14.2857%
            const pct = (-1 + this.stripOffset) * (100 / 7);
            const trans = this.stripAnimating ? 'transform 240ms cubic-bezier(0.22, 1, 0.36, 1)' : 'none';
            return `width: 140%; transform: translateX(${pct}%); transition: ${trans};`;
        },

        get dashboardScheduleList() {
            const q = (this.scheduleQuery || this.$store.nav.search || '').toLowerCase();
            const activeList = this.allBookings.filter(b => b.raw_status !== 'rejected');
            if (q) {
                return activeList.filter(b => b.name.toLowerCase().includes(q) || b.service.toLowerCase().includes(q) || b.code.toLowerCase().includes(q));
            }
            return activeList.filter(b => {
                const start = b.event_date_iso;
                const end = b.event_end_date_iso || start;
                return this.selectedDateIso >= start && this.selectedDateIso <= end;
            });
        },

        get upcomingList() {
            return this.allBookings
                .filter(b => b.raw_status !== 'rejected')
                .slice(0, 5);
        },

        get outstandingList() {
            return this.allBookings.filter(b => b.raw_status !== 'rejected' && b.remaining_balance > 0);
        },

        get availableServices() {
            return [...new Set(this.allBookings.map(b => b.service))];
        },

        get filteredBookings() {
            const q = (this.bookingSearch || this.$store.nav.search || '').toLowerCase();
            return this.allBookings.filter(item => {
                const matchSearch = !q || item.name.toLowerCase().includes(q) || item.code.toLowerCase().includes(q) || item.service.toLowerCase().includes(q);
                const matchStatus = this.bookingFilter === 'all' || item.bookingStatus === this.bookingFilter;
                const matchService = this.bookingServiceFilter === 'all' || item.service === this.bookingServiceFilter;
                return matchSearch && matchStatus && matchService;
            });
        },

        get totalBookingPages() {
            return Math.max(1, Math.ceil(this.filteredBookings.length / this.bookingPerPage));
        },
        get paginatedBookings() {
            const total = this.totalBookingPages;
            if (this.bookingPage > total) this.bookingPage = total;
            if (this.bookingPage < 1) this.bookingPage = 1;
            const start = (this.bookingPage - 1) * this.bookingPerPage;
            return this.filteredBookings.slice(start, start + this.bookingPerPage);
        },
        get bookingFrom() {
            if (this.filteredBookings.length === 0) return 0;
            return (this.bookingPage - 1) * this.bookingPerPage + 1;
        },
        get bookingTo() {
            return Math.min(this.bookingPage * this.bookingPerPage, this.filteredBookings.length);
        },
        get bookingPaginationRange() {
            const current = this.bookingPage;
            const total = this.totalBookingPages;
            if (total <= 7) {
                return Array.from({ length: total }, (_, i) => i + 1);
            }
            if (current <= 3) {
                return [1, 2, 3, 4, '...', total];
            }
            if (current >= total - 2) {
                return [1, '...', total - 3, total - 2, total - 1, total];
            }
            return [1, '...', current - 1, current, current + 1, '...', total];
        },

        get filteredClients() {
            const q = (this.clientSearch || this.$store.nav.search || '').toLowerCase();
            return this.clientsList.filter(c => {
                const matchSearch = !q || c.name.toLowerCase().includes(q) || c.email.toLowerCase().includes(q) || c.phone.toLowerCase().includes(q);
                const matchStatus = this.clientFilter === 'all' || c.status === this.clientFilter;
                return matchSearch && matchStatus;
            });
        },

        get totalClientPages() {
            return Math.max(1, Math.ceil(this.filteredClients.length / this.clientPerPage));
        },
        get paginatedClients() {
            const total = this.totalClientPages;
            if (this.clientPage > total) this.clientPage = total;
            if (this.clientPage < 1) this.clientPage = 1;
            const start = (this.clientPage - 1) * this.clientPerPage;
            return this.filteredClients.slice(start, start + this.clientPerPage);
        },
        get clientFrom() {
            if (this.filteredClients.length === 0) return 0;
            return (this.clientPage - 1) * this.clientPerPage + 1;
        },
        get clientTo() {
            return Math.min(this.clientPage * this.clientPerPage, this.filteredClients.length);
        },
        get clientPaginationRange() {
            const current = this.clientPage;
            const total = this.totalClientPages;
            if (total <= 7) {
                return Array.from({ length: total }, (_, i) => i + 1);
            }
            if (current <= 3) {
                return [1, 2, 3, 4, '...', total];
            }
            if (current >= total - 2) {
                return [1, '...', total - 3, total - 2, total - 1, total];
            }
            return [1, '...', current - 1, current, current + 1, '...', total];
        },

        get filteredInvoices() {
            const q = (this.invoiceSearch || this.$store.nav.search || '').toLowerCase();
            return this.allBookings.filter(inv => {
                const matchSearch = !q || inv.name.toLowerCase().includes(q) || inv.code.toLowerCase().includes(q) || inv.service.toLowerCase().includes(q);
                const matchStatus = this.invoiceFilter === 'all' || inv.invoiceStatus === this.invoiceFilter;
                return matchSearch && matchStatus;
            });
        },

        get totalInvoicePages() {
            return Math.max(1, Math.ceil(this.filteredInvoices.length / this.invoicePerPage));
        },
        get paginatedInvoices() {
            const total = this.totalInvoicePages;
            if (this.invoicePage > total) this.invoicePage = total;
            if (this.invoicePage < 1) this.invoicePage = 1;
            const start = (this.invoicePage - 1) * this.invoicePerPage;
            return this.filteredInvoices.slice(start, start + this.invoicePerPage);
        },
        get invoiceFrom() {
            if (this.filteredInvoices.length === 0) return 0;
            return (this.invoicePage - 1) * this.invoicePerPage + 1;
        },
        get invoiceTo() {
            return Math.min(this.invoicePage * this.invoicePerPage, this.filteredInvoices.length);
        },
        get invoicePaginationRange() {
            const current = this.invoicePage;
            const total = this.totalInvoicePages;
            if (total <= 7) {
                return Array.from({ length: total }, (_, i) => i + 1);
            }
            if (current <= 3) {
                return [1, 2, 3, 4, '...', total];
            }
            if (current >= total - 2) {
                return [1, '...', total - 3, total - 2, total - 1, total];
            }
            return [1, '...', current - 1, current, current + 1, '...', total];
        },

        get calHeaderLabel() {
            return `${this.monthNames[this.calMonth - 1]} ${this.calYear}`;
        },
        get calRangeLabel() {
            const lastDay = new Date(this.calYear, this.calMonth, 0).getDate();
            return `${this.monthShort[this.calMonth - 1]} 1, ${this.calYear} - ${this.monthShort[this.calMonth - 1]} ${lastDay}, ${this.calYear}`;
        },
        get calendarCells() {
            const firstWeekday = new Date(this.calYear, this.calMonth - 1, 1).getDay();
            const daysInMonth = new Date(this.calYear, this.calMonth, 0).getDate();
            const todayIso = '{{ now()->format('Y-m-d') }}';
            const dayNames = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
            const cells = [];

            for (let b = 0; b < firstWeekday; b++) {
                cells.push({ blank: true, key: 'b-' + b });
            }
            for (let d = 1; d <= daysInMonth; d++) {
                const iso = `${this.calYear}-${String(this.calMonth).padStart(2, '0')}-${String(d).padStart(2, '0')}`;
                const dateObj = new Date(this.calYear, this.calMonth - 1, d);
                const dayName = dayNames[dateObj.getDay()];
                const dateFull = `${dayName}, ${this.monthShort[this.calMonth - 1]} ${d}, ${this.calYear}`;

                const events = this.allBookings.filter(ev => {
                    const start = ev.event_date_iso;
                    const end = ev.event_end_date_iso || start;
                    return iso >= start && iso <= end;
                });
                const nonRejectedCount = events.filter(e => e.raw_status !== 'rejected').length;

                cells.push({
                    blank: false,
                    key: 'd-' + d,
                    day: d,
                    iso: iso,
                    dateFull: dateFull,
                    isToday: iso === todayIso,
                    events,
                    nonRejectedCount,
                    hasConflict: nonRejectedCount > this.maxEventsPerDay
                });
            }
            return cells;
        },
        get monthBookings() {
            const monthStart = `${this.calYear}-${String(this.calMonth).padStart(2, '0')}-01`;
            const lastDay = new Date(this.calYear, this.calMonth, 0).getDate();
            const monthEnd = `${this.calYear}-${String(this.calMonth).padStart(2, '0')}-${String(lastDay).padStart(2, '0')}`;
            return this.allBookings.filter(ev => {
                const start = ev.event_date_iso;
                const end = ev.event_end_date_iso || start;
                return (start <= monthEnd && end >= monthStart);
            }).sort((a, b) => a.event_date_iso.localeCompare(b.event_date_iso));
        },
        prevCalMonth() {
            if (this.calMonth === 1) { this.calMonth = 12; this.calYear--; }
            else { this.calMonth--; }
        },
        nextCalMonth() {
            if (this.calMonth === 12) { this.calMonth = 1; this.calYear++; }
            else { this.calMonth++; }
        },
        goToTodayCalendar() {
            this.calYear = this.currentRealYear;
            this.calMonth = this.currentRealMonth;
        },

        openDetail(item) {
            if (this.$store.nav.tab !== 'booking_detail') {
                this.previousTab = this.$store.nav.tab;
            }
            this.detailLoading = true;
            this.activeDetail = item;
            this.$store.nav.setTab('booking_detail');
            window.scrollTo({ top: 0, behavior: 'smooth' });
            setTimeout(() => {
                this.detailLoading = false;
            }, 450);
        },
        closeDetail() {
            this.activeDetail = null;
            const fallbackTab = (this.previousTab && this.previousTab !== 'booking_detail') ? this.previousTab : 'dashboard';
            this.$store.nav.setTab(fallbackTab);
            try {
                const cleanUrl = new URL(window.location);
                cleanUrl.searchParams.delete('booking_id');
                cleanUrl.searchParams.delete('id');
                window.history.replaceState({}, '', cleanUrl);
            } catch (e) {}
        },
        openClientProfile(clientOrBooking) {
            if (this.$store.nav.tab !== 'client_profile') {
                this.previousTab = this.$store.nav.tab;
            }
            const found = this.clientsList.find(c => c.name === clientOrBooking.name || c.phone === clientOrBooking.phone);
            this.activeClient = found || {
                name: clientOrBooking.name,
                email: clientOrBooking.email || '-',
                phone: clientOrBooking.phone || '-',
                status: 'Active',
                totalBookings: 1,
                totalSpentFormatted: clientOrBooking.grand_total_formatted || 'Rp 0',
                lastEvent: clientOrBooking.date_short || '-',
                package: clientOrBooking.service || '-',
                avatar: clientOrBooking.avatar,
                whatsapp_url: clientOrBooking.whatsapp_url || '#'
            };
            this.$store.nav.setTab('client_profile');
            window.scrollTo({ top: 0, behavior: 'smooth' });
        },
        triggerConfirm(title, message, subtext, actionUrl, btnText, btnClass = 'bg-brand-600 hover:bg-brand-700 text-white', iconType = 'check') {
            this.confirmModal = { open: true, title, message, subtext, actionUrl, btnText, btnClass, iconType };
        }
     }"
     class="space-y-6">

    <!-- ================================================================== -->
    <!-- DASHBOARD SKELETON SHIMMER (FLUTTER STYLE)                         -->
    <!-- ================================================================== -->
    <div x-show="$store.nav.tab === 'dashboard' && pageLoading" class="space-y-6" x-cloak>
        <!-- Shimmer KPI Strip -->
        <div class="flex flex-col xl:flex-row xl:items-center justify-between gap-4 sm:gap-6 px-0.5 sm:px-1">
            <div class="space-y-2 pt-1 sm:pt-0">
                <div class="h-4 w-28 rounded-md shimmer-mask"></div>
                <div class="h-8 w-56 rounded-xl shimmer-mask"></div>
            </div>
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-2.5 sm:gap-4 xl:gap-0 xl:flex xl:items-center xl:divide-x xl:divide-[#E3EAE7]">
                <div class="bg-white rounded-2xl p-3 sm:p-4 border border-[#EDF2F0] shadow-card xl:bg-transparent xl:border-0 xl:shadow-none xl:rounded-none xl:p-0 flex items-center gap-2.5 sm:gap-3.5 xl:pr-7">
                    <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl sm:rounded-full shimmer-mask shrink-0"></div>
                    <div class="space-y-2 min-w-0">
                        <div class="h-5 sm:h-6 w-12 rounded shimmer-mask"></div>
                        <div class="h-3 w-20 sm:w-24 rounded shimmer-mask"></div>
                    </div>
                </div>
                <div class="bg-white rounded-2xl p-3 sm:p-4 border border-[#EDF2F0] shadow-card xl:bg-transparent xl:border-0 xl:shadow-none xl:rounded-none xl:p-0 flex items-center gap-2.5 sm:gap-3.5 xl:px-7">
                    <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl sm:rounded-full shimmer-mask shrink-0"></div>
                    <div class="space-y-2 min-w-0">
                        <div class="h-5 sm:h-6 w-16 rounded shimmer-mask"></div>
                        <div class="h-3 w-24 sm:w-28 rounded shimmer-mask"></div>
                    </div>
                </div>
                <div class="bg-white rounded-2xl p-3 sm:p-4 border border-[#EDF2F0] shadow-card xl:bg-transparent xl:border-0 xl:shadow-none xl:rounded-none xl:p-0 flex items-center gap-2.5 sm:gap-3.5 xl:px-7">
                    <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl sm:rounded-full shimmer-mask shrink-0"></div>
                    <div class="space-y-2 min-w-0">
                        <div class="h-5 sm:h-6 w-12 rounded shimmer-mask"></div>
                        <div class="h-3 w-24 sm:w-28 rounded shimmer-mask"></div>
                    </div>
                </div>
                <div class="bg-white rounded-2xl p-3 sm:p-4 border border-[#EDF2F0] shadow-card xl:bg-transparent xl:border-0 xl:shadow-none xl:rounded-none xl:p-0 flex items-center gap-2.5 sm:gap-3.5 xl:pl-7">
                    <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl sm:rounded-full shimmer-mask shrink-0"></div>
                    <div class="space-y-2 min-w-0">
                        <div class="h-5 sm:h-6 w-20 sm:w-24 rounded shimmer-mask"></div>
                        <div class="h-3 w-20 sm:w-24 rounded shimmer-mask"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Shimmer 3-Column Bento -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-5 items-stretch">
            <!-- Col 1: Schedule -->
            <div class="bg-white rounded-[28px] p-5 sm:p-6 shadow-card border border-[#EDF2F0] space-y-4">
                <div class="flex items-center justify-between">
                    <div class="h-5 w-32 rounded-md shimmer-mask"></div>
                    <div class="h-4 w-20 rounded-md shimmer-mask"></div>
                </div>
                <div class="h-10 w-full rounded-2xl shimmer-mask"></div>
                <div class="space-y-3">
                    <div class="h-16 w-full rounded-2xl shimmer-mask"></div>
                    <div class="h-16 w-full rounded-2xl shimmer-mask"></div>
                    <div class="h-16 w-full rounded-2xl shimmer-mask"></div>
                </div>
            </div>

            <!-- Col 2: Upcoming Bookings -->
            <div class="bg-white rounded-[28px] p-5 sm:p-6 shadow-card border border-[#EDF2F0] space-y-4">
                <div class="flex items-center justify-between">
                    <div class="h-5 w-36 rounded-md shimmer-mask"></div>
                    <div class="h-4 w-16 rounded-md shimmer-mask"></div>
                </div>
                <div class="space-y-3">
                    <div class="h-14 w-full rounded-2xl shimmer-mask"></div>
                    <div class="h-14 w-full rounded-2xl shimmer-mask"></div>
                    <div class="h-14 w-full rounded-2xl shimmer-mask"></div>
                    <div class="h-14 w-full rounded-2xl shimmer-mask"></div>
                    <div class="h-14 w-full rounded-2xl shimmer-mask"></div>
                </div>
            </div>

            <!-- Col 3: Outstanding Invoices + Business Insight -->
            <div class="flex flex-col gap-5">
                <div class="bg-white rounded-[28px] p-5 sm:p-6 shadow-card border border-[#EDF2F0] space-y-4">
                    <div class="flex items-center justify-between">
                        <div class="h-5 w-36 rounded-md shimmer-mask"></div>
                        <div class="h-4 w-16 rounded-md shimmer-mask"></div>
                    </div>
                    <div class="space-y-3">
                        <div class="h-14 w-full rounded-2xl shimmer-mask"></div>
                        <div class="h-14 w-full rounded-2xl shimmer-mask"></div>
                        <div class="h-14 w-full rounded-2xl shimmer-mask"></div>
                    </div>
                </div>
                <div class="h-32 w-full rounded-[28px] shimmer-mask"></div>
            </div>
        </div>
    </div>

    <!-- ================================================================== -->
    <!-- GREETING + 4 REAL-TIME KPI STRIP (DASHBOARD ONLY)                  -->
    <!-- ================================================================== -->
    <section x-show="$store.nav.tab === 'dashboard' && !pageLoading"
             class="flex flex-col xl:flex-row xl:items-center justify-between gap-4 sm:gap-6 px-0.5 sm:px-1">

        <div class="shrink-0 pt-1 sm:pt-0">
            <p class="text-xs sm:text-sm font-medium text-slate-400">{{ $greeting }}</p>
            <h1 class="text-2xl sm:text-[30px] font-bold text-slate-900 tracking-tight leading-tight mt-0.5">
                {{ $ownerName }}
            </h1>
        </div>

        <div class="grid grid-cols-2 lg:grid-cols-4 gap-2.5 sm:gap-4 xl:gap-0 xl:flex xl:items-center xl:divide-x xl:divide-[#E3EAE7]">

            <!-- KPI 1: Today's Schedule -->
            <div class="bg-white rounded-2xl p-3 sm:p-4 border border-[#EDF2F0] shadow-card xl:bg-transparent xl:border-0 xl:shadow-none xl:rounded-none xl:p-0 flex items-center gap-2.5 sm:gap-3.5 xl:pr-7">
                <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl sm:rounded-full bg-emerald-50 text-brand-600 xl:bg-white xl:shadow-card xl:border xl:border-[#EEF2F1] flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
                <div class="min-w-0">
                    <div class="text-xl sm:text-2xl font-bold text-slate-900 leading-none">{{ $metrics['today_schedule'] }}</div>
                    <div class="text-[11px] sm:text-xs font-medium text-slate-500 mt-1 sm:mt-1.5 truncate">Today's Schedule</div>
                </div>
            </div>

            <!-- KPI 2: Upcoming Bookings -->
            <div class="bg-white rounded-2xl p-3 sm:p-4 border border-[#EDF2F0] shadow-card xl:bg-transparent xl:border-0 xl:shadow-none xl:rounded-none xl:p-0 flex items-center gap-2.5 sm:gap-3.5 xl:px-7">
                <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl sm:rounded-full bg-emerald-50 text-brand-600 xl:bg-white xl:shadow-card xl:border xl:border-[#EEF2F1] flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div class="min-w-0">
                    <div class="flex items-center gap-1.5 flex-wrap">
                        <span class="text-xl sm:text-2xl font-bold text-slate-900 leading-none">{{ $metrics['upcoming_count'] }}</span>
                        @if($metrics['pending_count'] > 0)
                            <span class="inline-flex items-center px-1.5 py-0.5 rounded-full text-[9px] sm:text-[10px] font-bold bg-amber-50 text-amber-700 whitespace-nowrap">
                                {{ $metrics['pending_count'] }} pending
                            </span>
                        @else
                            <span class="inline-flex items-center px-1.5 py-0.5 rounded-full text-[9px] sm:text-[10px] font-bold bg-[#E6F4F1] text-brand-600 whitespace-nowrap">
                                On Track
                            </span>
                        @endif
                    </div>
                    <div class="text-[11px] sm:text-xs font-medium text-slate-500 mt-1 sm:mt-1.5 truncate">Upcoming Bookings</div>
                </div>
            </div>

            <!-- KPI 3: Outstanding Invoices -->
            <div class="bg-white rounded-2xl p-3 sm:p-4 border border-[#EDF2F0] shadow-card xl:bg-transparent xl:border-0 xl:shadow-none xl:rounded-none xl:p-0 flex items-center gap-2.5 sm:gap-3.5 xl:px-7">
                <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl sm:rounded-full bg-emerald-50 text-brand-600 xl:bg-white xl:shadow-card xl:border xl:border-[#EEF2F1] flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <div class="min-w-0">
                    <div class="text-xl sm:text-2xl font-bold text-slate-900 leading-none">{{ $metrics['outstanding_count'] }}</div>
                    <div class="text-[11px] sm:text-xs font-medium text-slate-500 mt-1 sm:mt-1.5 truncate">Outstanding Invoices</div>
                </div>
            </div>

            <!-- KPI 4: Verified Revenue -->
            <div class="bg-white rounded-2xl p-3 sm:p-4 border border-[#EDF2F0] shadow-card xl:bg-transparent xl:border-0 xl:shadow-none xl:rounded-none xl:p-0 flex items-center gap-2.5 sm:gap-3.5 xl:pl-7">
                <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl sm:rounded-full bg-emerald-50 text-brand-600 xl:bg-white xl:shadow-card xl:border xl:border-[#EEF2F1] flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div class="min-w-0">
                    <div class="text-xl sm:text-2xl font-bold text-slate-900 leading-none tracking-tight">{{ $metrics['revenue_short'] }}</div>
                    <div class="text-[11px] sm:text-xs font-medium text-slate-500 mt-1 sm:mt-1.5 truncate">Verified Revenue</div>
                </div>
            </div>

        </div>
    </section>

    <!-- ================================================================== -->
    <!-- TAB 1: DASHBOARD (3-COLUMN BENTO — REAL DATABASE DATA)             -->
    <!-- ================================================================== -->
    <div x-show="$store.nav.tab === 'dashboard' && !pageLoading"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 translate-y-1"
         x-transition:enter-end="opacity-100 translate-y-0"
         class="grid grid-cols-1 lg:grid-cols-3 gap-5 items-stretch">

        <!-- COLUMN 1: TODAY'S SCHEDULE -->
        <div class="bg-white rounded-[28px] p-5 sm:p-6 shadow-card border border-[#EDF2F0] flex flex-col">
            <div class="flex items-center justify-between gap-2">
                <h2 class="text-[17px] font-bold text-slate-900 tracking-tight">Today's Schedule</h2>
                <button type="button"
                        @click="$store.nav.setTab('calendar')"
                        class="px-3.5 py-1.5 rounded-full border border-[#E5ECE9] bg-white hover:bg-[#F6F8F7] text-xs font-medium text-slate-600 transition">
                    View Calendar
                </button>
            </div>

            <div class="mt-4 bg-[#F6F8F7] rounded-full px-3 py-2 flex items-center justify-between text-xs font-semibold text-slate-700">
                <button type="button" @click="shiftScheduleMonth(-1)" title="Previous Month" class="w-7 h-7 rounded-full hover:bg-white active:scale-90 flex items-center justify-center text-slate-500 transition cursor-pointer">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.4" d="M15 19l-7-7 7-7"/></svg>
                </button>
                <span x-text="scheduleMonthLabel" class="select-none"></span>
                <button type="button" @click="shiftScheduleMonth(1)" title="Next Month" class="w-7 h-7 rounded-full hover:bg-white active:scale-90 flex items-center justify-center text-slate-500 transition cursor-pointer">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.4" d="M9 5l7 7-7 7"/></svg>
                </button>
            </div>

            <!-- 5-Day Horizontal Carousel Strip (True 60fps Physical Slide, Zero Flicker!) -->
            <div class="mt-4 flex items-center justify-between gap-1">
                <button type="button" @click="shiftScheduleDay(-1)" title="Previous Day" class="w-7 h-7 rounded-full hover:bg-[#F6F8F7] active:scale-90 flex items-center justify-center text-slate-400 hover:text-brand-600 transition shrink-0 cursor-pointer">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.4" d="M15 19l-7-7 7-7"/></svg>
                </button>

                <div class="relative flex-1 overflow-hidden py-1">
                    <!-- Stationary Centered Active Pill Backdrop -->
                    <div class="pointer-events-none absolute top-1 bottom-1 left-[40%] w-[20%] px-0.5">
                        <div class="w-full h-full rounded-2xl bg-brand-600 shadow-pill"></div>
                    </div>

                    <!-- Sliding 7-Day Track (5 Visible + 1 Buffer Each Side) -->
                    <div class="flex will-change-transform relative z-10" :style="stripTrackStyle">
                        <template x-for="d in scheduleDaysStrip" :key="d.iso">
                            <div class="w-[14.285714%] shrink-0 px-0.5">
                                <button type="button"
                                        @click="selectScheduleDate(d.iso, d.offset)"
                                        :class="(d.offset + stripOffset) === 0
                                            ? 'text-white'
                                            : 'text-slate-700 hover:bg-[#F6F8F7]'"
                                        class="w-full h-[66px] rounded-2xl flex flex-col items-center justify-center transition-colors duration-200 cursor-pointer select-none">
                                    <span class="text-[11px] font-medium leading-none transition-colors duration-200"
                                          :class="(d.offset + stripOffset) === 0 ? 'text-teal-100' : 'text-slate-400'"
                                          x-text="d.day"></span>
                                    <span class="text-sm font-bold mt-1.5 leading-none" x-text="d.num"></span>
                                    <span :class="[
                                            d.hasEvent ? 'opacity-100' : 'opacity-0',
                                            (d.offset + stripOffset) === 0 ? 'bg-emerald-300' : 'bg-brand-600'
                                          ]"
                                          class="w-1.5 h-1.5 rounded-full mt-1.5 transition-colors duration-200"></span>
                                </button>
                            </div>
                        </template>
                    </div>
                </div>

                <button type="button" @click="shiftScheduleDay(1)" title="Next Day" class="w-7 h-7 rounded-full hover:bg-[#F6F8F7] active:scale-90 flex items-center justify-center text-slate-400 hover:text-brand-600 transition shrink-0 cursor-pointer">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.4" d="M9 5l7 7-7 7"/></svg>
                </button>
            </div>

            <div class="mt-4 relative flex items-center">
                <svg class="w-4 h-4 text-slate-400 absolute left-3.5 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-5.2-5.2m2.2-4.8a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input type="text"
                       x-model="scheduleQuery"
                       placeholder="Search schedule..."
                       class="w-full pl-9 pr-4 py-2.5 rounded-2xl bg-white border border-[#E8EFED] text-xs text-slate-700 placeholder-slate-400 focus:outline-none focus:border-brand-400 transition">
            </div>

            <div class="mt-4 space-y-3 relative flex-1 overflow-hidden">
                <template x-for="item in dashboardScheduleList" :key="item.id">
                    <div @click="openDetail(item)"
                         class="p-3.5 rounded-2xl bg-[#F8FAFA] hover:bg-[#F1F6F5] border border-[#EFF4F2] transition-all duration-200 hover:-translate-y-0.5 cursor-pointer group">
                        <div class="flex items-center justify-between gap-3">
                            <div class="flex items-center gap-3 min-w-0">
                                <img :src="item.avatar" :alt="item.name" class="w-10 h-10 rounded-xl object-cover shrink-0">
                                <div class="min-w-0">
                                    <p class="text-[13px] font-bold text-slate-900 truncate" x-text="item.name"></p>
                                    <p class="text-[11px] font-medium text-slate-500 mt-0.5" x-text="item.date_short + ' · #' + item.code"></p>
                                </div>
                            </div>
                            <svg class="w-4 h-4 text-slate-400 group-hover:text-brand-600 group-hover:translate-x-0.5 transition shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </div>

                        <div class="mt-3 pt-2.5 border-t border-[#EBF1EF] flex items-center justify-between gap-2">
                            <span class="text-[11px] font-medium text-slate-500 truncate" x-text="item.service"></span>
                            <span :class="{
                                    'bg-[#E6F6EF] text-emerald-700': item.bookingStatus === 'Confirmed',
                                    'bg-[#EAF3FF] text-blue-600': item.bookingStatus === 'Inquiry',
                                    'bg-[#F3EAFF] text-purple-600': item.bookingStatus === 'Complete',
                                    'bg-[#FEECEB] text-rose-600': item.bookingStatus === 'Cancelled'
                                  }"
                                  class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-bold shrink-0">
                                <span class="w-1.5 h-1.5 rounded-full"
                                      :class="{
                                        'bg-emerald-500': item.bookingStatus === 'Confirmed',
                                        'bg-blue-500': item.bookingStatus === 'Inquiry',
                                        'bg-purple-500': item.bookingStatus === 'Complete',
                                        'bg-rose-500': item.bookingStatus === 'Cancelled'
                                      }"></span>
                                <span x-text="item.bookingStatus"></span>
                            </span>
                        </div>
                    </div>
                </template>

                <div x-show="dashboardScheduleList.length === 0" class="py-8 px-3 text-center space-y-3">
                    <div class="w-10 h-10 rounded-full bg-[#F4F8F7] text-brand-600 flex items-center justify-center mx-auto">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-slate-700" x-text="'No events on ' + selectedDatePretty"></p>
                        <p class="text-[11px] text-slate-400 mt-0.5">Select another date or jump to a scheduled event:</p>
                    </div>
                    <!-- <div class="flex flex-wrap items-center justify-center gap-1.5 pt-1">
                        <template x-for="ev in upcomingList.slice(0, 3)" :key="'jump-' + ev.id">
                            <button type="button"
                                    @click="selectedDateIso = ev.event_date_iso"
                                    class="px-3 py-1 rounded-full bg-[#F4F8F7] hover:bg-brand-50 border border-[#E5ECE9] text-[11px] font-semibold text-brand-700 transition cursor-pointer"
                                    x-text="ev.date_short + ' · ' + ev.shortName">
                            </button>
                        </template>
                    </div> -->
                </div>
            </div>
        </div>

        <!-- COLUMN 2: UPCOMING BOOKINGS -->
        <div class="bg-white rounded-[28px] p-5 sm:p-6 shadow-card border border-[#EDF2F0] flex flex-col">
            <div class="flex items-center justify-between gap-2">
                <h2 class="text-[17px] font-bold text-slate-900 tracking-tight">Upcoming Bookings</h2>
                <button type="button"
                        @click="$store.nav.setTab('bookings')"
                        class="px-3.5 py-1.5 rounded-full border border-[#E5ECE9] bg-white hover:bg-[#F6F8F7] text-xs font-medium text-slate-600 transition">
                    View All
                </button>
            </div>

            <div class="mt-4 space-y-3 relative flex-1 overflow-hidden">
                <template x-for="item in upcomingList" :key="item.id">
                    <div @click="openDetail(item)"
                         class="p-3.5 rounded-2xl bg-[#F8FAFA] hover:bg-[#F1F6F5] border border-[#EFF4F2] transition cursor-pointer group">
                        <div class="flex items-center justify-between gap-3">
                            <div class="flex items-center gap-3 min-w-0">
                                <img :src="item.avatar" :alt="item.name" class="w-10 h-10 rounded-xl object-cover shrink-0">
                                <div class="min-w-0">
                                    <p class="text-[13px] font-bold text-slate-900 truncate" x-text="item.name"></p>
                                    <p class="text-[11px] font-medium text-slate-500 mt-0.5 truncate" x-text="item.date_short + ' · ' + item.relative_date"></p>
                                </div>
                            </div>
                            <svg class="w-4 h-4 text-slate-400 group-hover:text-brand-600 group-hover:translate-x-0.5 transition shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </div>

                        <div class="mt-3 pt-2.5 border-t border-[#EBF1EF] flex items-center justify-between gap-2">
                            <span class="text-[11px] font-medium text-slate-500 truncate" x-text="item.service"></span>
                            <span :class="{
                                    'bg-[#E6F6EF] text-emerald-700': item.bookingStatus === 'Confirmed',
                                    'bg-[#EAF3FF] text-blue-600': item.bookingStatus === 'Inquiry',
                                    'bg-[#F3EAFF] text-purple-600': item.bookingStatus === 'Complete'
                                  }"
                                  class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-bold shrink-0">
                                <span class="w-1.5 h-1.5 rounded-full"
                                      :class="{
                                        'bg-emerald-500': item.bookingStatus === 'Confirmed',
                                        'bg-blue-500': item.bookingStatus === 'Inquiry',
                                        'bg-purple-500': item.bookingStatus === 'Complete'
                                      }"></span>
                                <span x-text="item.bookingStatus"></span>
                            </span>
                        </div>
                    </div>
                </template>

                <div x-show="upcomingList.length === 0" class="py-12 text-center text-xs text-slate-400">
                    No bookings available yet.
                </div>
            </div>
        </div>

        <!-- COLUMN 3: OUTSTANDING INVOICES + BUSINESS INSIGHT -->
        <div class="flex flex-col gap-5">

            <div class="bg-white rounded-[28px] p-5 sm:p-6 shadow-card border border-[#EDF2F0] flex-1 flex flex-col">
                <div class="flex items-center justify-between gap-2">
                    <h2 class="text-[17px] font-bold text-slate-900 tracking-tight">Outstanding Invoices</h2>
                    <button type="button"
                            @click="$store.nav.setTab('invoice')"
                            class="px-3.5 py-1.5 rounded-full border border-[#E5ECE9] bg-white hover:bg-[#F6F8F7] text-xs font-medium text-slate-600 transition">
                        View All
                    </button>
                </div>

                <div class="mt-4 space-y-3 relative flex-1 overflow-hidden">
                    <template x-for="inv in outstandingList.slice(0, 3)" :key="inv.id">
                        <div @click="openDetail(inv)"
                             class="p-3.5 rounded-2xl bg-[#F8FAFA] hover:bg-[#F1F6F5] border border-[#EFF4F2] transition cursor-pointer group">
                            <div class="flex items-center justify-between gap-3">
                                <div class="flex items-center gap-3 min-w-0">
                                    <img :src="inv.avatar" :alt="inv.name" class="w-10 h-10 rounded-xl object-cover shrink-0">
                                    <div class="min-w-0">
                                        <p class="text-[13px] font-bold text-slate-900 truncate" x-text="inv.name"></p>
                                        <p class="text-[11px] font-medium text-slate-500 mt-0.5 truncate">
                                            <span class="font-semibold text-slate-700" x-text="inv.remaining_formatted"></span>
                                            <span> · Event: </span>
                                            <span x-text="inv.date_short"></span>
                                        </p>
                                    </div>
                                </div>
                                <svg class="w-4 h-4 text-slate-400 group-hover:text-brand-600 group-hover:translate-x-0.5 transition shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </div>

                            <div class="mt-3 pt-2.5 border-t border-[#EBF1EF] flex items-center justify-between gap-2">
                                <span class="text-[11px] font-semibold font-mono text-slate-400" x-text="'#' + inv.code"></span>
                                <span :class="inv.invoiceStatus === 'Overdue' ? 'bg-rose-50 text-rose-600' : 'bg-amber-50 text-amber-600'"
                                      class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-bold shrink-0">
                                    <span class="w-1.5 h-1.5 rounded-full" :class="inv.invoiceStatus === 'Overdue' ? 'bg-rose-500' : 'bg-amber-400'"></span>
                                    <span x-text="inv.invoiceStatus"></span>
                                </span>
                            </div>
                        </div>
                    </template>

                    <div x-show="outstandingList.length === 0" class="py-10 text-center space-y-1.5">
                        <span class="inline-flex w-10 h-10 rounded-full bg-emerald-50 text-emerald-600 items-center justify-center">✓</span>
                        <p class="text-xs font-semibold text-slate-700">All Invoices Paid!</p>
                        <p class="text-[11px] text-slate-400">No outstanding balance at the moment.</p>
                    </div>
                </div>
            </div>

            <!-- Business Insight Card -->
            <div class="rounded-[28px] bg-gradient-to-br from-[#023834] via-[#045D56] to-[#0D8278] p-6 text-white shadow-insight relative overflow-hidden">
                <div class="pointer-events-none absolute -top-12 -right-12 w-44 h-44 rounded-full bg-teal-300/20 blur-2xl"></div>
                <div class="pointer-events-none absolute -bottom-10 -left-10 w-32 h-32 rounded-full bg-emerald-400/10 blur-2xl"></div>

                <div class="relative z-10 space-y-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-white text-brand-600 flex items-center justify-center shadow-sm shrink-0">
                            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M12 2l1.9 5.3L19.2 9l-5.3 1.9L12 16.2l-1.9-5.3L4.8 9l5.3-1.7L12 2zm6.5 12l.9 2.6 2.6.9-2.6.9-.9 2.6-.9-2.6-2.6-.9 2.6-.9.9-2.6z"/>
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold tracking-tight">Business Insight</h3>
                    </div>

                    <p class="text-[13px] text-teal-50/90 leading-relaxed">
                        <strong class="text-white font-semibold">{{ $metrics['top_package'] }}</strong> is your top-converting wedding package this season.
                    </p>

                    <div class="pt-1 flex flex-wrap items-center justify-between gap-3">
                        <div class="inline-flex items-center gap-1.5 text-xs font-semibold text-emerald-300">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                            </svg>
                            <span>{{ $metrics['total_bookings'] }} total bookings</span>
                        </div>

                        <button type="button"
                                @click="$store.nav.setTab('services')"
                                class="px-4 py-2 rounded-full bg-white hover:bg-teal-50 text-brand-700 text-xs font-bold transition shadow-sm">
                            Manage Packages
                        </button>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- ================================================================== -->
    <!-- TAB 2: BOOKINGS SKELETON SHIMMER (FLUTTER STYLE)                   -->
    <!-- ================================================================== -->
    <div x-show="$store.nav.tab === 'bookings' && pageLoading"
         class="space-y-5"
         x-cloak>
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-3">
            <div class="flex flex-wrap items-center gap-2.5 sm:gap-3">
                <div class="h-10 w-64 sm:w-72 rounded-full shimmer-mask"></div>
                <div class="h-10 w-36 rounded-full shimmer-mask"></div>
                <div class="h-10 w-36 rounded-full shimmer-mask"></div>
            </div>
            <div class="flex items-center gap-2.5">
                <div class="h-10 w-28 rounded-full shimmer-mask"></div>
                <div class="h-10 w-36 rounded-full shimmer-mask"></div>
            </div>
        </div>

        <div class="bg-white rounded-[28px] shadow-card border border-[#EDF2F0] overflow-hidden p-6 space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-[#EFF4F2]">
                <div class="h-4 w-28 rounded shimmer-mask"></div>
                <div class="h-4 w-36 rounded shimmer-mask"></div>
                <div class="h-4 w-32 rounded shimmer-mask"></div>
                <div class="h-4 w-28 rounded shimmer-mask"></div>
                <div class="h-4 w-24 rounded shimmer-mask"></div>
                <div class="h-4 w-24 rounded shimmer-mask"></div>
            </div>
            <div class="space-y-4">
                <template x-for="i in 6" :key="'shimmer-b-row-' + i">
                    <div class="flex items-center justify-between py-2 border-b border-[#F8FAFA]">
                        <div class="h-4 w-24 rounded shimmer-mask"></div>
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg shimmer-mask shrink-0"></div>
                            <div class="h-4 w-32 rounded shimmer-mask"></div>
                        </div>
                        <div class="h-4 w-28 rounded shimmer-mask"></div>
                        <div class="h-4 w-28 rounded shimmer-mask"></div>
                        <div class="h-6 w-20 rounded-full shimmer-mask"></div>
                        <div class="h-6 w-16 rounded-full shimmer-mask"></div>
                    </div>
                </template>
            </div>
            <div class="pt-4 flex items-center justify-between border-t border-[#EFF4F2]">
                <div class="h-4 w-40 rounded shimmer-mask"></div>
                <div class="h-8 w-48 rounded-lg shimmer-mask"></div>
            </div>
        </div>
    </div>

    <!-- ================================================================== -->
    <!-- TAB 2: BOOKINGS (6-COLUMN TABLE — REAL DATABASE DATA)              -->
    <!-- ================================================================== -->
    <div x-show="$store.nav.tab === 'bookings' && !pageLoading"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 translate-y-1"
         x-transition:enter-end="opacity-100 translate-y-0"
         class="space-y-5"
         x-cloak>

        <div class="flex flex-col md:flex-row md:items-center justify-between gap-3">
            <div class="flex flex-wrap items-center gap-2.5 sm:gap-3">
                <div class="relative flex items-center bg-white rounded-full border border-[#E7EEEC] shadow-[0_2px_8px_rgba(0,0,0,0.02)] focus-within:border-brand-400 transition w-full sm:w-64 lg:w-72">
                    <svg class="w-4 h-4 text-slate-400 absolute left-4 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-5.2-5.2m2.2-4.8a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input type="text"
                           x-model="bookingSearch"
                           placeholder="Search Bookings..."
                           class="w-full bg-transparent pl-10 pr-4 py-2.5 text-xs text-slate-700 placeholder-slate-400 focus:outline-none">
                </div>

                <div class="relative" @click.away="statusMenuOpen = false">
                    <button type="button"
                            @click="statusMenuOpen = !statusMenuOpen; serviceMenuOpen = false"
                            class="inline-flex items-center justify-between gap-4 bg-white hover:bg-[#FAFDFC] rounded-full border border-[#E7EEEC] shadow-[0_2px_8px_rgba(0,0,0,0.02)] px-4 py-2.5 text-xs font-medium text-slate-700 transition min-w-[148px]">
                        <span class="inline-flex items-center gap-2">
                            <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4h18M6 12h12M10 20h4"/>
                            </svg>
                            <span x-text="bookingFilter === 'all' ? 'All Status' : bookingFilter"></span>
                        </span>
                        <svg class="w-3.5 h-3.5 text-slate-400 transition-transform" :class="statusMenuOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>

                    <div x-show="statusMenuOpen"
                         class="absolute left-0 mt-2 w-44 bg-white rounded-2xl shadow-xl border border-[#EAF0EE] py-1.5 z-30"
                         x-cloak>
                        <template x-for="st in ['all', 'Inquiry', 'Confirmed', 'Complete', 'Cancelled']" :key="st">
                            <button type="button"
                                    @click="bookingFilter = st; statusMenuOpen = false"
                                    :class="bookingFilter === st ? 'bg-brand-50 text-brand-700 font-semibold' : 'text-slate-600 hover:bg-[#F6F8F7]'"
                                    class="w-full text-left px-4 py-2 text-xs transition flex items-center justify-between">
                                <span x-text="st === 'all' ? 'All Status' : st"></span>
                                <span x-show="bookingFilter === st" class="text-brand-600">✓</span>
                            </button>
                        </template>
                    </div>
                </div>

                <div class="relative" @click.away="serviceMenuOpen = false">
                    <button type="button"
                            @click="serviceMenuOpen = !serviceMenuOpen; statusMenuOpen = false"
                            class="inline-flex items-center justify-between gap-4 bg-white hover:bg-[#FAFDFC] rounded-full border border-[#E7EEEC] shadow-[0_2px_8px_rgba(0,0,0,0.02)] px-4 py-2.5 text-xs font-medium text-slate-700 transition min-w-[155px]">
                        <span class="truncate max-w-[135px]" x-text="bookingServiceFilter === 'all' ? 'All Services' : bookingServiceFilter"></span>
                        <svg class="w-3.5 h-3.5 text-slate-400 transition-transform shrink-0" :class="serviceMenuOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>

                    <div x-show="serviceMenuOpen"
                         class="absolute left-0 mt-2 w-56 bg-white rounded-2xl shadow-xl border border-[#EAF0EE] py-1.5 z-30"
                         x-cloak>
                        <button type="button"
                                @click="bookingServiceFilter = 'all'; serviceMenuOpen = false"
                                :class="bookingServiceFilter === 'all' ? 'bg-brand-50 text-brand-700 font-semibold' : 'text-slate-600 hover:bg-[#F6F8F7]'"
                                class="w-full text-left px-4 py-2 text-xs transition flex items-center justify-between">
                            <span>All Services</span>
                            <span x-show="bookingServiceFilter === 'all'" class="text-brand-600">✓</span>
                        </button>
                        <template x-for="srv in availableServices" :key="srv">
                            <button type="button"
                                    @click="bookingServiceFilter = srv; serviceMenuOpen = false"
                                    :class="bookingServiceFilter === srv ? 'bg-brand-50 text-brand-700 font-semibold' : 'text-slate-600 hover:bg-[#F6F8F7]'"
                                    class="w-full text-left px-4 py-2 text-xs transition flex items-center justify-between gap-2">
                                <span class="truncate" x-text="srv"></span>
                                <span x-show="bookingServiceFilter === srv" class="text-brand-600 shrink-0">✓</span>
                            </button>
                        </template>
                    </div>
                </div>
            </div>

            <a href="{{ route('booking.create') }}" target="_blank"
               class="inline-flex items-center justify-center gap-1.5 px-5 py-2.5 rounded-full bg-brand-600 hover:bg-brand-700 text-white text-xs font-semibold transition shadow-pill shrink-0">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                <span>New Booking</span>
            </a>
        </div>

        <div class="hidden md:block bg-white rounded-[28px] shadow-card border border-[#EDF2F0] overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="text-xs font-semibold text-slate-600 border-b border-[#EFF4F2]">
                            <th class="py-4 px-6">Booking ID</th>
                            <th class="py-4 px-5">Client</th>
                            <th class="py-4 px-5">Event Due</th>
                            <th class="py-4 px-5">Services</th>
                            <th class="py-4 px-5">Booking Status</th>
                            <th class="py-4 px-6">Invoice Status</th>
                        </tr>
                    </thead>
                    <!-- Shimmer Rows during Pagination -->
                    <tbody x-show="bookingTableLoading" class="divide-y divide-[#F1F5F4]" x-cloak>
                        <template x-for="i in Math.min(5, bookingPerPage)" :key="'b-shim-' + i">
                            <tr>
                                <td class="py-4 px-6 whitespace-nowrap">
                                    <div class="h-4 w-20 rounded-md shimmer-mask"></div>
                                </td>
                                <td class="py-4 px-5 whitespace-nowrap">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-lg shimmer-mask shrink-0"></div>
                                        <div class="h-4 w-32 rounded-md shimmer-mask"></div>
                                    </div>
                                </td>
                                <td class="py-4 px-5 whitespace-nowrap">
                                    <div class="h-4 w-24 rounded-md shimmer-mask"></div>
                                </td>
                                <td class="py-4 px-5 whitespace-nowrap">
                                    <div class="h-4 w-28 rounded-md shimmer-mask"></div>
                                </td>
                                <td class="py-4 px-5 whitespace-nowrap">
                                    <div class="h-6 w-20 rounded-full shimmer-mask"></div>
                                </td>
                                <td class="py-4 px-6 whitespace-nowrap">
                                    <div class="h-6 w-16 rounded-full shimmer-mask"></div>
                                </td>
                            </tr>
                        </template>
                    </tbody>

                    <!-- Real Data Rows -->
                    <tbody x-show="!bookingTableLoading" class="divide-y divide-[#F1F5F4]">
                            <template x-for="b in paginatedBookings" :key="b.id">
                                <tr @click="openDetail(b)" class="hover:bg-[#F8FBFA] transition cursor-pointer group">
                                    <td class="py-4 px-6 whitespace-nowrap">
                                        <span class="text-[13px] font-semibold text-[#0284C7] group-hover:underline font-mono" x-text="'#' + b.code"></span>
                                    </td>
                                    <td class="py-4 px-5 whitespace-nowrap">
                                        <div class="flex items-center gap-3">
                                            <img :src="b.avatar" :alt="b.name" class="w-8 h-8 rounded-lg object-cover shrink-0">
                                            <span class="text-[13px] font-medium text-slate-800" x-text="b.name"></span>
                                        </div>
                                    </td>
                                    <td class="py-4 px-5 whitespace-nowrap">
                                        <span class="text-[13px] font-medium text-slate-800" x-text="b.date_short"></span>
                                        <span class="text-[11px] font-medium text-slate-400 ml-1.5" x-text="b.time"></span>
                                    </td>
                                    <td class="py-4 px-5 whitespace-nowrap">
                                        <span class="text-[13px] font-medium text-slate-700" x-text="b.service"></span>
                                    </td>
                                    <td class="py-4 px-5 whitespace-nowrap">
                                        <span :class="{
                                                'bg-[#E6F6EF] text-emerald-700': b.bookingStatus === 'Confirmed',
                                                'bg-[#EAF3FF] text-blue-600': b.bookingStatus === 'Inquiry',
                                                'bg-[#F3EAFF] text-purple-600': b.bookingStatus === 'Complete',
                                                'bg-[#FEECEB] text-rose-600': b.bookingStatus === 'Cancelled'
                                              }"
                                              class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-semibold">
                                            <span class="w-1.5 h-1.5 rounded-full"
                                                  :class="{
                                                    'bg-emerald-500': b.bookingStatus === 'Confirmed',
                                                    'bg-blue-500': b.bookingStatus === 'Inquiry',
                                                    'bg-purple-500': b.bookingStatus === 'Complete',
                                                    'bg-rose-500': b.bookingStatus === 'Cancelled'
                                                  }"></span>
                                            <span x-text="b.bookingStatus"></span>
                                        </span>
                                    </td>
                                    <td class="py-4 px-6 whitespace-nowrap">
                                        <span :class="{
                                                'bg-[#E6F6EF] text-emerald-700': b.invoiceStatus === 'Paid',
                                                'bg-[#FEF6E0] text-amber-600': b.invoiceStatus === 'Sent',
                                                'bg-[#F1F5F9] text-slate-500': b.invoiceStatus === 'Draft',
                                                'bg-[#FEECEB] text-rose-600': b.invoiceStatus === 'Overdue' || b.invoiceStatus === 'Cancelled'
                                              }"
                                              class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-semibold">
                                            <span class="w-1.5 h-1.5 rounded-full"
                                                  :class="{
                                                    'bg-emerald-500': b.invoiceStatus === 'Paid',
                                                    'bg-amber-400': b.invoiceStatus === 'Sent',
                                                    'bg-slate-400': b.invoiceStatus === 'Draft',
                                                    'bg-rose-500': b.invoiceStatus === 'Overdue' || b.invoiceStatus === 'Cancelled'
                                                  }"></span>
                                            <span x-text="b.invoiceStatus"></span>
                                        </span>
                                    </td>
                                </tr>
                            </template>

                        <tr x-show="filteredBookings.length === 0">
                            <td colspan="6" class="py-12 text-center text-xs text-slate-400">
                                No bookings match your search or filter.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Table Footer with Interactive Pagination -->
            <div class="px-6 py-4 bg-[#FAFDFC] border-t border-[#EFF4F2] flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs text-slate-500 font-medium">
                <div class="flex items-center gap-3">
                    <span x-show="filteredBookings.length > 0">
                        Showing <strong class="text-slate-900 font-semibold" x-text="bookingFrom"></strong>–<strong class="text-slate-900 font-semibold" x-text="bookingTo"></strong> of <strong class="text-slate-900 font-semibold" x-text="filteredBookings.length"></strong> bookings
                        <span x-show="filteredBookings.length !== allBookings.length" class="text-slate-400 text-[11px]" x-text="'(' + allBookings.length + ' total)'"></span>
                    </span>
                    <span x-show="filteredBookings.length === 0">
                        0 bookings
                    </span>

                    <div class="hidden sm:flex items-center gap-1.5 pl-3 border-l border-slate-200">
                        <span class="text-slate-400 text-[11px]">Per page:</span>
                        <select x-model.number="bookingPerPage"
                                class="bg-white border border-[#E2EAE7] rounded-lg px-2 py-1 text-xs font-semibold text-slate-700 focus:outline-none focus:border-brand-600 cursor-pointer">
                            <option :value="10">10</option>
                            <option :value="20">20</option>
                            <option :value="50">50</option>
                        </select>
                    </div>
                </div>

                <!-- Pagination Buttons (Prev, Numbers, Next) -->
                <div x-show="totalBookingPages > 1" class="flex items-center gap-1">
                    <!-- Prev -->
                    <button type="button"
                            @click="if (bookingPage > 1) { bookingPage--; }"
                            :disabled="bookingPage <= 1"
                            :class="bookingPage <= 1 ? 'opacity-35 cursor-not-allowed text-slate-300' : 'text-slate-600 hover:bg-white hover:text-brand-700 hover:border-slate-300'"
                            class="inline-flex items-center justify-center w-8 h-8 rounded-lg border border-[#E2EAE7] bg-white transition cursor-pointer"
                            title="Previous page">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    </button>

                    <!-- Numbers -->
                    <template x-for="(p, idx) in bookingPaginationRange" :key="'bp-' + idx + '-' + p">
                        <div>
                            <template x-if="p === '...'">
                                <span class="inline-flex items-center justify-center w-8 h-8 text-xs text-slate-400 select-none">…</span>
                            </template>
                            <template x-if="p !== '...'">
                                <button type="button"
                                        @click="bookingPage = p"
                                        :class="bookingPage === p 
                                            ? 'bg-brand-600 text-white font-bold border-brand-600 shadow-2xs' 
                                            : 'bg-white text-slate-600 hover:bg-slate-50 border-[#E2EAE7] hover:border-slate-300'"
                                        class="inline-flex items-center justify-center min-w-[32px] h-8 px-2 rounded-lg border text-xs font-semibold transition cursor-pointer"
                                        x-text="p">
                                </button>
                            </template>
                        </div>
                    </template>

                    <!-- Next -->
                    <button type="button"
                            @click="if (bookingPage < totalBookingPages) { bookingPage++; }"
                            :disabled="bookingPage >= totalBookingPages"
                            :class="bookingPage >= totalBookingPages ? 'opacity-35 cursor-not-allowed text-slate-300' : 'text-slate-600 hover:bg-white hover:text-brand-700 hover:border-slate-300'"
                            class="inline-flex items-center justify-center w-8 h-8 rounded-lg border border-[#E2EAE7] bg-white transition cursor-pointer"
                            title="Next page">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </button>
                </div>
            </div>
        </div>

        <div class="md:hidden space-y-3">
            <template x-for="b in paginatedBookings" :key="b.id">
                <div @click="openDetail(b)" class="bg-white rounded-[24px] p-4 shadow-card border border-[#EDF2F0] space-y-3 cursor-pointer active:scale-[0.99] transition">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold font-mono text-[#0284C7]" x-text="'#' + b.code"></span>
                        <span class="text-[11px] text-slate-400" x-text="b.date_short"></span>
                    </div>
                    <div class="flex items-center gap-3">
                        <img :src="b.avatar" :alt="b.name" class="w-10 h-10 rounded-xl object-cover">
                        <div class="min-w-0">
                            <p class="text-sm font-bold text-slate-900 truncate" x-text="b.name"></p>
                            <p class="text-xs text-slate-500 truncate" x-text="b.service"></p>
                        </div>
                    </div>
                    <div class="pt-2.5 border-t border-[#F1F5F4] flex items-center justify-between">
                        <span :class="{
                                'bg-[#E6F6EF] text-emerald-700': b.bookingStatus === 'Confirmed',
                                'bg-[#EAF3FF] text-blue-600': b.bookingStatus === 'Inquiry',
                                'bg-[#F3EAFF] text-purple-600': b.bookingStatus === 'Complete',
                                'bg-[#FEECEB] text-rose-600': b.bookingStatus === 'Cancelled'
                              }"
                              class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-semibold">
                            <span x-text="b.bookingStatus"></span>
                        </span>
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-semibold bg-[#F4F7F6] text-slate-700" x-text="b.invoiceStatus"></span>
                    </div>
                </div>
            </template>

            <!-- Mobile Pagination for Bookings -->
            <div x-show="totalBookingPages > 1" class="flex items-center justify-between p-3.5 bg-white rounded-2xl shadow-card border border-[#EDF2F0]">
                <button type="button"
                        @click="if (bookingPage > 1) { bookingPage--; }"
                        :disabled="bookingPage <= 1"
                        :class="bookingPage <= 1 ? 'opacity-35 cursor-not-allowed text-slate-400' : 'text-brand-700 font-bold active:scale-95'"
                        class="px-3 py-1.5 rounded-xl border border-slate-200 text-xs transition cursor-pointer">
                    ← Prev
                </button>
                <span class="text-xs text-slate-500 font-medium">
                    Page <strong class="text-slate-900" x-text="bookingPage"></strong> of <strong class="text-slate-900" x-text="totalBookingPages"></strong>
                </span>
                <button type="button"
                        @click="if (bookingPage < totalBookingPages) { bookingPage++; }"
                        :disabled="bookingPage >= totalBookingPages"
                        :class="bookingPage >= totalBookingPages ? 'opacity-35 cursor-not-allowed text-slate-400' : 'text-brand-700 font-bold active:scale-95'"
                        class="px-3 py-1.5 rounded-xl border border-slate-200 text-xs transition cursor-pointer">
                    Next →
                </button>
            </div>
        </div>
    </div>

    <!-- ================================================================== -->
    <!-- TAB 3: CALENDAR (DYNAMIC REAL MONTHLY GRID + CONFLICT ALERT ⚠)     -->
    <!-- ================================================================== -->
    <div x-show="$store.nav.tab === 'calendar'"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 translate-y-1"
         x-transition:enter-end="opacity-100 translate-y-0"
         class="bg-white rounded-[28px] shadow-card border border-[#EDF2F0] overflow-hidden"
         x-cloak>

        <div class="px-4 sm:px-6 py-3.5 sm:py-4 border-b border-[#EFF4F2] flex flex-col lg:flex-row lg:items-center justify-between gap-3 sm:gap-4">
            <!-- Row 1 di Mobile / Left Section di Desktop -->
            <div class="flex items-center justify-between gap-2 w-full lg:w-auto lg:justify-start lg:gap-4">
                <!-- Kiri: Navigator Bulan -->
                <div class="flex items-center gap-1.5 sm:gap-2.5">
                    <button type="button" @click="prevCalMonth()"
                            class="w-8 h-8 rounded-full hover:bg-[#F4F7F6] active:scale-90 text-slate-500 flex items-center justify-center transition cursor-pointer shrink-0"
                            title="Bulan sebelumnya">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M15 19l-7-7 7-7"/></svg>
                    </button>

                    <!-- Mini Date Sheet (hanya tampil di sm ke atas agar di HP tidak sesak) -->
                    <div class="hidden sm:block w-11 rounded-xl border border-[#E5ECE9] overflow-hidden text-center shadow-2xs shrink-0">
                        <div class="bg-[#F4F7F6] text-[9px] font-bold uppercase tracking-wider text-slate-500 py-0.5"
                             x-text="monthShort[calMonth - 1]"></div>
                        <div class="bg-white text-xs font-extrabold text-slate-900 py-1 leading-none"
                             x-text="(calYear === currentRealYear && calMonth === currentRealMonth) ? currentRealDay : '01'"></div>
                    </div>

                    <div class="min-w-0">
                        <h2 class="text-sm sm:text-[15px] font-bold text-slate-900 leading-tight whitespace-nowrap" x-text="calHeaderLabel"></h2>
                        <p class="hidden sm:block text-[11px] font-medium text-slate-400 mt-0.5 whitespace-nowrap" x-text="calRangeLabel"></p>
                    </div>

                    <button type="button" @click="nextCalMonth()"
                            class="w-8 h-8 rounded-full hover:bg-[#F4F7F6] active:scale-90 text-slate-500 flex items-center justify-center transition cursor-pointer shrink-0"
                            title="Bulan berikutnya">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M9 5l7 7-7 7"/></svg>
                    </button>
                </div>

                <!-- Kanan: Tombol Today + Switcher View (Monthly / Agenda) -->
                <div class="flex items-center gap-1.5 sm:gap-2 shrink-0">
                    <button type="button"
                            @click="goToTodayCalendar()"
                            :class="(calYear === currentRealYear && calMonth === currentRealMonth) ? 'bg-[#F4F7F6] text-slate-400 border-[#E5ECE9] cursor-default' : 'bg-white hover:bg-[#F4F7F6] text-slate-700 border-[#E5ECE9] cursor-pointer shadow-2xs active:scale-95'"
                            class="px-2.5 sm:px-3 py-1.5 rounded-full border text-xs font-semibold transition shrink-0">
                        Today
                    </button>

                    <div class="relative shrink-0" @click.away="calendarViewOpen = false">
                        <button type="button"
                                @click="calendarViewOpen = !calendarViewOpen"
                                class="inline-flex items-center justify-between gap-1.5 sm:gap-3 bg-white hover:bg-[#FAFDFC] rounded-xl border border-[#E5ECE9] px-2.5 sm:px-3.5 py-1.5 sm:py-2 text-xs font-semibold text-slate-700 transition cursor-pointer">
                            <span x-text="calendarView === 'Monthly View' ? 'Monthly' : 'Agenda'" class="sm:hidden"></span>
                            <span x-text="calendarView" class="hidden sm:inline"></span>
                            <svg class="w-3.5 h-3.5 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-show="calendarViewOpen" class="absolute right-0 lg:left-0 mt-2 w-40 bg-white rounded-2xl shadow-xl border border-[#EAF0EE] py-1.5 z-30" x-cloak>
                            <template x-for="v in ['Monthly View', 'Agenda List']" :key="v">
                                <button type="button" @click="calendarView = v; calendarViewOpen = false"
                                        :class="calendarView === v ? 'bg-brand-50 text-brand-700 font-semibold' : 'text-slate-600 hover:bg-[#F6F8F7]'"
                                        class="w-full text-left px-4 py-2 text-xs transition flex items-center justify-between cursor-pointer">
                                    <span x-text="v"></span>
                                    <span x-show="calendarView === v" class="text-brand-600 font-bold">✓</span>
                                </button>
                            </template>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Row 2 di Mobile: Strip Status Legend Rapi 4 Kolom (Di Desktop tetap sejajar di sisi kanan) -->
            <div class="grid grid-cols-4 gap-1 py-1.5 px-2 bg-[#F8FAFA] border border-[#EAF0EE] rounded-xl text-[11px] font-medium text-slate-600 lg:bg-transparent lg:border-0 lg:p-0 lg:flex lg:flex-wrap lg:items-center lg:gap-4 lg:text-xs">
                <span class="inline-flex items-center justify-center lg:justify-start gap-1.5 whitespace-nowrap">
                    <span class="w-2 h-2 sm:w-2.5 sm:h-2.5 rounded-[3px] bg-blue-600 shrink-0"></span>
                    Inquiry
                </span>
                <span class="inline-flex items-center justify-center lg:justify-start gap-1.5 whitespace-nowrap">
                    <span class="w-2 h-2 sm:w-2.5 sm:h-2.5 rounded-[3px] bg-emerald-500 shrink-0"></span>
                    Confirmed
                </span>
                <span class="inline-flex items-center justify-center lg:justify-start gap-1.5 whitespace-nowrap">
                    <span class="w-2 h-2 sm:w-2.5 sm:h-2.5 rounded-[3px] bg-purple-500 shrink-0"></span>
                    Complete
                </span>
                <span class="inline-flex items-center justify-center lg:justify-start gap-1.5 whitespace-nowrap">
                    <span class="w-2 h-2 sm:w-2.5 sm:h-2.5 rounded-[3px] bg-rose-500 shrink-0"></span>
                    Cancelled
                </span>
            </div>
        </div>

        <!-- MONTHLY VIEW GRID -->
        <div x-show="calendarView === 'Monthly View'" class="overflow-x-auto">
            <div class="min-w-[780px]">
                <div class="grid grid-cols-7 border-b border-[#EFF4F2] bg-[#FCFDFD]">
                    <div class="py-3 text-center text-xs font-semibold text-slate-700">Sun</div>
                    <div class="py-3 text-center text-xs font-semibold text-slate-700">Mon</div>
                    <div class="py-3 text-center text-xs font-semibold text-slate-700">Tue</div>
                    <div class="py-3 text-center text-xs font-semibold text-slate-700">Wed</div>
                    <div class="py-3 text-center text-xs font-semibold text-slate-700">Thu</div>
                    <div class="py-3 text-center text-xs font-semibold text-slate-700">Fri</div>
                    <div class="py-3 text-center text-xs font-semibold text-slate-700">Sat</div>
                </div>

                <div class="grid grid-cols-7">
                    <template x-for="cell in calendarCells" :key="cell.key">
                        <div :class="{
                                'bg-[#FAFDFC]/60': cell.blank,
                                'bg-[#F4F9F7] ring-1 ring-brand-300 ring-inset': !cell.blank && cell.isToday,
                                'bg-white hover:bg-[#F8FBFA]': !cell.blank && !cell.isToday
                             }"
                             :title="cell.blank ? '' : 'Click date to view all events (' + (cell.events ? cell.events.length : 0) + ' bookings)'"
                             @click="if (!cell.blank) openDayModal(cell)"
                             class="min-h-[120px] p-2 border-r border-b border-[#EFF4F2] transition-colors flex flex-col gap-1.5 cursor-pointer select-none group relative">
                            <template x-if="!cell.blank">
                                <div class="flex flex-col gap-1.5 flex-1">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center gap-1.5">
                                            <span :class="cell.isToday
                                                    ? 'w-6 h-6 rounded-full bg-brand-600 text-white text-[11px] font-bold inline-flex items-center justify-center shadow-xs'
                                                    : (cell.events.length > 0
                                                        ? 'w-6 h-6 rounded-full bg-slate-900 text-white text-[11px] font-bold inline-flex items-center justify-center group-hover:bg-brand-600 transition'
                                                        : 'text-xs font-bold text-slate-800')"
                                                  x-text="cell.day"></span>
                                            <template x-if="cell.isToday">
                                                <span class="px-1.5 py-0.5 rounded-full text-[9.5px] font-bold bg-brand-50 text-brand-700 border border-brand-200">
                                                    Today
                                                </span>
                                            </template>
                                        </div>
                                        <template x-if="cell.hasConflict">
                                            <span title="Daily capacity limit exceeded!" class="text-orange-500">
                                                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor">
                                                    <path d="M12 2L1 21h22L12 2zm0 4.5l7.5 13h-15l7.5-13zM11 10v5h2v-5h-2zm0 6v2h2v-2h-2z"/>
                                                </svg>
                                            </span>
                                        </template>
                                    </div>

                                    <!-- Event chips (Up to 2 shown in cell) -->
                                    <div class="space-y-1">
                                        <template x-for="ev in cell.events.slice(0, 2)" :key="ev.id">
                                            <button type="button"
                                                    @click.stop="openDetail(ev)"
                                                    :class="{
                                                        'bg-[#E6F6EF] hover:bg-[#D7F0E5] text-emerald-800': ev.bookingStatus === 'Confirmed',
                                                        'bg-[#F3EAFF] hover:bg-[#E9DAFF] text-purple-800': ev.bookingStatus === 'Complete',
                                                        'bg-[#EAF3FF] hover:bg-[#DCEBFF] text-blue-800': ev.bookingStatus === 'Inquiry',
                                                        'bg-[#FEECEB] hover:bg-[#FCDCD9] text-rose-700': ev.bookingStatus === 'Cancelled'
                                                    }"
                                                    class="w-full text-left px-2 py-1 rounded-lg transition cursor-pointer text-xs block group/chip">
                                                <p class="text-[10px] font-bold leading-tight truncate" x-text="'#' + ev.code + ' · ' + ev.shortName + (ev.duration_days > 1 ? ' (' + ev.duration_days + 'D)' : '')"></p>
                                                <p class="text-[9.5px] opacity-80 leading-tight truncate mt-0.5" x-text="ev.service"></p>
                                            </button>
                                        </template>

                                        <!-- "+X more" Button if > 2 -->
                                        <template x-if="cell.events.length > 2">
                                            <button type="button"
                                                    @click.stop="openDayModal(cell)"
                                                    class="w-full py-1 px-2 rounded-lg bg-[#F4F7F6] hover:bg-brand-50 hover:text-brand-700 hover:border-brand-300 text-slate-700 font-bold text-[10px] text-center transition flex items-center justify-center gap-1 border border-[#E2EAE7] cursor-pointer">
                                                <span x-text="'+ ' + (cell.events.length - 2) + ' more'"></span>
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M9 5l7 7-7 7"/></svg>
                                            </button>
                                        </template>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </template>
                </div>
            </div>
        </div>

        <!-- AGENDA LIST VIEW -->
        <div x-show="calendarView === 'Agenda List'" class="p-4 sm:p-6 space-y-3 sm:space-y-4">
            <div class="flex items-center justify-between gap-3 pb-3 border-b border-[#EFF4F2]">
                <div>
                    <h3 class="text-sm font-bold text-slate-900 leading-snug">
                        <span class="sm:hidden" x-text="'Agenda · ' + calHeaderLabel"></span>
                        <span class="hidden sm:inline" x-text="'Monthly Booking Agenda · ' + calHeaderLabel"></span>
                    </h3>
                    <p class="text-[11px] text-slate-400 sm:hidden">All scheduled bookings</p>
                </div>
                <span class="px-2.5 py-1 rounded-full bg-[#F4F7F6] border border-[#E8EEEB] text-xs font-bold text-slate-700 whitespace-nowrap shrink-0"
                      x-text="monthBookings.length + ' Bookings'"></span>
            </div>

            <template x-if="monthBookings.length === 0">
                <div class="py-12 text-center text-xs text-slate-400">
                    No bookings scheduled for this month.
                </div>
            </template>

            <div class="space-y-3" x-show="monthBookings.length > 0">
                <template x-for="ev in monthBookings" :key="'agenda-' + ev.id">
                    <div @click="openDetail(ev)"
                         class="p-3.5 sm:p-4 rounded-2xl bg-[#FAFDFC] hover:bg-white border border-[#EDF2F0] hover:border-brand-300 hover:shadow-card transition cursor-pointer flex flex-col sm:flex-row sm:items-center justify-between gap-3 sm:gap-4 group">
                        
                        <!-- Bagian Atas / Kiri: Date Sheet + Info Reservasi -->
                        <div class="flex items-center gap-3 sm:gap-3.5 min-w-0">
                            <!-- Date Sheet -->
                            <div class="w-11 h-11 sm:w-12 sm:h-12 rounded-xl bg-white border border-[#E5ECE9] flex flex-col items-center justify-center text-center shrink-0 shadow-2xs group-hover:border-brand-200 transition">
                                <span class="text-[9px] font-bold text-slate-400 uppercase leading-none" x-text="monthShort[ev.event_month - 1]"></span>
                                <span class="text-sm sm:text-base font-extrabold text-slate-900 leading-none mt-0.5" x-text="ev.event_day"></span>
                            </div>

                            <!-- Text Info -->
                            <div class="min-w-0 flex-1">
                                <div class="flex items-center gap-1.5 sm:gap-2 flex-wrap">
                                    <span class="text-xs font-bold font-mono text-[#0284C7]" x-text="'#' + ev.code"></span>
                                    <span class="text-xs sm:text-sm font-bold text-slate-900 truncate" x-text="ev.name"></span>
                                    <span x-show="ev.duration_days > 1" class="px-1.5 py-0.2 rounded-full text-[9.5px] font-bold bg-amber-50 text-amber-700 border border-amber-200" x-text="ev.duration_days + ' Days'"></span>
                                </div>
                                <p class="text-[11.5px] sm:text-xs text-slate-500 truncate mt-0.5" x-text="ev.service + ' · ' + ev.date"></p>
                            </div>
                        </div>

                        <!-- Bagian Bawah di Mobile / Kanan di Desktop: Keuangan & Badge Status -->
                        <div class="border-t border-[#F0F4F2] pt-2.5 sm:border-t-0 sm:pt-0 flex items-center justify-between sm:justify-end gap-3 shrink-0">
                            <!-- Info Biaya & Pelunasan -->
                            <div class="flex items-baseline sm:block sm:text-right gap-1.5">
                                <p class="text-xs sm:text-xs font-bold text-slate-900" x-text="ev.grand_total_formatted"></p>
                                <span class="text-[10px] sm:text-[11px] font-semibold px-1.5 py-0.2 sm:px-0 sm:py-0 rounded sm:rounded-none inline-block"
                                      :class="ev.remaining_balance <= 0 ? 'text-emerald-700 bg-emerald-50 sm:bg-transparent' : 'text-amber-700 bg-amber-50 sm:bg-transparent'"
                                      x-text="ev.remaining_balance <= 0 ? 'Paid' : 'Due ' + ev.remaining_formatted"></span>
                            </div>

                            <!-- Status Badge -->
                            <div class="flex items-center gap-2">
                                <span :class="{
                                        'bg-[#E6F6EF] text-emerald-700': ev.bookingStatus === 'Confirmed',
                                        'bg-[#F3EAFF] text-purple-600': ev.bookingStatus === 'Complete',
                                        'bg-[#EAF3FF] text-blue-600': ev.bookingStatus === 'Inquiry',
                                        'bg-[#FEECEB] text-rose-600': ev.bookingStatus === 'Cancelled'
                                      }"
                                      class="px-2.5 sm:px-3 py-1 rounded-full text-[11px] font-semibold whitespace-nowrap"
                                      x-text="ev.bookingStatus"></span>
                                <svg class="w-4 h-4 text-slate-300 group-hover:text-brand-600 group-hover:translate-x-0.5 transition hidden sm:block shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </div>
                        </div>

                    </div>
                </template>
            </div>
        </div>

        <!-- ================================================================== -->
        <!-- MODAL: DETAIL AGENDA TANGGAL / DAY BOOKINGS MODAL                  -->
        <!-- ================================================================== -->
        <div x-show="selectedDayModal"
             @keydown.window.escape="closeDayModal()"
             class="fixed inset-0 z-50 overflow-y-auto"
             x-cloak>
            <!-- Backdrop Blur -->
            <div class="fixed inset-0 bg-slate-900/40 backdrop-blur-xs transition-opacity"
                 @click="closeDayModal()"
                 x-transition:enter="ease-out duration-200"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="ease-in duration-150"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"></div>

            <!-- Modal Dialog Box -->
            <div class="min-h-full flex items-center justify-center p-4 sm:p-6 text-center">
                <div @click.stop
                     x-transition:enter="ease-out duration-200"
                     x-transition:enter-start="opacity-0 scale-95 translate-y-2"
                     x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                     x-transition:leave="ease-in duration-150"
                     x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                     x-transition:leave-end="opacity-0 scale-95 translate-y-2"
                     class="relative bg-white rounded-[28px] max-w-xl w-full p-5 sm:p-7 text-left shadow-2xl border border-[#EDF2F0] space-y-5 transform transition-all overflow-hidden">

                    <!-- Header -->
                    <div class="flex items-start justify-between gap-4 pb-4 border-b border-[#EFF4F2]">
                        <div class="flex items-center gap-3.5">
                            <div class="w-12 h-12 rounded-2xl bg-brand-50 border border-brand-100 text-brand-700 flex flex-col items-center justify-center text-center shrink-0 shadow-2xs">
                                <span class="text-[9px] font-bold uppercase leading-none" x-text="selectedDayModal ? monthShort[calMonth - 1] : ''"></span>
                                <span class="text-base font-extrabold leading-none mt-0.5 text-slate-900" x-text="selectedDayModal?.day"></span>
                            </div>
                            <div>
                                <div class="flex items-center gap-2">
                                    <h3 class="text-base sm:text-lg font-bold text-slate-900 tracking-tight" x-text="selectedDayModal?.dateFull"></h3>
                                    <template x-if="selectedDayModal?.isToday">
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-brand-100 text-brand-800">Today</span>
                                    </template>
                                </div>
                                <p class="text-xs text-slate-500 mt-0.5" x-text="(selectedDayModal?.events?.length || 0) + ' Scheduled Bookings'"></p>
                            </div>
                        </div>
                        <button type="button"
                                @click="closeDayModal()"
                                class="w-8 h-8 rounded-full bg-[#F4F7F6] hover:bg-[#EAEFEA] text-slate-500 hover:text-slate-800 flex items-center justify-center transition cursor-pointer shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>

                    <!-- Quota / Conflict Banner -->
                    <template x-if="selectedDayModal && (selectedDayModal.events.filter(e => e.raw_status !== 'rejected').length > maxEventsPerDay)">
                        <div class="p-3.5 rounded-2xl bg-amber-50 border border-amber-200 text-amber-900 flex items-start gap-3 text-xs">
                            <span class="text-base shrink-0 mt-0.5">⚠️</span>
                            <div>
                                <p class="font-bold">Warning: Daily Capacity Limit Exceeded!</p>
                                <p class="text-[11.5px] text-amber-800 mt-0.5"
                                   x-text="'There are ' + selectedDayModal.events.filter(e => e.raw_status !== 'rejected').length + ' events on this date (Standard capacity: ' + maxEventsPerDay + ' events/day). Ensure crew and inventory readiness.'"></p>
                            </div>
                        </div>
                    </template>

                    <!-- Bookings Cards Container -->
                    <div class="max-h-[55vh] overflow-y-auto space-y-3 pr-1 divide-y divide-[#F4F7F6]">
                        <template x-if="selectedDayModal?.events?.length === 0">
                            <div class="py-12 text-center space-y-2">
                                <div class="w-12 h-12 rounded-2xl bg-[#F4F7F6] text-slate-400 mx-auto flex items-center justify-center">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                </div>
                                <p class="text-xs font-semibold text-slate-700">No bookings on this date</p>
                                <p class="text-[11px] text-slate-400">This date is currently open and ready for new bookings.</p>
                            </div>
                        </template>

                        <template x-for="(ev, idx) in (selectedDayModal?.events || [])" :key="ev.id">
                            <div class="pt-3 first:pt-0">
                                <div class="p-4 rounded-2xl bg-[#FAFDFC] hover:bg-white border border-[#EAF0EE] hover:border-brand-300 hover:shadow-card transition space-y-3 group">
                                    <!-- Top Row: Code, Date & Status -->
                                    <div class="flex items-center justify-between gap-2">
                                        <div class="flex items-center gap-2">
                                            <span class="text-xs font-bold font-mono text-[#0284C7]" x-text="'#' + ev.code"></span>
                                            <template x-if="ev.duration_days > 1">
                                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200"
                                                      x-text="ev.duration_days + ' Days: ' + ev.date_short"></span>
                                            </template>
                                        </div>
                                        <span :class="{
                                                'bg-[#E6F6EF] text-emerald-700': ev.bookingStatus === 'Confirmed',
                                                'bg-[#F3EAFF] text-purple-600': ev.bookingStatus === 'Complete',
                                                'bg-[#EAF3FF] text-blue-600': ev.bookingStatus === 'Inquiry',
                                                'bg-[#FEECEB] text-rose-600': ev.bookingStatus === 'Cancelled'
                                              }"
                                              class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-semibold">
                                            <span class="w-1.5 h-1.5 rounded-full"
                                                  :class="{
                                                    'bg-emerald-500': ev.bookingStatus === 'Confirmed',
                                                    'bg-purple-500': ev.bookingStatus === 'Complete',
                                                    'bg-blue-500': ev.bookingStatus === 'Inquiry',
                                                    'bg-rose-500': ev.bookingStatus === 'Cancelled'
                                                  }"></span>
                                            <span x-text="ev.bookingStatus"></span>
                                        </span>
                                    </div>

                                    <!-- Middle Row: Customer Info & Service -->
                                    <div class="flex items-start justify-between gap-3">
                                        <div class="flex items-center gap-3 min-w-0">
                                            <img :src="ev.avatar" :alt="ev.name"
                                                 class="w-10 h-10 rounded-xl object-cover shrink-0">
                                            <div class="min-w-0">
                                                <p class="text-xs sm:text-sm font-bold text-slate-900 truncate" x-text="ev.name"></p>
                                                <p class="text-[11px] text-slate-500 truncate" x-text="ev.phone"></p>
                                            </div>
                                        </div>
                                        <div class="text-right shrink-0">
                                            <p class="text-xs font-bold text-brand-700" x-text="ev.service"></p>
                                            <p class="text-[11px] text-slate-500" x-text="ev.grand_total_formatted"></p>
                                        </div>
                                    </div>

                                    <!-- Bottom Row: Payment Summary & Action Buttons -->
                                    <div class="pt-2.5 border-t border-[#EDF2F0] flex flex-wrap items-center justify-between gap-2.5 text-xs">
                                        <div class="flex items-center gap-2">
                                            <span class="text-[11px] text-slate-400">Payment:</span>
                                            <span class="font-bold text-[11.5px]"
                                                  :class="ev.remaining_balance <= 0 ? 'text-emerald-600' : 'text-amber-600'"
                                                  x-text="ev.remaining_balance <= 0 ? 'Paid in Full (100%)' : 'Due ' + ev.remaining_formatted"></span>
                                        </div>
                                        <div class="flex items-center gap-2 ml-auto">
                                            <template x-if="ev.whatsapp_url">
                                                <a :href="ev.whatsapp_url" target="_blank"
                                                   class="inline-flex items-center gap-1 px-3 py-1.5 rounded-full bg-emerald-50 hover:bg-emerald-100 text-emerald-700 text-xs font-semibold transition cursor-pointer">
                                                    <span>WhatsApp</span>
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                                </a>
                                            </template>
                                            <button type="button"
                                                    @click="closeDayModal(); openDetail(ev)"
                                                    class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full bg-brand-600 hover:bg-brand-700 text-white text-xs font-semibold transition shadow-xs cursor-pointer">
                                                <span>View Details</span>
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>

                    <!-- Footer Modal -->
                    <div class="pt-3 border-t border-[#EFF4F2] flex items-center justify-between gap-3">
                        <a href="{{ route('booking.create') }}" target="_blank"
                           class="inline-flex items-center gap-1.5 text-xs font-semibold text-brand-700 hover:text-brand-800 hover:underline">
                            <span>+ New Booking</span>
                        </a>
                        <button type="button"
                                @click="closeDayModal()"
                                class="px-5 py-2 rounded-full bg-[#F4F7F6] hover:bg-[#EAEFEA] text-slate-700 text-xs font-semibold transition cursor-pointer">
                            Close
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ================================================================== -->
    <!-- TAB 4: CLIENTS SKELETON SHIMMER (FLUTTER STYLE)                    -->
    <!-- ================================================================== -->
    <div x-show="$store.nav.tab === 'clients' && pageLoading"
         class="space-y-5"
         x-cloak>
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-3">
            <div class="flex flex-wrap items-center gap-2.5 sm:gap-3">
                <div class="h-10 w-64 sm:w-72 rounded-full shimmer-mask"></div>
                <div class="h-10 w-36 rounded-full shimmer-mask"></div>
            </div>
            <div class="h-10 w-32 rounded-full shimmer-mask"></div>
        </div>

        <div class="bg-white rounded-[28px] shadow-card border border-[#EDF2F0] p-6 space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-[#EFF4F2]">
                <div class="h-4 w-32 rounded shimmer-mask"></div>
                <div class="h-4 w-40 rounded shimmer-mask"></div>
                <div class="h-4 w-28 rounded shimmer-mask"></div>
                <div class="h-4 w-24 rounded shimmer-mask"></div>
                <div class="h-4 w-24 rounded shimmer-mask"></div>
            </div>
            <div class="space-y-4">
                <template x-for="i in 5" :key="'shimmer-c-row-' + i">
                    <div class="flex items-center justify-between py-2 border-b border-[#F8FAFA]">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-full shimmer-mask shrink-0"></div>
                            <div class="space-y-1.5">
                                <div class="h-4 w-32 rounded shimmer-mask"></div>
                                <div class="h-3 w-24 rounded shimmer-mask"></div>
                            </div>
                        </div>
                        <div class="h-4 w-28 rounded shimmer-mask"></div>
                        <div class="h-4 w-24 rounded shimmer-mask"></div>
                        <div class="h-4 w-28 rounded shimmer-mask"></div>
                        <div class="h-6 w-20 rounded-full shimmer-mask"></div>
                    </div>
                </template>
            </div>
            <div class="pt-4 flex items-center justify-between border-t border-[#EFF4F2]">
                <div class="h-4 w-36 rounded shimmer-mask"></div>
                <div class="h-8 w-44 rounded-lg shimmer-mask"></div>
            </div>
        </div>
    </div>

    <!-- ================================================================== -->
    <!-- TAB 4: CLIENTS (REAL DIRECTORY)                                    -->
    <!-- ================================================================== -->
    <div x-show="$store.nav.tab === 'clients' && !pageLoading"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 translate-y-1"
         x-transition:enter-end="opacity-100 translate-y-0"
         class="space-y-5"
         x-cloak>

        <div class="flex flex-col md:flex-row md:items-center justify-between gap-3">
            <div class="flex flex-wrap items-center gap-2.5 sm:gap-3">
                <div class="relative flex items-center bg-white rounded-full border border-[#E7EEEC] w-full sm:w-64 lg:w-72">
                    <svg class="w-4 h-4 text-slate-400 absolute left-4 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-5.2-5.2m2.2-4.8a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input type="text" x-model="clientSearch" placeholder="Search Clients..."
                           class="w-full bg-transparent pl-10 pr-4 py-2.5 text-xs text-slate-700 placeholder-slate-400 focus:outline-none">
                </div>

                <div class="relative" @click.away="clientMenuOpen = false">
                    <button type="button" @click="clientMenuOpen = !clientMenuOpen"
                            class="inline-flex items-center justify-between gap-4 bg-white hover:bg-[#FAFDFC] rounded-full border border-[#E7EEEC] px-4 py-2.5 text-xs font-medium text-slate-700 transition min-w-[148px]">
                        <span x-text="clientFilter === 'all' ? 'All Clients' : clientFilter"></span>
                        <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div x-show="clientMenuOpen" class="absolute left-0 mt-2 w-44 bg-white rounded-2xl shadow-xl border border-[#EAF0EE] py-1.5 z-30" x-cloak>
                        <template x-for="st in ['all', 'Active', 'Complete', 'Lead']" :key="st">
                            <button type="button" @click="clientFilter = st; clientMenuOpen = false"
                                    :class="clientFilter === st ? 'bg-brand-50 text-brand-700 font-semibold' : 'text-slate-600 hover:bg-[#F6F8F7]'"
                                    class="w-full text-left px-4 py-2 text-xs transition flex items-center justify-between">
                                <span x-text="st === 'all' ? 'All Clients' : st"></span>
                                <span x-show="clientFilter === st" class="text-brand-600">✓</span>
                            </button>
                        </template>
                    </div>
                </div>
            </div>

            <a href="{{ route('booking.create') }}" target="_blank"
               class="inline-flex items-center justify-center gap-1.5 px-5 py-2.5 rounded-full bg-brand-600 hover:bg-brand-700 text-white text-xs font-semibold transition shadow-pill shrink-0">
                <span>+ Add Client</span>
            </a>
        </div>

        <div class="hidden md:block bg-white rounded-[28px] shadow-card border border-[#EDF2F0] overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="text-xs font-semibold text-slate-600 border-b border-[#EFF4F2]">
                            <th class="py-4 px-6">Client</th>
                            <th class="py-4 px-5">Contact</th>
                            <th class="py-4 px-5">Latest Package</th>
                            <th class="py-4 px-5">Bookings</th>
                            <th class="py-4 px-5">Total Spent</th>
                            <th class="py-4 px-6">Status</th>
                        </tr>
                    </thead>
                    <!-- Shimmer Rows during Pagination -->
                    <tbody x-show="clientTableLoading" class="divide-y divide-[#F1F5F4]" x-cloak>
                        <template x-for="i in Math.min(5, clientPerPage)" :key="'c-shim-' + i">
                            <tr>
                                <td class="py-4 px-6 whitespace-nowrap">
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-xl shimmer-mask shrink-0"></div>
                                        <div class="space-y-1.5">
                                            <div class="h-4 w-28 rounded shimmer-mask"></div>
                                            <div class="h-3 w-20 rounded shimmer-mask"></div>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-4 px-5 whitespace-nowrap">
                                    <div class="h-4 w-24 rounded shimmer-mask"></div>
                                </td>
                                <td class="py-4 px-5 whitespace-nowrap">
                                    <div class="h-4 w-24 rounded shimmer-mask"></div>
                                </td>
                                <td class="py-4 px-5 whitespace-nowrap">
                                    <div class="h-4 w-16 rounded shimmer-mask"></div>
                                </td>
                                <td class="py-4 px-5 whitespace-nowrap">
                                    <div class="h-4 w-24 rounded shimmer-mask"></div>
                                </td>
                                <td class="py-4 px-6 whitespace-nowrap">
                                    <div class="h-6 w-16 rounded-full shimmer-mask"></div>
                                </td>
                            </tr>
                        </template>
                    </tbody>

                    <!-- Real Data Rows -->
                    <tbody x-show="!clientTableLoading" class="divide-y divide-[#F1F5F4]">
                        <template x-for="c in paginatedClients" :key="c.id">
                            <tr @click="openClientProfile(c)" class="hover:bg-[#F8FBFA] transition cursor-pointer group">
                                <td class="py-4 px-6 whitespace-nowrap">
                                    <div class="flex items-center gap-3">
                                        <img :src="c.avatar" :alt="c.name" class="w-9 h-9 rounded-xl object-cover shrink-0">
                                        <div>
                                            <p class="text-[13px] font-bold text-slate-900 group-hover:text-brand-600 transition" x-text="c.name"></p>
                                            <p class="text-[11px] text-slate-400" x-text="'Event: ' + c.lastEvent"></p>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-4 px-5 whitespace-nowrap">
                                    <p class="text-[12.5px] font-medium text-slate-800" x-text="c.phone"></p>
                                    <p class="text-[11px] text-slate-400" x-text="c.email"></p>
                                </td>
                                <td class="py-4 px-5 whitespace-nowrap text-[13px] font-medium text-slate-700" x-text="c.package"></td>
                                <td class="py-4 px-5 whitespace-nowrap">
                                    <span class="px-2.5 py-1 rounded-full bg-[#F4F7F6] text-slate-700 text-[11px] font-bold" x-text="c.totalBookings + 'x Event'"></span>
                                </td>
                                <td class="py-4 px-5 whitespace-nowrap text-[13px] font-bold text-slate-900" x-text="c.totalSpentFormatted"></td>
                                <td class="py-4 px-6 whitespace-nowrap">
                                    <span :class="{
                                            'bg-[#E6F6EF] text-emerald-700': c.status === 'Active',
                                            'bg-[#F3EAFF] text-purple-600': c.status === 'Complete',
                                            'bg-[#EAF3FF] text-blue-600': c.status === 'Lead'
                                          }"
                                          class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-semibold">
                                        <span class="w-1.5 h-1.5 rounded-full"
                                              :class="{
                                                'bg-emerald-500': c.status === 'Active',
                                                'bg-purple-500': c.status === 'Complete',
                                                'bg-blue-500': c.status === 'Lead'
                                              }"></span>
                                        <span x-text="c.status"></span>
                                    </span>
                                </td>
                            </tr>
                        </template>

                        <tr x-show="filteredClients.length === 0">
                            <td colspan="6" class="py-12 text-center text-xs text-slate-400">
                                No clients match your search or filter.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Table Footer with Interactive Pagination for Clients -->
            <div class="px-6 py-4 bg-[#FAFDFC] border-t border-[#EFF4F2] flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs text-slate-500 font-medium">
                <div class="flex items-center gap-3">
                    <span x-show="filteredClients.length > 0">
                        Showing <strong class="text-slate-900 font-semibold" x-text="clientFrom"></strong>–<strong class="text-slate-900 font-semibold" x-text="clientTo"></strong> of <strong class="text-slate-900 font-semibold" x-text="filteredClients.length"></strong> registered clients
                        <span x-show="filteredClients.length !== clientsList.length" class="text-slate-400 text-[11px]" x-text="'(' + clientsList.length + ' total)'"></span>
                    </span>
                    <span x-show="filteredClients.length === 0">
                        0 clients
                    </span>

                    <div class="hidden sm:flex items-center gap-1.5 pl-3 border-l border-slate-200">
                        <span class="text-slate-400 text-[11px]">Per page:</span>
                        <select x-model.number="clientPerPage"
                                class="bg-white border border-[#E2EAE7] rounded-lg px-2 py-1 text-xs font-semibold text-slate-700 focus:outline-none focus:border-brand-600 cursor-pointer">
                            <option :value="10">10</option>
                            <option :value="20">20</option>
                            <option :value="50">50</option>
                        </select>
                    </div>
                </div>

                <!-- Pagination Buttons -->
                <div x-show="totalClientPages > 1" class="flex items-center gap-1">
                    <button type="button"
                            @click="if (clientPage > 1) { clientPage--; }"
                            :disabled="clientPage <= 1"
                            :class="clientPage <= 1 ? 'opacity-35 cursor-not-allowed text-slate-300' : 'text-slate-600 hover:bg-white hover:text-brand-700 hover:border-slate-300'"
                            class="inline-flex items-center justify-center w-8 h-8 rounded-lg border border-[#E2EAE7] bg-white transition cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    </button>

                    <template x-for="(p, idx) in clientPaginationRange" :key="'cp-' + idx + '-' + p">
                        <div>
                            <template x-if="p === '...'">
                                <span class="inline-flex items-center justify-center w-8 h-8 text-xs text-slate-400 select-none">…</span>
                            </template>
                            <template x-if="p !== '...'">
                                <button type="button"
                                        @click="clientPage = p"
                                        :class="clientPage === p 
                                            ? 'bg-brand-600 text-white font-bold border-brand-600 shadow-2xs' 
                                            : 'bg-white text-slate-600 hover:bg-slate-50 border-[#E2EAE7] hover:border-slate-300'"
                                        class="inline-flex items-center justify-center min-w-[32px] h-8 px-2 rounded-lg border text-xs font-semibold transition cursor-pointer"
                                        x-text="p">
                                </button>
                            </template>
                        </div>
                    </template>

                    <button type="button"
                            @click="if (clientPage < totalClientPages) { clientPage++; }"
                            :disabled="clientPage >= totalClientPages"
                            :class="clientPage >= totalClientPages ? 'opacity-35 cursor-not-allowed text-slate-300' : 'text-slate-600 hover:bg-white hover:text-brand-700 hover:border-slate-300'"
                            class="inline-flex items-center justify-center w-8 h-8 rounded-lg border border-[#E2EAE7] bg-white transition cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </button>
                </div>
            </div>
        </div>

        <div class="md:hidden space-y-3">
            <template x-for="c in paginatedClients" :key="c.id">
                <div @click="openClientProfile(c)" class="bg-white rounded-[24px] p-4 shadow-card border border-[#EDF2F0] space-y-3 cursor-pointer">
                    <div class="flex items-center justify-between gap-3">
                        <div class="flex items-center gap-3 min-w-0">
                            <img :src="c.avatar" :alt="c.name" class="w-10 h-10 rounded-xl object-cover shrink-0">
                            <div class="min-w-0">
                                <p class="text-sm font-bold text-slate-900 truncate" x-text="c.name"></p>
                                <p class="text-xs text-slate-400 truncate" x-text="c.phone"></p>
                            </div>
                        </div>
                        <span class="px-2.5 py-1 rounded-full bg-[#E6F6EF] text-emerald-700 text-[10px] font-bold shrink-0" x-text="c.status"></span>
                    </div>
                    <div class="pt-2.5 border-t border-[#F1F5F4] flex items-center justify-between text-xs">
                        <span class="text-slate-500 truncate" x-text="c.package"></span>
                        <span class="font-bold text-brand-600 shrink-0" x-text="c.totalSpentFormatted"></span>
                    </div>
                </div>
            </template>

            <!-- Mobile Pagination for Clients -->
            <div x-show="totalClientPages > 1" class="flex items-center justify-between p-3.5 bg-white rounded-2xl shadow-card border border-[#EDF2F0]">
                <button type="button"
                        @click="if (clientPage > 1) { clientPage--; }"
                        :disabled="clientPage <= 1"
                        :class="clientPage <= 1 ? 'opacity-35 cursor-not-allowed text-slate-400' : 'text-brand-700 font-bold active:scale-95'"
                        class="px-3 py-1.5 rounded-xl border border-slate-200 text-xs transition cursor-pointer">
                    ← Prev
                </button>
                <span class="text-xs text-slate-500 font-medium">
                    Page <strong class="text-slate-900" x-text="clientPage"></strong> of <strong class="text-slate-900" x-text="totalClientPages"></strong>
                </span>
                <button type="button"
                        @click="if (clientPage < totalClientPages) { clientPage++; }"
                        :disabled="clientPage >= totalClientPages"
                        :class="clientPage >= totalClientPages ? 'opacity-35 cursor-not-allowed text-slate-400' : 'text-brand-700 font-bold active:scale-95'"
                        class="px-3 py-1.5 rounded-xl border border-slate-200 text-xs transition cursor-pointer">
                    Next →
                </button>
            </div>
        </div>
    </div>

    <!-- ================================================================== -->
    <!-- TAB 5: SERVICES (REAL PACKAGES & ADD-ONS FROM MASTER DATA)         -->
    <!-- ================================================================== -->
    <div x-show="$store.nav.tab === 'services'"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 translate-y-1"
         x-transition:enter-end="opacity-100 translate-y-0"
         class="space-y-5"
         x-cloak>

        <div class="flex flex-col md:flex-row md:items-center justify-between gap-3">
            <div class="flex flex-wrap items-center gap-3">
                <div class="inline-flex items-center gap-1 bg-white p-1 rounded-full border border-[#E7EEEC]">
                    <button type="button" @click="serviceSubTab = 'packages'"
                            :class="serviceSubTab === 'packages' ? 'bg-brand-600 text-white shadow-sm' : 'text-slate-600 hover:text-slate-900'"
                            class="px-4 py-2 rounded-full text-xs font-semibold transition">
                        Wedding Packages (<span x-text="packagesList.length"></span>)
                    </button>
                    <button type="button" @click="serviceSubTab = 'addons'"
                            :class="serviceSubTab === 'addons' ? 'bg-brand-600 text-white shadow-sm' : 'text-slate-600 hover:text-slate-900'"
                            class="px-4 py-2 rounded-full text-xs font-semibold transition">
                        Add-ons & Extras (<span x-text="addonsList.length"></span>)
                    </button>
                </div>

                <div class="relative flex items-center bg-white rounded-full border border-[#E7EEEC] w-full sm:w-60">
                    <svg class="w-4 h-4 text-slate-400 absolute left-4 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-5.2-5.2m2.2-4.8a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input type="text" x-model="serviceSearch" placeholder="Search Services..."
                           class="w-full bg-transparent pl-10 pr-4 py-2.5 text-xs text-slate-700 placeholder-slate-400 focus:outline-none">
                </div>
            </div>

            <a href="{{ route('admin.masters.index') }}"
               class="inline-flex items-center justify-center gap-1.5 px-5 py-2.5 rounded-full bg-brand-600 hover:bg-brand-700 text-white text-xs font-semibold transition shadow-pill shrink-0">
                <span>Manage Master Data</span>
            </a>
        </div>

        <div x-show="serviceSubTab === 'packages'" class="grid grid-cols-1 lg:grid-cols-3 gap-5">
            <template x-for="pkg in packagesList.filter(p => !serviceSearch || p.name.toLowerCase().includes(serviceSearch.toLowerCase()))" :key="pkg.id">
                <div class="bg-white rounded-[28px] p-6 shadow-card border border-[#EDF2F0] flex flex-col justify-between space-y-5 hover:border-brand-300 transition">
                    <div class="space-y-4">
                        <div class="flex items-center justify-between gap-2">
                            <span class="px-3 py-1 rounded-full text-[11px] font-bold bg-brand-50 text-brand-600" x-text="'Booked ' + pkg.booked_count + 'x'"></span>
                            <form :action="pkg.toggle_url" method="POST">
                                @csrf
                                @method('PATCH')
                                <button type="submit"
                                        :class="pkg.is_active ? 'bg-[#E6F6EF] text-emerald-700' : 'bg-slate-100 text-slate-500'"
                                        class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold transition cursor-pointer">
                                    <span class="w-1.5 h-1.5 rounded-full" :class="pkg.is_active ? 'bg-emerald-500' : 'bg-slate-400'"></span>
                                    <span x-text="pkg.is_active ? 'Active' : 'Inactive'"></span>
                                </button>
                            </form>
                        </div>

                        <div>
                            <h3 class="text-[17px] font-bold text-slate-900" x-text="pkg.name"></h3>
                            <p class="text-2xl font-extrabold text-brand-600 tracking-tight mt-2" x-text="pkg.price_formatted"></p>
                        </div>

                        <p class="text-xs text-slate-500 leading-relaxed pt-2 border-t border-[#EFF4F2]" x-text="pkg.description"></p>
                    </div>

                    <a href="{{ route('admin.masters.index') }}"
                       class="w-full py-2.5 rounded-full border border-[#E5ECE9] hover:bg-[#F6F8F7] text-xs font-semibold text-slate-700 text-center transition">
                        Edit Package
                    </a>
                </div>
            </template>
        </div>

        <div x-show="serviceSubTab === 'addons'" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4" x-cloak>
            <template x-for="addon in addonsList.filter(a => !serviceSearch || a.name.toLowerCase().includes(serviceSearch.toLowerCase()))" :key="addon.id">
                <div class="bg-white rounded-[24px] p-5 shadow-card border border-[#EDF2F0] hover:border-brand-300 transition flex flex-col justify-between gap-4">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <span class="text-[10px] font-bold uppercase tracking-wider text-brand-600 bg-brand-50 px-2.5 py-0.5 rounded-full" x-text="'Unit: ' + addon.unit"></span>
                            <h3 class="text-sm font-bold text-slate-900 mt-2" x-text="addon.name"></h3>
                        </div>
                        <form :action="addon.toggle_url" method="POST">
                            @csrf
                            @method('PATCH')
                            <button type="submit"
                                    :class="addon.is_active ? 'bg-[#E6F6EF] text-emerald-700' : 'bg-slate-100 text-slate-500'"
                                    class="px-2.5 py-1 rounded-full text-[10px] font-bold transition cursor-pointer">
                                <span x-text="addon.is_active ? 'Active' : 'Hidden'"></span>
                            </button>
                        </form>
                    </div>
                    <div class="pt-3 border-t border-[#EFF4F2] flex items-baseline justify-between">
                        <div>
                            <span class="text-base font-extrabold text-slate-900" x-text="addon.price_formatted"></span>
                            <span class="text-[11px] text-slate-400" x-text="' / ' + addon.unit"></span>
                        </div>
                        <a href="{{ route('admin.masters.index') }}" class="text-[11px] font-semibold text-brand-600 hover:underline">Edit ›</a>
                    </div>
                </div>
            </template>
        </div>
    </div>

    <!-- ================================================================== -->
    <!-- TAB 6: INVOICE SKELETON SHIMMER (FLUTTER STYLE)                    -->
    <!-- ================================================================== -->
    <div x-show="$store.nav.tab === 'invoice' && pageLoading"
         class="space-y-5"
         x-cloak>
        <!-- 3 Financial Summary Cards Shimmer -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="bg-white rounded-[24px] p-5 shadow-card border border-[#EDF2F0] flex items-center gap-4">
                <div class="w-12 h-12 rounded-full shimmer-mask shrink-0"></div>
                <div class="space-y-2">
                    <div class="h-3 w-28 rounded shimmer-mask"></div>
                    <div class="h-6 w-32 rounded shimmer-mask"></div>
                </div>
            </div>
            <div class="bg-white rounded-[24px] p-5 shadow-card border border-[#EDF2F0] flex items-center gap-4">
                <div class="w-12 h-12 rounded-full shimmer-mask shrink-0"></div>
                <div class="space-y-2">
                    <div class="h-3 w-28 rounded shimmer-mask"></div>
                    <div class="h-6 w-32 rounded shimmer-mask"></div>
                </div>
            </div>
            <div class="bg-white rounded-[24px] p-5 shadow-card border border-[#EDF2F0] flex items-center gap-4">
                <div class="w-12 h-12 rounded-full shimmer-mask shrink-0"></div>
                <div class="space-y-2">
                    <div class="h-3 w-28 rounded shimmer-mask"></div>
                    <div class="h-6 w-32 rounded shimmer-mask"></div>
                </div>
            </div>
        </div>

        <!-- Table Skeleton -->
        <div class="bg-white rounded-[28px] shadow-card border border-[#EDF2F0] p-6 space-y-4">
            <div class="h-10 w-72 rounded-full shimmer-mask"></div>
            <div class="flex items-center justify-between pb-3 border-b border-[#EFF4F2]">
                <div class="h-4 w-28 rounded shimmer-mask"></div>
                <div class="h-4 w-36 rounded shimmer-mask"></div>
                <div class="h-4 w-28 rounded shimmer-mask"></div>
                <div class="h-4 w-28 rounded shimmer-mask"></div>
                <div class="h-4 w-24 rounded shimmer-mask"></div>
            </div>
            <div class="space-y-4">
                <template x-for="i in 5" :key="'shimmer-inv-row-' + i">
                    <div class="flex items-center justify-between py-2 border-b border-[#F8FAFA]">
                        <div class="h-4 w-24 rounded shimmer-mask"></div>
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg shimmer-mask shrink-0"></div>
                            <div class="h-4 w-32 rounded shimmer-mask"></div>
                        </div>
                        <div class="h-4 w-28 rounded shimmer-mask"></div>
                        <div class="h-4 w-28 rounded shimmer-mask"></div>
                        <div class="h-6 w-20 rounded-full shimmer-mask"></div>
                    </div>
                </template>
            </div>
            <div class="pt-4 flex items-center justify-between border-t border-[#EFF4F2]">
                <div class="h-4 w-36 rounded shimmer-mask"></div>
                <div class="h-8 w-44 rounded-lg shimmer-mask"></div>
            </div>
        </div>
    </div>

    <!-- ================================================================== -->
    <!-- TAB 6: INVOICE (3 FINANCIAL SUMMARY CARDS + REAL 6-COLUMN TABLE)   -->
    <!-- ================================================================== -->
    <div x-show="$store.nav.tab === 'invoice' && !pageLoading"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 translate-y-1"
         x-transition:enter-end="opacity-100 translate-y-0"
         class="space-y-5"
         x-cloak>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="bg-white rounded-[24px] p-5 shadow-card border border-[#EDF2F0] flex items-center gap-4">
                <div class="w-12 h-12 rounded-full bg-[#E6F6EF] text-emerald-700 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M5 13l4 4L19 7"/></svg>
                </div>
                <div>
                    <span class="text-xs font-medium text-slate-400 block">Total Collected (Paid)</span>
                    <span class="text-xl font-extrabold text-slate-900 tracking-tight mt-0.5 block">{{ $metrics['total_collected_fmt'] }}</span>
                </div>
            </div>

            <div class="bg-white rounded-[24px] p-5 shadow-card border border-[#EDF2F0] flex items-center gap-4">
                <div class="w-12 h-12 rounded-full bg-[#FEF6E0] text-amber-600 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div>
                    <span class="text-xs font-medium text-slate-400 block">Outstanding Balance</span>
                    <span class="text-xl font-extrabold text-amber-600 tracking-tight mt-0.5 block">{{ $metrics['total_outstanding_fmt'] }}</span>
                </div>
            </div>

            <div class="bg-white rounded-[24px] p-5 shadow-card border border-[#EDF2F0] flex items-center gap-4">
                <div class="w-12 h-12 rounded-full bg-brand-50 text-brand-600 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                </div>
                <div>
                    <span class="text-xs font-medium text-slate-400 block">Total Billed Value</span>
                    <span class="text-xl font-extrabold text-slate-900 tracking-tight mt-0.5 block">{{ $metrics['total_billed_fmt'] }}</span>
                </div>
            </div>
        </div>

        <div class="flex flex-wrap items-center gap-3">
            <div class="relative flex items-center bg-white rounded-full border border-[#E7EEEC] w-full sm:w-72">
                <svg class="w-4 h-4 text-slate-400 absolute left-4 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-5.2-5.2m2.2-4.8a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input type="text" x-model="invoiceSearch" placeholder="Search Invoice ID or Client..."
                       class="w-full bg-transparent pl-10 pr-4 py-2.5 text-xs text-slate-700 placeholder-slate-400 focus:outline-none">
            </div>

            <div class="relative" @click.away="invoiceMenuOpen = false">
                <button type="button" @click="invoiceMenuOpen = !invoiceMenuOpen"
                        class="inline-flex items-center justify-between gap-4 bg-white hover:bg-[#FAFDFC] rounded-full border border-[#E7EEEC] px-4 py-2.5 text-xs font-medium text-slate-700 transition min-w-[148px]">
                    <span x-text="invoiceFilter === 'all' ? 'All Status' : invoiceFilter"></span>
                    <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div x-show="invoiceMenuOpen" class="absolute left-0 mt-2 w-44 bg-white rounded-2xl shadow-xl border border-[#EAF0EE] py-1.5 z-30" x-cloak>
                    <template x-for="st in ['all', 'Paid', 'Sent', 'Draft', 'Overdue']" :key="st">
                        <button type="button" @click="invoiceFilter = st; invoiceMenuOpen = false"
                                :class="invoiceFilter === st ? 'bg-brand-50 text-brand-700 font-semibold' : 'text-slate-600 hover:bg-[#F6F8F7]'"
                                class="w-full text-left px-4 py-2 text-xs transition flex items-center justify-between">
                            <span x-text="st === 'all' ? 'All Status' : st"></span>
                            <span x-show="invoiceFilter === st" class="text-brand-600">✓</span>
                        </button>
                    </template>
                </div>
            </div>
        </div>

        <div class="hidden md:block bg-white rounded-[28px] shadow-card border border-[#EDF2F0] overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="text-xs font-semibold text-slate-600 border-b border-[#EFF4F2]">
                            <th class="py-4 px-6">Invoice ID</th>
                            <th class="py-4 px-5">Client</th>
                            <th class="py-4 px-5">Amount & Balance</th>
                            <th class="py-4 px-5">Event Date</th>
                            <th class="py-4 px-5">Status</th>
                            <th class="py-4 px-6 text-right">Quick Actions</th>
                        </tr>
                    </thead>
                    <!-- Shimmer Rows during Pagination -->
                    <tbody x-show="invoiceTableLoading" class="divide-y divide-[#F1F5F4]" x-cloak>
                        <template x-for="i in Math.min(5, invoicePerPage)" :key="'inv-shim-' + i">
                            <tr>
                                <td class="py-4 px-6 whitespace-nowrap">
                                    <div class="h-4 w-20 rounded-md shimmer-mask"></div>
                                </td>
                                <td class="py-4 px-5 whitespace-nowrap">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-lg shimmer-mask shrink-0"></div>
                                        <div class="h-4 w-32 rounded-md shimmer-mask"></div>
                                    </div>
                                </td>
                                <td class="py-4 px-5 whitespace-nowrap">
                                    <div class="h-4 w-28 rounded-md shimmer-mask"></div>
                                </td>
                                <td class="py-4 px-5 whitespace-nowrap">
                                    <div class="h-4 w-24 rounded-md shimmer-mask"></div>
                                </td>
                                <td class="py-4 px-5 whitespace-nowrap">
                                    <div class="h-6 w-16 rounded-full shimmer-mask"></div>
                                </td>
                                <td class="py-4 px-6 whitespace-nowrap text-right">
                                    <div class="h-6 w-24 rounded-full shimmer-mask ml-auto"></div>
                                </td>
                            </tr>
                        </template>
                    </tbody>

                    <!-- Real Data Rows -->
                    <tbody x-show="!invoiceTableLoading" class="divide-y divide-[#F1F5F4]">
                        <template x-for="inv in paginatedInvoices" :key="inv.id">
                            <tr @click="openDetail(inv)" class="hover:bg-[#F8FBFA] transition cursor-pointer group">
                                <td class="py-4 px-6 whitespace-nowrap">
                                    <span class="text-[13px] font-semibold font-mono text-[#0284C7] group-hover:underline" x-text="'#' + inv.code"></span>
                                </td>
                                <td class="py-4 px-5 whitespace-nowrap">
                                    <div class="flex items-center gap-3">
                                        <img :src="inv.avatar" :alt="inv.name" class="w-8 h-8 rounded-lg object-cover shrink-0">
                                        <div>
                                            <p class="text-[13px] font-medium text-slate-800" x-text="inv.name"></p>
                                            <p class="text-[11px] text-slate-400" x-text="inv.service"></p>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-4 px-5 whitespace-nowrap">
                                    <p class="text-[13px] font-bold text-slate-900" x-text="inv.grand_total_formatted"></p>
                                    <p class="text-[11px] mt-0.5" :class="inv.remaining_balance <= 0 ? 'text-emerald-600 font-semibold' : 'text-amber-600 font-medium'"
                                       x-text="inv.remaining_balance <= 0 ? 'Settled (Paid in Full)' : 'Remaining: ' + inv.remaining_formatted"></p>
                                </td>
                                <td class="py-4 px-5 whitespace-nowrap text-[13px] font-medium text-slate-700" x-text="inv.date_short"></td>
                                <td class="py-4 px-5 whitespace-nowrap">
                                    <span :class="{
                                            'bg-[#E6F6EF] text-emerald-700': inv.invoiceStatus === 'Paid',
                                            'bg-[#FEF6E0] text-amber-600': inv.invoiceStatus === 'Sent',
                                            'bg-[#F1F5F9] text-slate-500': inv.invoiceStatus === 'Draft',
                                            'bg-[#FEECEB] text-rose-600': inv.invoiceStatus === 'Overdue' || inv.invoiceStatus === 'Cancelled'
                                          }"
                                          class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-semibold">
                                        <span class="w-1.5 h-1.5 rounded-full"
                                              :class="{
                                                'bg-emerald-500': inv.invoiceStatus === 'Paid',
                                                'bg-amber-400': inv.invoiceStatus === 'Sent',
                                                'bg-slate-400': inv.invoiceStatus === 'Draft',
                                                'bg-rose-500': inv.invoiceStatus === 'Overdue' || inv.invoiceStatus === 'Cancelled'
                                              }"></span>
                                        <span x-text="inv.invoiceStatus"></span>
                                    </span>
                                </td>
                                <td class="py-4 px-6 whitespace-nowrap text-right" @click.stop>
                                    <div class="inline-flex items-center gap-1.5">
                                        <a :href="inv.receipt_url" target="_blank"
                                           class="px-3 py-1.5 rounded-full border border-[#E5ECE9] bg-white hover:bg-[#F6F8F7] text-[11px] font-semibold text-slate-600 transition">
                                            PDF
                                        </a>
                                        <a :href="inv.whatsapp_url" target="_blank"
                                           class="px-3 py-1.5 rounded-full bg-[#E6F6EF] hover:bg-[#D5F0E4] text-[11px] font-semibold text-emerald-700 transition">
                                            WhatsApp
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        </template>

                        <tr x-show="filteredInvoices.length === 0">
                            <td colspan="6" class="py-12 text-center text-xs text-slate-400">
                                No invoices match your search or filter.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Table Footer with Interactive Pagination for Invoices -->
            <div class="px-6 py-4 bg-[#FAFDFC] border-t border-[#EFF4F2] flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs text-slate-500 font-medium">
                <div class="flex items-center gap-3">
                    <span x-show="filteredInvoices.length > 0">
                        Showing <strong class="text-slate-900 font-semibold" x-text="invoiceFrom"></strong>–<strong class="text-slate-900 font-semibold" x-text="invoiceTo"></strong> of <strong class="text-slate-900 font-semibold" x-text="filteredInvoices.length"></strong> invoices
                        <span x-show="filteredInvoices.length !== allBookings.length" class="text-slate-400 text-[11px]" x-text="'(' + allBookings.length + ' total)'"></span>
                    </span>
                    <span x-show="filteredInvoices.length === 0">
                        0 invoices
                    </span>

                    <div class="hidden sm:flex items-center gap-1.5 pl-3 border-l border-slate-200">
                        <span class="text-slate-400 text-[11px]">Per page:</span>
                        <select x-model.number="invoicePerPage"
                                class="bg-white border border-[#E2EAE7] rounded-lg px-2 py-1 text-xs font-semibold text-slate-700 focus:outline-none focus:border-brand-600 cursor-pointer">
                            <option :value="10">10</option>
                            <option :value="20">20</option>
                            <option :value="50">50</option>
                        </select>
                    </div>
                </div>

                <!-- Pagination Buttons for Invoices -->
                <div x-show="totalInvoicePages > 1" class="flex items-center gap-1">
                    <button type="button"
                            @click="if (invoicePage > 1) { invoicePage--; }"
                            :disabled="invoicePage <= 1"
                            :class="invoicePage <= 1 ? 'opacity-35 cursor-not-allowed text-slate-300' : 'text-slate-600 hover:bg-white hover:text-brand-700 hover:border-slate-300'"
                            class="inline-flex items-center justify-center w-8 h-8 rounded-lg border border-[#E2EAE7] bg-white transition cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    </button>

                    <template x-for="(p, idx) in invoicePaginationRange" :key="'ip-' + idx + '-' + p">
                        <div>
                            <template x-if="p === '...'">
                                <span class="inline-flex items-center justify-center w-8 h-8 text-xs text-slate-400 select-none">…</span>
                            </template>
                            <template x-if="p !== '...'">
                                <button type="button"
                                        @click="invoicePage = p"
                                        :class="invoicePage === p 
                                            ? 'bg-brand-600 text-white font-bold border-brand-600 shadow-2xs' 
                                            : 'bg-white text-slate-600 hover:bg-slate-50 border-[#E2EAE7] hover:border-slate-300'"
                                        class="inline-flex items-center justify-center min-w-[32px] h-8 px-2 rounded-lg border text-xs font-semibold transition cursor-pointer"
                                        x-text="p">
                                </button>
                            </template>
                        </div>
                    </template>

                    <button type="button"
                            @click="if (invoicePage < totalInvoicePages) { invoicePage++; }"
                            :disabled="invoicePage >= totalInvoicePages"
                            :class="invoicePage >= totalInvoicePages ? 'opacity-35 cursor-not-allowed text-slate-300' : 'text-slate-600 hover:bg-white hover:text-brand-700 hover:border-slate-300'"
                            class="inline-flex items-center justify-center w-8 h-8 rounded-lg border border-[#E2EAE7] bg-white transition cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </button>
                </div>
            </div>
        </div>

        <div class="md:hidden space-y-3">
            <template x-for="inv in paginatedInvoices" :key="inv.id">
                <div @click="openDetail(inv)" class="bg-white rounded-[24px] p-4 shadow-card border border-[#EDF2F0] space-y-3 cursor-pointer">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold font-mono text-[#0284C7]" x-text="'#' + inv.code"></span>
                        <span class="text-[11px] text-slate-400" x-text="inv.date_short"></span>
                    </div>
                    <div class="flex items-center gap-3">
                        <img :src="inv.avatar" :alt="inv.name" class="w-10 h-10 rounded-xl object-cover">
                        <div class="min-w-0">
                            <p class="text-sm font-bold text-slate-900 truncate" x-text="inv.name"></p>
                            <p class="text-xs text-slate-500 truncate" x-text="inv.service"></p>
                        </div>
                    </div>
                    <div class="pt-2.5 border-t border-[#F1F5F4] flex items-center justify-between">
                        <div>
                            <p class="text-xs font-bold text-slate-900" x-text="inv.grand_total_formatted"></p>
                            <p class="text-[10px]" :class="inv.remaining_balance <= 0 ? 'text-emerald-600 font-semibold' : 'text-amber-600'"
                               x-text="inv.remaining_balance <= 0 ? 'Paid 100%' : 'Due: ' + inv.remaining_formatted"></p>
                        </div>
                        <span class="px-2.5 py-1 rounded-full text-[11px] font-semibold bg-[#F4F7F6] text-slate-700" x-text="inv.invoiceStatus"></span>
                    </div>
                </div>
            </template>

            <!-- Mobile Pagination for Invoices -->
            <div x-show="totalInvoicePages > 1" class="flex items-center justify-between p-3.5 bg-white rounded-2xl shadow-card border border-[#EDF2F0]">
                <button type="button"
                        @click="if (invoicePage > 1) { invoicePage--; }"
                        :disabled="invoicePage <= 1"
                        :class="invoicePage <= 1 ? 'opacity-35 cursor-not-allowed text-slate-400' : 'text-brand-700 font-bold active:scale-95'"
                        class="px-3 py-1.5 rounded-xl border border-slate-200 text-xs transition cursor-pointer">
                    ← Prev
                </button>
                <span class="text-xs text-slate-500 font-medium">
                    Page <strong class="text-slate-900" x-text="invoicePage"></strong> of <strong class="text-slate-900" x-text="totalInvoicePages"></strong>
                </span>
                <button type="button"
                        @click="if (invoicePage < totalInvoicePages) { invoicePage++; }"
                        :disabled="invoicePage >= totalInvoicePages"
                        :class="invoicePage >= totalInvoicePages ? 'opacity-35 cursor-not-allowed text-slate-400' : 'text-brand-700 font-bold active:scale-95'"
                        class="px-3 py-1.5 rounded-xl border border-slate-200 text-xs transition cursor-pointer">
                    Next →
                </button>
            </div>
        </div>
    </div>

    <!-- ================================================================== -->
    <!-- TAB 7: SETTINGS & SYSTEM CONFIGURATION                             -->
    <!-- ================================================================== -->
    <div x-show="$store.nav.tab === 'settings'"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 translate-y-1"
         x-transition:enter-end="opacity-100 translate-y-0"
         class="space-y-6"
         x-cloak>

        <!-- Header Panel -->
        <div class="bg-white rounded-[28px] p-6 sm:p-7 shadow-card border border-[#EDF2F0] flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-2xl bg-brand-50 border border-brand-100 flex items-center justify-center text-brand-600 shrink-0 shadow-xs">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.9" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.9" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
                <div>
                    <div class="flex items-center gap-2.5 flex-wrap">
                        <h1 class="text-xl sm:text-2xl font-bold tracking-tight text-slate-900">Pengaturan & Kebijakan Acara</h1>
                    </div>
                </div>
            </div>

            <a href="{{ route('booking.create') }}" target="_blank"
               class="inline-flex items-center gap-2 px-5 py-2.5 rounded-full bg-[#F6F8F7] hover:bg-[#EAF0EE] text-slate-700 text-xs font-semibold transition border border-[#E3ECE9] shrink-0">
                <span>Buka Formulir Klien</span>
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
            </a>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

            <!-- FORM 1: ATURAN & KEBIJAKAN OPERASIONAL ACARA (Col Span 7) -->
            <div class="lg:col-span-7 bg-white rounded-[28px] p-6 sm:p-7 shadow-card border border-[#EDF2F0] space-y-6">
                <div class="flex items-center justify-between pb-4 border-b border-[#F0F4F2]">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center font-bold text-sm border border-amber-100">
                            ⚖
                        </div>
                        <div>
                            <h2 class="text-base font-bold text-slate-900">Aturan & Kebijakan Acara</h2>
                            <p class="text-xs text-slate-400">Parameter ini langsung mempengaruhi perhitungan harga & kalender klien</p>
                        </div>
                    </div>
                </div>

                <form action="{{ route('admin.settings.update_rules') }}" method="POST" class="space-y-4">
                    @csrf

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <!-- Durasi Standar Paket -->
                        <div class="space-y-1.5">
                            <label class="block text-xs font-bold text-slate-700">
                                Durasi Standar Paket (Hari)
                            </label>
                            <div class="relative">
                                <input type="number" name="standard_package_max_days" min="1" max="14" required
                                       value="{{ $settings['standard_package_max_days'] ?? 2 }}"
                                       class="w-full pl-4 pr-14 py-2.5 rounded-xl border border-slate-200 text-sm font-semibold text-slate-900 focus:outline-none focus:border-brand-600 focus:ring-2 focus:ring-brand-600/15 transition [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none">
                                <span class="absolute right-3.5 top-1/2 -translate-y-1/2 text-xs text-slate-400 font-medium pointer-events-none">Hari</span>
                            </div>
                            <p class="text-[11px] text-slate-400">Durasi yang sudah termasuk di harga dasar paket.</p>
                        </div>

                        <!-- Charge Hari Tambahan -->
                        <div class="space-y-1.5">
                            <label class="block text-xs font-bold text-slate-700">
                                Charge Hari Tambahan (%)
                            </label>
                            <div class="relative">
                                <input type="number" step="0.5" name="extra_day_charge_rate" min="0" max="100" required
                                       value="{{ $settings['extra_day_charge_rate'] ?? 15 }}"
                                       class="w-full pl-4 pr-16 py-2.5 rounded-xl border border-slate-200 text-sm font-semibold text-slate-900 focus:outline-none focus:border-brand-600 focus:ring-2 focus:ring-brand-600/15 transition [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none">
                                <span class="absolute right-3.5 top-1/2 -translate-y-1/2 text-xs text-slate-400 font-medium pointer-events-none">% / hari</span>
                            </div>
                            <p class="text-[11px] text-slate-400">Dihitung dari harga dasar paket per hari ekstra.</p>
                        </div>

                        <!-- Minimal DP -->
                        <div x-data="{
                            rawAmount: {{ (int) ($settings['min_dp_amount'] ?? 1000000) }},
                            displayAmount: '{{ number_format((int) ($settings['min_dp_amount'] ?? 1000000), 0, ',', '.') }}',
                            formatInput(e) {
                                const clean = e.target.value.replace(/\D/g, '');
                                if (!clean) {
                                    this.rawAmount = 0;
                                    this.displayAmount = '';
                                    e.target.value = '';
                                    return;
                                }
                                const num = parseInt(clean, 10);
                                this.rawAmount = num;
                                this.displayAmount = new Intl.NumberFormat('id-ID').format(num);
                                e.target.value = this.displayAmount;
                            }
                        }" class="space-y-1.5">
                            <label class="block text-xs font-bold text-slate-700">
                                Minimal Uang Muka / DP
                            </label>
                            <div class="relative">
                                <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-xs text-slate-400 font-bold pointer-events-none select-none">Rp</span>
                                <input type="text"
                                       inputmode="numeric"
                                       :value="displayAmount"
                                       value="{{ number_format((int) ($settings['min_dp_amount'] ?? 1000000), 0, ',', '.') }}"
                                       @input="formatInput($event)"
                                       @blur="if (rawAmount) { displayAmount = new Intl.NumberFormat('id-ID').format(rawAmount); $el.value = displayAmount; }"
                                       placeholder="1.000.000"
                                       class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-slate-200 text-sm font-semibold font-mono text-slate-900 focus:outline-none focus:border-brand-600 focus:ring-2 focus:ring-brand-600/15 transition">
                                <input type="hidden" name="min_dp_amount" :value="rawAmount" value="{{ (int) ($settings['min_dp_amount'] ?? 1000000) }}">
                            </div>
                            <p class="text-[11px] text-slate-400">Nominal batas bawah transfer DP untuk mengunci jadwal.</p>
                        </div>

                        <!-- Kapasitas Acara per Hari (Anti-Overbooking) -->
                        <div class="space-y-1.5">
                            <label class="block text-xs font-bold text-slate-700">
                                Kapasitas Acara per Hari
                            </label>
                            <div class="relative">
                                <input type="number" name="max_events_per_day" min="1" max="10" required
                                       value="{{ $settings['max_events_per_day'] ?? 1 }}"
                                       class="w-full pl-4 pr-16 py-2.5 rounded-xl border border-slate-200 text-sm font-semibold text-slate-900 focus:outline-none focus:border-brand-600 focus:ring-2 focus:ring-brand-600/15 transition [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none">
                                <span class="absolute right-3.5 top-1/2 -translate-y-1/2 text-xs text-slate-400 font-medium pointer-events-none">Acara</span>
                            </div>
                            <p class="text-[11px] text-slate-400">Batas maksimal kuota acara pada tanggal yang sama.</p>
                        </div>
                    </div>

                    <!-- WhatsApp CS -->
                    <div class="space-y-1.5 pt-1">
                        <label class="block text-xs font-bold text-slate-700">
                            Nomor WhatsApp Customer Service (Admin)
                        </label>
                        <input type="text" name="admin_whatsapp" required
                               value="{{ $settings['admin_whatsapp'] ?? '6282255969313' }}"
                               placeholder="6282255969313"
                               class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm font-mono font-semibold text-slate-900 focus:outline-none focus:border-brand-600 focus:ring-2 focus:ring-brand-600/15 transition">
                        <p class="text-[11px] text-slate-400">Format internasional tanpa spasi/tanda hubung (Contoh: 6282255969313).</p>
                    </div>

                    <div class="pt-3 border-t border-[#F0F4F2] flex items-center justify-end">
                        <button type="submit"
                                class="inline-flex items-center gap-2 px-6 py-2.5 rounded-full bg-brand-600 hover:bg-brand-700 text-white text-xs font-bold transition shadow-pill cursor-pointer">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M5 13l4 4L19 7"/></svg>
                            <span>Simpan Perubahan Aturan</span>
                        </button>
                    </div>
                </form>
            </div>

            <!-- FORM 2: REKENING RESMI TRANSFER BANK (Col Span 5) -->
            <div class="lg:col-span-5 bg-white rounded-[28px] p-6 sm:p-7 shadow-card border border-[#EDF2F0] space-y-6">
                <div class="flex items-center justify-between pb-4 border-b border-[#F0F4F2]">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold text-sm border border-indigo-100">
                            💳
                        </div>
                        <div>
                            <h2 class="text-base font-bold text-slate-900">Rekening Resmi Pembayaran</h2>
                            <p class="text-xs text-slate-400">Tampil otomatis di formulir klien, invoice, & nota</p>
                        </div>
                    </div>
                </div>

                <form action="{{ route('admin.settings.update_banks') }}" method="POST" class="space-y-4">
                    @csrf

                    <!-- Bank BCA Section -->
                    <div class="p-4 rounded-2xl bg-[#F8FAFA] border border-[#EFF4F2] space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-[#003B73] uppercase tracking-wider">Bank BCA (Utama)</span>
                            <span class="w-2 h-2 rounded-full bg-blue-600"></span>
                        </div>
                        <div class="space-y-2">
                            <div>
                                <label class="block text-[11px] font-semibold text-slate-600 mb-1">Nomor Rekening</label>
                                <input type="text" name="bca_account_number" required
                                       value="{{ $settings['bca_account_number'] ?? '3620648107' }}"
                                       class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs font-mono font-bold text-slate-900 focus:outline-none focus:border-brand-600 focus:ring-2 focus:ring-brand-600/15 bg-white transition">
                            </div>
                            <div>
                                <label class="block text-[11px] font-semibold text-slate-600 mb-1">Atas Nama (Pemilik)</label>
                                <input type="text" name="bca_account_name" required
                                       value="{{ $settings['bca_account_name'] ?? 'EZZU RAKHMAT FIRDAUS' }}"
                                       class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs font-semibold text-slate-900 uppercase focus:outline-none focus:border-brand-600 focus:ring-2 focus:ring-brand-600/15 bg-white transition">
                            </div>
                        </div>
                    </div>

                    <!-- Bank BRI Section -->
                    <div class="p-4 rounded-2xl bg-[#F8FAFA] border border-[#EFF4F2] space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-[#004D99] uppercase tracking-wider">Bank BRI (Alternatif)</span>
                            <span class="w-2 h-2 rounded-full bg-sky-600"></span>
                        </div>
                        <div class="space-y-2">
                            <div>
                                <label class="block text-[11px] font-semibold text-slate-600 mb-1">Nomor Rekening</label>
                                <input type="text" name="bri_account_number" required
                                       value="{{ $settings['bri_account_number'] ?? '344301026581532' }}"
                                       class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs font-mono font-bold text-slate-900 focus:outline-none focus:border-brand-600 focus:ring-2 focus:ring-brand-600/15 bg-white transition">
                            </div>
                            <div>
                                <label class="block text-[11px] font-semibold text-slate-600 mb-1">Atas Nama (Pemilik)</label>
                                <input type="text" name="bri_account_name" required
                                       value="{{ $settings['bri_account_name'] ?? 'EZZU RAKHMAT FIRDAUS' }}"
                                       class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs font-semibold text-slate-900 uppercase focus:outline-none focus:border-brand-600 focus:ring-2 focus:ring-brand-600/15 bg-white transition">
                            </div>
                        </div>
                    </div>

                    <div class="pt-2 border-t border-[#F0F4F2] flex items-center justify-end">
                        <button type="submit"
                                class="inline-flex items-center gap-2 px-6 py-2.5 rounded-full bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold transition shadow-sm cursor-pointer">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M5 13l4 4L19 7"/></svg>
                            <span>Simpan Rekening Bank</span>
                        </button>
                    </div>
                </form>
            </div>

            <!-- FORM 3: INTEGRASI GOOGLE CLOUD (SHEETS & CALENDAR) (Col Span 12) -->
            <div class="lg:col-span-12 bg-white rounded-[28px] p-6 sm:p-7 shadow-card border border-[#EDF2F0] space-y-6">
                <div class="flex items-center justify-between pb-4 border-b border-[#F0F4F2]">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-2xl bg-sky-50 text-sky-600 flex items-center justify-center font-bold text-sm border border-sky-100">
                            G
                        </div>
                        <div>
                            <h2 class="text-base font-bold text-slate-900">Integrasi Google Cloud (Sheets & Calendar)</h2>
                            <p class="text-xs text-slate-400">Atur ID Google Sheets dan Google Calendar untuk sinkronisasi otomatis</p>
                        </div>
                    </div>
                    <span class="px-3 py-1 rounded-full text-xs font-bold text-emerald-700 bg-emerald-50 border border-emerald-200">Terhubung</span>
                </div>

                <form action="{{ route('admin.settings.update_google') }}" method="POST" class="space-y-5">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <!-- Google Spreadsheet ID -->
                        <div class="space-y-1.5">
                            <div class="flex items-center justify-between">
                                <label class="block text-xs font-bold text-slate-700">
                                    Google Spreadsheet ID (Database Rekap)
                                </label>
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold text-emerald-700 bg-emerald-50">Auto Sync</span>
                            </div>
                            <input type="text" name="google_sheet_id"
                                   value="{{ $settings['google_sheet_id'] ?? '' }}"
                                   placeholder="Contoh: 1RzvZTREK-1dNA99yYV8K59..."
                                   class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs sm:text-sm font-mono font-semibold text-slate-900 focus:outline-none focus:border-brand-600 focus:ring-2 focus:ring-brand-600/15 transition">
                            <p class="text-[11px] text-slate-400">ID spreadsheet dari URL Google Sheets tempat data pesanan direkap.</p>
                        </div>

                        <!-- Google Calendar ID -->
                        <div class="space-y-1.5">
                            <div class="flex items-center justify-between">
                                <label class="block text-xs font-bold text-slate-700">
                                    Google Calendar ID (Jadwal Acara)
                                </label>
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold text-sky-700 bg-sky-50">Auto Event</span>
                            </div>
                            <input type="text" name="google_calendar_id"
                                   value="{{ $settings['google_calendar_id'] ?? '' }}"
                                   placeholder="Contoh: primary atau email@gmail.com"
                                   class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs sm:text-sm font-mono font-semibold text-slate-900 focus:outline-none focus:border-brand-600 focus:ring-2 focus:ring-brand-600/15 transition">
                            <p class="text-[11px] text-slate-400">ID kalender Google (biasanya alamat email akun Google atau primary).</p>
                        </div>
                    </div>

                    <div class="pt-3 border-t border-[#F0F4F2] flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                        <p class="text-xs text-slate-500">
                            Perubahan ID Google akan langsung diterapkan untuk pesanan baru maupun sinkronisasi ulang.
                        </p>
                        <button type="submit"
                                class="inline-flex items-center justify-center gap-2 px-6 py-2.5 rounded-full bg-brand-600 hover:bg-brand-700 text-white text-xs font-bold transition shadow-pill cursor-pointer shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M5 13l4 4L19 7"/></svg>
                            <span>Simpan ID Google</span>
                        </button>
                    </div>
                </form>

                <!-- Box Sinkronisasi Ulang Semua Data -->
                <div class="p-5 rounded-2xl bg-gradient-to-br from-brand-50/50 to-brand-100/30 border border-brand-200/70 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div class="space-y-0.5">
                        <h3 class="text-sm font-bold text-slate-900">Sinkronisasi Ulang Database ke Google</h3>
                        <p class="text-xs text-slate-600 leading-relaxed">
                            Kirim atau segarkan seluruh data reservasi di database lokal ke Google Sheets dan Google Calendar saat ini.
                        </p>
                    </div>

                    <form action="{{ route('admin.settings.sync_all') }}" method="POST" class="shrink-0">
                        @csrf
                        <button type="submit"
                                class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-full bg-white hover:bg-slate-50 border border-brand-200 text-brand-700 hover:text-brand-800 text-xs font-bold transition shadow-2xs cursor-pointer">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                            <span>Mulai Sinkronisasi Sekarang</span>
                        </button>
                    </form>
                </div>
            </div>

        </div>

    </div>

    <!-- ================================================================== -->
    <!-- FULL-PAGE VIEW: CLIENT PROFILE                                     -->
    <!-- ================================================================== -->
    <div x-show="$store.nav.tab === 'client_profile' && activeClient !== null"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 translate-y-1"
         x-transition:enter-end="opacity-100 translate-y-0"
         class="space-y-5"
         x-cloak>

        <button type="button" @click="$store.nav.setTab('clients')" class="inline-flex items-center gap-2 group cursor-pointer">
            <span class="w-8 h-8 rounded-full bg-white border border-[#E5ECE9] text-slate-600 group-hover:text-brand-600 flex items-center justify-center shadow-2xs transition">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.4" d="M15 19l-7-7 7-7"/></svg>
            </span>
            <span class="px-4 py-1.5 rounded-full bg-[#ECEFEF] group-hover:bg-[#E2E8E7] text-xs font-semibold text-slate-700 transition">
                Back to Clients
            </span>
        </button>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 items-start">
            <div class="lg:col-span-4 bg-white rounded-[28px] p-6 shadow-card border border-[#EDF2F0] space-y-5">
                <div class="flex items-center gap-4">
                    <img :src="activeClient?.avatar" :alt="activeClient?.name" class="w-16 h-16 rounded-2xl object-cover shrink-0">
                    <div>
                        <h2 class="text-lg font-bold text-slate-900" x-text="activeClient?.name"></h2>
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-[#E6F6EF] text-emerald-700 mt-1">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                            <span x-text="activeClient?.status"></span>
                        </span>
                    </div>
                </div>

                <div class="space-y-2.5 pt-4 border-t border-[#EFF4F2] text-xs">
                    <div class="flex justify-between"><span class="text-slate-400">WhatsApp</span><span class="font-semibold text-slate-800" x-text="activeClient?.phone"></span></div>
                    <div class="flex justify-between"><span class="text-slate-400">Email</span><span class="font-semibold text-slate-800" x-text="activeClient?.email"></span></div>
                    <div class="flex justify-between"><span class="text-slate-400">Total Spent</span><span class="font-bold text-brand-600" x-text="activeClient?.totalSpentFormatted"></span></div>
                </div>

                <a :href="activeClient?.whatsapp_url || '#'" target="_blank"
                   class="block w-full py-2.5 rounded-full bg-brand-600 hover:bg-brand-700 text-white text-xs font-semibold text-center transition shadow-pill">
                    Chat via WhatsApp
                </a>
            </div>

            <div class="lg:col-span-8 bg-white rounded-[28px] p-6 shadow-card border border-[#EDF2F0] space-y-4">
                <h3 class="text-lg font-bold text-slate-900">Booking & Invoice History</h3>
                <div class="space-y-3">
                    <template x-for="b in allBookings.filter(i => i.name === activeClient?.name || i.phone === activeClient?.phone)" :key="b.id">
                        <div @click="openDetail(b)" class="p-4 rounded-2xl bg-[#F8FAFA] hover:bg-[#F1F6F5] border border-[#EFF4F2] flex flex-col sm:flex-row sm:items-center justify-between gap-3 transition cursor-pointer">
                            <div>
                                <div class="flex items-center gap-2">
                                    <span class="text-xs font-bold font-mono text-[#0284C7]" x-text="'#' + b.code"></span>
                                    <span class="text-sm font-bold text-slate-900" x-text="b.service"></span>
                                </div>
                                <p class="text-xs text-slate-400 mt-0.5" x-text="b.date"></p>
                            </div>
                            <div class="flex items-center gap-3">
                                <span class="text-sm font-bold text-slate-900" x-text="b.grand_total_formatted"></span>
                                <span class="px-2.5 py-1 rounded-full text-[11px] font-semibold bg-[#E6F6EF] text-emerald-700" x-text="b.bookingStatus"></span>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </div>

    <!-- ================================================================== -->
    <!-- FULL-PAGE VIEW: BOOKING INFORMATION (REAL DATA + PAYMENT PROOF)    -->
    <!-- ================================================================== -->
    <div x-show="$store.nav.tab === 'booking_detail' && activeDetail !== null"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 translate-y-1"
         x-transition:enter-end="opacity-100 translate-y-0"
         class="space-y-5"
         x-cloak>

        <!-- SKELETON SHIMMER SCREEN (FLUTTER STYLE) -->
        <div x-show="detailLoading" class="space-y-5" x-cloak>
            <div class="flex items-center justify-between">
                <div class="h-8 w-24 rounded-full shimmer-mask"></div>
                <div class="h-8 w-28 rounded-full shimmer-mask"></div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 items-start">
                <div class="lg:col-span-8 space-y-5">
                    <div class="bg-white rounded-[28px] p-5 sm:p-7 shadow-card border border-[#EDF2F0] space-y-6">
                        <div class="h-5 w-44 rounded-md shimmer-mask"></div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                            <div class="flex items-center gap-3.5">
                                <div class="w-11 h-11 rounded-full shimmer-mask shrink-0"></div>
                                <div class="space-y-2">
                                    <div class="h-3 w-16 rounded shimmer-mask"></div>
                                    <div class="h-4 w-36 rounded shimmer-mask"></div>
                                </div>
                            </div>
                            <div class="flex items-center gap-3.5">
                                <div class="w-11 h-11 rounded-full shimmer-mask shrink-0"></div>
                                <div class="space-y-2">
                                    <div class="h-3 w-16 rounded shimmer-mask"></div>
                                    <div class="h-4 w-36 rounded shimmer-mask"></div>
                                </div>
                            </div>
                        </div>
                        <div class="pt-5 border-t border-[#EFF4F2] flex items-center gap-4">
                            <div class="h-4 w-36 rounded-full shimmer-mask"></div>
                            <div class="h-4 w-40 rounded-full shimmer-mask"></div>
                        </div>
                    </div>

                    <div class="bg-white rounded-[28px] p-5 sm:p-7 shadow-card border border-[#EDF2F0] space-y-4">
                        <div class="h-5 w-40 rounded-md shimmer-mask"></div>
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3.5">
                                <div class="w-12 h-12 rounded-full shimmer-mask shrink-0"></div>
                                <div class="space-y-2">
                                    <div class="h-4 w-36 rounded shimmer-mask"></div>
                                    <div class="h-3 w-28 rounded shimmer-mask"></div>
                                </div>
                            </div>
                            <div class="h-8 w-28 rounded-full shimmer-mask"></div>
                        </div>
                    </div>

                    <div class="bg-white rounded-[28px] p-5 sm:p-7 shadow-card border border-[#EDF2F0] space-y-4">
                        <div class="h-5 w-32 rounded-md shimmer-mask"></div>
                        <div class="space-y-3">
                            <div class="h-12 w-full rounded-2xl shimmer-mask"></div>
                            <div class="h-10 w-full rounded-2xl shimmer-mask"></div>
                        </div>
                    </div>
                </div>

                <div class="lg:col-span-4 space-y-5">
                    <div class="bg-white rounded-[28px] p-5 sm:p-7 shadow-card border border-[#EDF2F0] space-y-4">
                        <div class="h-5 w-24 rounded-md shimmer-mask"></div>
                        <div class="space-y-3">
                            <div class="h-7 w-full rounded-xl shimmer-mask"></div>
                            <div class="h-7 w-full rounded-xl shimmer-mask"></div>
                        </div>
                    </div>

                    <div class="bg-white rounded-[28px] p-5 sm:p-7 shadow-card border border-[#EDF2F0] space-y-4">
                        <div class="h-5 w-28 rounded-md shimmer-mask"></div>
                        <div class="h-48 w-full rounded-2xl shimmer-mask"></div>
                    </div>

                    <div class="bg-white rounded-[28px] p-5 sm:p-7 shadow-card border border-[#EDF2F0] space-y-3">
                        <div class="h-5 w-20 rounded-md shimmer-mask"></div>
                        <div class="h-11 w-full rounded-full shimmer-mask"></div>
                        <div class="h-11 w-full rounded-full shimmer-mask"></div>
                        <div class="h-11 w-full rounded-full shimmer-mask"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- REAL DATA VIEW -->
        <div x-show="!detailLoading" class="space-y-5">
        <div class="flex items-center justify-between">
            <button type="button" @click="closeDetail()" class="inline-flex items-center gap-2 group cursor-pointer">
                <span class="w-8 h-8 rounded-full bg-white border border-[#E5ECE9] text-slate-600 group-hover:text-brand-600 flex items-center justify-center shadow-2xs transition">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.4" d="M15 19l-7-7 7-7"/></svg>
                </span>
                <span class="px-4 py-1.5 rounded-full bg-[#ECEFEF] group-hover:bg-[#E2E8E7] text-xs font-semibold text-slate-700 transition">
                    Back
                </span>
            </button>

            <span class="text-xs font-mono font-bold text-[#0284C7] bg-white px-3.5 py-1.5 rounded-full border border-[#E5ECE9]"
                  x-text="'#' + (activeDetail?.code || '')"></span>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 items-start">

            <!-- LEFT COLUMN (8 COLS) -->
            <div class="lg:col-span-8 space-y-5">

                <!-- Booking Information -->
                <div class="bg-white rounded-[28px] p-5 sm:p-7 shadow-card border border-[#EDF2F0] space-y-6">
                    <h2 class="text-lg font-bold text-slate-900 tracking-tight">Booking Information</h2>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div class="flex items-center gap-3.5">
                            <div class="w-11 h-11 rounded-full bg-[#F4F8F7] border border-[#E8F0EE] text-brand-600 flex items-center justify-center shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            </div>
                            <div>
                                <span class="text-xs font-medium text-slate-400 block">Event Date</span>
                                <span class="text-sm font-bold text-slate-900 mt-0.5 block" x-text="activeDetail?.date"></span>
                            </div>
                        </div>

                        <div class="flex items-center gap-3.5">
                            <div class="w-11 h-11 rounded-full bg-[#F4F8F7] border border-[#E8F0EE] text-brand-600 flex items-center justify-center shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            <div>
                                <span class="text-xs font-medium text-slate-400 block">Booked At</span>
                                <span class="text-sm font-bold text-slate-900 mt-0.5 block" x-text="activeDetail?.created_at_label"></span>
                            </div>
                        </div>
                    </div>

                    <div class="pt-5 border-t border-[#EFF4F2] flex flex-wrap items-center gap-4 text-xs">
                        <span class="inline-flex items-center gap-1.5 font-semibold" :class="activeDetail?.synced_to_sheets ? 'text-emerald-700' : 'text-amber-600'">
                            <span class="w-2 h-2 rounded-full" :class="activeDetail?.synced_to_sheets ? 'bg-emerald-500' : 'bg-amber-400'"></span>
                            <span x-text="activeDetail?.synced_to_sheets ? 'Google Sheets Synced' : 'Google Sheets Queued'"></span>
                        </span>
                        <span class="inline-flex items-center gap-1.5 font-semibold" :class="activeDetail?.google_calendar_synced ? 'text-emerald-700' : 'text-slate-400'">
                            <span class="w-2 h-2 rounded-full" :class="activeDetail?.google_calendar_synced ? 'bg-emerald-500' : 'bg-slate-300'"></span>
                            <span x-text="activeDetail?.google_calendar_synced ? 'Google Calendar Registered' : 'Not on Calendar'"></span>
                        </span>
                    </div>
                </div>

                <!-- Client Information -->
                <div class="bg-white rounded-[28px] p-5 sm:p-7 shadow-card border border-[#EDF2F0] space-y-4">
                    <h2 class="text-lg font-bold text-slate-900 tracking-tight">Client Information</h2>

                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <div class="flex items-center gap-4">
                            <img :src="activeDetail?.avatar" :alt="activeDetail?.name" class="w-14 h-14 rounded-2xl object-cover shrink-0">
                            <div class="space-y-1">
                                <h3 class="text-sm font-bold text-slate-900" x-text="activeDetail?.name"></h3>
                                <p class="text-xs text-slate-400" x-text="activeDetail?.email"></p>
                                <p class="text-xs font-mono text-slate-600" x-text="activeDetail?.phone"></p>
                            </div>
                        </div>

                        <button type="button"
                                @click="openClientProfile(activeDetail)"
                                class="px-4 py-2 rounded-full bg-[#F4F7F6] hover:bg-[#EAF0EE] text-xs font-semibold text-slate-700 transition self-start sm:self-center">
                            View Profile
                        </button>
                    </div>
                </div>

                <!-- Service Details -->
                <div class="bg-white rounded-[28px] p-5 sm:p-7 shadow-card border border-[#EDF2F0] space-y-4">
                    <h2 class="text-lg font-bold text-slate-900 tracking-tight">Service Details</h2>

                    <div class="p-4 rounded-2xl bg-[#F8FAFA] border border-[#EFF4F2] space-y-3 text-xs">
                        <div class="flex items-center justify-between gap-3">
                            <div>
                                <p class="text-sm font-bold text-slate-900" x-text="activeDetail?.service"></p>
                                <p class="text-[11px] text-slate-400">Core Wedding Organizer Package</p>
                            </div>
                            <span class="text-sm font-bold text-slate-900 shrink-0" x-text="activeDetail?.package_price_formatted"></span>
                        </div>

                        <template x-for="(ad, aIdx) in (activeDetail?.addons || [])" :key="aIdx">
                            <div class="flex items-center justify-between gap-3 pt-2 border-t border-[#EBF1EF]">
                                <div>
                                    <p class="font-semibold text-slate-800" x-text="ad.name + ' (x' + ad.quantity + ')'"></p>
                                    <p class="text-[11px] text-slate-400">Add-on Service</p>
                                </div>
                                <span class="font-bold text-slate-800 shrink-0" x-text="ad.subtotal"></span>
                            </div>
                        </template>

                        <template x-if="(activeDetail?.additional_day_charge || 0) > 0">
                            <div class="flex items-center justify-between gap-3 pt-2 border-t border-[#EBF1EF] text-amber-800">
                                <div>
                                    <p class="font-semibold" x-text="'Extra Duration (' + (activeDetail.duration_days - 2) + ' extra days @ 15%)'"></p>
                                    <p class="text-[11px] text-amber-600">Additional Day Surcharge</p>
                                </div>
                                <span class="font-bold shrink-0" x-text="'+ ' + activeDetail.additional_day_charge_formatted"></span>
                            </div>
                        </template>

                        <div class="pt-3 border-t border-[#DFE8E5] space-y-1.5">
                            <div class="flex justify-between">
                                <span class="text-slate-500">Grand Total</span>
                                <span class="font-extrabold text-slate-900 text-sm" x-text="activeDetail?.grand_total_formatted"></span>
                            </div>
                            <div class="flex justify-between text-emerald-700">
                                <span x-text="'Amount Paid (' + (activeDetail?.payment_type || 'DP') + ')'"></span>
                                <span class="font-bold" x-text="activeDetail?.amount_paid_formatted"></span>
                            </div>
                            <div class="flex justify-between" :class="(activeDetail?.remaining_balance || 0) > 0 ? 'text-rose-600 font-bold' : 'text-emerald-700 font-bold'">
                                <span>Remaining Balance</span>
                                <span x-text="(activeDetail?.remaining_balance || 0) > 0 ? activeDetail?.remaining_formatted : 'PAID (100%)'"></span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Payment Proof -->
                <div class="bg-white rounded-[28px] p-5 sm:p-7 shadow-card border border-[#EDF2F0] space-y-4">
                    <div class="flex items-center justify-between">
                        <h2 class="text-lg font-bold text-slate-900 tracking-tight">Payment Proof</h2>
                        <template x-if="activeDetail?.payment_proof_url">
                            <a :href="activeDetail.payment_proof_url" target="_blank" class="text-xs font-semibold text-brand-600 hover:underline">
                                Open Full Resolution
                            </a>
                        </template>
                    </div>

                    <template x-if="activeDetail?.payment_proof_url && !activeDetail?.is_proof_pdf">
                        <a :href="activeDetail.payment_proof_url" target="_blank" class="block rounded-2xl overflow-hidden border border-[#EFF4F2] bg-[#F8FAFA]">
                            <img :src="activeDetail.payment_proof_url" alt="Payment Proof" class="w-full max-h-96 object-contain mx-auto">
                        </a>
                    </template>
                    <template x-if="activeDetail?.payment_proof_url && activeDetail?.is_proof_pdf">
                        <div class="p-6 rounded-2xl bg-[#F8FAFA] border border-[#EFF4F2] text-center space-y-3">
                            <p class="text-xs font-bold text-slate-700">Bank Transfer Receipt (PDF)</p>
                            <a :href="activeDetail.payment_proof_url" target="_blank" class="inline-block px-5 py-2 rounded-full bg-brand-600 text-white text-xs font-semibold">
                                Open PDF Document ↗
                            </a>
                        </div>
                    </template>
                    <template x-if="!activeDetail?.payment_proof_url">
                        <p class="text-xs text-slate-400 py-4 text-center">No payment proof uploaded.</p>
                    </template>
                </div>

            </div>

            <!-- RIGHT COLUMN (4 COLS: TIMELINE + STATUS + REAL ACTIONS) -->
            <div class="lg:col-span-4 space-y-5">

                <!-- Timeline -->
                <div class="bg-white rounded-[28px] p-5 sm:p-6 shadow-card border border-[#EDF2F0] space-y-4">
                    <h2 class="text-lg font-bold text-slate-900 tracking-tight">Timeline</h2>
                    <div class="space-y-4 relative pl-4 before:content-[''] before:absolute before:left-[5px] before:top-2 before:bottom-2 before:w-px before:bg-[#E8EFED]">
                        <div class="relative">
                            <span class="w-2.5 h-2.5 rounded-full bg-purple-500 ring-4 ring-white absolute -left-4 top-1"></span>
                            <p class="text-xs font-bold text-slate-800">Booking Created</p>
                            <p class="text-[11px] text-slate-400 mt-0.5" x-text="activeDetail?.created_at_label"></p>
                        </div>
                        <div class="relative">
                            <span class="w-2.5 h-2.5 rounded-full ring-4 ring-white absolute -left-4 top-1"
                                  :class="activeDetail?.raw_status === 'confirmed' || activeDetail?.raw_status === 'completed' ? 'bg-emerald-500' : 'bg-slate-300'"></span>
                            <p class="text-xs font-bold text-slate-800" x-text="'Status: ' + (activeDetail?.bookingStatus || '')"></p>
                            <p class="text-[11px] text-slate-400 mt-0.5" x-text="activeDetail?.updated_at_label"></p>
                        </div>
                        <div class="relative">
                            <span class="w-2.5 h-2.5 rounded-full ring-4 ring-white absolute -left-4 top-1"
                                  :class="activeDetail?.google_calendar_synced ? 'bg-brand-600' : 'bg-slate-300'"></span>
                            <p class="text-xs font-bold text-slate-800" x-text="activeDetail?.google_calendar_synced ? 'Synced to Google Calendar' : 'Pending Calendar Sync'"></p>
                            <p class="text-[11px] text-slate-400 mt-0.5" x-text="activeDetail?.date"></p>
                        </div>
                    </div>
                </div>

                <!-- Status -->
                <div class="bg-white rounded-[28px] p-5 sm:p-6 shadow-card border border-[#EDF2F0] space-y-4">
                    <h2 class="text-lg font-bold text-slate-900 tracking-tight">Status</h2>
                    <div class="space-y-3 text-xs">
                        <div class="flex items-center justify-between">
                            <span class="text-slate-500 font-medium">Booking Status</span>
                            <span class="px-3 py-1 rounded-full text-[11px] font-semibold bg-[#E6F6EF] text-emerald-700" x-text="activeDetail?.bookingStatus"></span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-slate-500 font-medium">Invoice Status</span>
                            <span class="px-3 py-1 rounded-full text-[11px] font-semibold bg-[#FEF6E0] text-amber-700" x-text="activeDetail?.invoiceStatus"></span>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="bg-white rounded-[28px] p-5 sm:p-6 shadow-card border border-[#EDF2F0] space-y-2.5">
                    <h2 class="text-lg font-bold text-slate-900 tracking-tight mb-3">Actions</h2>

                    <a :href="activeDetail?.whatsapp_url || '#'" target="_blank"
                       class="block w-full py-2.5 px-4 rounded-full bg-brand-600 hover:bg-brand-700 text-white text-xs font-semibold text-center transition shadow-pill">
                        Chat WhatsApp Client
                    </a>

                    <template x-if="activeDetail?.raw_status === 'pending_verification'">
                        <button type="button"
                                @click="triggerConfirm(
                                    'Verify Payment Proof',
                                    'Have you verified the bank transfer of ' + activeDetail.amount_paid_formatted + ' from ' + activeDetail.name + '?',
                                    'Once confirmed, the event date will be locked and automatically synced to Google Calendar & Sheets.',
                                    activeDetail.confirm_url,
                                    'Yes, Verify & Confirm',
                                    'bg-brand-600 hover:bg-brand-700 text-white',
                                    'check'
                                )"
                                class="w-full py-2.5 px-4 rounded-full bg-[#E6F6EF] hover:bg-[#D5F0E4] text-emerald-700 text-xs font-semibold transition">
                            ✓ Verify & Confirm Payment
                        </button>
                    </template>

                    <template x-if="activeDetail?.raw_status === 'confirmed' && (activeDetail?.remaining_balance || 0) > 0">
                        <button type="button"
                                @click="triggerConfirm(
                                    'Record Full Settlement',
                                    'Are you sure you want to record full settlement of ' + activeDetail.remaining_formatted + ' for ' + activeDetail.name + '?',
                                    'Please ensure the remaining balance has cleared in your bank account. Invoice status will update to PAID (100%).',
                                    activeDetail.settle_url,
                                    'Yes, Mark as Paid',
                                    'bg-emerald-600 hover:bg-emerald-700 text-white',
                                    'check'
                                )"
                                class="w-full py-2.5 px-4 rounded-full bg-[#E6F6EF] hover:bg-[#D5F0E4] text-emerald-700 text-xs font-semibold transition">
                            ✓ Record Settlement (Paid 100%)
                        </button>
                    </template>

                    <template x-if="activeDetail?.raw_status === 'confirmed'">
                        <button type="button"
                                @click="triggerConfirm(
                                    'Mark Event as Complete',
                                    'Confirm that all wedding event series for ' + activeDetail.name + ' have been successfully completed?',
                                    'Booking status will change to Complete and payment will be automatically recorded as PAID 100%.',
                                    activeDetail.complete_url,
                                    'Yes, Mark Complete',
                                    'bg-slate-900 hover:bg-slate-800 text-white',
                                    'check'
                                )"
                                class="w-full py-2.5 px-4 rounded-full bg-slate-900 hover:bg-slate-800 text-white text-xs font-semibold transition">
                            Mark Event Complete
                        </button>
                    </template>

                    <a :href="activeDetail?.receipt_url || '#'" target="_blank"
                       class="block w-full py-2.5 px-4 rounded-full border border-[#E5ECE9] hover:bg-[#F6F8F7] text-slate-700 text-xs font-semibold text-center transition">
                        Download Receipt PDF
                    </a>

                    <template x-if="activeDetail?.raw_status === 'pending_verification'">
                        <button type="button"
                                @click="triggerConfirm(
                                    'Reject Booking',
                                    'Are you sure you want to reject booking #' + activeDetail.code + ' for ' + activeDetail.name + '?',
                                    'This booking will be marked as Cancelled and removed from your active schedule.',
                                    activeDetail.reject_url,
                                    'Yes, Reject Booking',
                                    'bg-rose-600 hover:bg-rose-700 text-white',
                                    'warning'
                                )"
                                class="w-full py-2.5 px-4 rounded-full bg-rose-50 hover:bg-rose-100 text-rose-600 text-xs font-semibold transition">
                            Reject Booking
                        </button>
                    </template>
                </div>

            </div>
        </div>
        </div>
    </div>

    <!-- ================================================================== -->
    <!-- CONFIRMATION MODAL (MODERN REPLACEMENT FOR NATIVE BROWSER ALERT)   -->
    <!-- ================================================================== -->
    <div x-show="confirmModal.open"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/60 backdrop-blur-xs"
         @keydown.escape.window="confirmModal.open = false"
         x-cloak>
        <div class="bg-white rounded-[28px] max-w-md w-full p-6 shadow-2xl border border-[#EAF0EE] space-y-4"
             @click.away="confirmModal.open = false"
             x-transition:enter="transition ease-out duration-200 transform"
             x-transition:enter-start="opacity-0 scale-95 translate-y-2"
             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
             x-transition:leave="transition ease-in duration-150 transform"
             x-transition:leave-start="opacity-100 scale-100 translate-y-0"
             x-transition:leave-end="opacity-0 scale-95 translate-y-2">
            <div class="flex items-start justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl flex items-center justify-center shrink-0 border"
                         :class="confirmModal.iconType === 'warning' ? 'bg-rose-50 text-rose-600 border-rose-100' : 'bg-brand-50 text-brand-600 border-brand-100'">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.4" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-slate-900" x-text="confirmModal.title"></h3>
                        <p class="text-[11px] text-slate-400">Owner Action Confirmation</p>
                    </div>
                </div>
                <button type="button" @click="confirmModal.open = false" class="p-1.5 rounded-full text-slate-400 hover:bg-slate-100">✕</button>
            </div>

            <div class="space-y-3">
                <p class="text-xs sm:text-sm font-semibold text-slate-800 leading-relaxed" x-text="confirmModal.message"></p>
                <div class="p-3.5 bg-[#F6F8F7] rounded-2xl border border-[#E8EFED] text-[11px] text-slate-600 leading-normal" x-show="confirmModal.subtext">
                    <span x-text="confirmModal.subtext"></span>
                </div>
            </div>

            <form :action="confirmModal.actionUrl" method="POST" class="flex items-center gap-2.5 pt-2">
                @csrf
                <button type="button" @click="confirmModal.open = false"
                        class="flex-1 py-2.5 px-4 rounded-full border border-[#DCE5E2] text-slate-700 font-semibold text-xs hover:bg-slate-50 transition">
                    Cancel
                </button>
                <button type="submit" :class="confirmModal.btnClass"
                        class="flex-1 py-2.5 px-4 rounded-full font-bold text-xs transition shadow-sm">
                    <span x-text="confirmModal.btnText"></span>
                </button>
            </form>
        </div>
    </div>

</div>
@endsection
