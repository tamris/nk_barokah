<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = ['key', 'value'];

    public static function get(string $key, mixed $default = null): mixed
    {
        $setting = static::where('key', $key)->first();
        if ($setting !== null && $setting->value !== null) {
            return $setting->value;
        }

        return config("wo.{$key}", $default);
    }

    public static function set(string $key, mixed $value): self
    {
        return static::updateOrCreate(
            ['key' => $key],
            ['value' => is_array($value) ? json_encode($value) : (string) $value]
        );
    }

    public static function getExtraDayChargeRateFloat(): float
    {
        $val = (float) static::get('extra_day_charge_rate', 15);
        return $val > 1 ? $val / 100 : $val;
    }

    public static function getAll(): array
    {
        $dbSettings = static::pluck('value', 'key')->toArray();

        return [
            'standard_package_max_days' => (int) ($dbSettings['standard_package_max_days'] ?? config('wo.standard_package_max_days', 2)),
            'extra_day_charge_rate'     => (float) ($dbSettings['extra_day_charge_rate'] ?? 15),
            'min_dp_amount'             => (float) ($dbSettings['min_dp_amount'] ?? config('wo.min_dp_amount', 1000000)),
            'max_events_per_day'        => (int) ($dbSettings['max_events_per_day'] ?? config('wo.max_events_per_day', 1)),
            'admin_whatsapp'            => (string) ($dbSettings['admin_whatsapp'] ?? config('wo.admin_whatsapp', '6282255969313')),
            'bca_account_number'        => (string) ($dbSettings['bca_account_number'] ?? config('wo.banks.bca.account_number', '3620648107')),
            'bca_account_name'          => (string) ($dbSettings['bca_account_name'] ?? config('wo.banks.bca.account_name', 'EZZU RAKHMAT FIRDAUS')),
            'bri_account_number'        => (string) ($dbSettings['bri_account_number'] ?? config('wo.banks.bri.account_number', '344301026581532')),
            'bri_account_name'          => (string) ($dbSettings['bri_account_name'] ?? config('wo.banks.bri.account_name', 'EZZU RAKHMAT FIRDAUS')),
            'google_sheet_id'           => (string) ($dbSettings['google_sheet_id'] ?? config('services.google.sheet_id', '')),
            'google_calendar_id'        => (string) ($dbSettings['google_calendar_id'] ?? config('services.google.calendar_id', '')),
        ];
    }
}
