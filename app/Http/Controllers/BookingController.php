<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBookingRequest;
use App\Models\Booking;
use App\Models\Package;
use App\Models\Addon;
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
        $maxPerDay = config('wo.max_events_per_day', 1);
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

        return view('booking.form', compact('packages', 'addons', 'bookedDates'));
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

            // Hitung Durasi & Biaya Tambahan Hari (> 2 hari kena charge 15% per hari)
            $eventDate = $validated['event_date'];
            $eventEndDate = $validated['event_end_date'] ?? $eventDate;
            $start = \Carbon\Carbon::parse($eventDate);
            $end = \Carbon\Carbon::parse($eventEndDate);
            $durationDays = max(1, (int) ($start->diffInDays($end) + 1));
            $extraDays = max(0, $durationDays - 2);
            $extraDayChargeRate = (float) config('wo.extra_day_charge_rate', 0.15);
            $additionalDayCharge = $extraDays * $extraDayChargeRate * (float) $package->base_price;

            $grandTotal = (float) $package->base_price + $addonTotalPrice + $additionalDayCharge;

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
                'payment_type'          => $validated['payment_type'],
                'payment_proof_path'    => $proofPath,
                'status'                => 'pending_verification',
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
        $adminWhatsapp = config('wo.admin_whatsapp', '6282255969313');

        // Pre-filled WhatsApp message
        $waMessage = urlencode(
            "Halo Admin NK Barokah Wedding Organizer, saya sudah melakukan reservasi dengan rincian:\n" .
            "- No. Invoice: {$booking->invoice_number}\n" .
            "- Nama: {$booking->customer_name}\n" .
            "- Tanggal Acara: " . $booking->formatted_event_date . "\n" .
            "- Pembayaran: " . strtoupper($booking->payment_type) . " (Rp " . number_format($booking->amount_paid, 0, ',', '.') . ")\n\n" .
            "Mohon diverifikasi pembayaran saya. Terima kasih!"
        );

        $whatsappUrl = "https://wa.me/{$adminWhatsapp}?text={$waMessage}";

        return view('booking.success', compact('booking', 'whatsappUrl'));
    }

    public function downloadReceipt(string $invoiceNumber)
    {
        $booking = Booking::with('addons')->where('invoice_number', $invoiceNumber)->firstOrFail();
        $pdf = Pdf::loadView('pdf.receipt', compact('booking'))->setPaper('a4', 'portrait');

        $safeFilename = str_replace(['/', '\\'], '-', $booking->invoice_number);
        return $pdf->download("Kuitansi-{$safeFilename}.pdf");
    }
}
