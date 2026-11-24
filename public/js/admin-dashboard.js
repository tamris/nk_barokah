/**
 * NK Barokah Admin Dashboard - Alpine.js State & Controller
 * Registered via Alpine.data('adminDashboard', (config) => ({ ... }))
 */
document.addEventListener('alpine:init', () => {
    Alpine.data('adminDashboard', (config = {}) => ({
        allBookings: config.allBookings || [],
        clientsList: config.clientsList || [],
        packagesList: config.packagesList || [],
        addonsList: config.addonsList || [],

        todayIso: config.todayIso || '',
        selectedDateIso: config.todayIso || '',
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

        currentRealYear: config.currentRealYear,
        currentRealMonth: config.currentRealMonth,
        currentRealDay: config.currentRealDay,
        calYear: config.calYear,
        calMonth: config.calMonth,
        calendarView: 'Monthly View',
        calendarViewOpen: false,
        calendarMode: 'events',
        calendarModeOpen: false,
        selectedDayModal: null,
        maxEventsPerDay: config.maxEventsPerDay ?? 1,
        addDaysToIso(isoStr, numDays) {
            if (!isoStr) return '';
            const parts = isoStr.split('-').map(Number);
            const dt = new Date(parts[0], parts[1] - 1, parts[2] + numDays);
            const y = dt.getFullYear();
            const m = String(dt.getMonth() + 1).padStart(2, '0');
            const d = String(dt.getDate()).padStart(2, '0');
            return `${y}-${m}-${d}`;
        },
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

        serviceSubTab: new URLSearchParams(window.location.search).get('subtab') || 'packages',
        serviceSearch: '',

        packageModal: {
            open: false,
            isEdit: false,
            id: null,
            name: '',
            base_price: '',
            display_price: '',
            description: '',
            action: config.packageStoreUrl
        },
        openPackageModal(pkg = null) {
            if (pkg) {
                const hasPrice = (pkg.price !== undefined && pkg.price !== null && pkg.price !== '');
                const price = hasPrice ? Math.round(Number(pkg.price)) : 0;
                this.packageModal = {
                    open: true,
                    isEdit: true,
                    id: pkg.id,
                    name: pkg.name || '',
                    base_price: hasPrice ? price : '',
                    display_price: hasPrice ? new Intl.NumberFormat('id-ID').format(price) : '',
                    description: (pkg.raw_description !== undefined && pkg.raw_description !== null && pkg.raw_description !== '') ? pkg.raw_description : (pkg.description || ''),
                    action: '/admin/packages/' + pkg.id
                };
            } else {
                this.packageModal = {
                    open: true,
                    isEdit: false,
                    id: null,
                    name: '',
                    base_price: '',
                    display_price: '',
                    description: '',
                    action: config.packageStoreUrl
                };
            }
        },
        formatPackagePriceInput(e) {
            const clean = e.target.value.replace(/\D/g, '');
            if (!clean) {
                this.packageModal.base_price = '';
                this.packageModal.display_price = '';
                e.target.value = '';
                return;
            }
            const num = parseInt(clean, 10);
            this.packageModal.base_price = num;
            this.packageModal.display_price = new Intl.NumberFormat('id-ID').format(num);
            e.target.value = this.packageModal.display_price;
        },
        closePackageModal() {
            this.packageModal.open = false;
        },

        addonModal: {
            open: false,
            isEdit: false,
            id: null,
            name: '',
            unit: 'unit',
            unit_price: '',
            display_price: '',
            action: config.addonStoreUrl
        },
        openAddonModal(addon = null) {
            if (addon) {
                const price = addon.price ? Math.round(Number(addon.price)) : 0;
                this.addonModal = {
                    open: true,
                    isEdit: true,
                    id: addon.id,
                    name: addon.name || '',
                    unit: addon.unit || 'unit',
                    unit_price: price || '',
                    display_price: price > 0 ? new Intl.NumberFormat('id-ID').format(price) : '',
                    action: '/admin/addons/' + addon.id
                };
            } else {
                this.addonModal = {
                    open: true,
                    isEdit: false,
                    id: null,
                    name: '',
                    unit: 'unit',
                    unit_price: '',
                    display_price: '',
                    action: config.addonStoreUrl
                };
            }
        },
        formatAddonPriceInput(e) {
            const clean = e.target.value.replace(/\D/g, '');
            if (!clean) {
                this.addonModal.unit_price = '';
                this.addonModal.display_price = '';
                e.target.value = '';
                return;
            }
            const num = parseInt(clean, 10);
            this.addonModal.unit_price = num;
            this.addonModal.display_price = new Intl.NumberFormat('id-ID').format(num);
            e.target.value = this.addonModal.display_price;
        },
        closeAddonModal() {
            this.addonModal.open = false;
        },

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
            }, 250);

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
            iconType: 'check',
            method: 'POST'
        },

        parseIsoDate(iso) {
            const [y, m, d] = (iso || this.todayIso).split('-').map(Number);
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
            const today = this.todayIso;
            return this.allBookings
                .filter(b => {
                    if (b.raw_status === 'rejected' || b.raw_status === 'completed') return false;
                    return b.event_date_iso > today;
                })
                .sort((a, b) => a.event_date_iso.localeCompare(b.event_date_iso) || a.id - b.id)
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
                const matchSearch = !q 
                    || item.name.toLowerCase().includes(q) 
                    || item.code.toLowerCase().includes(q) 
                    || item.service.toLowerCase().includes(q)
                    || (item.job_source && item.job_source.toLowerCase().includes(q))
                    || (item.tent_color_theme && item.tent_color_theme.toLowerCase().includes(q))
                    || (item.venue_address && item.venue_address.toLowerCase().includes(q));
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
            const todayIso = this.todayIso;
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

                // Field Operations: Tent Setup (H-2) and Teardown (H+1)
                const operations = [];
                this.allBookings.forEach(b => {
                    if (b.raw_status === 'rejected') return;
                    const start = b.event_date_iso;
                    const end = b.event_end_date_iso || start;
                    const setupDate = b.setup_date_iso || this.addDaysToIso(start, -2);
                    const teardownDate = this.addDaysToIso(end, 1);

                    // 1. Setup / Loading H-2
                    if (iso === setupDate) {
                        operations.push({
                            id: b.id + '-setup',
                            type: 'setup',
                            statusLabel: 'Setup',
                            badgeClass: 'bg-[#EAF3FF] text-blue-700',
                            badgeDot: 'bg-blue-600',
                            chipClass: 'bg-[#EAF3FF] hover:bg-[#DCEBFF] text-blue-800',
                            chipTitle: '#' + b.code + ' · ' + b.shortName + ' (Setup)',
                            chipSubtitle: 'Setup: ' + b.service + (b.tent_color_theme && b.tent_color_theme !== 'Standard / Natural' ? ' · ' + b.tent_color_theme : ''),
                            instruction: 'Fleet loading and tent installation at venue.',
                            booking: b
                        });
                    }

                    // 2. Teardown / Return H+1
                    if (iso === teardownDate) {
                        operations.push({
                            id: b.id + '-teardown',
                            type: 'teardown',
                            statusLabel: 'Teardown',
                            badgeClass: 'bg-[#FEF3C7] text-amber-800',
                            badgeDot: 'bg-amber-500',
                            chipClass: 'bg-[#FEF3C7] hover:bg-[#FDE68A] text-amber-900',
                            chipTitle: '#' + b.code + ' · ' + b.shortName + ' (Teardown)',
                            chipSubtitle: 'Teardown: ' + b.service,
                            instruction: 'Tent dismantling and returning equipment to warehouse.',
                            booking: b
                        });
                    }

                    // 3. Site Survey
                    if (b.survey_requested && b.survey_date && iso === b.survey_date) {
                        const isDone = b.survey_status === 'completed';
                        operations.push({
                            id: b.id + '-survey',
                            type: 'survey',
                            statusLabel: isDone ? 'Survey Done' : 'Site Survey',
                            badgeClass: isDone ? 'bg-[#ECFDF5] text-emerald-800 border border-emerald-200' : 'bg-[#F3E8FF] text-purple-900 border border-purple-200',
                            badgeDot: isDone ? 'bg-emerald-500' : 'bg-purple-600',
                            chipClass: isDone ? 'bg-[#ECFDF5] hover:bg-[#D1FAE5] text-emerald-900 border border-emerald-200/80' : 'bg-[#F3E8FF] hover:bg-[#E9D5FF] text-purple-950 border border-purple-200/80',
                            chipTitle: '#' + b.code + ' · ' + b.shortName + ' (Survey)',
                            chipSubtitle: (b.survey_time_slot_label || 'Survey') + ': ' + b.service,
                            instruction: 'Venue inspection & measurements. ' + (b.survey_notes ? 'Notes: ' + b.survey_notes : 'Contact: ' + b.phone),
                            booking: b
                        });
                    }
                });

                cells.push({
                    blank: false,
                    key: 'd-' + d,
                    day: d,
                    iso: iso,
                    dateFull: dateFull,
                    isToday: iso === todayIso,
                    events,
                    operations,
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
        get monthOperations() {
            const monthStart = `${this.calYear}-${String(this.calMonth).padStart(2, '0')}-01`;
            const lastDay = new Date(this.calYear, this.calMonth, 0).getDate();
            const monthEnd = `${this.calYear}-${String(this.calMonth).padStart(2, '0')}-${String(lastDay).padStart(2, '0')}`;
            const ops = [];

            this.allBookings.forEach(b => {
                if (b.raw_status === 'rejected') return;
                const start = b.event_date_iso;
                const end = b.event_end_date_iso || start;
                const setupDate = b.setup_date_iso || this.addDaysToIso(start, -2);
                const teardownDate = this.addDaysToIso(end, 1);

                if (setupDate >= monthStart && setupDate <= monthEnd) {
                    ops.push({
                        key: b.id + '-setup',
                        date_iso: setupDate,
                        day: parseInt(setupDate.split('-')[2], 10),
                        month: parseInt(setupDate.split('-')[1], 10),
                        type: 'setup',
                        statusLabel: 'Setup',
                        badgeClass: 'bg-[#EAF3FF] text-blue-700',
                        badgeDot: 'bg-blue-600',
                        instruction: 'Fleet loading and tent installation at venue.',
                        booking: b
                    });
                }
                if (teardownDate >= monthStart && teardownDate <= monthEnd) {
                    ops.push({
                        key: b.id + '-teardown',
                        date_iso: teardownDate,
                        day: parseInt(teardownDate.split('-')[2], 10),
                        month: parseInt(teardownDate.split('-')[1], 10),
                        type: 'teardown',
                        statusLabel: 'Teardown',
                        badgeClass: 'bg-[#FEF3C7] text-amber-800',
                        badgeDot: 'bg-amber-500',
                        instruction: 'Tent dismantling and returning equipment to warehouse.',
                        booking: b
                    });
                }
                if (b.survey_requested && b.survey_date && b.survey_date >= monthStart && b.survey_date <= monthEnd) {
                    const isDone = b.survey_status === 'completed';
                    ops.push({
                        key: b.id + '-survey',
                        date_iso: b.survey_date,
                        day: parseInt(b.survey_date.split('-')[2], 10),
                        month: parseInt(b.survey_date.split('-')[1], 10),
                        type: 'survey',
                        statusLabel: isDone ? 'Survey Done' : 'Site Survey',
                        badgeClass: isDone ? 'bg-[#ECFDF5] text-emerald-800 border border-emerald-200' : 'bg-[#F3E8FF] text-purple-900 border border-purple-200',
                        badgeDot: isDone ? 'bg-emerald-500' : 'bg-purple-600',
                        instruction: (b.survey_time_slot_label || 'Survey') + ': Venue inspection & measurements. ' + (b.survey_notes ? 'Notes: ' + b.survey_notes : ''),
                        booking: b
                    });
                }
            });

            return ops.sort((a, b) => a.date_iso.localeCompare(b.date_iso));
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
        triggerConfirm(title, message, subtext, actionUrl, btnText, btnClass = 'bg-brand-600 hover:bg-brand-700 text-white', iconType = 'check', method = 'POST') {
            this.confirmModal = { open: true, title, message, subtext, actionUrl, btnText, btnClass, iconType, method };
        }
    }));
});
