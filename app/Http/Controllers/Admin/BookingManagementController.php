<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Addon;
use App\Models\Booking;
use App\Models\Package;
use App\Jobs\SyncBookingToGoogleServicesJob;
use Carbon\Carbon;
use Illuminate\Http\Request;

class BookingManagementController extends Controller
{
    public function index(Request $request)
    {
        Carbon::setLocale('en');

        $allBookings = Booking::with(['package', 'addons'])
            ->orderByDesc('created_at')
            ->get();

        $packages = Package::withCount('bookings')
            ->orderBy('base_price')
            ->get();

        $addons = Addon::orderBy('name')->get();

        // Format semua reservasi untuk UI interaktif (Dashboard, Bookings, Calendar, Clients, Invoice, Detail)
        $bookingsPayload = $allBookings->map(function (Booking $b) {
            $eventDate = Carbon::parse($b->event_date);

            $bookingStatusMap = [
                'pending_verification' => 'Inquiry',
                'confirmed'            => 'Confirmed',
                'completed'            => 'Complete',
                'rejected'             => 'Cancelled',
            ];

            $bookingStatus = $bookingStatusMap[$b->status] ?? 'Inquiry';

            if ($b->status === 'rejected') {
                $invoiceStatus = 'Cancelled';
            } elseif ($b->status === 'completed' || $b->remaining_balance <= 0 || strtolower((string) $b->payment_type) === 'lunas') {
                $invoiceStatus = 'Paid';
            } elseif ($b->status === 'pending_verification') {
                $invoiceStatus = 'Draft';
            } else {
                $invoiceStatus = $eventDate->isPast() ? 'Overdue' : 'Sent';
            }

            $isPdf = $b->payment_proof_path
                ? strtolower(pathinfo($b->payment_proof_path, PATHINFO_EXTENSION)) === 'pdf'
                : false;

            return [
                'id'                      => $b->id,
                'code'                    => $b->invoice_number,
                'name'                    => $b->customer_name,
                'shortName'               => explode(' ', trim($b->customer_name))[0] ?? $b->customer_name,
                'email'                   => $b->customer_email ?: '-',
                'phone'                   => $b->customer_phone,
                'service'                 => $b->package_name,
                'package_price'           => (float) $b->package_price,
                'package_price_formatted' => 'Rp ' . number_format((float) $b->package_price, 0, ',', '.'),
                'addons'                  => $b->addons->map(fn ($a) => [
                    'name'               => $a->addon_name,
                    'quantity'           => (int) $a->quantity,
                    'unit_price'         => 'Rp ' . number_format((float) $a->unit_price, 0, ',', '.'),
                    'subtotal'           => 'Rp ' . number_format((float) $a->subtotal, 0, ',', '.'),
                ])->values()->all(),
                'grand_total'             => (float) $b->grand_total,
                'amount_paid'             => (float) $b->amount_paid,
                'remaining_balance'       => (float) $b->remaining_balance,
                'grand_total_formatted'   => 'Rp ' . number_format((float) $b->grand_total, 0, ',', '.'),
                'amount_paid_formatted'   => 'Rp ' . number_format((float) $b->amount_paid, 0, ',', '.'),
                'remaining_formatted'     => 'Rp ' . number_format((float) $b->remaining_balance, 0, ',', '.'),
                'payment_type'            => strtoupper((string) $b->payment_type),
                'raw_status'              => $b->status,
                'bookingStatus'           => $bookingStatus,
                'invoiceStatus'           => $invoiceStatus,
                'event_date_iso'          => $eventDate->format('Y-m-d'),
                'event_year'              => (int) $eventDate->year,
                'event_month'             => (int) $eventDate->month,
                'event_day'               => (int) $eventDate->day,
                'date'                    => $eventDate->format('l, M d, Y'),
                'date_short'              => $eventDate->format('M d, Y'),
                'time'                    => $b->created_at->format('h:i A'),
                'relative_date'           => $eventDate->diffForHumans(),
                'created_at_label'        => $b->created_at->format('M d, Y · h:i A'),
                'updated_at_label'        => $b->updated_at->format('M d, Y · h:i A'),
                'payment_proof_url'       => $b->payment_proof_path ? asset('storage/' . $b->payment_proof_path) : null,
                'is_proof_pdf'            => $isPdf,
                'synced_to_sheets'        => (bool) $b->synced_to_sheets,
                'google_calendar_synced'  => !empty($b->google_calendar_event_id),
                'whatsapp_url'            => $b->whatsapp_url,
                'receipt_url'             => route('booking.receipt', $b->invoice_number),
                'show_url'                => route('admin.bookings.show', $b->id),
                'confirm_url'             => route('admin.bookings.confirm', $b->id),
                'reject_url'              => route('admin.bookings.reject', $b->id),
                'settle_url'              => route('admin.bookings.settle', $b->id),
                'complete_url'            => route('admin.bookings.complete', $b->id),
                'avatar'                  => 'https://ui-avatars.com/api/?name=' . urlencode($b->customer_name) . '&background=E6F4F1&color=045D56&bold=true&size=128',
            ];
        })->values();

        // Agregasi data Clients langsung dari riwayat reservasi
        $clientsPayload = $allBookings
            ->groupBy(fn (Booking $b) => preg_replace('/[^\d]/', '', (string) $b->customer_phone) ?: strtolower($b->customer_name))
            ->map(function ($group) {
                /** @var Booking $latest */
                $latest = $group->sortByDesc('event_date')->first();
                $totalSpent = (float) $group->whereIn('status', ['confirmed', 'completed'])->sum('grand_total');
                $hasActive = $group->whereIn('status', ['confirmed', 'pending_verification'])->isNotEmpty();
                $hasCompleted = $group->where('status', 'completed')->isNotEmpty();

                return [
                    'id'                   => $latest->id,
                    'name'                 => $latest->customer_name,
                    'email'                => $latest->customer_email ?: '-',
                    'phone'                => $latest->customer_phone,
                    'avatar'               => 'https://ui-avatars.com/api/?name=' . urlencode($latest->customer_name) . '&background=E6F4F1&color=045D56&bold=true&size=128',
                    'package'              => $latest->package_name,
                    'totalBookings'        => $group->count(),
                    'totalSpent'           => $totalSpent,
                    'totalSpentFormatted'  => 'Rp ' . number_format($totalSpent, 0, ',', '.'),
                    'lastEvent'            => Carbon::parse($latest->event_date)->format('M d, Y'),
                    'status'               => $hasActive ? 'Active' : ($hasCompleted ? 'Complete' : 'Lead'),
                    'whatsapp_url'         => $latest->whatsapp_url,
                    'bookingIds'           => $group->pluck('id')->values()->all(),
                ];
            })
            ->values();

        // Paket & Add-on real untuk tab Services
        $packagesPayload = $packages->map(fn (Package $p) => [
            'id'              => $p->id,
            'name'            => $p->name,
            'price'           => (float) $p->base_price,
            'price_formatted' => 'Rp ' . number_format((float) $p->base_price, 0, ',', '.'),
            'description'     => $p->description ?: 'Complete Wedding Organizer package by NK Barokah.',
            'is_active'       => (bool) $p->is_active,
            'booked_count'    => (int) $p->bookings_count,
            'toggle_url'      => route('admin.packages.toggle', $p->id),
        ])->values();

        $addonsPayload = $addons->map(fn (Addon $a) => [
            'id'              => $a->id,
            'name'            => $a->name,
            'unit'            => $a->unit ?: 'unit',
            'price'           => (float) $a->unit_price,
            'price_formatted' => 'Rp ' . number_format((float) $a->unit_price, 0, ',', '.'),
            'is_active'       => (bool) $a->is_active,
            'toggle_url'      => route('admin.addons.toggle', $a->id),
        ])->values();

        // Metrik Statistik Real untuk Owner
        $today = Carbon::today();
        $todayCount = $allBookings->filter(fn ($b) => Carbon::parse($b->event_date)->isSameDay($today) && $b->status !== 'rejected')->count();
        $upcomingCount = $allBookings->filter(fn ($b) => in_array($b->status, ['pending_verification', 'confirmed']) && Carbon::parse($b->event_date)->greaterThanOrEqualTo($today))->count();
        $pendingCount = $allBookings->where('status', 'pending_verification')->count();
        $outstandingBookings = $allBookings->filter(fn ($b) => in_array($b->status, ['pending_verification', 'confirmed']) && (float) $b->remaining_balance > 0);
        $totalCollected = (float) $allBookings->whereIn('status', ['confirmed', 'completed'])->sum('amount_paid');
        $totalOutstanding = (float) $outstandingBookings->sum('remaining_balance');
        $totalBilled = (float) $allBookings->where('status', '!=', 'rejected')->sum('grand_total');

        $topPackage = $allBookings
            ->where('status', '!=', 'rejected')
            ->groupBy('package_name')
            ->sortByDesc(fn ($g) => $g->count())
            ->keys()
            ->first() ?? 'Gold Royal Package';

        $metrics = [
            'today_schedule'      => $todayCount,
            'upcoming_count'      => $upcomingCount,
            'pending_count'       => $pendingCount,
            'outstanding_count'   => $outstandingBookings->count(),
            'total_bookings'      => $allBookings->count(),
            'revenue'             => $totalCollected,
            'revenue_short'       => $totalCollected >= 1000000
                ? 'Rp ' . number_format($totalCollected / 1000000, $totalCollected % 1000000 === 0 ? 0 : 1, ',', '.') . ' Jt'
                : 'Rp ' . number_format($totalCollected, 0, ',', '.'),
            'total_collected_fmt' => 'Rp ' . number_format($totalCollected, 0, ',', '.'),
            'total_outstanding_fmt' => 'Rp ' . number_format($totalOutstanding, 0, ',', '.'),
            'total_billed_fmt'    => 'Rp ' . number_format($totalBilled, 0, ',', '.'),
            'top_package'         => $topPackage,
        ];

        // Bulan awal kalender selalu default ke bulan & tahun sekarang (real-time)
        $initialCalYear = (int) $today->year;
        $initialCalMonth = (int) $today->month;

        return view('admin.bookings.index', compact(
            'bookingsPayload',
            'clientsPayload',
            'packagesPayload',
            'addonsPayload',
            'metrics',
            'initialCalYear',
            'initialCalMonth'
        ));
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

        SyncBookingToGoogleServicesJob::dispatch($booking);

        return back()->with('success', "Reservasi #{$booking->invoice_number} atas nama {$booking->customer_name} berhasil diverifikasi & dikonfirmasi sah!");
    }

    public function reject($id)
    {
        $booking = Booking::with('addons')->findOrFail($id);
        $booking->update(['status' => 'rejected']);

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

        SyncBookingToGoogleServicesJob::dispatch($booking);

        return back()->with('success', "Pelunasan reservasi #{$booking->invoice_number} ({$booking->customer_name}) berhasil dicatat. Status pembayaran kini LUNAS 100%.");
    }
}
