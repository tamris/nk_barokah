<?php

return [
    // Batas maksimal acara pernikahan per tanggal (default: 1 acara eksklusif)
    'max_events_per_day' => (int) env('WO_MAX_EVENTS_PER_DAY', 1),

    // Nomor WhatsApp resmi CS / Owner WO untuk konfirmasi (format: 628xxx)
    'admin_whatsapp'     => env('WO_ADMIN_WHATSAPP', '6281234567890'),

    // Batas minimal DP yang diperbolehkan (default: Rp 1.000.000)
    'min_dp_amount'      => (float) env('WO_MIN_DP_AMOUNT', 1000000),

    // Persentase minimal DP (opsional, misal 20% dari Grand Total)
    'min_dp_percentage'  => (float) env('WO_MIN_DP_PERCENTAGE', 20),

    // Rekening Resmi Pembayaran Transfer WO (Owner)
    'banks' => [
        'bca' => [
            'bank_name'      => 'Bank BCA',
            'account_number' => '3620648107',
            'account_name'   => 'EZZU RAKHMAT FIRDAUS',
        ],
        'bri' => [
            'bank_name'      => 'Bank BRI',
            'account_number' => '344301026581532',
            'account_name'   => 'EZZU RAKHMAT FIRDAUS',
        ],
    ],
];

