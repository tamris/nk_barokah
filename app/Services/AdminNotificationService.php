<?php

namespace App\Services;

use App\Models\Booking;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class AdminNotificationService
{
    /**
     * Mengambil seluruh data notifikasi operasional untuk dasbor admin.
     *
     * @return array{
     *     urgent_count: int,
     *     field_count: int,
     *     total_count: int,
     *     action_required: Collection,
     *     field_operations: Collection
     * }
     */
    public function getNotifications(): array
    {
        $today = Carbon::today();

        // 1. KATEGORI: BUTUH TINDAKAN (ACTION REQUIRED)
        // 1a. Reservasi baru yang menunggu verifikasi mutasi transfer
        $verifications = Booking::where('status', 'pending_verification')
            ->latest('created_at')
            ->take(8)
            ->get()
            ->map(function ($b) {
                $paymentTypeUpper = strtoupper($b->payment_type ?? 'DP');
                $amountFmt = 'Rp ' . number_format($b->amount_paid, 0, ',', '.');

                return [
                    'id'            => $b->id,
                    'type'          => 'verification',
                    'category'      => 'action_required',
                    'badge_text'    => 'VERIFIKASI ' . $paymentTypeUpper,
                    'badge_class'   => 'bg-amber-50 text-amber-700 border-amber-200/80',
                    'dot_color'     => 'bg-amber-500',
                    'invoice'       => $b->invoice_number,
                    'customer_name' => $b->customer_name,
                    'title'         => '#' . $b->invoice_number . ' · ' . $b->customer_name,
                    'desc'          => "Transfer {$paymentTypeUpper} sebesar {$amountFmt}",
                    'meta'          => "Acara: {$b->formatted_event_date} · {$b->created_at->diffForHumans()}",
                    'url'           => route('admin.bookings.index', ['booking_id' => $b->id]),
                    'action_label'  => 'Periksa Bukti',
                    'action_type'   => 'link',
                    'action_url'    => route('admin.bookings.index', ['booking_id' => $b->id]),
                ];
            });

        // 1b. Tagihan Pelunasan Jatuh Tempo H-7 (Status confirmed, ada sisa saldo, acara <= H+7)
        $sevenDaysLimit = $today->copy()->addDays(7)->toDateString();
        $dueSettlements = Booking::where('status', 'confirmed')
            ->where('remaining_balance', '>', 0)
            ->whereDate('event_date', '<=', $sevenDaysLimit)
            ->whereDate('event_date', '>=', $today->toDateString())
            ->orderBy('event_date', 'asc')
            ->take(8)
            ->get()
            ->map(function ($b) use ($today) {
                $eventDate = Carbon::parse($b->event_date);
                $diffDays = (int) $today->diffInDays($eventDate, false);
                $daysText = $diffDays === 0 ? 'Hari ini' : ($diffDays === 1 ? 'Besok' : "{$diffDays} hari lagi");
                $remainingFmt = 'Rp ' . number_format($b->remaining_balance, 0, ',', '.');

                $waMsg = urlencode(
                    "Halo Kak {$b->customer_name}, salam hangat dari NK Barokah Wedding Organizer! 💍✨\n\n" .
                    "Mengingatkan persiapan acara pernikahan Kakak pada *{$b->formatted_event_date}* (tersisa *{$daysText}*).\n" .
                    "Saat ini tercatat sisa tagihan pelunasan sebesar *{$remainingFmt}*.\n\n" .
                    "Mohon dapat diselesaikan untuk finalisasi jadwal armada dan tim kru dekorasi kami. Terima kasih banyak! 🙏😊"
                );
                $waUrl = "https://wa.me/{$b->formatted_phone}?text={$waMsg}";

                return [
                    'id'            => $b->id,
                    'type'          => 'settlement',
                    'category'      => 'action_required',
                    'badge_text'    => 'PELUNASAN H-7',
                    'badge_class'   => 'bg-rose-50 text-rose-700 border-rose-200/80',
                    'dot_color'     => 'bg-rose-500',
                    'invoice'       => $b->invoice_number,
                    'customer_name' => $b->customer_name,
                    'title'         => '#' . $b->invoice_number . ' · ' . $b->customer_name,
                    'desc'          => "Sisa tagihan: {$remainingFmt}",
                    'meta'          => "Jadwal: {$b->formatted_event_date} ({$daysText})",
                    'url'           => route('admin.bookings.index', ['booking_id' => $b->id]),
                    'action_label'  => 'Ingatkan WA',
                    'action_type'   => 'whatsapp',
                    'action_url'    => $waUrl,
                ];
            });

        // Gabungkan seluruh item butuh tindakan
        $actionRequired = $verifications->concat($dueSettlements);

        // 2. KATEGORI: JADWAL OPERASIONAL LAPANGAN (FIELD OPERATIONS)
        // 2a. Pemasangan / Loading Tenda H-2 & Hari H Acara
        $twoDaysLimit = $today->copy()->addDays(2)->toDateString();
        $fieldOperations = Booking::where('status', 'confirmed')
            ->whereDate('event_date', '<=', $twoDaysLimit)
            ->where(function ($q) use ($today) {
                $q->whereDate('event_date', '>=', $today->toDateString())
                  ->orWhere(function ($q2) use ($today) {
                      $q2->whereNotNull('event_end_date')
                         ->whereDate('event_end_date', '>=', $today->toDateString());
                  });
            })
            ->orderBy('event_date', 'asc')
            ->take(8)
            ->get()
            ->map(function ($b) use ($today) {
                $startDate = Carbon::parse($b->event_date);
                $isToday = $startDate->isSameDay($today);
                $diffDays = (int) $today->diffInDays($startDate, false);

                if ($isToday) {
                    $badgeText = 'HARI H ACARA';
                    $badgeClass = 'bg-emerald-50 text-emerald-800 border-emerald-200/80';
                    $dotColor = 'bg-emerald-600';
                    $desc = "Acara sedang berlangsung hari ini!";
                    $meta = "Paket: {$b->package_name} · Kontak: {$b->customer_phone}";
                } else {
                    $badgeText = 'LOADING TENDA (H-2)';
                    $badgeClass = 'bg-teal-50 text-teal-800 border-teal-200/80';
                    $dotColor = 'bg-teal-600';
                    $desc = "Waktu loading & pemasangan tenda ({$diffDays} hari menuju hari H)";
                    $meta = "Paket: {$b->package_name} · Acara: {$b->formatted_event_date}";
                }

                return [
                    'id'            => $b->id,
                    'type'          => 'field',
                    'category'      => 'field_operations',
                    'badge_text'    => $badgeText,
                    'badge_class'   => $badgeClass,
                    'dot_color'     => $dotColor,
                    'invoice'       => $b->invoice_number,
                    'customer_name' => $b->customer_name,
                    'title'         => '#' . $b->invoice_number . ' · ' . $b->customer_name,
                    'desc'          => $desc,
                    'meta'          => $meta,
                    'url'           => route('admin.bookings.index', ['booking_id' => $b->id]),
                    'action_label'  => 'Detail Kru',
                    'action_type'   => 'link',
                    'action_url'    => route('admin.bookings.index', ['booking_id' => $b->id]),
                ];
            });

        $urgentCount = $actionRequired->count();
        $fieldCount = $fieldOperations->count();

        return [
            'urgent_count'     => $urgentCount,
            'field_count'      => $fieldCount,
            'total_count'      => $urgentCount + $fieldCount,
            'action_required'  => $actionRequired,
            'field_operations' => $fieldOperations,
        ];
    }
}
