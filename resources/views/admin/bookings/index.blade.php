@extends('admin.layout')

@section('title', 'Dashboard Admin')

@section('content')
@php
    $ownerName = Auth::user()->name ?? 'Alex Rivera';
    $hour = now()->hour;
    $greeting = $hour < 12 ? 'Good Morning' : ($hour < 17 ? 'Good Afternoon' : 'Good Evening');
@endphp

<div x-data="{
        selectedDay: 12,
        currentMonthIndex: 0,
        months: ['November 2026', 'December 2026', 'January 2027'],
        scheduleQuery: '',
        bookingSearch: '',
        bookingFilter: 'all',
        bookingServiceFilter: 'all',
        statusMenuOpen: false,
        serviceMenuOpen: false,
        calendarView: 'Monthly View',
        calendarViewOpen: false,
        previousTab: 'dashboard',
        invoiceFilter: 'all',
        activeDetail: null,
        calendarMonths: [
            { label: 'January 2027', short: 'JAN', range: 'Jan 1, 2027 - Jan 31, 2027', badgeDay: '19' },
            { label: 'February 2027', short: 'FEB', range: 'Feb 1, 2027 - Feb 28, 2027', badgeDay: '14' },
            { label: 'March 2027', short: 'MAR', range: 'Mar 1, 2027 - Mar 31, 2027', badgeDay: '21' }
        ],
        calendarMonthIdx: 0,
        calendarEvents: {
            8:  [{ time: '11:00 AM', shortName: 'Michael', name: 'Michael Brown', service: 'Pre-Wedding Session', status: 'Complete', invoiceStatus: 'Paid', code: 'NKB/11/06', date: 'Friday, Jan 08, 2027', amount: 'Rp 15.000.000', phone: '0813-5566-7788', email: 'michael.brown@email.com', location: 'Taman Menteng Studio, Jakarta', notes: 'Outdoor golden hour session with classic minimalist wardrobe.', avatar: 'https://i.pravatar.cc/100?img=53' }],
            12: [
                { time: '10:00 AM', shortName: 'Sarah', name: 'Sarah Chen', service: 'Gold Royal Package', status: 'Confirmed', invoiceStatus: 'Paid', code: 'NKB/11/01', date: 'Tuesday, Jan 12, 2027', amount: 'Rp 45.000.000', phone: '0812-3456-7890', email: 'sarah.chen@email.com', location: 'The Grand Ballroom, Downtown', notes: 'Spring wedding theme. Bride requested specific shots at sunset & akad outdoor setup.', avatar: 'https://i.pravatar.cc/100?img=47' },
                { time: '02:00 PM', shortName: 'Emily', name: 'Emily Rodriguez', service: 'Platinum Luxury Package', status: 'Confirmed', invoiceStatus: 'Sent', code: 'NKB/11/02', date: 'Tuesday, Jan 12, 2027', amount: 'Rp 75.000.000', phone: '0856-2211-8899', email: 'emily.rodriguez@email.com', location: 'Kempinski Grand Hall, Central', notes: 'Full floral arch setup with live acoustic band and VIP family lounge.', avatar: 'https://i.pravatar.cc/100?img=44' }
            ],
            15: [{ time: '09:00 AM', shortName: 'Marcus', name: 'Marcus Johnson', service: 'Silver Intimate Wedding', status: 'Confirmed', invoiceStatus: 'Draft', code: 'NKB/11/03', date: 'Friday, Jan 15, 2027', amount: 'Rp 28.000.000', phone: '0821-4455-6677', email: 'marcus.j@email.com', location: 'Glasshouse Garden Venue', notes: 'Intimate 120 guests reception with warm rustic lighting.', avatar: 'https://i.pravatar.cc/100?img=12' }],
            18: [{ time: '03:00 PM', shortName: 'Jessica', name: 'Jessica Taylor', service: 'Pre-Wedding Session', status: 'Inquiry', invoiceStatus: null, code: 'NKB/11/04', date: 'Monday, Jan 18, 2027', amount: 'Rp 15.000.000', phone: '0878-9900-1122', email: 'jessica.t@email.com', location: 'Botanical Courtyard', notes: 'Initial consultation and venue moodboard review.', avatar: 'https://i.pravatar.cc/100?img=25' }],
            20: [{ time: '07:00 AM', shortName: 'David', name: 'David Kim', service: 'Bridal Makeup', status: 'Confirmed', invoiceStatus: 'Sent', code: 'NKB/11/05', date: 'Wednesday, Jan 20, 2027', amount: 'Rp 12.500.000', phone: '0819-3344-5566', email: 'david.kim@email.com', location: 'Ritz Residence Suite 1402', notes: 'Early morning akad preparation + retouch session for reception.', avatar: 'https://i.pravatar.cc/100?img=15' }],
            25: [{ time: '10:00 AM', shortName: 'Adinda', name: 'Adinda Maharani', service: 'Event Videography', status: 'Confirmed', invoiceStatus: 'Overdue', code: 'NKB/11/07', date: 'Monday, Jan 25, 2027', amount: 'Rp 22.000.000', phone: '0813-9149-7365', email: 'adinda.m@email.com', location: 'Pendopo Agung Heritage', notes: 'Same-day edit video screening requested before dinner toast.', avatar: 'https://i.pravatar.cc/100?img=5' }]
        },
        allBookings: [
            { id: 1, code: 'NKB/11/01', name: 'Sarah Chen', date: 'Jan 12, 2027', time: '10:00 AM', service: 'Gold Royal Package', bookingStatus: 'Confirmed', invoiceStatus: 'Paid', amount: 'Rp 45.000.000', phone: '0812-3456-7890', avatar: 'https://i.pravatar.cc/100?img=47' },
            { id: 2, code: 'NKB/11/02', name: 'Emily Rodriguez', date: 'Jan 12, 2027', time: '02:00 PM', service: 'Platinum Luxury Package', bookingStatus: 'Confirmed', invoiceStatus: 'Sent', amount: 'Rp 75.000.000', phone: '0856-2211-8899', avatar: 'https://i.pravatar.cc/100?img=44' },
            { id: 3, code: 'NKB/11/03', name: 'Marcus Johnson', date: 'Jan 15, 2027', time: '09:00 AM', service: 'Silver Intimate Wedding', bookingStatus: 'Confirmed', invoiceStatus: 'Draft', amount: 'Rp 28.000.000', phone: '0821-4455-6677', avatar: 'https://i.pravatar.cc/100?img=12' },
            { id: 4, code: 'NKB/11/04', name: 'Jessica Taylor', date: 'Jan 18, 2027', time: '03:00 PM', service: 'Pre-Wedding Session', bookingStatus: 'Inquiry', invoiceStatus: null, amount: 'Rp 15.000.000', phone: '0878-9900-1122', avatar: 'https://i.pravatar.cc/100?img=25' },
            { id: 5, code: 'NKB/11/05', name: 'David Kim', date: 'Jan 20, 2027', time: '07:00 AM', service: 'Bridal Makeup', bookingStatus: 'Confirmed', invoiceStatus: 'Sent', amount: 'Rp 12.500.000', phone: '0819-3344-5566', avatar: 'https://i.pravatar.cc/100?img=15' },
            { id: 6, code: 'NKB/11/06', name: 'Michael Brown', date: 'Jan 08, 2027', time: '11:00 AM', service: 'Pre-Wedding Session', bookingStatus: 'Complete', invoiceStatus: 'Paid', amount: 'Rp 15.000.000', phone: '0813-5566-7788', avatar: 'https://i.pravatar.cc/100?img=53' },
            { id: 7, code: 'NKB/11/07', name: 'Adinda Maharani', date: 'Jan 25, 2027', time: '10:00 AM', service: 'Event Videography', bookingStatus: 'Confirmed', invoiceStatus: 'Overdue', amount: 'Rp 22.000.000', phone: '0813-9149-7365', avatar: 'https://i.pravatar.cc/100?img=5' },
            { id: 8, code: 'NKB/11/08', name: 'Muhammad Reza', date: 'Jan 10, 2027', time: '06:00 AM', service: 'Bridal Makeup', bookingStatus: 'Complete', invoiceStatus: 'Paid', amount: 'Rp 12.500.000', phone: '0889-2332-2332', avatar: 'https://i.pravatar.cc/100?img=11' }
        ],
        days: [
            { day: 'Sat', num: 10 },
            { day: 'Sun', num: 11 },
            { day: 'Mon', num: 12 },
            { day: 'Tue', num: 13 },
            { day: 'Wed', num: 14 },
        ],
        todaySchedule: [
            {
                id: 1,
                name: 'Sarah Chen',
                time: '10:00 AM',
                service: 'Gold Royal Wedding Package',
                status: 'Confirmed',
                invoice: 'NKB/11/01',
                amount: 'Rp 45.000.000',
                phone: '0812-3456-7890',
                avatar: 'https://i.pravatar.cc/100?img=47',
                dayNum: 12
            },
            {
                id: 2,
                name: 'Nadia Putri',
                time: '06:00 AM',
                service: 'Akad & Makeup Fitting',
                status: 'Cancelled',
                invoice: 'NKB/11/02',
                amount: 'Rp 18.000.000',
                phone: '0813-9876-5432',
                avatar: 'https://i.pravatar.cc/100?img=32',
                dayNum: 12
            },
            {
                id: 3,
                name: 'Emily Rodriguez',
                time: '02:00 PM',
                service: 'Platinum Luxury Package',
                status: 'Confirmed',
                invoice: 'NKB/11/03',
                amount: 'Rp 75.000.000',
                phone: '0856-2211-8899',
                avatar: 'https://i.pravatar.cc/100?img=44',
                dayNum: 12
            },
            {
                id: 4,
                name: 'Muhammad Reza',
                time: '09:00 AM',
                service: 'Venue Technical Meeting',
                status: 'Confirmed',
                invoice: 'NKB/11/04',
                amount: 'Rp 45.000.000',
                phone: '0889-2332-2332',
                avatar: 'https://i.pravatar.cc/100?img=11',
                dayNum: 13
            },
            {
                id: 5,
                name: 'Adinda Maharani',
                time: '01:00 PM',
                service: 'Intimate Garden Wedding',
                status: 'Confirmed',
                invoice: 'NKB/10/03',
                amount: 'Rp 32.000.000',
                phone: '0813-9149-7365',
                avatar: 'https://i.pravatar.cc/100?img=5',
                dayNum: 11
            }
        ],
        upcomingBookings: [
            {
                id: 101,
                name: 'Sarah Chen',
                datetime: 'Jan 12, 2027 · 10:00 AM',
                service: 'Gold Royal Package',
                status: 'Confirmed',
                invoice: 'NKB/11/01',
                amount: 'Rp 45.000.000',
                phone: '0812-3456-7890',
                avatar: 'https://i.pravatar.cc/100?img=47'
            },
            {
                id: 102,
                name: 'Emily Rodriguez',
                datetime: 'Jan 12, 2027 · 02:00 PM',
                service: 'Platinum Luxury Package',
                status: 'Confirmed',
                invoice: 'NKB/11/02',
                amount: 'Rp 75.000.000',
                phone: '0856-2211-8899',
                avatar: 'https://i.pravatar.cc/100?img=44'
            },
            {
                id: 103,
                name: 'Marcus Johnson',
                datetime: 'Jan 15, 2027 · 09:00 AM',
                service: 'Silver Intimate Wedding',
                status: 'Confirmed',
                invoice: 'NKB/11/03',
                amount: 'Rp 28.000.000',
                phone: '0821-4455-6677',
                avatar: 'https://i.pravatar.cc/100?img=12'
            },
            {
                id: 104,
                name: 'Jessica Taylor',
                datetime: 'Jan 18, 2027 · 03:00 PM',
                service: 'Pre-Wedding & Engagement',
                status: 'Inquiry',
                invoice: 'NKB/11/04',
                amount: 'Rp 15.000.000',
                phone: '0878-9900-1122',
                avatar: 'https://i.pravatar.cc/100?img=25'
            },
            {
                id: 105,
                name: 'David Kim',
                datetime: 'Jan 22, 2027 · 08:00 AM',
                service: 'Full Reception Organizer',
                status: 'Confirmed',
                invoice: 'NKB/11/05',
                amount: 'Rp 52.000.000',
                phone: '0819-3344-5566',
                avatar: 'https://i.pravatar.cc/100?img=15'
            }
        ],
        outstandingInvoices: [
            {
                id: 201,
                name: 'Emily Rodriguez',
                amount: 'Rp 40.000.000',
                due: 'Due: Jan 10, 2027',
                code: 'NKB/11/01',
                status: 'Sent',
                service: 'Platinum Luxury Package',
                phone: '0856-2211-8899',
                avatar: 'https://i.pravatar.cc/100?img=44'
            },
            {
                id: 202,
                name: 'David Kim',
                amount: 'Rp 12.500.000',
                due: 'Due: Jan 15, 2027',
                code: 'NKB/11/04',
                status: 'Sent',
                service: 'Full Reception Organizer',
                phone: '0819-3344-5566',
                avatar: 'https://i.pravatar.cc/100?img=15'
            },
            {
                id: 203,
                name: 'Sarah Chen',
                amount: 'Rp 22.000.000',
                due: 'Due: Jan 08, 2027',
                code: 'NKB/10/03',
                status: 'Overdue',
                service: 'Gold Royal Package',
                phone: '0812-3456-7890',
                avatar: 'https://i.pravatar.cc/100?img=47'
            }
        ],
        get filteredSchedule() {
            const q = (this.scheduleQuery || this.$store.nav.search || '').toLowerCase();
            return this.todaySchedule.filter(item => {
                const matchText = !q || item.name.toLowerCase().includes(q) || item.service.toLowerCase().includes(q);
                return matchText;
            });
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
        prevMonth() {
            this.currentMonthIndex = (this.currentMonthIndex - 1 + this.months.length) % this.months.length;
        },
        nextMonth() {
            this.currentMonthIndex = (this.currentMonthIndex + 1) % this.months.length;
        },
        openDetail(item) {
            if (this.$store.nav.tab !== 'booking_detail') {
                this.previousTab = this.$store.nav.tab;
            }
            const cleanName = (item.name || 'Client').toLowerCase().replace(/[^a-z0-9]+/g, '.');
            this.activeDetail = {
                ...item,
                code: item.code || item.invoice || 'NKB/11/01',
                date: item.date || (item.datetime ? item.datetime.split('·')[0].trim() : 'Monday, Jan 12, 2027'),
                time: item.time || (item.datetime && item.datetime.includes('·') ? item.datetime.split('·')[1].trim() : '10:00 AM'),
                email: item.email || `${cleanName}@email.com`,
                phone: item.phone || '0812-3456-7890',
                location: item.location || 'The Grand Ballroom, Downtown',
                notes: item.notes || 'Spring wedding theme. Bride requested specific shots at sunset & seamless akad-to-reception transition.',
                bookingStatus: item.bookingStatus || item.status || 'Confirmed',
                invoiceStatus: item.invoiceStatus !== undefined ? item.invoiceStatus : (item.status === 'Overdue' ? 'Overdue' : 'Paid'),
                amount: item.amount || 'Rp 45.000.000',
            };
            this.$store.nav.setTab('booking_detail');
            window.scrollTo({ top: 0, behavior: 'smooth' });
        },
        closeDetail() {
            this.$store.nav.setTab(this.previousTab || 'dashboard');
        }
     }"
     class="space-y-6">

    <!-- ================================================================== -->
    <!-- GREETING + 4 KPI HORIZONTAL STRIP (HANYA DI TAB DASHBOARD)         -->
    <!-- ================================================================== -->
    <section x-show="$store.nav.tab === 'dashboard'"
             class="flex flex-col xl:flex-row xl:items-center justify-between gap-6 px-1">

        <!-- Left Greeting -->
        <div class="shrink-0">
            <p class="text-sm font-medium text-slate-400">{{ $greeting }}</p>
            <h1 class="text-2xl sm:text-[30px] font-bold text-slate-900 tracking-tight leading-tight mt-0.5">
                {{ $ownerName }}
            </h1>
        </div>

        <!-- Right 4 KPI Stat Strip -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 xl:gap-0 xl:flex xl:items-center xl:divide-x xl:divide-[#E3EAE7]">

            <!-- KPI 1: Today's Schedule -->
            <div class="flex items-center gap-3.5 xl:pr-7">
                <div class="w-12 h-12 rounded-full bg-white shadow-card border border-[#EEF2F1] flex items-center justify-center text-brand-600 shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
                <div>
                    <div class="text-2xl font-bold text-slate-900 leading-none">2</div>
                    <div class="text-xs font-medium text-slate-500 mt-1.5 whitespace-nowrap">Today's Schedule</div>
                </div>
            </div>

            <!-- KPI 2: Upcoming Bookings -->
            <div class="flex items-center gap-3.5 xl:px-7">
                <div class="w-12 h-12 rounded-full bg-white shadow-card border border-[#EEF2F1] flex items-center justify-center text-brand-600 shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <span class="text-2xl font-bold text-slate-900 leading-none">5</span>
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-[#E6F4F1] text-brand-600 whitespace-nowrap">
                            +12% <span class="font-medium text-brand-600/80">vs last month</span>
                        </span>
                    </div>
                    <div class="text-xs font-medium text-slate-500 mt-1.5 whitespace-nowrap">Upcoming Bookings</div>
                </div>
            </div>

            <!-- KPI 3: Outstanding Invoices -->
            <div class="flex items-center gap-3.5 xl:px-7">
                <div class="w-12 h-12 rounded-full bg-white shadow-card border border-[#EEF2F1] flex items-center justify-center text-brand-600 shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <div>
                    <div class="text-2xl font-bold text-slate-900 leading-none">3</div>
                    <div class="text-xs font-medium text-slate-500 mt-1.5 whitespace-nowrap">Outstanding Invoices</div>
                </div>
            </div>

            <!-- KPI 4: Monthly Revenue -->
            <div class="flex items-center gap-3.5 xl:pl-7">
                <div class="w-12 h-12 rounded-full bg-white shadow-card border border-[#EEF2F1] flex items-center justify-center text-brand-600 shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <div class="text-2xl font-bold text-slate-900 leading-none tracking-tight">Rp 151 Jt</div>
                    <div class="text-xs font-medium text-slate-500 mt-1.5 whitespace-nowrap">Monthly Revenue</div>
                </div>
            </div>

        </div>
    </section>

    <!-- ================================================================== -->
    <!-- TAB 1: DASHBOARD (EXACT 3-COLUMN BENTO GRID FROM REFERENCE)        -->
    <!-- ================================================================== -->
    <div x-show="$store.nav.tab === 'dashboard'"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 translate-y-1"
         x-transition:enter-end="opacity-100 translate-y-0"
         class="grid grid-cols-1 lg:grid-cols-3 gap-5 items-stretch">

        <!-- ============================================================ -->
        <!-- COLUMN 1: TODAY'S SCHEDULE                                   -->
        <!-- ============================================================ -->
        <div class="bg-white rounded-[28px] p-5 sm:p-6 shadow-card border border-[#EDF2F0] flex flex-col">

            <!-- Card Header -->
            <div class="flex items-center justify-between gap-2">
                <h2 class="text-[17px] font-bold text-slate-900 tracking-tight">Today's Schedule</h2>
                <button type="button"
                        @click="$store.nav.setTab('calendar')"
                        class="px-3.5 py-1.5 rounded-full border border-[#E5ECE9] bg-white hover:bg-[#F6F8F7] text-xs font-medium text-slate-600 transition">
                    View Calendar
                </button>
            </div>

            <!-- Month Selector Pill -->
            <div class="mt-4 bg-[#F6F8F7] rounded-full px-3 py-2 flex items-center justify-between text-xs font-semibold text-slate-700">
                <button type="button" @click="prevMonth()" class="w-7 h-7 rounded-full hover:bg-white flex items-center justify-center text-slate-500 transition">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.4" d="M15 19l-7-7 7-7"/></svg>
                </button>
                <span x-text="months[currentMonthIndex]"></span>
                <button type="button" @click="nextMonth()" class="w-7 h-7 rounded-full hover:bg-white flex items-center justify-center text-slate-500 transition">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.4" d="M9 5l7 7-7 7"/></svg>
                </button>
            </div>

            <!-- 5-Day Horizontal Date Strip -->
            <div class="mt-4 flex items-center justify-between gap-1">
                <button type="button" @click="selectedDay = Math.max(10, selectedDay - 1)" class="w-7 h-7 rounded-full hover:bg-[#F6F8F7] flex items-center justify-center text-slate-400 shrink-0">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M15 19l-7-7 7-7"/></svg>
                </button>

                <div class="grid grid-cols-5 gap-1.5 flex-1">
                    <template x-for="d in days" :key="d.num">
                        <button type="button"
                                @click="selectedDay = d.num"
                                :class="selectedDay === d.num
                                    ? 'bg-brand-600 text-white shadow-pill'
                                    : 'bg-transparent text-slate-700 hover:bg-[#F6F8F7]'"
                                class="py-2.5 px-1 rounded-2xl flex flex-col items-center justify-center transition-all duration-200 cursor-pointer">
                            <span class="text-[11px] font-medium"
                                  :class="selectedDay === d.num ? 'text-teal-100' : 'text-slate-400'"
                                  x-text="d.day"></span>
                            <span class="text-sm font-bold mt-0.5" x-text="d.num"></span>
                        </button>
                    </template>
                </div>

                <button type="button" @click="selectedDay = Math.min(14, selectedDay + 1)" class="w-7 h-7 rounded-full hover:bg-[#F6F8F7] flex items-center justify-center text-slate-400 shrink-0">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M9 5l7 7-7 7"/></svg>
                </button>
            </div>

            <!-- Inline Search & Filter -->
            <div class="mt-4 relative flex items-center">
                <svg class="w-4 h-4 text-slate-400 absolute left-3.5 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-5.2-5.2m2.2-4.8a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input type="text"
                       x-model="scheduleQuery"
                       placeholder="Search"
                       class="w-full pl-9 pr-10 py-2.5 rounded-2xl bg-white border border-[#E8EFED] text-xs text-slate-700 placeholder-slate-400 focus:outline-none focus:border-brand-400 transition">
                <button type="button" class="absolute right-3 text-slate-400 hover:text-brand-600 transition" title="Filter">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4h18M6 12h12M10 20h4"/>
                    </svg>
                </button>
            </div>

            <!-- Schedule List -->
            <div class="mt-4 space-y-3 relative flex-1 overflow-hidden">
                <template x-for="(item, idx) in filteredSchedule.slice(0, 3)" :key="item.id">
                    <div @click="openDetail(item)"
                         :class="idx === 2 ? 'opacity-85' : ''"
                         class="p-3.5 rounded-2xl bg-[#F8FAFA] hover:bg-[#F1F6F5] border border-[#EFF4F2] transition cursor-pointer group">
                        <div class="flex items-center justify-between gap-3">
                            <div class="flex items-center gap-3 min-w-0">
                                <img :src="item.avatar" :alt="item.name" class="w-10 h-10 rounded-xl object-cover shrink-0 bg-slate-200">
                                <div class="min-w-0">
                                    <p class="text-[13px] font-bold text-slate-900 truncate" x-text="item.name"></p>
                                    <p class="text-[11px] font-medium text-slate-500 mt-0.5" x-text="item.time"></p>
                                </div>
                            </div>
                            <svg class="w-4 h-4 text-slate-400 group-hover:text-brand-600 group-hover:translate-x-0.5 transition shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </div>

                        <div class="mt-3 pt-2.5 border-t border-[#EBF1EF] flex items-center justify-between gap-2">
                            <span class="text-[11px] font-medium text-slate-500 truncate" x-text="item.service"></span>
                            <span :class="item.status === 'Confirmed'
                                    ? 'bg-[#E6F6EF] text-emerald-700'
                                    : 'bg-rose-50 text-rose-600'"
                                  class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-bold shrink-0">
                                <span class="w-1.5 h-1.5 rounded-full"
                                      :class="item.status === 'Confirmed' ? 'bg-emerald-500' : 'bg-rose-500'"></span>
                                <span x-text="item.status"></span>
                            </span>
                        </div>
                    </div>
                </template>

                <!-- Subtle bottom fade matching reference -->
                <div class="pointer-events-none absolute inset-x-0 bottom-0 h-8 bg-gradient-to-t from-white/90 to-transparent"></div>
            </div>
        </div>

        <!-- ============================================================ -->
        <!-- COLUMN 2: UPCOMING BOOKINGS                                  -->
        <!-- ============================================================ -->
        <div class="bg-white rounded-[28px] p-5 sm:p-6 shadow-card border border-[#EDF2F0] flex flex-col">

            <!-- Card Header -->
            <div class="flex items-center justify-between gap-2">
                <h2 class="text-[17px] font-bold text-slate-900 tracking-tight">Upcoming Bookings</h2>
                <button type="button"
                        @click="$store.nav.setTab('bookings')"
                        class="px-3.5 py-1.5 rounded-full border border-[#E5ECE9] bg-white hover:bg-[#F6F8F7] text-xs font-medium text-slate-600 transition">
                    View All
                </button>
            </div>

            <!-- Upcoming Bookings List -->
            <div class="mt-4 space-y-3 relative flex-1 overflow-hidden">
                <template x-for="(item, idx) in upcomingBookings" :key="item.id">
                    <div @click="openDetail(item)"
                         :class="idx === 4 ? 'opacity-65' : ''"
                         class="p-3.5 rounded-2xl bg-[#F8FAFA] hover:bg-[#F1F6F5] border border-[#EFF4F2] transition cursor-pointer group">
                        <div class="flex items-center justify-between gap-3">
                            <div class="flex items-center gap-3 min-w-0">
                                <img :src="item.avatar" :alt="item.name" class="w-10 h-10 rounded-xl object-cover shrink-0 bg-slate-200">
                                <div class="min-w-0">
                                    <p class="text-[13px] font-bold text-slate-900 truncate" x-text="item.name"></p>
                                    <p class="text-[11px] font-medium text-slate-500 mt-0.5 truncate" x-text="item.datetime"></p>
                                </div>
                            </div>
                            <svg class="w-4 h-4 text-slate-400 group-hover:text-brand-600 group-hover:translate-x-0.5 transition shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </div>

                        <div class="mt-3 pt-2.5 border-t border-[#EBF1EF] flex items-center justify-between gap-2">
                            <span class="text-[11px] font-medium text-slate-500 truncate" x-text="item.service"></span>
                            <span :class="item.status === 'Confirmed'
                                    ? 'bg-[#E6F6EF] text-emerald-700'
                                    : 'bg-sky-50 text-sky-600'"
                                  class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-bold shrink-0">
                                <span class="w-1.5 h-1.5 rounded-full"
                                      :class="item.status === 'Confirmed' ? 'bg-emerald-500' : 'bg-sky-500'"></span>
                                <span x-text="item.status"></span>
                            </span>
                        </div>
                    </div>
                </template>

                <!-- Subtle bottom fade matching reference -->
                <div class="pointer-events-none absolute inset-x-0 bottom-0 h-12 bg-gradient-to-t from-white via-white/75 to-transparent"></div>
            </div>
        </div>

        <!-- ============================================================ -->
        <!-- COLUMN 3: OUTSTANDING INVOICES + BUSINESS INSIGHT CARD       -->
        <!-- ============================================================ -->
        <div class="flex flex-col gap-5">

            <!-- Top Card: Outstanding Invoices -->
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
                    <template x-for="(inv, idx) in outstandingInvoices" :key="inv.id">
                        <div @click="openDetail(inv)"
                             :class="idx === 2 ? 'opacity-65' : ''"
                             class="p-3.5 rounded-2xl bg-[#F8FAFA] hover:bg-[#F1F6F5] border border-[#EFF4F2] transition cursor-pointer group">
                            <div class="flex items-center justify-between gap-3">
                                <div class="flex items-center gap-3 min-w-0">
                                    <img :src="inv.avatar" :alt="inv.name" class="w-10 h-10 rounded-xl object-cover shrink-0 bg-slate-200">
                                    <div class="min-w-0">
                                        <p class="text-[13px] font-bold text-slate-900 truncate" x-text="inv.name"></p>
                                        <p class="text-[11px] font-medium text-slate-500 mt-0.5 truncate">
                                            <span class="font-semibold text-slate-700" x-text="inv.amount"></span>
                                            <span> · </span>
                                            <span x-text="inv.due"></span>
                                        </p>
                                    </div>
                                </div>
                                <svg class="w-4 h-4 text-slate-400 group-hover:text-brand-600 group-hover:translate-x-0.5 transition shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </div>

                            <div class="mt-3 pt-2.5 border-t border-[#EBF1EF] flex items-center justify-between gap-2">
                                <span class="text-[11px] font-semibold font-mono text-slate-400" x-text="inv.code"></span>
                                <span :class="inv.status === 'Overdue'
                                        ? 'bg-rose-50 text-rose-600'
                                        : 'bg-amber-50 text-amber-600'"
                                      class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-bold shrink-0">
                                    <span class="w-1.5 h-1.5 rounded-full"
                                          :class="inv.status === 'Overdue' ? 'bg-rose-500' : 'bg-amber-400'"></span>
                                    <span x-text="inv.status"></span>
                                </span>
                            </div>
                        </div>
                    </template>

                    <!-- Subtle bottom fade matching reference -->
                    <div class="pointer-events-none absolute inset-x-0 bottom-0 h-10 bg-gradient-to-t from-white via-white/80 to-transparent"></div>
                </div>
            </div>

            <!-- Bottom Card: Business Insight (Deep Emerald-Teal Gradient) -->
            <div class="rounded-[28px] bg-gradient-to-br from-[#023834] via-[#045D56] to-[#0D8278] p-6 text-white shadow-insight relative overflow-hidden">
                <!-- Soft ambient radial glow -->
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
                        Wedding organizer packages convert <strong class="text-white font-semibold">2.3x higher</strong> than other categories this month.
                    </p>

                    <div class="pt-1 flex flex-wrap items-center justify-between gap-3">
                        <div class="inline-flex items-center gap-1.5 text-xs font-semibold text-emerald-300">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                            </svg>
                            <span>+45% booking rate</span>
                        </div>

                        <button type="button"
                                @click="$store.nav.setTab('services')"
                                class="px-4 py-2 rounded-full bg-white hover:bg-teal-50 text-brand-700 text-xs font-bold transition shadow-sm">
                            Promote Wedding Package
                        </button>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- ================================================================== -->
    <!-- TAB 2: BOOKINGS (EXACT MATCH TO BOOKINGS REFERENCE IMAGE)          -->
    <!-- ================================================================== -->
    <div x-show="$store.nav.tab === 'bookings'"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 translate-y-1"
         x-transition:enter-end="opacity-100 translate-y-0"
         class="space-y-5"
         x-cloak>

        <!-- Floating Toolbar Row (Search + All Status + All Services + New Booking) -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-3">

            <!-- Left Filter Pills -->
            <div class="flex flex-wrap items-center gap-2.5 sm:gap-3">

                <!-- Search Bookings Pill -->
                <div class="relative flex items-center bg-white rounded-full border border-[#E7EEEC] shadow-[0_2px_8px_rgba(0,0,0,0.02)] focus-within:border-brand-400 transition w-full sm:w-64 lg:w-72">
                    <svg class="w-4 h-4 text-slate-400 absolute left-4 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-5.2-5.2m2.2-4.8a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input type="text"
                           x-model="bookingSearch"
                           placeholder="Search Bookings..."
                           class="w-full bg-transparent pl-10 pr-4 py-2.5 text-xs text-slate-700 placeholder-slate-400 focus:outline-none">
                </div>

                <!-- All Status Dropdown Pill -->
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
                         x-transition:enter="transition ease-out duration-150"
                         x-transition:enter-start="opacity-0 scale-95"
                         x-transition:enter-end="opacity-100 scale-100"
                         class="absolute left-0 mt-2 w-44 bg-white rounded-2xl shadow-xl border border-[#EAF0EE] py-1.5 z-30"
                         x-cloak>
                        <template x-for="st in ['all', 'Confirmed', 'Inquiry', 'Complete']" :key="st">
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

                <!-- All Services Dropdown Pill -->
                <div class="relative" @click.away="serviceMenuOpen = false">
                    <button type="button"
                            @click="serviceMenuOpen = !serviceMenuOpen; statusMenuOpen = false"
                            class="inline-flex items-center justify-between gap-4 bg-white hover:bg-[#FAFDFC] rounded-full border border-[#E7EEEC] shadow-[0_2px_8px_rgba(0,0,0,0.02)] px-4 py-2.5 text-xs font-medium text-slate-700 transition min-w-[155px]">
                        <span class="truncate max-w-[130px]" x-text="bookingServiceFilter === 'all' ? 'All Services' : bookingServiceFilter"></span>
                        <svg class="w-3.5 h-3.5 text-slate-400 transition-transform shrink-0" :class="serviceMenuOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>

                    <div x-show="serviceMenuOpen"
                         x-transition:enter="transition ease-out duration-150"
                         x-transition:enter-start="opacity-0 scale-95"
                         x-transition:enter-end="opacity-100 scale-100"
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

            <!-- Right Primary CTA Pill -->
            <a href="{{ route('booking.create') }}" target="_blank"
               class="inline-flex items-center justify-center gap-1.5 px-5 py-2.5 rounded-full bg-brand-600 hover:bg-brand-700 text-white text-xs font-semibold transition shadow-pill shrink-0">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                </svg>
                <span>New Booking</span>
            </a>

        </div>

        <!-- Main White Bookings Table Card (Desktop & Tablet) -->
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
                    <tbody class="divide-y divide-[#F1F5F4]">
                        <template x-for="b in filteredBookings" :key="b.id">
                            <tr @click="openDetail(b)" class="hover:bg-[#F8FBFA] transition cursor-pointer group">
                                <!-- 1. Booking ID (Sky Blue) -->
                                <td class="py-4 px-6 whitespace-nowrap">
                                    <span class="text-[13px] font-semibold text-[#0284C7] group-hover:underline" x-text="b.code"></span>
                                </td>

                                <!-- 2. Client (Rounded Avatar + Name) -->
                                <td class="py-4 px-5 whitespace-nowrap">
                                    <div class="flex items-center gap-3">
                                        <img :src="b.avatar" :alt="b.name" class="w-8 h-8 rounded-lg object-cover bg-slate-200 shrink-0">
                                        <span class="text-[13px] font-medium text-slate-800" x-text="b.name"></span>
                                    </div>
                                </td>

                                <!-- 3. Event Due (Date + Muted Time) -->
                                <td class="py-4 px-5 whitespace-nowrap">
                                    <span class="text-[13px] font-medium text-slate-800" x-text="b.date"></span>
                                    <span class="text-[11px] font-medium text-slate-400 ml-1.5" x-text="b.time"></span>
                                </td>

                                <!-- 4. Services -->
                                <td class="py-4 px-5 whitespace-nowrap">
                                    <span class="text-[13px] font-medium text-slate-700" x-text="b.service"></span>
                                </td>

                                <!-- 5. Booking Status Pill -->
                                <td class="py-4 px-5 whitespace-nowrap">
                                    <span :class="{
                                            'bg-[#E6F6EF] text-emerald-700': b.bookingStatus === 'Confirmed',
                                            'bg-[#EAF3FF] text-blue-600': b.bookingStatus === 'Inquiry',
                                            'bg-[#F3EAFF] text-purple-600': b.bookingStatus === 'Complete'
                                          }"
                                          class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-semibold">
                                        <span class="w-1.5 h-1.5 rounded-full"
                                              :class="{
                                                'bg-emerald-500': b.bookingStatus === 'Confirmed',
                                                'bg-blue-500': b.bookingStatus === 'Inquiry',
                                                'bg-purple-500': b.bookingStatus === 'Complete'
                                              }"></span>
                                        <span x-text="b.bookingStatus"></span>
                                    </span>
                                </td>

                                <!-- 6. Invoice Status Pill or Em-Dash -->
                                <td class="py-4 px-6 whitespace-nowrap">
                                    <template x-if="b.invoiceStatus">
                                        <span :class="{
                                                'bg-[#E6F6EF] text-emerald-700': b.invoiceStatus === 'Paid',
                                                'bg-[#FEF6E0] text-amber-600': b.invoiceStatus === 'Sent',
                                                'bg-[#F1F5F9] text-slate-500': b.invoiceStatus === 'Draft',
                                                'bg-[#FEECEB] text-rose-600': b.invoiceStatus === 'Overdue'
                                              }"
                                              class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-semibold">
                                            <span class="w-1.5 h-1.5 rounded-full"
                                                  :class="{
                                                    'bg-emerald-500': b.invoiceStatus === 'Paid',
                                                    'bg-amber-400': b.invoiceStatus === 'Sent',
                                                    'bg-slate-400': b.invoiceStatus === 'Draft',
                                                    'bg-rose-500': b.invoiceStatus === 'Overdue'
                                                  }"></span>
                                            <span x-text="b.invoiceStatus"></span>
                                        </span>
                                    </template>
                                    <template x-if="!b.invoiceStatus">
                                        <span class="text-slate-400 font-medium pl-2">—</span>
                                    </template>
                                </td>
                            </tr>
                        </template>

                        <!-- Empty State -->
                        <tr x-show="filteredBookings.length === 0">
                            <td colspan="6" class="py-12 text-center text-xs text-slate-400">
                                Tidak ada booking yang cocok dengan filter pencarian.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Mobile Stacked Cards View (< md) -->
        <div class="md:hidden space-y-3">
            <template x-for="b in filteredBookings" :key="b.id">
                <div @click="openDetail(b)" class="bg-white rounded-[24px] p-4 shadow-card border border-[#EDF2F0] space-y-3 cursor-pointer active:scale-[0.99] transition">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-[#0284C7]" x-text="b.code"></span>
                        <span class="text-[11px] text-slate-400" x-text="b.date + ' · ' + b.time"></span>
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
                                'bg-[#F3EAFF] text-purple-600': b.bookingStatus === 'Complete'
                              }"
                              class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-semibold">
                            <span class="w-1.5 h-1.5 rounded-full"
                                  :class="{
                                    'bg-emerald-500': b.bookingStatus === 'Confirmed',
                                    'bg-blue-500': b.bookingStatus === 'Inquiry',
                                    'bg-purple-500': b.bookingStatus === 'Complete'
                                  }"></span>
                            <span x-text="b.bookingStatus"></span>
                        </span>

                        <template x-if="b.invoiceStatus">
                            <span :class="{
                                    'bg-[#E6F6EF] text-emerald-700': b.invoiceStatus === 'Paid',
                                    'bg-[#FEF6E0] text-amber-600': b.invoiceStatus === 'Sent',
                                    'bg-[#F1F5F9] text-slate-500': b.invoiceStatus === 'Draft',
                                    'bg-[#FEECEB] text-rose-600': b.invoiceStatus === 'Overdue'
                                  }"
                                  class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-semibold">
                                <span class="w-1.5 h-1.5 rounded-full"
                                      :class="{
                                        'bg-emerald-500': b.invoiceStatus === 'Paid',
                                        'bg-amber-400': b.invoiceStatus === 'Sent',
                                        'bg-slate-400': b.invoiceStatus === 'Draft',
                                        'bg-rose-500': b.invoiceStatus === 'Overdue'
                                      }"></span>
                                <span x-text="b.invoiceStatus"></span>
                            </span>
                        </template>
                        <template x-if="!b.invoiceStatus">
                            <span class="text-xs text-slate-400">—</span>
                        </template>
                    </div>
                </div>
            </template>
        </div>

    </div>

    <!-- ================================================================== -->
    <!-- TAB 3: CALENDAR (EXACT MATCH TO CALENDAR REFERENCE IMAGE)          -->
    <!-- ================================================================== -->
    <div x-show="$store.nav.tab === 'calendar'"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 translate-y-1"
         x-transition:enter-end="opacity-100 translate-y-0"
         class="bg-white rounded-[28px] shadow-card border border-[#EDF2F0] overflow-hidden"
         x-cloak>

        <!-- Calendar Top Toolbar -->
        <div class="px-5 sm:px-6 py-4 border-b border-[#EFF4F2] flex flex-col lg:flex-row lg:items-center justify-between gap-4">

            <!-- Left Controls: Prev/Next + Mini Date Box + Month Title + View Selector -->
            <div class="flex flex-wrap items-center gap-3 sm:gap-4">
                <div class="flex items-center gap-2.5">
                    <button type="button"
                            @click="calendarMonthIdx = (calendarMonthIdx - 1 + calendarMonths.length) % calendarMonths.length"
                            class="w-8 h-8 rounded-full hover:bg-[#F4F7F6] text-slate-500 flex items-center justify-center transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M15 19l-7-7 7-7"/></svg>
                    </button>

                    <!-- Mini Calendar Date Icon Box (JAN / 19) -->
                    <div class="w-11 rounded-xl border border-[#E5ECE9] overflow-hidden text-center shadow-2xs shrink-0">
                        <div class="bg-[#F4F7F6] text-[9px] font-bold uppercase tracking-wider text-slate-500 py-0.5"
                             x-text="calendarMonths[calendarMonthIdx].short"></div>
                        <div class="bg-white text-xs font-extrabold text-slate-900 py-1 leading-none"
                             x-text="calendarMonths[calendarMonthIdx].badgeDay"></div>
                    </div>

                    <div class="min-w-[130px]">
                        <h2 class="text-[15px] font-bold text-slate-900 leading-tight"
                            x-text="calendarMonths[calendarMonthIdx].label"></h2>
                        <p class="text-[11px] font-medium text-slate-400 mt-0.5"
                           x-text="calendarMonths[calendarMonthIdx].range"></p>
                    </div>

                    <button type="button"
                            @click="calendarMonthIdx = (calendarMonthIdx + 1) % calendarMonths.length"
                            class="w-8 h-8 rounded-full hover:bg-[#F4F7F6] text-slate-500 flex items-center justify-center transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M9 5l7 7-7 7"/></svg>
                    </button>
                </div>

                <!-- View Selector Dropdown (Monthly View v) -->
                <div class="relative" @click.away="calendarViewOpen = false">
                    <button type="button"
                            @click="calendarViewOpen = !calendarViewOpen"
                            class="inline-flex items-center justify-between gap-4 bg-white hover:bg-[#FAFDFC] rounded-xl border border-[#E5ECE9] px-3.5 py-2 text-xs font-medium text-slate-700 transition min-w-[138px]">
                        <span x-text="calendarView"></span>
                        <svg class="w-3.5 h-3.5 text-slate-400 transition-transform" :class="calendarViewOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>

                    <div x-show="calendarViewOpen"
                         x-transition:enter="transition ease-out duration-150"
                         x-transition:enter-start="opacity-0 scale-95"
                         x-transition:enter-end="opacity-100 scale-100"
                         class="absolute left-0 mt-2 w-40 bg-white rounded-2xl shadow-xl border border-[#EAF0EE] py-1.5 z-30"
                         x-cloak>
                        <template x-for="v in ['Monthly View', 'Weekly View', 'Agenda List']" :key="v">
                            <button type="button"
                                    @click="calendarView = v; calendarViewOpen = false"
                                    :class="calendarView === v ? 'bg-brand-50 text-brand-700 font-semibold' : 'text-slate-600 hover:bg-[#F6F8F7]'"
                                    class="w-full text-left px-4 py-2 text-xs transition flex items-center justify-between">
                                <span x-text="v"></span>
                                <span x-show="calendarView === v" class="text-brand-600">✓</span>
                            </button>
                        </template>
                    </div>
                </div>
            </div>

            <!-- Right Legend Indicators -->
            <div class="flex flex-wrap items-center gap-4 text-xs font-medium text-slate-600">
                <span class="inline-flex items-center gap-1.5">
                    <span class="w-2.5 h-2.5 rounded-[3px] bg-blue-600"></span>
                    <span>Inquiry</span>
                </span>
                <span class="inline-flex items-center gap-1.5">
                    <span class="w-2.5 h-2.5 rounded-[3px] bg-emerald-500"></span>
                    <span>Confirmed</span>
                </span>
                <span class="inline-flex items-center gap-1.5">
                    <span class="w-2.5 h-2.5 rounded-[3px] bg-purple-500"></span>
                    <span>Complete</span>
                </span>
                <span class="inline-flex items-center gap-1.5">
                    <span class="w-2.5 h-2.5 rounded-[3px] bg-rose-500"></span>
                    <span>Cancelled</span>
                </span>
            </div>
        </div>

        <!-- 7-Column Bordered Monthly Calendar Grid -->
        <div class="overflow-x-auto">
            <div class="min-w-[780px]">
                <!-- Weekday Headers -->
                <div class="grid grid-cols-7 border-b border-[#EFF4F2] bg-[#FCFDFD]">
                    <div class="py-3 text-center text-xs font-semibold text-slate-700">Sun</div>
                    <div class="py-3 text-center text-xs font-semibold text-slate-700">Mon</div>
                    <div class="py-3 text-center text-xs font-semibold text-slate-700">Tue</div>
                    <div class="py-3 text-center text-xs font-semibold text-slate-700">Wed</div>
                    <div class="py-3 text-center text-xs font-semibold text-slate-700">Thu</div>
                    <div class="py-3 text-center text-xs font-semibold text-slate-700">Fri</div>
                    <div class="py-3 text-center text-xs font-semibold text-slate-700">Sat</div>
                </div>

                <!-- Day Cells Grid (Starts on Thu = 4 leading blank cells, matching reference) -->
                <div class="grid grid-cols-7">
                    @for($blank = 0; $blank < 4; $blank++)
                        <div class="min-h-[116px] p-2.5 border-r border-b border-[#EFF4F2] bg-[#FAFDFC]/50"></div>
                    @endfor

                    @for($day = 1; $day <= 31; $day++)
                        <div class="min-h-[116px] p-2.5 border-r border-b border-[#EFF4F2] bg-white hover:bg-[#FAFDFC] transition flex flex-col gap-1.5 {{ (($day + 4) % 7 === 0) ? 'border-r-0' : '' }}">
                            <!-- Day Number Row -->
                            <div class="flex items-center justify-between">
                                @if($day === 12)
                                    <span class="w-6 h-6 rounded-full bg-slate-900 text-white text-[11px] font-bold inline-flex items-center justify-center">
                                        {{ $day }}
                                    </span>
                                    <!-- Multi-booking conflict warning triangle matching reference -->
                                    <span title="2 jadwal di hari yang sama" class="text-orange-500">
                                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor">
                                            <path d="M12 2L1 21h22L12 2zm0 4.5l7.5 13h-15l7.5-13zM11 10v5h2v-5h-2zm0 6v2h2v-2h-2z"/>
                                        </svg>
                                    </span>
                                @else
                                    <span class="text-xs font-bold text-slate-800">{{ $day }}</span>
                                @endif
                            </div>

                            <!-- Event Chips for this Day -->
                            <template x-if="calendarEvents[{{ $day }}]">
                                <div class="space-y-1.5">
                                    <template x-for="(ev, eIdx) in calendarEvents[{{ $day }}]" :key="eIdx">
                                        <button type="button"
                                                @click="openDetail(ev)"
                                                :class="{
                                                    'bg-[#E6F6EF] hover:bg-[#D7F0E5] text-emerald-800': ev.status === 'Confirmed',
                                                    'bg-[#F3EAFF] hover:bg-[#E9DAFF] text-purple-800': ev.status === 'Complete',
                                                    'bg-[#EAF3FF] hover:bg-[#DCEBFF] text-blue-800': ev.status === 'Inquiry',
                                                    'bg-[#FEECEB] hover:bg-[#FCDCD9] text-rose-700': ev.status === 'Cancelled'
                                                }"
                                                class="w-full text-left px-2.5 py-1.5 rounded-lg transition cursor-pointer">
                                            <p class="text-[10.5px] font-bold leading-tight truncate" x-text="ev.time + ' · ' + ev.shortName"></p>
                                            <p class="text-[10px] opacity-80 leading-tight truncate mt-0.5" x-text="ev.service"></p>
                                        </button>
                                    </template>
                                </div>
                            </template>
                        </div>
                    @endfor
                </div>
            </div>
        </div>
    </div>

    <!-- ================================================================== -->
    <!-- TAB 4: CLIENTS (UI/UX DIRECTORY — MATCHING DESIGN SYSTEM)          -->
    <!-- ================================================================== -->
    <div x-show="$store.nav.tab === 'clients'"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 translate-y-1"
         x-transition:enter-end="opacity-100 translate-y-0"
         class="space-y-4"
         x-cloak>
        <div class="flex items-center justify-between px-1">
            <div>
                <h2 class="text-lg font-bold text-slate-900">Clients Directory</h2>
                <p class="text-xs text-slate-400">Daftar calon pengantin & histori pemesanan</p>
            </div>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            <template x-for="c in upcomingBookings" :key="c.id">
                <div @click="openDetail(c)" class="bg-white rounded-[24px] p-5 shadow-card border border-[#EDF2F0] hover:border-brand-300 transition cursor-pointer space-y-4">
                    <div class="flex items-center gap-3.5">
                        <img :src="c.avatar" :alt="c.name" class="w-12 h-12 rounded-2xl object-cover">
                        <div class="min-w-0">
                            <p class="text-sm font-bold text-slate-900 truncate" x-text="c.name"></p>
                            <p class="text-xs text-slate-400" x-text="c.phone"></p>
                        </div>
                    </div>
                    <div class="p-3 rounded-2xl bg-[#F8FAFA] flex items-center justify-between text-xs">
                        <span class="text-slate-500 truncate" x-text="c.service"></span>
                        <span class="font-bold text-brand-600 shrink-0" x-text="c.amount"></span>
                    </div>
                </div>
            </template>
        </div>
    </div>

    <!-- ================================================================== -->
    <!-- TAB 5: SERVICES (PACKAGES & ADDONS SHOWCASE UI)                    -->
    <!-- ================================================================== -->
    <div x-show="$store.nav.tab === 'services'"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 translate-y-1"
         x-transition:enter-end="opacity-100 translate-y-0"
         class="space-y-4"
         x-cloak>
        <div class="flex flex-wrap items-center justify-between gap-3 px-1">
            <div>
                <h2 class="text-lg font-bold text-slate-900">Wedding Packages & Services</h2>
                <p class="text-xs text-slate-400">Katalog paket pernikahan utama & fasilitas tambahan</p>
            </div>
            <a href="{{ route('admin.masters.index') }}"
               class="px-4 py-2 rounded-full bg-brand-600 hover:bg-brand-700 text-white text-xs font-semibold transition shadow-pill">
                Manage Master Data ↗
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
            @php
                $sampleServices = [
                    ['name' => 'Silver Intimate Package', 'price' => 'Rp 28.000.000', 'tag' => 'Popular', 'desc' => 'Dekorasi akad minimalis, MUA pengantin, dokumentasi foto & video cinematic, 4 crew WO profesional.'],
                    ['name' => 'Gold Royal Package', 'price' => 'Rp 45.000.000', 'tag' => 'Best Seller', 'desc' => 'Full dekorasi pelaminan modern, busana akad & resepsi, live acoustic, catering 300 pax, 8 crew WO.'],
                    ['name' => 'Platinum Luxury Package', 'price' => 'Rp 75.000.000', 'tag' => 'Premium', 'desc' => 'All-in ballroom wedding, custom floral arch, drone & same-day edit, MC nasional, VIP hospitality team.'],
                ];
            @endphp
            @foreach($sampleServices as $srv)
                <div class="bg-white rounded-[28px] p-6 shadow-card border border-[#EDF2F0] flex flex-col justify-between space-y-5">
                    <div class="space-y-3">
                        <span class="inline-block px-3 py-1 rounded-full text-[11px] font-bold bg-brand-50 text-brand-600">{{ $srv['tag'] }}</span>
                        <h3 class="text-base font-bold text-slate-900">{{ $srv['name'] }}</h3>
                        <p class="text-2xl font-extrabold text-brand-600 tracking-tight">{{ $srv['price'] }}</p>
                        <p class="text-xs text-slate-500 leading-relaxed">{{ $srv['desc'] }}</p>
                    </div>
                    <a href="{{ route('admin.masters.index') }}"
                       class="w-full py-2.5 rounded-full border border-[#E5ECE9] hover:bg-[#F6F8F7] text-xs font-semibold text-slate-700 text-center transition">
                        Edit Package
                    </a>
                </div>
            @endforeach
        </div>
    </div>

    <!-- ================================================================== -->
    <!-- TAB 6: INVOICE (UI/UX VIEW — MATCHING DESIGN SYSTEM)               -->
    <!-- ================================================================== -->
    <div x-show="$store.nav.tab === 'invoice'"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 translate-y-1"
         x-transition:enter-end="opacity-100 translate-y-0"
         class="bg-white rounded-[28px] p-5 sm:p-6 shadow-card border border-[#EDF2F0] space-y-5"
         x-cloak>

        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h2 class="text-lg font-bold text-slate-900">Invoices & Billing</h2>
                <p class="text-xs text-slate-400 mt-0.5">Pantau tagihan DP, pelunasan, dan status pembayaran klien</p>
            </div>
            <div class="flex items-center gap-1 bg-[#F4F7F6] p-1 rounded-full">
                <button type="button" @click="invoiceFilter = 'all'" :class="invoiceFilter === 'all' ? 'bg-brand-600 text-white shadow-sm' : 'text-slate-600'" class="px-3.5 py-1.5 rounded-full text-xs font-semibold transition">All</button>
                <button type="button" @click="invoiceFilter = 'Sent'" :class="invoiceFilter === 'Sent' ? 'bg-brand-600 text-white shadow-sm' : 'text-slate-600'" class="px-3.5 py-1.5 rounded-full text-xs font-semibold transition">Sent</button>
                <button type="button" @click="invoiceFilter = 'Overdue'" :class="invoiceFilter === 'Overdue' ? 'bg-brand-600 text-white shadow-sm' : 'text-slate-600'" class="px-3.5 py-1.5 rounded-full text-xs font-semibold transition">Overdue</button>
            </div>
        </div>

        <div class="space-y-3">
            <template x-for="inv in outstandingInvoices.filter(i => invoiceFilter === 'all' || i.status === invoiceFilter)" :key="inv.id">
                <div @click="openDetail(inv)"
                     class="p-4 rounded-2xl bg-[#F8FAFA] hover:bg-[#F1F6F5] border border-[#EFF4F2] flex flex-col sm:flex-row sm:items-center justify-between gap-3 transition cursor-pointer">
                    <div class="flex items-center gap-3.5">
                        <img :src="inv.avatar" :alt="inv.name" class="w-11 h-11 rounded-xl object-cover">
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="text-sm font-bold text-slate-900" x-text="inv.name"></span>
                                <span class="text-[11px] font-mono text-slate-400" x-text="'#' + inv.code"></span>
                            </div>
                            <p class="text-xs text-slate-500 mt-0.5" x-text="inv.service + ' · ' + inv.due"></p>
                        </div>
                    </div>
                    <div class="flex items-center justify-between sm:justify-end gap-4">
                        <span class="text-sm font-bold text-slate-900" x-text="inv.amount"></span>
                        <span :class="inv.status === 'Overdue' ? 'bg-rose-50 text-rose-600' : 'bg-amber-50 text-amber-600'"
                              class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-bold">
                            <span class="w-1.5 h-1.5 rounded-full" :class="inv.status === 'Overdue' ? 'bg-rose-500' : 'bg-amber-400'"></span>
                            <span x-text="inv.status"></span>
                        </span>
                    </div>
                </div>
            </template>
        </div>
    </div>

    <!-- ================================================================== -->
    <!-- FULL-PAGE VIEW: BOOKING INFORMATION (MATCHING REFERENCE IMAGE)     -->
    <!-- ================================================================== -->
    <div x-show="$store.nav.tab === 'booking_detail' && activeDetail !== null"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 translate-y-1"
         x-transition:enter-end="opacity-100 translate-y-0"
         class="space-y-5"
         x-cloak>

        <!-- Back Navigation Row -->
        <div class="flex items-center justify-between">
            <button type="button"
                    @click="closeDetail()"
                    class="inline-flex items-center gap-2 group cursor-pointer">
                <span class="w-8 h-8 rounded-full bg-white border border-[#E5ECE9] text-slate-600 group-hover:text-brand-600 flex items-center justify-center shadow-2xs transition">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.4" d="M15 19l-7-7 7-7"/></svg>
                </span>
                <span class="px-4 py-1.5 rounded-full bg-[#ECEFEF] group-hover:bg-[#E2E8E7] text-xs font-semibold text-slate-700 transition">
                    Back
                </span>
            </button>

            <span class="text-xs font-mono font-bold text-[#0284C7] bg-white px-3.5 py-1.5 rounded-full border border-[#E5ECE9]"
                  x-text="'#' + (activeDetail?.code || 'NKB/11/01')"></span>
        </div>

        <!-- 2-Column Asymmetric Layout (Left 8 cols: Info/Client/Service, Right 4 cols: Timeline/Status/Actions) -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 items-start">

            <!-- LEFT COLUMN (8 COLS) -->
            <div class="lg:col-span-8 space-y-5">

                <!-- Card 1: Booking Information -->
                <div class="bg-white rounded-[28px] p-5 sm:p-7 shadow-card border border-[#EDF2F0] space-y-6">
                    <h2 class="text-lg font-bold text-slate-900 tracking-tight">Booking Information</h2>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <!-- Event Date -->
                        <div class="flex items-center gap-3.5">
                            <div class="w-11 h-11 rounded-full bg-[#F4F8F7] border border-[#E8F0EE] text-brand-600 flex items-center justify-center shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            <div>
                                <span class="text-xs font-medium text-slate-400 block">Event Date</span>
                                <span class="text-sm font-bold text-slate-900 mt-0.5 block" x-text="activeDetail?.date"></span>
                            </div>
                        </div>

                        <!-- Event Time -->
                        <div class="flex items-center gap-3.5">
                            <div class="w-11 h-11 rounded-full bg-[#F4F8F7] border border-[#E8F0EE] text-brand-600 flex items-center justify-center shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <div>
                                <span class="text-xs font-medium text-slate-400 block">Event Time</span>
                                <span class="text-sm font-bold text-slate-900 mt-0.5 block" x-text="activeDetail?.time"></span>
                            </div>
                        </div>

                        <!-- Location -->
                        <div class="sm:col-span-2 flex items-center gap-3.5">
                            <div class="w-11 h-11 rounded-full bg-[#F4F8F7] border border-[#E8F0EE] text-brand-600 flex items-center justify-center shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                            </div>
                            <div>
                                <span class="text-xs font-medium text-slate-400 block">Location</span>
                                <span class="text-sm font-bold text-slate-900 mt-0.5 block" x-text="activeDetail?.location"></span>
                            </div>
                        </div>
                    </div>

                    <!-- Notes -->
                    <div class="pt-5 border-t border-[#EFF4F2]">
                        <span class="text-xs font-medium text-slate-400 block mb-1.5">Notes</span>
                        <p class="text-[13px] font-medium text-slate-800 leading-relaxed" x-text="activeDetail?.notes"></p>
                    </div>
                </div>

                <!-- Card 2: Client Information -->
                <div class="bg-white rounded-[28px] p-5 sm:p-7 shadow-card border border-[#EDF2F0] space-y-4">
                    <h2 class="text-lg font-bold text-slate-900 tracking-tight">Client Information</h2>

                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <div class="flex items-center gap-4">
                            <img :src="activeDetail?.avatar" :alt="activeDetail?.name" class="w-14 h-14 rounded-2xl object-cover bg-slate-200 shrink-0">
                            <div class="space-y-1">
                                <h3 class="text-sm font-bold text-slate-900" x-text="activeDetail?.name"></h3>
                                <p class="text-xs text-slate-400 flex items-center gap-1.5">
                                    <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                    <span x-text="activeDetail?.email"></span>
                                </p>
                                <p class="text-xs text-slate-400 flex items-center gap-1.5">
                                    <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                    <span x-text="activeDetail?.phone"></span>
                                </p>
                            </div>
                        </div>

                        <button type="button"
                                @click="$store.nav.setTab('clients')"
                                class="px-4 py-2 rounded-full bg-[#F4F7F6] hover:bg-[#EAF0EE] text-xs font-semibold text-slate-700 transition self-start sm:self-center">
                            View Profile
                        </button>
                    </div>
                </div>

                <!-- Card 3: Service Details -->
                <div class="bg-white rounded-[28px] p-5 sm:p-7 shadow-card border border-[#EDF2F0] space-y-4">
                    <h2 class="text-lg font-bold text-slate-900 tracking-tight">Service Details</h2>

                    <div class="p-4 rounded-2xl bg-[#F8FAFA] border border-[#EFF4F2] space-y-3">
                        <div class="flex items-center justify-between gap-3">
                            <div>
                                <p class="text-sm font-bold text-slate-900" x-text="activeDetail?.service"></p>
                                <p class="text-xs text-slate-400 mt-0.5">Paket Inti Wedding Organizer · Full Crew & Dekorasi</p>
                            </div>
                            <span class="text-sm font-bold text-slate-900 shrink-0" x-text="activeDetail?.amount"></span>
                        </div>
                        <div class="pt-3 border-t border-[#EBF1EF] flex items-center justify-between text-xs">
                            <span class="text-slate-500">Total Package Value</span>
                            <span class="text-base font-extrabold text-brand-600" x-text="activeDetail?.amount"></span>
                        </div>
                    </div>
                </div>

            </div>

            <!-- RIGHT COLUMN (4 COLS: TIMELINE + STATUS + ACTIONS) -->
            <div class="lg:col-span-4 space-y-5">

                <!-- Card 1: Timeline -->
                <div class="bg-white rounded-[28px] p-5 sm:p-6 shadow-card border border-[#EDF2F0] space-y-4">
                    <h2 class="text-lg font-bold text-slate-900 tracking-tight">Timeline</h2>

                    <div class="space-y-4 relative pl-4 before:content-[''] before:absolute before:left-[5px] before:top-2 before:bottom-2 before:w-px before:bg-[#E8EFED]">
                        <div class="relative">
                            <span class="w-2.5 h-2.5 rounded-full bg-purple-500 ring-4 ring-white absolute -left-4 top-1"></span>
                            <p class="text-xs font-bold text-slate-800">Booking Created</p>
                            <p class="text-[11px] text-slate-400 mt-0.5">Nov 01, 2026 · 09:06 AM</p>
                        </div>
                        <div class="relative">
                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 ring-4 ring-white absolute -left-4 top-1"></span>
                            <p class="text-xs font-bold text-slate-800">Payment Verified</p>
                            <p class="text-[11px] text-slate-400 mt-0.5">Nov 01, 2026 · 10:15 AM</p>
                        </div>
                        <div class="relative">
                            <span class="w-2.5 h-2.5 rounded-full bg-brand-600 ring-4 ring-white absolute -left-4 top-1"></span>
                            <p class="text-xs font-bold text-slate-800">Synced to Google Calendar</p>
                            <p class="text-[11px] text-slate-400 mt-0.5">Nov 01, 2026 · 10:16 AM</p>
                        </div>
                    </div>
                </div>

                <!-- Card 2: Status -->
                <div class="bg-white rounded-[28px] p-5 sm:p-6 shadow-card border border-[#EDF2F0] space-y-4">
                    <h2 class="text-lg font-bold text-slate-900 tracking-tight">Status</h2>

                    <div class="space-y-3 text-xs">
                        <div class="flex items-center justify-between">
                            <span class="text-slate-500 font-medium">Booking Status</span>
                            <span :class="{
                                    'bg-[#E6F6EF] text-emerald-700': activeDetail?.bookingStatus === 'Confirmed',
                                    'bg-[#EAF3FF] text-blue-600': activeDetail?.bookingStatus === 'Inquiry',
                                    'bg-[#F3EAFF] text-purple-600': activeDetail?.bookingStatus === 'Complete',
                                    'bg-[#FEECEB] text-rose-600': activeDetail?.bookingStatus === 'Cancelled'
                                  }"
                                  class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-semibold">
                                <span class="w-1.5 h-1.5 rounded-full"
                                      :class="{
                                        'bg-emerald-500': activeDetail?.bookingStatus === 'Confirmed',
                                        'bg-blue-500': activeDetail?.bookingStatus === 'Inquiry',
                                        'bg-purple-500': activeDetail?.bookingStatus === 'Complete',
                                        'bg-rose-500': activeDetail?.bookingStatus === 'Cancelled'
                                      }"></span>
                                <span x-text="activeDetail?.bookingStatus"></span>
                            </span>
                        </div>

                        <div class="flex items-center justify-between">
                            <span class="text-slate-500 font-medium">Invoice Status</span>
                            <template x-if="activeDetail?.invoiceStatus">
                                <span :class="{
                                        'bg-[#E6F6EF] text-emerald-700': activeDetail?.invoiceStatus === 'Paid',
                                        'bg-[#FEF6E0] text-amber-600': activeDetail?.invoiceStatus === 'Sent',
                                        'bg-[#F1F5F9] text-slate-500': activeDetail?.invoiceStatus === 'Draft',
                                        'bg-[#FEECEB] text-rose-600': activeDetail?.invoiceStatus === 'Overdue'
                                      }"
                                      class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-semibold">
                                    <span class="w-1.5 h-1.5 rounded-full"
                                          :class="{
                                            'bg-emerald-500': activeDetail?.invoiceStatus === 'Paid',
                                            'bg-amber-400': activeDetail?.invoiceStatus === 'Sent',
                                            'bg-slate-400': activeDetail?.invoiceStatus === 'Draft',
                                            'bg-rose-500': activeDetail?.invoiceStatus === 'Overdue'
                                          }"></span>
                                    <span x-text="activeDetail?.invoiceStatus"></span>
                                </span>
                            </template>
                            <template x-if="!activeDetail?.invoiceStatus">
                                <span class="text-slate-400">—</span>
                            </template>
                        </div>
                    </div>
                </div>

                <!-- Card 3: Actions -->
                <div class="bg-white rounded-[28px] p-5 sm:p-6 shadow-card border border-[#EDF2F0] space-y-2.5">
                    <h2 class="text-lg font-bold text-slate-900 tracking-tight mb-3">Actions</h2>

                    <button type="button"
                            class="w-full py-2.5 px-4 rounded-full bg-brand-600 hover:bg-brand-700 text-white text-xs font-semibold transition shadow-pill">
                        Chat WhatsApp Client
                    </button>
                    <button type="button"
                            class="w-full py-2.5 px-4 rounded-full bg-[#E6F6EF] hover:bg-[#D5F0E4] text-emerald-700 text-xs font-semibold transition">
                        Record Settlement (Lunas)
                    </button>
                    <button type="button"
                            class="w-full py-2.5 px-4 rounded-full border border-[#E5ECE9] hover:bg-[#F6F8F7] text-slate-700 text-xs font-semibold transition">
                        Download Receipt PDF
                    </button>
                    <button type="button"
                            class="w-full py-2.5 px-4 rounded-full bg-rose-50 hover:bg-rose-100 text-rose-600 text-xs font-semibold transition">
                        Cancel Booking
                    </button>
                </div>

            </div>
        </div>
    </div>

</div>
@endsection
