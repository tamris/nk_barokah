<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Setting;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;

class BookingOperationsController extends Controller
{
    public function updateSurveyStatus(Request $request, $id)
    {
        $validated = $request->validate([
            'survey_status' => ['nullable', 'string', 'in:none,requested,scheduled,completed'],
            'survey_date'   => ['nullable', 'date'],
            'survey_notes'  => ['nullable', 'string', 'max:500'],
        ]);

        $booking = Booking::findOrFail($id);
        if (!empty($validated['survey_status'])) {
            $booking->survey_status = $validated['survey_status'];
        }
        if ($request->has('survey_date')) {
            $booking->survey_date = $validated['survey_date'];
        }
        if ($request->has('survey_notes')) {
            $booking->survey_notes = $validated['survey_notes'];
        }
        $booking->save();

        if ($request->wantsJson()) {
            return response()->json([
                'success'       => true,
                'message'       => 'Site survey status and notes updated successfully.',
                'survey_status' => $booking->survey_status,
                'survey_date'   => $booking->survey_date ? $booking->survey_date->format('Y-m-d') : null,
            ]);
        }

        return redirect()->route('admin.bookings.index', ['booking_id' => $booking->id])
            ->with('success', 'Site survey status and notes updated successfully.');
    }

    public function updateOperations(Request $request, $id)
    {
        $booking = Booking::findOrFail($id);

        $validated = $request->validate([
            'tent_color_theme'  => ['nullable', 'string', 'max:100'],
            'custom_tent_size'  => ['nullable', 'string', 'max:150'],
            'operational_notes' => ['nullable', 'string', 'max:1000'],
            'job_source'        => ['nullable', 'string', 'max:100'],
            'setup_date'        => ['nullable', 'date'],
        ]);

        $booking->update($validated);

        return redirect()->route('admin.bookings.index', ['booking_id' => $booking->id])
            ->with('success', "Operational details & crew SPK for #{$booking->invoice_number} updated successfully.");
    }

    public function showSpk($id)
    {
        $booking = Booking::with(['package', 'addons'])->findOrFail($id);
        $settings = Setting::getAll();

        return view('admin.bookings.spk', compact('booking', 'settings'));
    }

