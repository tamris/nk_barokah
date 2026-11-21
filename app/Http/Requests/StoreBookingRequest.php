<?php

namespace App\Http\Requests;

use App\Models\Booking;
use App\Models\Package;
use App\Models\Addon;
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
        $maxPerDay = config('wo.max_events_per_day', 1);

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
            'payment_type'   => ['required', 'in:dp,lunas'],
            'nominal_dp'     => ['required_if:payment_type,dp', 'nullable', 'numeric'],
            'payment_proof'  => ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:3072'], // Max 3MB
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function ($validator) {
            if ($this->payment_type === 'dp') {
                $minDpConfig = config('wo.min_dp_amount', 1000000);
                $nominalDp = (float) $this->nominal_dp;

                // Hitung estimasi grand total server-side untuk memvalidasi batas atas & bawah DP
                $package = Package::find($this->package_id);
                if (!$package) {
                    return;
                }

                $addonTotal = 0;
                if (!empty($this->addons)) {
                    foreach ($this->addons as $item) {
                        $qty = (int) ($item['qty'] ?? 0);
                        if ($qty > 0) {
                            $addon = Addon::find($item['id'] ?? null);
                            if ($addon) {
                                $addonTotal += ($addon->unit_price * $qty);
                            }
                        }
                    }
                }

                $startDate = \Carbon\Carbon::parse($this->event_date);
                $endDate = $this->filled('event_end_date') ? \Carbon\Carbon::parse($this->event_end_date) : $startDate;
                $durationDays = max(1, (int) ($startDate->diffInDays($endDate) + 1));
                $extraDays = max(0, $durationDays - 2);
                $extraDayChargeRate = (float) config('wo.extra_day_charge_rate', 0.15);
                $additionalDayCharge = $extraDays * $extraDayChargeRate * (float) $package->base_price;

                $grandTotal = (float) $package->base_price + $addonTotal + $additionalDayCharge;

                if ($nominalDp < $minDpConfig) {
                    $validator->errors()->add('nominal_dp', 'Nominal DP minimal adalah Rp ' . number_format($minDpConfig, 0, ',', '.') . '.');
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
