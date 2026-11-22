<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Addon;
use App\Models\Booking;
use App\Models\Package;
use App\Models\Setting;
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

        $today = Carbon::today();

        // Format semua reservasi untuk UI interaktif (Dashboard, Bookings, Calendar, Clients, Invoice, Detail)
        $bookingsPayload = $allBookings->map(function (Booking $b) use ($today) {
            $eventDate = Carbon::parse($b->event_date);
            $eventEndDate = $b->event_end_date ? Carbon::parse($b->event_end_date) : $eventDate;
            $durationDays = max(1, (int) ($b->duration_days ?: ($eventDate->diffInDays($eventEndDate) + 1)));

            if ($durationDays > 1 && !$eventDate->isSameDay($eventEndDate)) {
                $dateFormatted = $eventDate->format('D, M d, Y') . ' – ' . $eventEndDate->format('D, M d, Y') . " ({$durationDays} Hari)";
                $dateShortFormatted = $eventDate->format('M d') . ' – ' . $eventEndDate->format('M d, Y');
            } else {
                $dateFormatted = $eventDate->format('l, M d, Y');
                $dateShortFormatted = $eventDate->format('M d, Y');
            }

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

            $subtotalBeforeDiscount = (float) $b->package_price + (float) $b->addon_total_price + (float) $b->additional_day_charge;
            $discountAmount = (float) ($b->discount_amount ?? 0);
            $discountPercent = (float) ($b->discount_percent ?? 0);
            if ($discountPercent <= 0 && $discountAmount > 0 && $subtotalBeforeDiscount > 0) {
                $discountPercent = round(($discountAmount / $subtotalBeforeDiscount) * 100, 1);
            }

            $eventStart = $eventDate->copy()->startOfDay();
            $todayStart = $today->copy()->startOfDay();
            $daysDiff = (int) $todayStart->diffInDays($eventStart, false);

            if ($eventDate->isToday()) {
                $relativeDate = 'Today';
            } elseif ($eventEndDate && $today->between($eventStart, $eventEndDate->copy()->endOfDay())) {
                $relativeDate = 'Ongoing';
            } elseif ($daysDiff === 1) {
                $relativeDate = '1 day from now';
            } elseif ($daysDiff > 1) {
                if ($daysDiff < 14) {
                    $relativeDate = "{$daysDiff} days from now";
                } elseif ($daysDiff < 30) {
                    $weeks = (int) round($daysDiff / 7);
                    $relativeDate = "{$weeks} " . ($weeks === 1 ? 'week' : 'weeks') . " from now";
                } else {
                    $months = (int) round($daysDiff / 30.416);
                    if ($months < 12) {
                        $relativeDate = "{$months} " . ($months === 1 ? 'month' : 'months') . " from now";
                    } else {
                        $years = (int) round($daysDiff / 365);
                        $relativeDate = "{$years} " . ($years === 1 ? 'year' : 'years') . " from now";
                    }
                }
            } else {
                $pastDays = abs($daysDiff);
                if ($pastDays === 1) {
                    $relativeDate = '1 day ago';
                } elseif ($pastDays < 14) {
                    $relativeDate = "{$pastDays} days ago";
                } elseif ($pastDays < 30) {
                    $weeks = (int) round($pastDays / 7);
                    $relativeDate = "{$weeks} " . ($weeks === 1 ? 'week' : 'weeks') . " ago";
                } else {
                    $months = (int) round($pastDays / 30.416);
                    if ($months < 12) {
                        $relativeDate = "{$months} " . ($months === 1 ? 'month' : 'months') . " ago";
                    } else {
                        $years = (int) round($pastDays / 365);
                        $relativeDate = "{$years} " . ($years === 1 ? 'year' : 'years') . " ago";
                    }
                }
            }

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
                'subtotal_before_discount'=> $subtotalBeforeDiscount,
                'subtotal_before_discount_formatted' => 'Rp ' . number_format($subtotalBeforeDiscount, 0, ',', '.'),
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
                'event_end_date_iso'      => $eventEndDate->format('Y-m-d'),
                'duration_days'           => $durationDays,
                'additional_day_charge'   => (float) $b->additional_day_charge,
                'additional_day_charge_formatted' => 'Rp ' . number_format((float) $b->additional_day_charge, 0, ',', '.'),
                'event_year'              => (int) $eventDate->year,
                'event_month'             => (int) $eventDate->month,
                'event_day'               => (int) $eventDate->day,
                'date'                    => $dateFormatted,
                'date_short'              => $dateShortFormatted,
                'formatted_event_date'    => $b->formatted_event_date,
                'time'                    => $b->created_at->format('h:i A'),
                'relative_date'           => $relativeDate,
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
                'discount_url'            => route('admin.bookings.discount', $b->id),
                'venue_address'           => $b->venue_address ?: '-',
                'address_landmark'        => $b->address_landmark ?: '-',
                'parents_name'            => $b->parents_name ?: '-',
                'instagram_account'       => $b->instagram_account ?: '-',
                'event_time'              => $b->event_time ?: '-',
                'vendor_name'             => $b->vendor_name ?: '',
                'discount_amount'         => $discountAmount,
                'discount_percent'        => $discountPercent,
                'discount_amount_formatted' => 'Rp ' . number_format($discountAmount, 0, ',', '.'),
                'vendor_mua'              => $b->vendor_mua ?: '-',
                'vendor_decor'            => $b->vendor_decor ?: '-',
                'vendor_photo_video'      => $b->vendor_photo_video ?: '-',
                'vendor_mc_wo'            => $b->vendor_mc_wo ?: '-',
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
        $todayCount = $allBookings->filter(function ($b) use ($today) {
            if ($b->status === 'rejected') return false;
            $start = Carbon::parse($b->event_date)->startOfDay();
            $end = ($b->event_end_date ? Carbon::parse($b->event_end_date) : $start)->endOfDay();
            return $today->between($start, $end);
        })->count();

        $upcomingCount = $allBookings->filter(function ($b) use ($today) {
            if (!in_array($b->status, ['pending_verification', 'confirmed'])) return false;
            $start = Carbon::parse($b->event_date)->startOfDay();
            return $start->greaterThan($today);
        })->count();
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
        $settings = Setting::getAll();

        return view('admin.bookings.index', compact(
            'bookingsPayload',
            'clientsPayload',
            'packagesPayload',
            'addonsPayload',
            'metrics',
            'initialCalYear',
            'initialCalMonth',
            'settings'
        ));
    }

    public function show($id)
    {
        $booking = Booking::findOrFail($id);

        return redirect()->route('admin.bookings.index', ['booking_id' => $booking->id]);
    }

    public function confirm($id)
    {
        $booking = Booking::with('addons')->findOrFail($id);
        $booking->update(['status' => 'confirmed']);

        SyncBookingToGoogleServicesJob::dispatch($booking);

        return redirect()->route('admin.bookings.index', ['booking_id' => $booking->id])
            ->with('success', "Reservasi #{$booking->invoice_number} atas nama {$booking->customer_name} berhasil diverifikasi & dikonfirmasi sah!");
    }

    public function reject($id)
    {
        $booking = Booking::with('addons')->findOrFail($id);
        $booking->update(['status' => 'rejected']);

        SyncBookingToGoogleServicesJob::dispatch($booking);

        return redirect()->route('admin.bookings.index', ['booking_id' => $booking->id])
            ->with('success', "Reservasi #{$booking->invoice_number} atas nama {$booking->customer_name} telah ditolak.");
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

        return redirect()->route('admin.bookings.index', ['booking_id' => $booking->id])
            ->with('success', "Status acara #{$booking->invoice_number} ({$booking->customer_name}) telah diselesaikan. Pembayaran otomatis tercatat LUNAS 100%.");
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

        return redirect()->route('admin.bookings.index', ['booking_id' => $booking->id])
            ->with('success', "Pelunasan reservasi #{$booking->invoice_number} ({$booking->customer_name}) berhasil dicatat. Status pembayaran kini LUNAS 100%.");
    }

    public function updateDiscount(Request $request, $id)
    {
        $booking = Booking::with('addons')->findOrFail($id);

        if ($request->has('discount_amount') && !$request->has('discount_percent')) {
            $cleanDiscount = preg_replace('/[^\d]/', '', (string) $request->input('discount_amount'));
            $request->merge(['discount_amount' => $cleanDiscount !== '' ? (float) $cleanDiscount : 0]);
        }

        $validated = $request->validate([
            'discount_percent'  => ['nullable', 'numeric', 'min:0', 'max:100'],
            'discount_amount'   => ['nullable', 'numeric', 'min:0'],
            'vendor_name'       => ['nullable', 'string', 'max:150'],
            'venue_address'     => ['nullable', 'string', 'max:500'],
            'address_landmark'  => ['nullable', 'string', 'max:255'],
            'event_time'        => ['nullable', 'string', 'max:100'],
            'parents_name'      => ['nullable', 'string', 'max:255'],
            'instagram_account' => ['nullable', 'string', 'max:100'],
            'vendor_mua'        => ['nullable', 'string', 'max:150'],
            'vendor_decor'      => ['nullable', 'string', 'max:150'],
            'vendor_photo_video'=> ['nullable', 'string', 'max:150'],
            'vendor_mc_wo'      => ['nullable', 'string', 'max:150'],
        ]);

        // Subtotal kotor sebelum diskon (paket + addon + extra charge)
        $basePrice = (float) $booking->package_price;
        $addonTotal = (float) $booking->addon_total_price;
        $extraCharge = (float) $booking->additional_day_charge;
        $subtotal = $basePrice + $addonTotal + $extraCharge;

        if ($request->filled('discount_percent')) {
            $percent = max(0, min(100, (float) $validated['discount_percent']));
            $rawDiscount = round(($percent / 100) * $subtotal);
        } elseif ($request->filled('discount_amount')) {
            $rawDiscount = (float) ($validated['discount_amount'] ?? 0);
            $percent = $subtotal > 0 ? round(($rawDiscount / $subtotal) * 100, 2) : 0;
        } else {
            $rawDiscount = 0;
            $percent = 0;
        }

        $discount = min($rawDiscount, $subtotal);
        $newGrandTotal = max(0, $subtotal - $discount);

        if (strtolower((string) $booking->payment_type) === 'lunas' || $booking->status === 'completed') {
            $newAmountPaid = $newGrandTotal;
            $newRemaining = 0;
        } else {
            $newAmountPaid = (float) $booking->amount_paid;
            $newRemaining = max(0, $newGrandTotal - $newAmountPaid);
        }

        $booking->update([
            'discount_percent'  => $percent,
            'discount_amount'   => $discount,
            'grand_total'       => $newGrandTotal,
            'amount_paid'       => $newAmountPaid,
            'remaining_balance' => $newRemaining,
            'vendor_name'       => $validated['vendor_name'] ?? $booking->vendor_name,
            'venue_address'     => $validated['venue_address'] ?? $booking->venue_address,
            'address_landmark'  => $validated['address_landmark'] ?? $booking->address_landmark,
            'event_time'        => $validated['event_time'] ?? $booking->event_time,
            'parents_name'      => $validated['parents_name'] ?? $booking->parents_name,
            'instagram_account' => $validated['instagram_account'] ?? $booking->instagram_account,
            'vendor_mua'        => $validated['vendor_mua'] ?? $booking->vendor_mua,
            'vendor_decor'      => $validated['vendor_decor'] ?? $booking->vendor_decor,
            'vendor_photo_video'=> $validated['vendor_photo_video'] ?? $booking->vendor_photo_video,
            'vendor_mc_wo'      => $validated['vendor_mc_wo'] ?? $booking->vendor_mc_wo,
        ]);

        SyncBookingToGoogleServicesJob::dispatch($booking);

        return redirect()->route('admin.bookings.index', ['booking_id' => $booking->id])
            ->with('success', "Diskon vendor ({$percent}%) & tagihan pesanan #{$booking->invoice_number} berhasil diperbarui!");
    }

    public function syncAllGoogle()
    {
        $bookings = Booking::with('addons')
            ->whereIn('status', ['confirmed', 'completed', 'pending_verification'])
            ->get();

        foreach ($bookings as $b) {
            SyncBookingToGoogleServicesJob::dispatch($b);
        }

        return back()->with('success', "Proses sinkronisasi seluruh reservasi ({$bookings->count()} data) ke Google Sheets & Google Calendar telah berhasil dijadwalkan!");
    }

    public function updateBusinessRules(Request $request)
    {
        if ($request->has('min_dp_amount')) {
            $cleanMinDp = preg_replace('/[^\d]/', '', (string) $request->input('min_dp_amount'));
            $request->merge(['min_dp_amount' => $cleanMinDp !== '' ? (float) $cleanMinDp : 0]);
        }

        $validated = $request->validate([
            'standard_package_max_days' => ['required', 'integer', 'min:1', 'max:14'],
            'extra_day_charge_rate'     => ['required', 'numeric', 'min:0', 'max:100'],
            'min_dp_amount'             => ['required', 'numeric', 'min:0'],
            'max_events_per_day'        => ['required', 'integer', 'min:1', 'max:10'],
            'admin_whatsapp'            => ['required', 'string', 'max:25'],
        ]);

        foreach ($validated as $key => $value) {
            Setting::set($key, $value);
        }

        return back()->with('success', 'Aturan operasional & kebijakan acara berhasil diperbarui!');
    }

    public function updateBankAccounts(Request $request)
    {
        $validated = $request->validate([
            'bca_account_number' => ['required', 'string', 'max:30'],
            'bca_account_name'   => ['required', 'string', 'max:100'],
            'bri_account_number' => ['required', 'string', 'max:30'],
            'bri_account_name'   => ['required', 'string', 'max:100'],
        ]);

        foreach ($validated as $key => $value) {
            Setting::set($key, $value);
        }

        return back()->with('success', 'Data rekening resmi transfer bank berhasil diperbarui!');
    }

    public function updateGoogleSettings(Request $request)
    {
        $validated = $request->validate([
            'google_sheet_id'    => ['nullable', 'string', 'max:255'],
            'google_calendar_id' => ['nullable', 'string', 'max:255'],
        ]);

        Setting::set('google_sheet_id', trim($validated['google_sheet_id'] ?? ''));
        Setting::set('google_calendar_id', trim($validated['google_calendar_id'] ?? ''));

        return back()->with('success', 'ID Google Sheets & Google Calendar berhasil diperbarui!');
    }
}
