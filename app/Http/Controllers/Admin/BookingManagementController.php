<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\SyncBookingToGoogleServicesJob;
use App\Models\Booking;
use App\Services\AdminDashboardService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class BookingManagementController extends Controller
{
    public function __construct(
        private readonly AdminDashboardService $dashboardService
    ) {}

    public function index()
    {
        $isOwner = Auth::user()?->isOwner() ?? false;

        return view('admin.bookings.index', $this->dashboardService->getDashboardData($isOwner));
    }

    public function show($id): RedirectResponse
    {
        $booking = Booking::findOrFail($id);

        return redirect()->route('admin.bookings.index', ['booking_id' => $booking->id]);
    }

    public function confirm($id): RedirectResponse
    {
        $booking = Booking::with('addons')->findOrFail($id);
        $booking->update(['status' => 'confirmed']);

        SyncBookingToGoogleServicesJob::dispatch($booking);

        return redirect()->route('admin.bookings.index', ['booking_id' => $booking->id])
            ->with('success', "Reservasi #{$booking->invoice_number} atas nama {$booking->customer_name} berhasil diverifikasi & dikonfirmasi sah!");
    }

    public function reject($id): RedirectResponse
    {
        $booking = Booking::with('addons')->findOrFail($id);

        if ($booking->status === 'completed') {
            return redirect()->route('admin.bookings.index', ['booking_id' => $booking->id])
                ->with('error', __('admin.detail.cancel_completed_blocked'));
        }

        if ($booking->status === 'confirmed' && (float) $booking->remaining_balance <= 0) {
            return redirect()->route('admin.bookings.index', ['booking_id' => $booking->id])
                ->with('error', __('admin.detail.cancel_paid_blocked'));
        }

        $wasConfirmed = $booking->status === 'confirmed';
        $hasPaidDeposit = (float) $booking->amount_paid > 0;
        $dpForfeited = $wasConfirmed && $hasPaidDeposit;

        $booking->update([
            'status'            => 'rejected',
            'dp_forfeited'      => $dpForfeited,
            'remaining_balance' => $wasConfirmed ? 0 : $booking->remaining_balance,
        ]);

        SyncBookingToGoogleServicesJob::dispatch($booking);

        if ($dpForfeited) {
            $paidFormatted = 'Rp ' . number_format((float) $booking->amount_paid, 0, ',', '.');

            return redirect()->route('admin.bookings.index', ['booking_id' => $booking->id])
                ->with('success', "Pesanan #{$booking->invoice_number} ({$booking->customer_name}) dibatalkan. DP sebesar {$paidFormatted} dicatat HANGUS dan slot tanggal acara kembali tersedia.");
        }

        return redirect()->route('admin.bookings.index', ['booking_id' => $booking->id])
            ->with('success', "Reservasi #{$booking->invoice_number} atas nama {$booking->customer_name} telah ditolak.");
    }

    public function complete($id): RedirectResponse
    {
        $booking = Booking::with('addons')->findOrFail($id);

        if ($booking->status !== 'confirmed') {
            return redirect()->route('admin.bookings.index', ['booking_id' => $booking->id])
                ->with('error', __('admin.detail.complete_not_confirmed'));
        }

        $eventEndDate = Carbon::parse($booking->event_end_date ?: $booking->event_date)->startOfDay();
        if (Carbon::today()->lt($eventEndDate)) {
            $formattedDate = $eventEndDate->copy()->locale(app()->getLocale())->isoFormat('D MMM Y');

            return redirect()->route('admin.bookings.index', ['booking_id' => $booking->id])
                ->with('error', __('admin.detail.complete_too_early', ['date' => $formattedDate]));
        }

        $booking->update([
            'status'            => 'completed',
            'payment_type'      => 'lunas',
            'amount_paid'       => $booking->grand_total,
            'remaining_balance' => 0,
        ]);

        SyncBookingToGoogleServicesJob::dispatch($booking);

        return redirect()->route('admin.bookings.index', ['booking_id' => $booking->id])
            ->with('success', "Status acara #{$booking->invoice_number} ({$booking->customer_name}) telah diselesaikan. Pembayaran otomatis tercatat LUNAS 100%.");
    }

    public function settle($id): RedirectResponse
    {
        $booking = Booking::with('addons')->findOrFail($id);
        $booking->update([
            'payment_type'      => 'lunas',
            'amount_paid'       => $booking->grand_total,
            'remaining_balance' => 0,
        ]);

        SyncBookingToGoogleServicesJob::dispatch($booking);

        return redirect()->route('admin.bookings.index', ['booking_id' => $booking->id])
            ->with('success', "Pelunasan reservasi #{$booking->invoice_number} ({$booking->customer_name}) berhasil dicatat. Status pembayaran kini LUNAS 100%.");
    }
}
