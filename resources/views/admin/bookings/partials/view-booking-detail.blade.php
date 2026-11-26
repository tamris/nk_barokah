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
                    {{ __('admin.nav.back') }}
                </span>
            </button>

            <div class="flex items-center gap-2">
                @if($isOwner ?? false)
                <template x-if="activeDetail && (activeDetail.raw_status === 'pending_verification' || activeDetail.raw_status === 'confirmed')">
                    <button type="button"
                            @click="openEditBookingModal(activeDetail)"
                            class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full bg-white hover:bg-[#F4F7F6] text-slate-700 border border-[#E5ECE9] text-xs font-semibold transition shadow-2xs cursor-pointer active:scale-95">
                        <svg class="w-3.5 h-3.5 text-brand-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.1" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        <span>{{ __('admin.edit_booking.btn_label') }}</span>
                    </button>
                </template>
                @endif

                <span class="text-xs font-mono font-bold text-[#0284C7] bg-white px-3.5 py-1.5 rounded-full border border-[#E5ECE9]"
                      x-text="'#' + (activeDetail?.code || '')"></span>
            </div>
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

