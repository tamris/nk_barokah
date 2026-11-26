<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBookingRequest;
use App\Models\Booking;
use App\Models\Package;
use App\Models\Addon;
use App\Models\Setting;
use App\Jobs\SyncBookingToGoogleServicesJob;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf;

class BookingController extends Controller
{
    public function create()
    {
        $packages = Package::where('is_active', true)->get();
        $addons = Addon::where('is_active', true)->get();

        // Ambil daftar tanggal yang sudah penuh (booked out) untuk di-disable pada kalender UI
        $maxPerDay = (int) Setting::get('max_events_per_day', 1);
        $settings = Setting::getAll();
        $activeBookings = Booking::whereIn('status', ['pending_verification', 'confirmed'])
            ->where(function ($q) {
                $q->whereDate('event_date', '>=', now()->toDateString())
                  ->orWhere(function ($q2) {
                      $q2->whereNotNull('event_end_date')
                         ->whereDate('event_end_date', '>=', now()->toDateString());
                  });
            })
            ->get(['event_date', 'event_end_date']);

        $dateCounts = [];
        foreach ($activeBookings as $b) {
            $startDate = \Carbon\Carbon::parse($b->event_date);
            $endDate = $b->event_end_date ? \Carbon\Carbon::parse($b->event_end_date) : $startDate;

            $curr = $startDate->copy();
            while ($curr->lessThanOrEqualTo($endDate)) {
                $dStr = $curr->toDateString();
                $dateCounts[$dStr] = ($dateCounts[$dStr] ?? 0) + 1;
                $curr->addDay();
            }
        }

        $bookedDates = [];
        foreach ($dateCounts as $dStr => $count) {
            if ($count >= $maxPerDay) {
                $bookedDates[] = $dStr;
            }
        }

        return view('booking.form', compact('packages', 'addons', 'bookedDates', 'settings'));
    }

    public function store(StoreBookingRequest $request)
    {
        $validated = $request->validated();

        $booking = DB::transaction(function () use ($request, $validated) {
            $package = Package::findOrFail($validated['package_id']);
            
            // Kalkulasi Server-Side untuk Add-ons
            $addonsPayload = [];
            $addonTotalPrice = 0;

            if (!empty($validated['addons'])) {
                foreach ($validated['addons'] as $item) {
                    $qty = (int) ($item['qty'] ?? 0);
                    if ($qty > 0) {
                        $addon = Addon::findOrFail($item['id']);
                        $subtotal = $addon->unit_price * $qty;
                        $addonTotalPrice += $subtotal;

                        $addonsPayload[] = [
                            'addon_id'   => $addon->id,
                            'addon_name' => $addon->name,
                            'unit_price' => $addon->unit_price,
                            'quantity'   => $qty,
                            'subtotal'   => $subtotal,
                        ];
                    }
                }
            }

            // Hitung Durasi & Biaya Tambahan Hari
            $eventDate = $validated['event_date'];
            $eventEndDate = $validated['event_end_date'] ?? $eventDate;
            $start = \Carbon\Carbon::parse($eventDate);
            $end = \Carbon\Carbon::parse($eventEndDate);
            $durationDays = max(1, (int) ($start->diffInDays($end) + 1));
            $standardDays = (int) Setting::get('standard_package_max_days', 2);
            $extraDays = max(0, $durationDays - $standardDays);
            $extraDayChargeRate = Setting::getExtraDayChargeRateFloat();
            $additionalDayCharge = $extraDays * $extraDayChargeRate * (float) $package->base_price;

            $deliveryDistance = (float) ($validated['delivery_distance_km'] ?? 0);
            $transportFee = Setting::calculateTransportFee($deliveryDistance);

            $grandTotal = (float) $package->base_price + $addonTotalPrice + $additionalDayCharge + $transportFee;

            if ($validated['payment_type'] === 'lunas') {
                $amountPaid = $grandTotal;
                $remaining = 0;
            } else {
                $amountPaid = (float) ($validated['nominal_dp'] ?? 0);
                $remaining = max(0, $grandTotal - $amountPaid);
            }

            // Simpan Berkas Bukti Transfer dengan nama hash yang aman
            $proofPath = $request->file('payment_proof')->store('proofs/' . date('Y/m'), 'public');

            // Format nomor invoice resmi berbasis bulan: NKB/MM/XX (Contoh: NKB/11/01)
            $currentMonth = date('m');
            $countThisMonth = Booking::whereYear('created_at', date('Y'))
                ->whereMonth('created_at', date('m'))
                ->count() + 1;

            do {
                $invoiceNumber = 'NKB/' . $currentMonth . '/' . str_pad($countThisMonth, 2, '0', STR_PAD_LEFT);
                $countThisMonth++;
            } while (Booking::where('invoice_number', $invoiceNumber)->exists());

            // Buat Record Utama Booking
            $booking = Booking::create([
                'invoice_number'        => $invoiceNumber,
                'customer_name'         => $validated['customer_name'],
                'customer_phone'        => $validated['customer_phone'],
                'customer_email'        => $validated['customer_email'] ?? null,
                'event_date'            => $eventDate,
                'event_end_date'        => $eventEndDate,
                'duration_days'         => $durationDays,
                'package_id'            => $package->id,
                'package_name'          => $package->name,
                'package_price'         => $package->base_price,
                'additional_day_charge' => $additionalDayCharge,
                'addon_total_price'     => $addonTotalPrice,
                'grand_total'           => $grandTotal,
                'amount_paid'           => $amountPaid,
                'remaining_balance'     => $remaining,
                'event_type'            => $validated['event_type'] ?? 'pernikahan',
                'child_name'            => $validated['child_name'] ?? null,
                'payment_type'          => $validated['payment_type'],
                'payment_proof_path'    => $proofPath,
                'status'                => 'pending_verification',
                'venue_address'         => $validated['venue_address'] ?? null,
                'delivery_distance_km'  => $deliveryDistance,
                'transport_fee'         => $transportFee,
                'address_landmark'      => $validated['address_landmark'] ?? null,
                'parents_name'          => $validated['parents_name'] ?? null,
                'instagram_account'     => $validated['instagram_account'] ?? null,
                'event_time'            => $validated['event_time'] ?? null,
                'vendor_name'           => $validated['vendor_name'] ?? null,
                'discount_amount'       => 0,
                'vendor_mua'            => $validated['vendor_mua'] ?? null,
                'vendor_decor'          => $validated['vendor_decor'] ?? null,
                'vendor_photo_video'    => $validated['vendor_photo_video'] ?? null,
                'vendor_mc_wo'          => $validated['vendor_mc_wo'] ?? null,
                'tent_color_theme'      => $validated['tent_color_theme'] ?? null,
                'custom_tent_size'      => $validated['custom_tent_size'] ?? null,
                'operational_notes'     => $validated['operational_notes'] ?? null,
                'job_source'            => $validated['job_source'] ?? ($validated['vendor_name'] ?? null),
                'setup_date'            => $validated['setup_date'] ?? null,
                'survey_requested'      => $request->boolean('survey_requested'),
                'survey_date'           => $request->boolean('survey_requested') ? ($validated['survey_date'] ?? null) : null,
                'survey_time_slot'      => $request->boolean('survey_requested') ? ($validated['survey_time_slot'] ?? 'flexible') : null,
                'survey_notes'          => $request->boolean('survey_requested') ? ($validated['survey_notes'] ?? null) : null,
                'survey_status'         => $request->boolean('survey_requested') ? 'requested' : 'none',
            ]);

            // Simpan Detail Relasi Add-ons
            if (!empty($addonsPayload)) {
                $booking->addons()->createMany($addonsPayload);
            }

            return $booking;
        });

        // Trigger Queue Job ke Google Sheets & Calendar (Background Process)
        SyncBookingToGoogleServicesJob::dispatch($booking);

        return redirect()->route('booking.success', $booking->invoice_number);
    }