    public function exportCalendarPdf(Request $request)
    {
        $year = (int) $request->query('year', now()->year);
        $month = (int) $request->query('month', now()->month);
        if ($year < 2020 || $year > 2100) {
            $year = (int) now()->year;
        }
        if ($month < 1 || $month > 12) {
            $month = (int) now()->month;
        }

        $mode = $request->query('mode', 'operations');
        if (!in_array($mode, ['operations', 'events'], true)) {
            $mode = 'operations';
        }

        $monthStart = Carbon::create($year, $month, 1)->startOfDay();
        $monthEnd = $monthStart->copy()->endOfMonth()->endOfDay();
        $monthStartIso = $monthStart->format('Y-m-d');
        $monthEndIso = $monthEnd->format('Y-m-d');
        $monthLabel = $monthStart->copy()->locale('id')->isoFormat('MMMM Y');

        // Only confirmed & completed bookings appear on crew/printable schedules
        $bookings = Booking::with(['package', 'addons'])
            ->whereIn('status', ['confirmed', 'completed'])
            ->orderBy('event_date')
            ->get();

        $rows = [];
        $counts = [
            'total'    => 0,
            'survey'   => 0,
            'setup'    => 0,
            'teardown' => 0,
            'events'   => 0,
        ];

        if ($mode === 'operations') {
            $slotLabels = [
                'morning'   => 'Pagi (09:00-12:00)',
                'afternoon' => 'Siang (13:00-17:00)',
                'flexible'  => 'Fleksibel',
            ];

            foreach ($bookings as $b) {
                $eventStart = Carbon::parse($b->event_date)->startOfDay();
                $eventEnd = $b->event_end_date ? Carbon::parse($b->event_end_date)->startOfDay() : $eventStart->copy();
                $setupDate = $b->effective_setup_date->copy()->startOfDay();
                $teardownDate = $eventEnd->copy()->addDay();

                $addonsSummary = $b->addons
                    ->groupBy('addon_name')
                    ->map(fn ($group, $name) => "{$name} (" . $group->sum('quantity') . ')')
                    ->implode(', ');
                $isCustomOnly = $b->event_type === 'sewa_alat' || strtolower(trim((string) $b->package_name)) === 'custom';
                $tentInfo = $isCustomOnly
                    ? ''
                    : trim(($b->effective_tent_size ?: '') . ($b->tent_color_theme ? ' · ' . $b->tent_color_theme : ''), ' ·');

                // 1. Site Survey
                if ($b->survey_requested && $b->survey_date) {
                    $surveyDate = Carbon::parse($b->survey_date)->startOfDay();
                    $surveyIso = $surveyDate->format('Y-m-d');
                    if ($surveyIso >= $monthStartIso && $surveyIso <= $monthEndIso) {
                        $isDone = $b->survey_status === 'completed';
                        $rows[] = [
                            'date_iso'   => $surveyIso,
                            'sort_order' => 1,
                            'day_name'   => $surveyDate->copy()->locale('id')->isoFormat('dddd'),
                            'date_label' => $surveyDate->copy()->locale('id')->isoFormat('D MMM Y'),
                            'type'       => $isDone ? 'survey_done' : 'survey',
                            'type_label' => $isDone ? 'SURVEI SELESAI' : 'SURVEI LOKASI',
                            'invoice'    => $b->invoice_number,
                            'client'     => $b->customer_name,
                            'phone'      => $b->customer_phone,
                            'service'    => $b->package_name,
                            'tent_info'  => $tentInfo,
                            'addons'     => $addonsSummary,
                            'address'    => $b->venue_address ?: '-',
                            'landmark'   => $b->address_landmark ?: '',
                            'notes'      => trim(($slotLabels[$b->survey_time_slot] ?? 'Waktu Fleksibel') . ($b->survey_notes ? ' — ' . $b->survey_notes : '')),
                        ];
                        $counts['survey']++;
                    }
                }

                // 2. Setup / Loading H-2
                $setupIso = $setupDate->format('Y-m-d');
                if ($setupIso >= $monthStartIso && $setupIso <= $monthEndIso) {
                    $rows[] = [
                        'date_iso'   => $setupIso,
                        'sort_order' => 2,
                        'day_name'   => $setupDate->copy()->locale('id')->isoFormat('dddd'),
                        'date_label' => $setupDate->copy()->locale('id')->isoFormat('D MMM Y'),
                        'type'       => 'setup',
                        'type_label' => 'PASANG',
                        'invoice'    => $b->invoice_number,
                        'client'     => $b->customer_name,
                        'phone'      => $b->customer_phone,
                        'service'    => $b->package_name,
                        'tent_info'  => $tentInfo,
                        'addons'     => $addonsSummary,
                        'address'    => $b->venue_address ?: '-',
                        'landmark'   => $b->address_landmark ?: '',
                        'notes'      => $b->operational_notes ?: 'Acara: ' . $b->formatted_event_date,
                    ];
                    $counts['setup']++;
                }

                // 3. Teardown / Bongkar H+1
                $teardownIso = $teardownDate->format('Y-m-d');
                if ($teardownIso >= $monthStartIso && $teardownIso <= $monthEndIso) {
                    $rows[] = [
                        'date_iso'   => $teardownIso,
                        'sort_order' => 3,
                        'day_name'   => $teardownDate->copy()->locale('id')->isoFormat('dddd'),
                        'date_label' => $teardownDate->copy()->locale('id')->isoFormat('D MMM Y'),
                        'type'       => 'teardown',
                        'type_label' => 'BONGKAR',
                        'invoice'    => $b->invoice_number,
                        'client'     => $b->customer_name,
                        'phone'      => $b->customer_phone,
                        'service'    => $b->package_name,
                        'tent_info'  => $tentInfo,
                        'addons'     => $addonsSummary,
                        'address'    => $b->venue_address ?: '-',
                        'landmark'   => $b->address_landmark ?: '',
                        'notes'      => $b->operational_notes ?: 'Bongkar & cek kelengkapan alat kembali ke gudang.',
                    ];
                    $counts['teardown']++;
                }
            }

            usort($rows, fn ($a, $b) => [$a['date_iso'], $a['sort_order']] <=> [$b['date_iso'], $b['sort_order']]);
            $counts['total'] = count($rows);
        } else {
            foreach ($bookings as $b) {
                $eventStart = Carbon::parse($b->event_date)->startOfDay();
                $eventEnd = $b->event_end_date ? Carbon::parse($b->event_end_date)->startOfDay() : $eventStart->copy();
                $startIso = $eventStart->format('Y-m-d');
                $endIso = $eventEnd->format('Y-m-d');

                if ($startIso <= $monthEndIso && $endIso >= $monthStartIso) {
                    $addonsSummary = $b->addons
                        ->groupBy('addon_name')
                        ->map(fn ($group, $name) => "{$name} (" . $group->sum('quantity') . ')')
                        ->implode(', ');
                    $isCustomOnly = $b->event_type === 'sewa_alat' || strtolower(trim((string) $b->package_name)) === 'custom';
                    $setupLabel = $b->effective_setup_date->copy()->locale('id')->isoFormat('D MMM');
                    $teardownLabel = $eventEnd->copy()->addDay()->locale('id')->isoFormat('D MMM');

                    $rows[] = [
                        'date_iso'   => $startIso,
                        'sort_order' => 1,
                        'day_name'   => $eventStart->copy()->locale('id')->isoFormat('dddd'),
                        'date_label' => $eventStart->isSameDay($eventEnd)
                            ? $eventStart->copy()->locale('id')->isoFormat('D MMM Y')
                            : $eventStart->copy()->locale('id')->isoFormat('D') . '–' . $eventEnd->copy()->locale('id')->isoFormat('D MMM Y'),
                        'type'       => $b->status === 'completed' ? 'completed' : 'event',
                        'type_label' => $b->status === 'completed' ? 'SELESAI' : 'TERKONFIRMASI',
                        'invoice'    => $b->invoice_number,
                        'client'     => $b->customer_name,
                        'phone'      => $b->customer_phone,
                        'service'    => $b->package_name . ' (' . $b->event_type_label . ')',
                        'tent_info'  => $isCustomOnly ? '' : trim(($b->effective_tent_size ?: '') . ($b->tent_color_theme ? ' · ' . $b->tent_color_theme : ''), ' ·'),
                        'addons'     => $addonsSummary,
                        'address'    => $b->venue_address ?: '-',
                        'landmark'   => $b->address_landmark ?: '',
                        'notes'      => "Pasang: {$setupLabel} | Bongkar: {$teardownLabel}" . ($b->operational_notes ? ' — ' . $b->operational_notes : ''),
                    ];
                    $counts['events']++;
                }
            }
            $counts['total'] = count($rows);
        }

        $settings = Setting::getAll();
        $printedAt = now()->locale('id')->isoFormat('dddd, D MMMM Y · HH:mm') . ' WIB';
        $modeTitle = $mode === 'operations'
            ? 'JADWAL OPERASIONAL KRU LAPANGAN'
            : 'JADWAL ACARA TERKONFIRMASI';

        $pdf = Pdf::loadView('pdf.calendar-schedule', compact(
            'rows',
            'counts',
            'mode',
            'modeTitle',
            'monthLabel',
            'settings',
            'printedAt'
        ))->setPaper('a4', 'landscape');

        $slugMode = $mode === 'operations' ? 'Operasional-Kru' : 'Acara';
        $filename = "Jadwal-{$slugMode}-NK-Barokah-{$year}-" . str_pad((string) $month, 2, '0', STR_PAD_LEFT) . '.pdf';

        return $pdf->download($filename);
    }
}
