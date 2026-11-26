<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\SyncBookingToGoogleServicesJob;
use App\Models\Addon;
use App\Models\Booking;
use App\Models\Package;
use App\Models\Setting;
use App\Services\AdminDashboardService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

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

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'event_type'         => ['required', 'string', 'in:pernikahan,khitanan,tasyakuran,instansi,sewa_alat'],
            'customer_name'      => ['required', 'string', 'max:150'],
            'customer_phone'     => ['required', 'string', 'max:25'],
            'customer_email'     => ['nullable', 'email', 'max:100'],
            'child_name'         => ['nullable', 'string', 'max:150'],
            'event_date'         => ['required', 'date'],
            'event_end_date'     => ['nullable', 'date', 'after_or_equal:event_date'],
            'package_id'         => ['nullable', 'integer', 'exists:packages,id'],
            'addons'             => ['nullable', 'array'],
            'addons.*.id'        => ['required_with:addons', 'integer', 'exists:addons,id'],
            'addons.*.qty'       => ['required_with:addons', 'integer', 'min:1'],
            'venue_address'      => ['nullable', 'string', 'max:500'],
            'address_landmark'   => ['nullable', 'string', 'max:255'],
            'tent_color_theme'   => ['nullable', 'string', 'max:100'],
            'custom_tent_size'   => ['nullable', 'string', 'max:150'],
            'job_source'         => ['nullable', 'string', 'max:100'],
            'operational_notes'  => ['nullable', 'string', 'max:1000'],
            'transport_fee'      => ['nullable', 'numeric', 'min:0'],
            'owner_discount'     => ['nullable', 'numeric', 'min:0'],
            'payment_type'       => ['required', 'string', 'in:dp,lunas'],
            'nominal_dp'         => ['nullable', 'numeric', 'min:0'],
            'status'             => ['required', 'string', 'in:confirmed,pending_verification'],
            'survey_requested'   => ['nullable', 'boolean'],
            'survey_date'        => ['nullable', 'date'],
            'survey_time_slot'   => ['nullable', 'string', 'in:morning,afternoon,flexible'],
            'payment_proof'      => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp,pdf', 'max:5120'],
        ]);

        $isSewaAlat = $validated['event_type'] === 'sewa_alat';

        if ($isSewaAlat) {
            $package = Package::where('slug', 'custom-alat-saja')
                ->orWhere('base_price', 0)
                ->first();
            if (!$package) {
                $package = Package::create([
                    'name'        => 'Custom',
                    'slug'        => 'custom-alat-saja',
                    'base_price'  => 0,
                    'description' => 'Sewa alat pesta lepas tanpa konstruksi tenda.',
                    'is_active'   => true,
                ]);
            }
        } else {
            if (empty($validated['package_id'])) {
                throw ValidationException::withMessages([
                    'package_id' => __('admin.manual_booking.package_required'),
                ]);
            }
            $package = Package::findOrFail($validated['package_id']);
        }

        // Hitung Add-ons secara server-side
        $addonsPayload = [];
        $addonTotalPrice = 0.0;
        if (!empty($validated['addons'])) {
            foreach ($validated['addons'] as $item) {
                $qty = (int) ($item['qty'] ?? 0);
                if ($qty > 0) {
                    $addon = Addon::findOrFail($item['id']);
                    $subtotal = (float) $addon->unit_price * $qty;
                    $addonTotalPrice += $subtotal;
                    $addonsPayload[] = [
                        'addon_id'      => $addon->id,
                        'addon_name'    => $addon->name,
                        'unit_price'    => $addon->unit_price,
                        'quantity'      => $qty,
                        'subtotal'      => $subtotal,
                        'added_by_role' => 'owner',
                    ];
                }
            }
        }

        if ($isSewaAlat && empty($addonsPayload)) {
            throw ValidationException::withMessages([
                'addons' => __('admin.manual_booking.addons_required_sewa_alat'),
            ]);
        }

        $booking = DB::transaction(function () use ($request, $validated, $package, $isSewaAlat, $addonsPayload, $addonTotalPrice) {
            $eventDate = $validated['event_date'];
            $eventEndDate = !empty($validated['event_end_date']) ? $validated['event_end_date'] : $eventDate;
            $start = Carbon::parse($eventDate)->startOfDay();
            $end = Carbon::parse($eventEndDate)->startOfDay();
            $durationDays = max(1, (int) ($start->diffInDays($end) + 1));

            $standardDays = (int) Setting::get('standard_package_max_days', 2);
            $extraDays = max(0, $durationDays - $standardDays);
            $extraDayChargeRate = Setting::getExtraDayChargeRateFloat();
            $additionalDayCharge = $extraDays * $extraDayChargeRate * (float) $package->base_price;

            $transportFee = max(0.0, (float) ($validated['transport_fee'] ?? 0));
            $grossTotal = (float) $package->base_price + $addonTotalPrice + $additionalDayCharge + $transportFee;
            $ownerDiscount = min($grossTotal, max(0.0, (float) ($validated['owner_discount'] ?? 0)));
            $grandTotal = max(0.0, $grossTotal - $ownerDiscount);

            if ($validated['payment_type'] === 'lunas') {
                $amountPaid = $grandTotal;
                $remaining = 0.0;
            } else {
                $amountPaid = min($grandTotal, max(0.0, (float) ($validated['nominal_dp'] ?? 0)));
                $remaining = max(0.0, $grandTotal - $amountPaid);
                if ($grandTotal > 0 && $remaining <= 0) {
                    $validated['payment_type'] = 'lunas';
                }
            }

            $proofPath = $request->hasFile('payment_proof')
                ? $request->file('payment_proof')->store('proofs/' . date('Y/m'), 'public')
                : '';

            $currentMonth = date('m');
            $countThisMonth = Booking::whereYear('created_at', date('Y'))
                ->whereMonth('created_at', date('m'))
                ->count() + 1;

            do {
                $invoiceNumber = 'NKB/' . $currentMonth . '/' . str_pad((string) $countThisMonth, 2, '0', STR_PAD_LEFT);
                $countThisMonth++;
            } while (Booking::where('invoice_number', $invoiceNumber)->exists());

            $surveyRequested = !$isSewaAlat && $request->boolean('survey_requested');

            $booking = Booking::create([
                'invoice_number'        => $invoiceNumber,
                'customer_name'         => strtoupper(trim($validated['customer_name'])),
                'customer_phone'        => trim($validated['customer_phone']),
                'customer_email'        => $validated['customer_email'] ?? null,
                'event_type'            => $validated['event_type'],
                'child_name'            => $validated['event_type'] === 'khitanan' ? ($validated['child_name'] ?? null) : null,
                'event_date'            => $eventDate,
                'event_end_date'        => $eventEndDate,
                'duration_days'         => $durationDays,
                'package_id'            => $package->id,
                'package_name'          => $package->name,
                'package_price'         => $package->base_price,
                'additional_day_charge' => $additionalDayCharge,
                'addon_total_price'     => $addonTotalPrice,
                'transport_fee'         => $transportFee,
                'discount_amount'       => 0,
                'owner_discount'        => $ownerDiscount,
                'grand_total'           => $grandTotal,
                'amount_paid'           => $amountPaid,
                'remaining_balance'     => $remaining,
                'payment_type'          => $validated['payment_type'],
                'payment_proof_path'    => $proofPath,
                'status'                => $validated['status'],
                'venue_address'         => $validated['venue_address'] ?? null,
                'address_landmark'      => $validated['address_landmark'] ?? null,
                'tent_color_theme'      => $isSewaAlat ? null : ($validated['tent_color_theme'] ?? null),
                'custom_tent_size'      => $isSewaAlat ? null : ($validated['custom_tent_size'] ?? null),
                'job_source'            => $validated['job_source'] ?? 'Manual Admin',
                'operational_notes'     => $validated['operational_notes'] ?? null,
                'survey_requested'      => $surveyRequested,
                'survey_date'           => $surveyRequested ? ($validated['survey_date'] ?? null) : null,
                'survey_time_slot'      => $surveyRequested ? ($validated['survey_time_slot'] ?? 'flexible') : null,
                'survey_status'         => $surveyRequested ? 'scheduled' : 'none',
            ]);

            if (!empty($addonsPayload)) {
                $booking->addons()->createMany($addonsPayload);
            }

            return $booking;
        });

        SyncBookingToGoogleServicesJob::dispatch($booking);

        return redirect()->route('admin.bookings.index', ['booking_id' => $booking->id])
            ->with('success', __('admin.manual_booking.created_success', [
                'invoice' => $booking->invoice_number,
                'name'    => $booking->customer_name,
            ]));
    }

    public function update(Request $request, $id): RedirectResponse
    {
        $booking = Booking::with('addons')->findOrFail($id);

        if (in_array($booking->status, ['completed', 'rejected'], true)) {
            return redirect()->route('admin.bookings.index', ['booking_id' => $booking->id])
                ->with('error', __('admin.edit_booking.locked_status'));
        }

        $validated = $request->validate([
            'event_type'       => ['required', 'string', 'in:pernikahan,khitanan,tasyakuran,instansi,sewa_alat'],
            'customer_name'    => ['required', 'string', 'max:150'],
            'customer_phone'   => ['required', 'string', 'max:25'],
            'customer_email'   => ['nullable', 'email', 'max:100'],
            'child_name'       => ['nullable', 'string', 'max:150'],
            'event_date'       => ['required', 'date'],
            'event_end_date'   => ['nullable', 'date', 'after_or_equal:event_date'],
            'package_id'       => ['nullable', 'integer', 'exists:packages,id'],
            'venue_address'    => ['nullable', 'string', 'max:500'],
            'address_landmark' => ['nullable', 'string', 'max:255'],
        ]);

        $isSewaAlat = $validated['event_type'] === 'sewa_alat';

        if ($isSewaAlat) {
            $package = Package::where('slug', 'custom-alat-saja')
                ->orWhere('base_price', 0)
                ->first();
            if (!$package) {
                $package = Package::create([
                    'name'        => 'Custom',
                    'slug'        => 'custom-alat-saja',
                    'base_price'  => 0,
                    'description' => 'Sewa alat pesta lepas tanpa konstruksi tenda.',
                    'is_active'   => true,
                ]);
            }
        } else {
            if (empty($validated['package_id'])) {
                throw ValidationException::withMessages([
                    'package_id' => __('admin.manual_booking.package_required'),
                ]);
            }
            $package = Package::findOrFail($validated['package_id']);
        }

        DB::transaction(function () use ($booking, $validated, $package, $isSewaAlat) {
            $oldStart = Carbon::parse($booking->event_date)->startOfDay();
            $newEventDate = $validated['event_date'];
            $newEventEndDate = !empty($validated['event_end_date']) ? $validated['event_end_date'] : $newEventDate;
            $newStart = Carbon::parse($newEventDate)->startOfDay();
            $newEnd = Carbon::parse($newEventEndDate)->startOfDay();
            $durationDays = max(1, (int) ($newStart->diffInDays($newEnd) + 1));

            // Keep custom setup_date aligned by shifting with the same day delta when rescheduled
            $setupDate = $booking->setup_date;
            if ($setupDate && !$oldStart->isSameDay($newStart)) {
                $dayShift = (int) $oldStart->diffInDays($newStart, false);
                $setupDate = Carbon::parse($setupDate)->addDays($dayShift)->format('Y-m-d');
            }

            $standardDays = (int) Setting::get('standard_package_max_days', 2);
            $extraDays = max(0, $durationDays - $standardDays);
            $extraDayChargeRate = Setting::getExtraDayChargeRateFloat();
            $additionalDayCharge = $extraDays * $extraDayChargeRate * (float) $package->base_price;

            $booking->update([
                'event_type'            => $validated['event_type'],
                'customer_name'         => strtoupper(trim($validated['customer_name'])),
                'customer_phone'        => trim($validated['customer_phone']),
                'customer_email'        => $validated['customer_email'] ?? null,
                'child_name'            => $validated['event_type'] === 'khitanan' ? ($validated['child_name'] ?? null) : null,
                'event_date'            => $newEventDate,
                'event_end_date'        => $newEventEndDate,
                'duration_days'         => $durationDays,
                'setup_date'            => $setupDate,
                'package_id'            => $package->id,
                'package_name'          => $package->name,
                'package_price'         => $package->base_price,
                'additional_day_charge' => $additionalDayCharge,
                'venue_address'         => $validated['venue_address'] ?? null,
                'address_landmark'      => $validated['address_landmark'] ?? null,
                'tent_color_theme'      => $isSewaAlat ? null : $booking->tent_color_theme,
                'custom_tent_size'      => $isSewaAlat ? null : $booking->custom_tent_size,
            ]);

            $booking->recalculateTotals();
            $booking->refresh();

            $newPaymentType = ((float) $booking->remaining_balance > 0) ? 'dp' : 'lunas';
            if ($booking->payment_type !== $newPaymentType) {
                $booking->update(['payment_type' => $newPaymentType]);
            }
        });

        SyncBookingToGoogleServicesJob::dispatch($booking->fresh('addons'));

        return redirect()->route('admin.bookings.index', ['booking_id' => $booking->id])
            ->with('success', __('admin.edit_booking.updated_success', [
                'invoice' => $booking->invoice_number,
                'name'    => $booking->fresh()->customer_name,
            ]));
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