    public function success(string $invoiceNumber)
    {
        $booking = Booking::with('addons')->where('invoice_number', $invoiceNumber)->firstOrFail();
        $adminWhatsapp = Setting::get('admin_whatsapp', '6282255969313');

        $surveyPart = "";
        if ($booking->survey_requested && $booking->survey_date) {
            $slot = match($booking->survey_time_slot) {
                'morning'   => 'Pagi (09:00 - 12:00)',
                'afternoon' => 'Siang-Sore (13:00 - 17:00)',
                'flexible'  => 'Fleksibel',
                default     => $booking->survey_time_slot ?: 'Fleksibel',
            };
            $surveyPart = "- Request Survey: " . \Carbon\Carbon::parse($booking->survey_date)->isoFormat('dddd, D MMMM Y') . " ({$slot})\n";
        }

        // Pre-filled WhatsApp message
        $waMessage = urlencode(
            "Halo Admin NK Barokah Tenda, saya sudah mengajukan pesanan:\n" .
            "- No. Invoice: #{$booking->invoice_number}\n" .
            "- Nama: {$booking->customer_name}\n" .
            "- Jenis Layanan: {$booking->event_type_label}\n" .
            "- Tanggal: " . $booking->formatted_event_date . "\n" .
            $surveyPart .
            "- Paket / Layanan: {$booking->package_name}\n" .
            "- Pembayaran: " . ($booking->payment_type === 'lunas' ? 'LUNAS (100%)' : 'UANG MUKA (DP)') . " (Rp " . number_format($booking->amount_paid, 0, ',', '.') . ")\n\n" .
            "Mohon bantuannya untuk verifikasi bukti transfer. Terima kasih!"
        );

        $whatsappUrl = "https://wa.me/{$adminWhatsapp}?text={$waMessage}";

        return view('booking.success', compact('booking', 'whatsappUrl', 'adminWhatsapp'));
    }

    public function downloadReceipt(string $invoiceNumber)
    {
        $booking = Booking::with(['package', 'addons'])->where('invoice_number', $invoiceNumber)->firstOrFail();
        $pdf = Pdf::loadView('pdf.receipt', compact('booking'))->setPaper('a4', 'portrait');

        $safeFilename = str_replace(['/', '\\'], '-', $booking->invoice_number);
        return $pdf->download("Kuitansi-{$safeFilename}.pdf");
    }
}
