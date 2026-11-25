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
                $paymentType = strtoupper($b->payment_type ?? 'DP');
                $paymentTypeLabel = $paymentType === 'LUNAS' ? __('admin.notifications.full_payment') : $paymentType;
                $amountFmt = 'Rp ' . number_format($b->amount_paid, 0, ',', '.');

                return [
                    'id'            => $b->id,
                    'booking_id'    => $b->id,
                    'type'          => 'verification',
                    'category'      => 'action_required',
                    'badge_text'    => __('admin.notifications.verify_prefix') . ' ' . $paymentTypeLabel,
                    'badge_class'   => 'bg-amber-50 text-amber-700 border-amber-200/80',
                    'dot_color'     => 'bg-amber-500',
                    'invoice'       => $b->invoice_number,
                    'customer_name' => $b->customer_name,
                    'title'         => '#' . $b->invoice_number . ' · ' . $b->customer_name,
                    'desc'          => __('admin.notifications.transfer_of', ['type' => $paymentTypeLabel, 'amount' => $amountFmt]),
                    'meta'          => __('admin.notifications.event_meta', ['date' => $b->formatted_event_date, 'ago' => $b->created_at->diffForHumans()]),
                    'url'           => route('admin.bookings.index', ['booking_id' => $b->id]),
                    'action_label'  => __('admin.notifications.review_proof'),
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
                $daysText = $diffDays === 0
                    ? __('admin.notifications.today')
                    : ($diffDays === 1 ? __('admin.notifications.tomorrow') : __('admin.notifications.days_left', ['count' => $diffDays]));
                $remainingFmt = 'Rp ' . number_format($b->remaining_balance, 0, ',', '.');

                $eventLabel = strtolower($b->event_type_label ?? 'acara');
                $waMsg = urlencode(
                    "Halo Kak {$b->customer_name}, salam hangat dari NK Barokah Wedding Organizer! 💍✨\n\n" .
                    "Mengingatkan persiapan {$eventLabel} Kakak pada *{$b->formatted_event_date}* (tersisa *{$daysText}*).\n" .
                    "Saat ini tercatat sisa tagihan pelunasan sebesar *{$remainingFmt}*.\n\n" .
                    "Mohon dapat diselesaikan untuk finalisasi jadwal armada dan tim operasional kami. Terima kasih banyak! 🙏😊"
                );
                $waUrl = "https://wa.me/{$b->formatted_phone}?text={$waMsg}";

                return [
                    'id'            => $b->id,
                    'booking_id'    => $b->id,
                    'type'          => 'settlement',
                    'category'      => 'action_required',
                    'badge_text'    => __('admin.notifications.due_settlement'),
                    'badge_class'   => 'bg-rose-50 text-rose-700 border-rose-200/80',
                    'dot_color'     => 'bg-rose-500',
                    'invoice'       => $b->invoice_number,
                    'customer_name' => $b->customer_name,
                    'title'         => '#' . $b->invoice_number . ' · ' . $b->customer_name,
                    'desc'          => __('admin.notifications.remaining_balance', ['amount' => $remainingFmt]),
                    'meta'          => __('admin.notifications.schedule_meta', ['date' => $b->formatted_event_date, 'days' => $daysText]),
                    'url'           => route('admin.bookings.index', ['booking_id' => $b->id]),
                    'action_label'  => __('admin.notifications.remind_wa'),
                    'action_type'   => 'whatsapp',
                    'action_url'    => $waUrl,
                ];
            });

        // Gabungkan seluruh item butuh tindakan
        $actionRequired = $verifications->concat($dueSettlements);

        // 2. KATEGORI: JADWAL OPERASIONAL LAPANGAN (FIELD OPERATIONS)
        // 2a. Request Survey Lokasi Lapangan (Status: requested/scheduled, survey_date dalam 7 hari)
        $sevenDaysAhead = $today->copy()->addDays(7)->toDateString();
        $surveys = Booking::where('survey_requested', true)
            ->whereIn('survey_status', ['requested', 'scheduled'])
            ->whereNotNull('survey_date')
            ->whereDate('survey_date', '<=', $sevenDaysAhead)
            ->orderBy('survey_date', 'asc')
            ->take(5)
            ->get()
            ->map(function ($b) use ($today) {
                $surveyDate = Carbon::parse($b->survey_date)->startOfDay();
                $todayStart = $today->copy()->startOfDay();
                $diff = (int) $todayStart->diffInDays($surveyDate, false);
                $timeSlot = match($b->survey_time_slot) {
                    'morning'   => __('admin.survey_slots.morning'),
                    'afternoon' => __('admin.survey_slots.afternoon'),
                    default     => __('admin.survey_slots.flexible'),
                };

                if ($diff === 0) {
                    $badgeText = __('admin.notifications.site_survey_today');
                    $desc = __('admin.notifications.survey_today_desc', ['slot' => $timeSlot]);
                } elseif ($diff === 1) {
                    $badgeText = __('admin.notifications.site_survey_tmrw');
                    $desc = __('admin.notifications.survey_tmrw_desc', ['slot' => $timeSlot]);
                } elseif ($diff < 0) {
                    $badgeText = __('admin.notifications.survey_due');
                    $desc = __('admin.notifications.survey_due_desc', ['days' => abs($diff)]);
                } else {
                    $badgeText = __('admin.notifications.site_survey_in', ['days' => $diff]);
                    $desc = __('admin.notifications.survey_in_desc', ['days' => $diff, 'slot' => $timeSlot]);
                }

                $eventLabel = strtolower($b->event_type_label ?? 'acara');
                $waMsg = urlencode(
                    "Halo Kak {$b->customer_name}, salam hangat dari tim survey NK Barokah Tenda! 📍📐\n\n" .
                    "Mengenai jadwal survey venue {$eventLabel} Anda pada hari *{$b->survey_date->format('l, d M Y')}* ({$timeSlot}), mohon koordinasi titik temu atau ancer-ancer lokasinya. Terima kasih! 🙏"
                );
                $waUrl = "https://wa.me/{$b->formatted_phone}?text={$waMsg}";

                return [
                    'id'            => $b->id . '-survey',
                    'booking_id'    => $b->id,
                    'type'          => 'survey',
                    'category'      => 'field_operations',
                    'badge_text'    => $badgeText,
                    'badge_class'   => 'bg-indigo-50 text-indigo-800 border-indigo-200/80',
                    'dot_color'     => 'bg-indigo-600',
                    'invoice'       => $b->invoice_number,
                    'customer_name' => $b->customer_name,
                    'title'         => '#' . $b->invoice_number . ' · ' . $b->customer_name,
                    'desc'          => $desc,
                    'meta'          => __('admin.notifications.venue_meta', ['venue' => $b->venue_address ?: 'Slawi']) . ($b->survey_notes ? __('admin.notifications.note_meta', ['note' => $b->survey_notes]) : ''),
                    'url'           => route('admin.bookings.index', ['booking_id' => $b->id]),
                    'action_label'  => __('admin.notifications.chat_client_wa'),
                    'action_type'   => 'whatsapp',
                    'action_url'    => $waUrl,
                ];
            });

        // 2b. Pemasangan / Loading Tenda H-2 & Hari H Acara
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
                $startDate = Carbon::parse($b->event_date)->startOfDay();
                $endDate = ($b->event_end_date ? Carbon::parse($b->event_end_date) : $startDate)->endOfDay();
                $todayStart = $today->copy()->startOfDay();
                $isOngoing = $todayStart->between($startDate, $endDate);
                $duration = (int) ($b->duration_days ?: 1);

                if ($isOngoing) {
                    $badgeText = __('admin.notifications.event_day_today');
                    $badgeClass = 'bg-emerald-50 text-emerald-800 border-emerald-200/80';
                    $dotColor = 'bg-emerald-600';
                    if ($duration > 1) {
                        $dayNumber = min($duration, (int) $startDate->diffInDays($todayStart) + 1);
                        $desc = __('admin.notifications.event_ongoing_multi', ['day' => $dayNumber, 'total' => $duration]);
                    } else {
                        $desc = __('admin.notifications.event_ongoing');
                    }
                    $meta = __('admin.notifications.package_contact', ['package' => $b->package_name, 'phone' => $b->customer_phone]);
                } else {
                    $diffDays = (int) $todayStart->diffInDays($startDate, false);
                    $badgeText = __('admin.notifications.tent_loading', ['days' => $diffDays]);
                    $badgeClass = 'bg-teal-50 text-teal-800 border-teal-200/80';
                    $dotColor = 'bg-teal-600';
                    $desc = $diffDays === 1
                        ? __('admin.notifications.loading_tmrw_desc')
                        : __('admin.notifications.loading_days_desc', ['days' => $diffDays]);
                    $meta = __('admin.notifications.package_event', ['package' => $b->package_name, 'date' => $b->formatted_event_date]);
                }

                return [
                    'id'            => $b->id,
                    'booking_id'    => $b->id,
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
                    'action_label'  => __('admin.notifications.crew_details'),
                    'action_type'   => 'link',
                    'action_url'    => route('admin.bookings.index', ['booking_id' => $b->id]),
                ];
            });

        $allFieldItems = $surveys->concat($fieldOperations);

        $urgentCount = $actionRequired->count();
        $fieldCount = $allFieldItems->count();

        return [
            'urgent_count'     => $urgentCount,
            'field_count'      => $fieldCount,
            'total_count'      => $urgentCount + $fieldCount,
            'action_required'  => $actionRequired,
            'field_operations' => $allFieldItems,
        ];
    }
}
