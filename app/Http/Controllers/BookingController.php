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
        $bookedDates = Booking::whereIn('status', ['pending_verification', 'confirmed'])
            ->whereDate('event_date', '>=', now()->toDateString())
            ->groupBy('event_date')
            ->havingRaw('count(*) >= ?', [$maxPerDay])
            ->pluck('event_date')
            ->map(fn($d) => \Carbon\Carbon::parse($d)->toDateString())
            ->toArray();

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

            $grandTotal = $package->base_price + $addonTotalPrice;

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
                'invoice_number'     => $invoiceNumber,
                'customer_name'      => $validated['customer_name'],
                'customer_phone'     => $validated['customer_phone'],
                'customer_email'     => $validated['customer_email'] ?? null,
                'event_date'         => $validated['event_date'],
                'package_id'         => $package->id,
                'package_name'       => $package->name,
                'package_price'      => $package->base_price,
                'addon_total_price'  => $addonTotalPrice,
                'grand_total'        => $grandTotal,
                'amount_paid'        => $amountPaid,
                'remaining_balance'  => $remaining,
                'payment_type'       => $validated['payment_type'],
                'payment_proof_path' => $proofPath,
                'status'             => 'pending_verification',
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
        $adminWhatsapp = config('wo.admin_whatsapp', '6281234567890');

        // Pre-filled WhatsApp message
        $waMessage = urlencode(
            "Halo Admin NK Barokah Wedding Organizer, saya sudah melakukan reservasi dengan rincian:\n" .
            "- No. Invoice: {$booking->invoice_number}\n" .
            "- Nama: {$booking->customer_name}\n" .
            "- Tanggal Acara: " . \Carbon\Carbon::parse($booking->event_date)->isoFormat('D MMMM Y') . "\n" .
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
