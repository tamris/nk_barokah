<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Setting;
use Illuminate\Http\Request;

class BookingOperationsController extends Controller
{
    public function updateSurveyStatus(Request $request, $id)
    {
        $validated = $request->validate([
            'survey_status' => ['required', 'string', 'in:none,requested,scheduled,completed'],
            'survey_date'   => ['nullable', 'date'],
            'survey_notes'  => ['nullable', 'string', 'max:500'],
        ]);

        $booking = Booking::findOrFail($id);
        $booking->survey_status = $validated['survey_status'];
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
                'message'       => 'Status survey lokasi berhasil diperbarui.',
                'survey_status' => $booking->survey_status,
                'survey_date'   => $booking->survey_date ? $booking->survey_date->format('Y-m-d') : null,
            ]);
        }

        return redirect()->route('admin.bookings.index', ['booking_id' => $booking->id])
            ->with('success', 'Status survey lokasi berhasil diperbarui.');
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
}
