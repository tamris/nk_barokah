    <!-- ================================================================== -->
    <!-- FULL-PAGE VIEW: BOOKING INFORMATION (REAL DATA + PAYMENT PROOF)    -->
    <!-- ================================================================== -->
    <div x-show="$store.nav.tab === 'booking_detail' && activeDetail !== null"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 translate-y-1"
         x-transition:enter-end="opacity-100 translate-y-0"
         class="space-y-5"
         x-cloak>

        @include('admin.bookings.partials.detail.skeleton')

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

                @include('admin.bookings.partials.detail.card-booking-info')

                @include('admin.bookings.partials.detail.card-client-info')

                @include('admin.bookings.partials.detail.card-logistics')

                @include('admin.bookings.partials.detail.card-operations')

                @include('admin.bookings.partials.detail.card-vendors')

                @include('admin.bookings.partials.detail.card-services')

                @include('admin.bookings.partials.detail.card-payment-proof')

            </div>

            @include('admin.bookings.partials.detail.sidebar')
        </div>
        </div>
    </div>

