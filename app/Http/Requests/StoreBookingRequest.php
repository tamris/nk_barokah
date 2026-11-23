<?php

namespace App\Http\Requests;

use App\Models\Booking;
use App\Models\Package;
use App\Models\Addon;
use App\Models\Setting;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreBookingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $maxPerDay = (int) Setting::get('max_events_per_day', 1);

        return [
            'customer_name'  => ['required', 'string', 'max:150'],
            'customer_phone' => ['required', 'regex:/^(\+62|62|0)8[1-9][0-9]{6,10}$/'],
            'customer_email' => ['nullable', 'email', 'max:100'],
            'event_date'     => [
                'required', 
                'date', 
                'after_or_equal:today',
                function ($attribute, $value, $fail) use ($maxPerDay) {
                    $start = \Carbon\Carbon::parse($value);
                    $endInput = $this->input('event_end_date');
                    $end = !empty($endInput) ? \Carbon\Carbon::parse($endInput) : $start;

                    if ($end->lessThan($start)) {
                        $fail('Tanggal selesai acara tidak boleh mendahului tanggal mulai acara.');
                        return;
                    }

                    $curr = $start->copy();
                    while ($curr->lessThanOrEqualTo($end)) {
                        $targetDate = $curr->toDateString();
                        $activeBookings = Booking::where(function ($q) use ($targetDate) {
                            $q->whereDate('event_date', $targetDate)
                              ->orWhere(function ($q2) use ($targetDate) {
                                  $q2->whereNotNull('event_end_date')
                                     ->whereDate('event_date', '<=', $targetDate)
                                     ->whereDate('event_end_date', '>=', $targetDate);
                              });
                        })->whereIn('status', ['pending_verification', 'confirmed'])->count();

                        if ($activeBookings >= $maxPerDay) {
                            $fail("Jadwal pada tanggal {$targetDate} sudah penuh (FULL). Silakan pilih rentang tanggal lain.");
                            return;
                        }
                        $curr->addDay();
                    }
                }
            ],
            'event_end_date' => ['nullable', 'date', 'after_or_equal:event_date'],
            'duration_days'  => ['nullable', 'integer', 'min:1', 'max:30'],
            'package_id'     => ['required', 'exists:packages,id'],
            'addons'         => ['nullable', 'array'],
            'addons.*.id'    => ['required_with:addons', 'exists:addons,id'],
            'addons.*.qty'   => ['nullable', 'integer', 'min:0', 'max:1000'],
            'payment_type'       => ['required', 'in:dp,lunas'],
            'nominal_dp'         => ['required_if:payment_type,dp', 'nullable', 'numeric'],
            'payment_proof'      => ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:3072'], // Max 3MB
            'venue_address'      => ['nullable', 'string', 'max:500'],
            'address_landmark'   => ['nullable', 'string', 'max:255'],
            'parents_name'       => ['nullable', 'string', 'max:255'],
            'instagram_account'  => ['nullable', 'string', 'max:100'],
            'event_time'         => ['nullable', 'string', 'max:100'],
            'vendor_name'        => ['nullable', 'string', 'max:150'],
            'vendor_mua'         => ['nullable', 'string', 'max:150'],
            'vendor_decor'       => ['nullable', 'string', 'max:150'],
            'vendor_photo_video' => ['nullable', 'string', 'max:150'],
            'vendor_mc_wo'       => ['nullable', 'string', 'max:150'],
            'delivery_distance_km' => ['nullable', 'numeric', 'min:0', 'max:500'],
            'survey_requested'   => ['nullable', 'boolean'],
            'survey_date'        => [
                'nullable',
                'required_if:survey_requested,true,1',
                'date',
                'after:today',
                function ($attribute, $value, $fail) {
                    if (!empty($value) && $this->filled('event_date')) {
                        $survey = \Carbon\Carbon::parse($value);
                        $event = \Carbon\Carbon::parse($this->input('event_date'));
                        if ($survey->greaterThanOrEqualTo($event)) {
                            $fail('Tanggal request survey lokasi harus sebelum tanggal pelaksanaan acara.');
                        }
                    }
                }
            ],
            'survey_time_slot'   => ['nullable', 'required_if:survey_requested,true,1', 'string', 'in:morning,afternoon,flexible'],
            'survey_notes'       => ['nullable', 'string', 'max:500'],
            'tent_color_theme'   => ['nullable', 'string', 'max:100'],
            'custom_tent_size'   => ['nullable', 'string', 'max:150'],
            'operational_notes'  => ['nullable', 'string', 'max:1000'],
            'job_source'         => ['nullable', 'string', 'max:100'],
            'setup_date'         => ['nullable', 'date'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function ($validator) {
            $package = Package::find($this->package_id);
            if (!$package) {
                return;
            }

            // Hitung total dan jumlah addon yang dipilih
            $selectedAddonsCount = 0;
            $addonTotal = 0;
            if (!empty($this->addons) && is_array($this->addons)) {
                foreach ($this->addons as $item) {
                    $qty = (int) ($item['qty'] ?? 0);
                    if ($qty > 0) {
                        $selectedAddonsCount++;
                        $addon = Addon::find($item['id'] ?? null);
                        if ($addon) {
                            $addonTotal += ($addon->unit_price * $qty);
                        }
                    }
                }
            }

            // Validasi paket Custom / Sewa Alat Saja (Tanpa Tenda): Wajib memilih minimal 1 perlengkapan
            $isCustom = ((float) $package->base_price == 0)
                || str_contains(strtolower($package->name), 'custom')
                || str_contains(strtolower($package->name), 'sewa alat')
                || str_contains(strtolower($package->name), 'tanpa tenda')
                || str_contains(strtolower($package->slug ?? ''), 'custom')
                || str_contains(strtolower($package->slug ?? ''), 'tanpa-tenda');

            if ($isCustom && $selectedAddonsCount === 0) {
                $validator->errors()->add('addons', 'Untuk paket Custom / Sewa Alat Saja, Anda wajib memilih minimal 1 jenis perlengkapan yang disewa.');
            }

            $startDate = \Carbon\Carbon::parse($this->event_date);
            $endDate = $this->filled('event_end_date') ? \Carbon\Carbon::parse($this->event_end_date) : $startDate;
            $durationDays = max(1, (int) ($startDate->diffInDays($endDate) + 1));
            $standardDays = (int) Setting::get('standard_package_max_days', 2);
            $extraDays = max(0, $durationDays - $standardDays);
            $extraDayChargeRate = Setting::getExtraDayChargeRateFloat();
            $additionalDayCharge = $extraDays * $extraDayChargeRate * (float) $package->base_price;

            $deliveryDistance = (float) $this->input('delivery_distance_km', 0);
            $transportFee = Setting::calculateTransportFee($deliveryDistance);

            $grandTotal = (float) $package->base_price + $addonTotal + $additionalDayCharge + $transportFee;

            if ($grandTotal <= 0) {
                $validator->errors()->add('package_id', 'Total nilai sewa reservasi tidak boleh Rp 0. Silakan pilih perlengkapan sewa yang dibutuhkan.');
            }

            if ($this->payment_type === 'dp') {
                $minDpConfig = (float) Setting::get('min_dp_amount', 1000000);
                $nominalDp = (float) $this->nominal_dp;
                $effectiveMinDp = min($minDpConfig, $grandTotal);

                if ($nominalDp < $effectiveMinDp) {
                    $validator->errors()->add('nominal_dp', 'Nominal DP minimal adalah Rp ' . number_format($effectiveMinDp, 0, ',', '.') . '.');
                }

                if ($nominalDp > $grandTotal) {
                    $validator->errors()->add('nominal_dp', 'Nominal DP tidak boleh melebihi Grand Total tagihan (Rp ' . number_format($grandTotal, 0, ',', '.') . ').');
                }
            }
        });
    }

    public function messages(): array
    {
        return [
            'customer_phone.regex'      => 'Format nomor WhatsApp tidak valid (contoh: 081234567890).',
            'event_date.after_or_equal' => 'Tanggal acara harus hari ini atau di masa mendatang.',
            'nominal_dp.required_if'    => 'Nominal DP wajib diisi jika memilih metode pembayaran DP.',
            'payment_proof.max'         => 'Ukuran berkas bukti transfer tidak boleh melebihi 3MB.',
            'payment_proof.mimes'       => 'Bukti transfer harus berformat JPG, PNG, atau PDF.',
        ];
    }
}
