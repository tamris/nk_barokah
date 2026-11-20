<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Jobs\SyncBookingToGoogleServicesJob;
use Illuminate\Http\Request;

class BookingManagementController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->query('status', 'all');
        $search = $request->query('search');

        $query = Booking::with(['package', 'addons'])->latest();

        if ($status !== 'all' && in_array($status, ['pending_verification', 'confirmed', 'rejected', 'completed'])) {
            $query->where('status', $status);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('invoice_number', 'like', "%{$search}%")
                  ->orWhere('customer_name', 'like', "%{$search}%")
                  ->orWhere('customer_phone', 'like', "%{$search}%");
            });
        }

        $bookings = $query->paginate(10)->withQueryString();

        // Metrics statistik cepat untuk Owner
        $metrics = [
            'total'     => Booking::count(),
            'pending'   => Booking::where('status', 'pending_verification')->count(),
            'confirmed' => Booking::where('status', 'confirmed')->count(),
            'revenue'   => Booking::whereIn('status', ['confirmed', 'completed'])->sum('amount_paid'),
        ];

        return view('admin.bookings.index', compact('bookings', 'metrics', 'status', 'search'));
    }

    public function show($id)
    {
        $booking = Booking::with(['package', 'addons'])->findOrFail($id);
        $waConfirmUrl = $booking->whatsapp_url;

        return view('admin.bookings.show', compact('booking', 'waConfirmUrl'));
    }

    public function confirm($id)
    {
        $booking = Booking::with('addons')->findOrFail($id);
        $booking->update(['status' => 'confirmed']);

        // Sinkronisasi status ke Google Calendar & Sheets
        SyncBookingToGoogleServicesJob::dispatch($booking);

        return back()->with('success', "Reservasi #{$booking->invoice_number} atas nama {$booking->customer_name} berhasil diverifikasi & dikonfirmasi sah!");
    }

    public function reject($id)
    {
        $booking = Booking::with('addons')->findOrFail($id);
        $booking->update(['status' => 'rejected']);

        // Sinkronisasi status pembatalan ke Google Calendar & Sheets
        SyncBookingToGoogleServicesJob::dispatch($booking);

        return back()->with('success', "Reservasi #{$booking->invoice_number} atas nama {$booking->customer_name} telah ditolak.");
    }

    public function complete($id)
    {
        $booking = Booking::with('addons')->findOrFail($id);
        $booking->update([
            'status'            => 'completed',
            'payment_type'      => 'lunas',
            'amount_paid'       => $booking->grand_total,
            'remaining_balance' => 0,
        ]);

        // Sinkronisasi status selesai ke Google Calendar & Sheets
        SyncBookingToGoogleServicesJob::dispatch($booking);

        return back()->with('success', "Status acara #{$booking->invoice_number} ({$booking->customer_name}) telah diselesaikan. Pembayaran otomatis tercatat LUNAS 100%.");
    }

    public function settle($id)
    {
        $booking = Booking::with('addons')->findOrFail($id);
        $booking->update([
            'payment_type'      => 'lunas',
            'amount_paid'       => $booking->grand_total,
            'remaining_balance' => 0,
        ]);

        // Sinkronisasi status pelunasan ke Google Calendar & Sheets
        SyncBookingToGoogleServicesJob::dispatch($booking);

        return back()->with('success', "Pelunasan reservasi #{$booking->invoice_number} ({$booking->customer_name}) berhasil dicatat. Status pembayaran kini LUNAS 100%.");
    }
}

