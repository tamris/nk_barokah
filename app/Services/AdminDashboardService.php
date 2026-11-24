<?php

namespace App\Services;

use App\Models\Addon;
use App\Models\Booking;
use App\Models\Package;
use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class AdminDashboardService
{
    public function getDashboardData(): array
    {
        Carbon::setLocale('en');

        $allBookings = Booking::with(['package', 'addons'])
            ->orderByDesc('created_at')
            ->get();

        $packages = Package::withCount('bookings')
            ->where('slug', '!=', 'custom-alat-saja')
            ->where('base_price', '>', 0)
            ->orderBy('base_price')
            ->get();

        $addons = Addon::orderBy('name')->get();
        $today = Carbon::today();

        return [
            'bookingsPayload' => $this->formatBookings($allBookings, $today),
            'clientsPayload'  => $this->formatClients($allBookings),
            'packagesPayload' => $this->formatPackages($packages),
            'addonsPayload'   => $this->formatAddons($addons),
            'metrics'         => $this->calculateMetrics($allBookings, $today),
            'initialCalYear'  => (int) $today->year,
            'initialCalMonth' => (int) $today->month,
            'settings'        => Setting::getAll(),
        ];
    }

    private function formatBookings(Collection $allBookings, Carbon $today): Collection
    {
        $warehouseAddress = Setting::get('warehouse_address', 'NK Barokah Tenda Slawi Kulon');

        return $allBookings->map(function (Booking $b) use ($today, $warehouseAddress) {
            $eventDate = Carbon::parse($b->event_date);
            $eventEndDate = $b->event_end_date ? Carbon::parse($b->event_end_date) : $eventDate;
            $durationDays = max(1, (int) ($b->duration_days ?: ($eventDate->diffInDays($eventEndDate) + 1)));

            if ($durationDays > 1 && !$eventDate->isSameDay($eventEndDate)) {
                $dateFormatted = $eventDate->format('D, M d, Y') . ' – ' . $eventEndDate->format('D, M d, Y');
                if ($eventDate->year !== $eventEndDate->year) {
                    $dateShortFormatted = $eventDate->format('j M Y') . ' - ' . $eventEndDate->format('j M Y');
                } elseif ($eventDate->month === $eventEndDate->month) {
                    $dateShortFormatted = $eventDate->format('j') . ' - ' . $eventEndDate->format('j M Y');
                } else {
                    $dateShortFormatted = $eventDate->format('j M') . ' - ' . $eventEndDate->format('j M Y');
                }
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
            } elseif ($b->status === 'completed' || (float) $b->remaining_balance <= 0) {
                $invoiceStatus = 'Paid';
            } elseif ($b->status === 'pending_verification') {
                $invoiceStatus = 'Draft';
            } else {
                $invoiceStatus = $eventDate->isPast() ? 'Overdue' : 'Sent';
            }

            $isPdf = $b->payment_proof_path
                ? strtolower(pathinfo($b->payment_proof_path, PATHINFO_EXTENSION)) === 'pdf'
                : false;

            $subtotalBeforeDiscount = (float) $b->package_price + (float) $b->addon_total_price + (float) $b->additional_day_charge + (float) ($b->transport_fee ?? 0);
            $discountAmount = (float) ($b->discount_amount ?? 0);
            $discountPercent = (float) ($b->discount_percent ?? 0);
            if ($discountPercent <= 0 && $discountAmount > 0 && $subtotalBeforeDiscount > 0) {
                $discountPercent = round(($discountAmount / $subtotalBeforeDiscount) * 100, 1);
            }

            $relativeDate = $this->formatRelativeDate($eventDate, $eventEndDate, $today);

            return [
                'id'                                 => $b->id,
                'code'                               => $b->invoice_number,
                'name'                               => $b->customer_name,
                'shortName'                          => explode(' ', trim($b->customer_name))[0] ?? $b->customer_name,
                'event_type'                         => $b->event_type ?: 'pernikahan',
                'event_type_label'                   => $b->event_type_label,
                'child_name'                         => $b->child_name ?: '',
                'email'                              => $b->customer_email ?: '-',
                'phone'                              => $b->customer_phone,
                'service'                            => $b->package_name,
                'package_price'                      => (float) $b->package_price,
                'package_price_formatted'            => 'Rp ' . number_format((float) $b->package_price, 0, ',', '.'),
                'addons'                             => $b->addons->map(fn ($a) => [
                    'id'         => $a->id,
                    'name'       => $a->addon_name,
                    'quantity'   => (int) $a->quantity,
                    'unit_price' => 'Rp ' . number_format((float) $a->unit_price, 0, ',', '.'),
                    'subtotal'   => 'Rp ' . number_format((float) $a->subtotal, 0, ',', '.'),
                ])->values()->all(),
                'subtotal_before_discount'           => $subtotalBeforeDiscount,
                'subtotal_before_discount_formatted' => 'Rp ' . number_format($subtotalBeforeDiscount, 0, ',', '.'),
                'grand_total'                        => (float) $b->grand_total,
                'amount_paid'                        => (float) $b->amount_paid,
                'remaining_balance'                  => (float) $b->remaining_balance,
                'grand_total_formatted'              => 'Rp ' . number_format((float) $b->grand_total, 0, ',', '.'),
                'amount_paid_formatted'              => 'Rp ' . number_format((float) $b->amount_paid, 0, ',', '.'),
                'remaining_formatted'                => 'Rp ' . number_format((float) $b->remaining_balance, 0, ',', '.'),
                'payment_type'                       => strtoupper((string) $b->payment_type),
                'raw_status'                         => $b->status,
                'bookingStatus'                      => $bookingStatus,
                'invoiceStatus'                      => $invoiceStatus,
                'event_date_iso'                     => $eventDate->format('Y-m-d'),
                'event_end_date_iso'                 => $eventEndDate->format('Y-m-d'),
                'duration_days'                      => $durationDays,
                'additional_day_charge'              => (float) $b->additional_day_charge,
                'additional_day_charge_formatted'    => 'Rp ' . number_format((float) $b->additional_day_charge, 0, ',', '.'),
                'event_year'                         => (int) $eventDate->year,
                'event_month'                        => (int) $eventDate->month,
                'event_day'                          => (int) $eventDate->day,
                'date'                               => $dateFormatted,
                'date_short'                         => $dateShortFormatted,
                'formatted_event_date'               => $b->formatted_event_date,
                'time'                               => $b->created_at->format('h:i A'),
                'relative_date'                      => $relativeDate,
                'created_at_label'                   => $b->created_at->format('M d, Y · h:i A'),
                'updated_at_label'                   => $b->updated_at->format('M d, Y · h:i A'),
                'payment_proof_url'                  => $b->payment_proof_path ? asset('storage/' . $b->payment_proof_path) : null,
                'is_proof_pdf'                       => $isPdf,
                'synced_to_sheets'                   => (bool) $b->synced_to_sheets,
                'google_calendar_synced'             => !empty($b->google_calendar_event_id),
                'whatsapp_url'                       => $b->whatsapp_url,
                'receipt_url'                        => route('booking.receipt', $b->invoice_number),
                'show_url'                           => route('admin.bookings.show', $b->id),
                'confirm_url'                        => route('admin.bookings.confirm', $b->id),
                'reject_url'                         => route('admin.bookings.reject', $b->id),
                'settle_url'                         => route('admin.bookings.settle', $b->id),
                'complete_url'                       => route('admin.bookings.complete', $b->id),
                'discount_url'                       => route('admin.bookings.discount', $b->id),
                'venue_address'                      => $b->venue_address ?: '-',
                'address_landmark'                   => $b->address_landmark ?: '-',
                'parents_name'                       => $b->parents_name ?: '-',
                'instagram_account'                  => $b->instagram_account ?: '-',
                'event_time'                         => $b->event_time ?: '-',
                'vendor_name'                        => $b->vendor_name ?: '',
                'discount_amount'                    => $discountAmount,
                'discount_percent'                   => $discountPercent,
                'discount_amount_formatted'          => 'Rp ' . number_format($discountAmount, 0, ',', '.'),
                'owner_discount'                     => (float) ($b->owner_discount ?? 0),
                'owner_discount_formatted'           => 'Rp ' . number_format((float) ($b->owner_discount ?? 0), 0, ',', '.'),
                'owner_discount_url'                 => route('admin.bookings.owner_discount', $b->id),
                'delivery_distance_km'               => (float) ($b->delivery_distance_km ?? 0),
                'transport_fee'                      => (float) ($b->transport_fee ?? 0),
                'transport_fee_formatted'            => 'Rp ' . number_format((float) ($b->transport_fee ?? 0), 0, ',', '.'),
                'transport_url'                      => route('admin.bookings.transport', $b->id),
                'maps_route_url'                     => 'https://www.google.com/maps/dir/?api=1&origin=' . urlencode($warehouseAddress) . '&destination=' . urlencode($b->venue_address ?? ''),
                'survey_requested'                   => (bool) $b->survey_requested,
                'survey_date'                        => $b->survey_date ? $b->survey_date->format('Y-m-d') : null,
                'survey_date_formatted'              => $b->survey_date ? $b->survey_date->format('D, M d, Y') : null,
                'survey_time_slot'                   => $b->survey_time_slot,
                'survey_time_slot_label'             => match ($b->survey_time_slot) {
                    'morning'   => 'Morning (09:00 - 12:00)',
                    'afternoon' => 'Afternoon (13:00 - 17:00)',
                    'flexible'  => 'Flexible Time',
                    default     => $b->survey_time_slot ?: 'Flexible',
                },
                'survey_notes'                       => $b->survey_notes,
                'survey_status'                      => $b->survey_status ?? 'none',
                'survey_status_url'                  => route('admin.bookings.survey_status', $b->id),
                'vendor_mua'                         => $b->vendor_mua ?: '-',
                'vendor_decor'                       => $b->vendor_decor ?: '-',
                'vendor_photo_video'                 => $b->vendor_photo_video ?: '-',
                'vendor_mc_wo'                       => $b->vendor_mc_wo ?: '-',
                'tent_color_theme'                   => ($b->event_type === 'sewa_alat') ? '-' : ($b->tent_color_theme ?: '-'),
                'custom_tent_size'                   => ($b->event_type === 'sewa_alat') ? '-' : ($b->custom_tent_size ?: '-'),
                'operational_notes'                  => $b->operational_notes ?: '',
                'job_source'                         => $b->job_source ?: ($b->vendor_name ?: 'Direct Client'),
                'setup_date_iso'                     => $b->effective_setup_date->format('Y-m-d'),
                'setup_date_formatted'               => $b->formatted_setup_date,
                'is_custom_setup_date'               => !empty($b->setup_date),
                'spk_url'                            => route('admin.bookings.spk', $b->id),
                'operations_url'                     => route('admin.bookings.operations', $b->id),
                'avatar'                             => 'https://ui-avatars.com/api/?name=' . urlencode($b->customer_name) . '&background=E6F4F1&color=045D56&bold=true&size=128',
            ];
        })->values();
    }

    private function formatRelativeDate(Carbon $eventDate, ?Carbon $eventEndDate, Carbon $today): string
    {
        $eventStart = $eventDate->copy()->startOfDay();
        $todayStart = $today->copy()->startOfDay();
        $daysDiff = (int) $todayStart->diffInDays($eventStart, false);

        if ($eventDate->isToday()) {
            return 'Today';
        }
        if ($eventEndDate && $today->between($eventStart, $eventEndDate->copy()->endOfDay())) {
            return 'Ongoing';
        }
        if ($daysDiff === 1) {
            return '1 day from now';
        }

        if ($daysDiff > 1) {
            if ($daysDiff < 14) {
                return "{$daysDiff} days from now";
            }
            if ($daysDiff < 30) {
                $weeks = (int) round($daysDiff / 7);
                return "{$weeks} " . ($weeks === 1 ? 'week' : 'weeks') . ' from now';
            }
            $months = (int) round($daysDiff / 30.416);
            if ($months < 12) {
                return "{$months} " . ($months === 1 ? 'month' : 'months') . ' from now';
            }
            $years = (int) round($daysDiff / 365);
            return "{$years} " . ($years === 1 ? 'year' : 'years') . ' from now';
        }

        $pastDays = abs($daysDiff);
        if ($pastDays === 1) {
            return '1 day ago';
        }
        if ($pastDays < 14) {
            return "{$pastDays} days ago";
        }
        if ($pastDays < 30) {
            $weeks = (int) round($pastDays / 7);
            return "{$weeks} " . ($weeks === 1 ? 'week' : 'weeks') . ' ago';
        }
        $months = (int) round($pastDays / 30.416);
        if ($months < 12) {
            return "{$months} " . ($months === 1 ? 'month' : 'months') . ' ago';
        }
        $years = (int) round($pastDays / 365);
        return "{$years} " . ($years === 1 ? 'year' : 'years') . ' ago';
    }

    private function formatClients(Collection $allBookings): Collection
    {
        return $allBookings
            ->groupBy(fn (Booking $b) => preg_replace('/[^\d]/', '', (string) $b->customer_phone) ?: strtolower($b->customer_name))
            ->map(function ($group) {
                /** @var Booking $latest */
                $latest = $group->sortByDesc('event_date')->first();
                $totalSpent = (float) $group->whereIn('status', ['confirmed', 'completed'])->sum('grand_total');
                $hasActive = $group->whereIn('status', ['confirmed', 'pending_verification'])->isNotEmpty();
                $hasCompleted = $group->where('status', 'completed')->isNotEmpty();

                return [
                    'id'                  => $latest->id,
                    'name'                => $latest->customer_name,
                    'email'               => $latest->customer_email ?: '-',
                    'phone'               => $latest->customer_phone,
                    'avatar'              => 'https://ui-avatars.com/api/?name=' . urlencode($latest->customer_name) . '&background=E6F4F1&color=045D56&bold=true&size=128',
                    'package'             => $latest->package_name,
                    'totalBookings'       => $group->count(),
                    'totalSpent'          => $totalSpent,
                    'totalSpentFormatted' => 'Rp ' . number_format($totalSpent, 0, ',', '.'),
                    'lastEvent'           => Carbon::parse($latest->event_date)->format('M d, Y'),
                    'status'              => $hasActive ? 'Active' : ($hasCompleted ? 'Complete' : 'Lead'),
                    'whatsapp_url'        => $latest->whatsapp_url,
                    'bookingIds'          => $group->pluck('id')->values()->all(),
                ];
            })
            ->values();
    }

    private function formatPackages(Collection $packages): Collection
    {
        return $packages->map(fn (Package $p) => [
            'id'              => $p->id,
            'name'            => $p->name,
            'price'           => (float) $p->base_price,
            'price_formatted' => 'Rp ' . number_format((float) $p->base_price, 0, ',', '.'),
            'description'     => $p->description ?: 'Complete Wedding Organizer package by NK Barokah.',
            'raw_description' => (string) ($p->description ?? ''),
            'is_active'       => (bool) $p->is_active,
            'booked_count'    => (int) $p->bookings_count,
            'toggle_url'      => route('admin.packages.toggle', $p->id),
        ])->values();
    }

    private function formatAddons(Collection $addons): Collection
    {
        return $addons->map(fn (Addon $a) => [
            'id'              => $a->id,
            'name'            => $a->name,
            'unit'            => $a->unit ?: 'unit',
            'price'           => (float) $a->unit_price,
            'price_formatted' => 'Rp ' . number_format((float) $a->unit_price, 0, ',', '.'),
            'is_active'       => (bool) $a->is_active,
            'toggle_url'      => route('admin.addons.toggle', $a->id),
        ])->values();
    }

    private function calculateMetrics(Collection $allBookings, Carbon $today): array
    {
        $todayCount = $allBookings->filter(function ($b) use ($today) {
            if ($b->status === 'rejected') {
                return false;
            }
            $start = Carbon::parse($b->event_date)->startOfDay();
            $end = ($b->event_end_date ? Carbon::parse($b->event_end_date) : $start)->endOfDay();
            return $today->between($start, $end);
        })->count();

        $upcomingCount = $allBookings->filter(function ($b) use ($today) {
            if (!in_array($b->status, ['pending_verification', 'confirmed'])) {
                return false;
            }
            return Carbon::parse($b->event_date)->startOfDay()->greaterThan($today);
        })->count();

        $pendingCount = $allBookings->where('status', 'pending_verification')->count();
        $outstandingBookings = $allBookings->filter(fn ($b) => in_array($b->status, ['pending_verification', 'confirmed']) && (float) $b->remaining_balance > 0);
        $totalCollected = (float) $allBookings->whereIn('status', ['confirmed', 'completed'])->sum('amount_paid');
        $totalOutstanding = (float) $outstandingBookings->sum('remaining_balance');
        $totalBilled = (float) $allBookings->where('status', '!=', 'rejected')->sum('grand_total');

        $topPackage = $allBookings
            ->where('status', '!=', 'rejected')
            ->filter(fn ($b) => $b->event_type !== 'sewa_alat' && (float) $b->package_price > 0)
            ->groupBy('package_name')
            ->sortByDesc(fn ($g) => $g->count())
            ->keys()
            ->first() ?? 'Paket 1';

        return [
            'today_schedule'        => $todayCount,
            'upcoming_count'        => $upcomingCount,
            'pending_count'         => $pendingCount,
            'outstanding_count'     => $outstandingBookings->count(),
            'total_bookings'        => $allBookings->count(),
            'revenue'               => $totalCollected,
            'revenue_short'         => $totalCollected >= 1000000
                ? 'Rp ' . number_format($totalCollected / 1000000, $totalCollected % 1000000 === 0 ? 0 : 1, ',', '.') . ' Jt'
                : 'Rp ' . number_format($totalCollected, 0, ',', '.'),
            'total_collected_fmt'   => 'Rp ' . number_format($totalCollected, 0, ',', '.'),
            'total_outstanding_fmt' => 'Rp ' . number_format($totalOutstanding, 0, ',', '.'),
            'total_billed_fmt'      => 'Rp ' . number_format($totalBilled, 0, ',', '.'),
            'top_package'           => $topPackage,
        ];
    }
}
