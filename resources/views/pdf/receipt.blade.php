<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Invoice Resmi #{{ $booking->invoice_number }} - NK Barokah</title>
    <style>
        @page {
            size: a4 portrait;
            margin: 12mm 16mm 14mm 16mm;
        }
        * {
            box-sizing: border-box;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 9px;
            color: #0F172A;
            line-height: 1.45;
            background: #FFFFFF;
            margin: 0;
            padding: 0;
        }

        /* Container */
        .invoice-canvas {
            width: 100%;
            background: #FFFFFF;
            position: relative;
        }

        /* Top Header */
        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }
        .header-brand-cell {
            vertical-align: top;
            width: 58%;
        }
        .header-meta-cell {
            vertical-align: top;
            width: 42%;
            text-align: right;
        }

        .brand-logo {
            height: 42px;
            width: auto;
            display: block;
            margin-bottom: 8px;
        }
        .brand-details {
            font-size: 7.5px;
            color: #64748B;
            line-height: 1.45;
            margin: 0;
        }
        .brand-details strong {
            color: #1E293B;
            font-weight: 700;
        }

        /* Title & Meta on Top Right */
        .invoice-title {
            font-size: 26px;
            font-weight: 300;
            letter-spacing: 6px;
            color: #0F172A;
            text-transform: uppercase;
            margin: 0 0 3px 0;
            line-height: 1;
        }
        .invoice-number-line {
            font-size: 11px;
            font-weight: 600;
            color: #334155;
            letter-spacing: 0.8px;
            margin-bottom: 3px;
        }
        .invoice-date-line {
            font-size: 7.8px;
            color: #94A3B8;
            margin-bottom: 6px;
        }

        /* Refined Status Pill */
        .status-pill {
            display: inline-block;
            font-size: 7.5px;
            font-weight: 700;
            letter-spacing: 1.2px;
            text-transform: uppercase;
            padding: 3px 10px;
            border-radius: 12px;
        }
        .status-pill-paid {
            background: #ECFDF5;
            color: #065F46;
            border: 1px solid #A7F3D0;
        }
        .status-pill-dp {
            background: #F8FAFC;
            color: #1E293B;
            border: 1px solid #CBD5E1;
        }
        .status-pill-pending {
            background: #FEF3C7;
            color: #92400E;
            border: 1px solid #FDE68A;
        }
        .status-pill-cancelled {
            background: #FEF2F2;
            color: #B91C1C;
            border: 1px solid #FECACA;
        }

        /* Official Paid Digital Stamp */
        .paid-stamp {
            display: inline-block;
            margin: 6px 0 10px 0;
            padding: 4px 14px;
            border: 2px solid #045D56;
            border-radius: 6px;
            color: #045D56;
            font-size: 11px;
            font-weight: 800;
            letter-spacing: 3px;
            text-transform: uppercase;
            background: #F0FDF8;
        }

        /* Hairline Divider */
        .hairline-divider {
            width: 100%;
            height: 1px;
            background: #E2E8F0;
            margin-top: 10px;
            margin-bottom: 14px;
        }

        /* 3-Column Editorial Metadata Grid */
        .meta-grid-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
        }
        .meta-grid-col {
            vertical-align: top;
            width: 33.333%;
            padding-right: 12px;
        }
        .meta-grid-col:last-child {
            padding-right: 0;
        }
        .grid-section-label {
            font-size: 7px;
            font-weight: 700;
            letter-spacing: 1.5px;
            color: #94A3B8;
            text-transform: uppercase;
            margin-bottom: 5px;
        }
        .grid-primary-val {
            font-size: 11px;
            font-weight: 700;
            color: #0F172A;
            margin-bottom: 2px;
            line-height: 1.25;
        }
        .grid-sub-val {
            font-size: 8px;
            color: #475569;
            line-height: 1.4;
        }
        .grid-muted-val {
            font-size: 7.5px;
            color: #64748B;
            margin-top: 2px;
        }

        /* Discreet Vendor Partners Strip */
        .vendor-strip {
            width: 100%;
            padding: 6px 10px;
            background: #F8FAFC;
            border-left: 2.5px solid #C5A059;
            font-size: 7.5px;
            color: #64748B;
            margin-bottom: 14px;
            border-radius: 0 3px 3px 0;
        }
        .vendor-strip strong {
            color: #0F172A;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* Items Table - Clean Luxury Hairline Style */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 14px;
        }
        .items-table th {
            background: transparent;
            color: #0F172A;
            font-size: 7.5px;
            font-weight: 700;
            letter-spacing: 1.2px;
            text-transform: uppercase;
            padding: 8px 6px;
            border-top: 1.5px solid #0F172A;
            border-bottom: 1.5px solid #0F172A;
        }
        .items-table th.col-no { width: 5%; text-align: center; }
        .items-table th.col-desc { width: 51%; text-align: left; }
        .items-table th.col-qty { width: 14%; text-align: center; }
        .items-table th.col-price { width: 15%; text-align: right; }
        .items-table th.col-total { width: 15%; text-align: right; }

        .items-table td {
            padding: 8px 6px;
            border-bottom: 1px solid #F1F5F9;
            font-size: 8.5px;
            vertical-align: middle;
        }
        .item-name {
            font-weight: 700;
            font-size: 9px;
            color: #0F172A;
            margin-bottom: 1px;
        }
        .item-desc {
            font-size: 7.5px;
            color: #64748B;
            line-height: 1.35;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }

        /* Bottom Section: Payment Info & Summary Calculations */
        .bottom-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 4px;
            margin-bottom: 16px;
        }
        .bottom-left-col {
            width: 48%;
            vertical-align: top;
            padding-right: 14px;
        }
        .bottom-right-col {
            width: 52%;
            vertical-align: top;
        }

        /* Left Side: Bank Transfer & Operational Notes */
        .section-label {
            font-size: 7px;
            font-weight: 700;
            letter-spacing: 1.5px;
            color: #94A3B8;
            text-transform: uppercase;
            margin-bottom: 6px;
        }
        .bank-accounts-block {
            font-size: 8px;
            color: #334155;
            line-height: 1.5;
            margin-bottom: 12px;
        }
        .bank-name {
            font-weight: 700;
            color: #0F172A;
        }
        .bank-no {
            font-weight: 700;
            color: #0F172A;
            letter-spacing: 0.5px;
        }

        .operational-notes-block {
            font-size: 7.2px;
            color: #64748B;
            line-height: 1.45;
            border-top: 1px solid #F1F5F9;
            padding-top: 8px;
        }
        .operational-notes-block strong {
            color: #1E293B;
        }

        /* Right Side: Calculation Summary */
        .summary-table {
            width: 100%;
            border-collapse: collapse;
        }
        .summary-table td {
            padding: 3.5px 0;
            font-size: 8.5px;
        }
        .summary-lbl {
            color: #64748B;
            text-align: left;
        }
        .summary-val {
            text-align: right;
            font-weight: 600;
            color: #1E293B;
        }

        .grand-total-row td {
            border-top: 1.5px solid #0F172A;
            border-bottom: 1.5px solid #0F172A;
            padding: 7px 0;
        }
        .grand-total-lbl {
            font-size: 9.5px;
            font-weight: 800;
            letter-spacing: 1.5px;
            color: #0F172A;
            text-transform: uppercase;
        }
        .grand-total-val {
            font-size: 13px;
            font-weight: 800;
            color: #0F172A;
            text-align: right;
        }

        .due-row td {
            padding: 6px 0 2px 0;
        }
        .due-settled {
            font-size: 8.5px;
            font-weight: 800;
            color: #065F46;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }
        .due-pending {
            font-size: 10.5px;
            font-weight: 800;
            color: #DC2626;
        }

        /* Signatures Section */
        .signatures-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 12px;
            margin-bottom: 16px;
        }
        .signatures-table td {
            width: 50%;
            vertical-align: bottom;
            text-align: center;
        }
        .sig-header {
            font-size: 7px;
            font-weight: 700;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            color: #94A3B8;
            margin-bottom: 10px;
        }
        .sig-spacer {
            height: 30px;
        }
        .sig-name {
            font-size: 8.5px;
            font-weight: 700;
            color: #0F172A;
            border-top: 1px solid #CBD5E1;
            display: inline-block;
            padding-top: 4px;
            min-width: 150px;
        }
        .sig-role {
            font-size: 7px;
            color: #94A3B8;
            margin-top: 2px;
        }

        /* Footer */
        .footer-line {
            border-top: 1px solid #F1F5F9;
            padding-top: 8px;
            text-align: center;
            font-size: 7px;
            color: #94A3B8;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            line-height: 1.4;
        }
    </style>
</head>
<body>
    @php
        $warehouseAddress = \App\Models\Setting::get('warehouse_address', 'Jl. K.H. Ahmad Dahlan, RT 04 / RW 01, Slawi Kulon, Kec. Slawi, Kab. Tegal');
        $rawWa = preg_replace('/\D+/', '', (string) \App\Models\Setting::get('admin_whatsapp', '6282255969313'));
        $displayWa = str_starts_with($rawWa, '62') ? '0' . substr($rawWa, 2) : $rawWa;
        if (strlen($displayWa) >= 10) {
            $displayWa = substr($displayWa, 0, 4) . '-' . substr($displayWa, 4, 4) . '-' . substr($displayWa, 8);
        }
        $ownerName = \App\Models\Setting::get('bca_account_name', 'EZZU RAKHMAT FIRDAUS');
        $freeKm = (int) \App\Models\Setting::get('free_distance_km', 10);
        $standardDays = (int) \App\Models\Setting::get('standard_package_max_days', 2);
        $extraDayRatePct = rtrim(rtrim(number_format((float) \App\Models\Setting::get('extra_day_charge_rate', 15), 1), '0'), '.');

        $eventStart = \Carbon\Carbon::parse($booking->event_date)->locale('id');
        $eventEnd = ($booking->event_end_date ? \Carbon\Carbon::parse($booking->event_end_date) : $eventStart->copy())->locale('id');
        $dueDateLabel = $eventEnd->copy()->addDay()->isoFormat('D MMMM Y');
        $setupDateLabel = $booking->effective_setup_date->copy()->locale('id')->isoFormat('D MMM Y');
        $teardownDateLabel = $eventEnd->copy()->addDay()->isoFormat('D MMM Y');
        $issuedAtLabel = ($booking->created_at ?? now())->copy()->locale('id')->isoFormat('D MMMM Y, HH:mm');

        $isSewaAlat = $booking->event_type === 'sewa_alat' || (float) $booking->package_price <= 0;
        $isCancelled = $booking->status === 'rejected';
        $isPaid = !$isCancelled && ($booking->status === 'completed' || ($booking->status === 'confirmed' && (float) $booking->remaining_balance <= 0));

        // Merge identical add-ons (same name + unit price) into single crisp rows
        $groupedAddons = $booking->addons
            ->groupBy(fn ($a) => mb_strtolower(trim((string) $a->addon_name)) . '|' . (string) ((float) $a->unit_price))
            ->map(function ($group) {
                $first = $group->first();
                return [
                    'name'       => $first->addon_name,
                    'unit_price' => (float) $first->unit_price,
                    'quantity'   => (int) $group->sum('quantity'),
                    'subtotal'   => (float) $group->sum('subtotal'),
                ];
            })
            ->values();
    @endphp

    <div class="invoice-canvas">
        
        <!-- HEADER -->
        <table class="header-table">
            <tr>
                <td class="header-brand-cell">
                    @if(file_exists(public_path('images/logo-invoice.png')))
                        <img src="{{ public_path('images/logo-invoice.png') }}" alt="NK Barokah Persewaan Tenda" class="brand-logo">
                    @elseif(file_exists(public_path('images/logo.png')))
                        <img src="{{ public_path('images/logo.png') }}" alt="NK Barokah" class="brand-logo">
                    @else
                        <h2 style="margin: 0 0 6px 0; color: #0F172A; font-size: 16px; font-weight: 800; letter-spacing: 2px;">NK BAROKAH</h2>
                    @endif

                    <p class="brand-details">
                        <strong>Kantor &amp; Gudang:</strong> {{ $warehouseAddress }}<br>
                        <strong>WhatsApp:</strong> {{ $displayWa }} &bull; 
                        <strong>Email:</strong> nkbarokah@gmail.com
                    </p>
                </td>
                <td class="header-meta-cell">
                    <div class="invoice-title">INVOICE</div>
                    <div class="invoice-number-line">#{{ $booking->invoice_number }}</div>
                    <div class="invoice-date-line">
                        Diterbitkan: {{ $issuedAtLabel }} WIB
                        @if(!$isCancelled && (float) $booking->remaining_balance > 0)
                            <br><span style="color: #64748B;">Batas Pelunasan: <strong style="color: #0F172A;">{{ $dueDateLabel }} (H+1)</strong></span>
                        @endif
                    </div>
                    <div>
                        @if($isCancelled)
                            <span class="status-pill status-pill-cancelled">&bull; {{ $booking->dp_forfeited ? 'DIBATALKAN (DP HANGUS)' : 'DIBATALKAN' }}</span>
                        @elseif($isPaid)
                            <span class="status-pill status-pill-paid">&bull; LUNAS (100% TERBAYAR)</span>
                        @elseif($booking->status === 'confirmed')
                            <span class="status-pill status-pill-dp">&bull; DP TERKONFIRMASI</span>
                        @else
                            <span class="status-pill status-pill-pending">&bull; MENUNGGU VERIFIKASI</span>
                        @endif
                    </div>
                </td>
            </tr>
        </table>

        <!-- HAIRLINE DIVIDER -->
        <div class="hairline-divider"></div>

        <!-- 3-COLUMN EDITORIAL METADATA GRID -->
        <table class="meta-grid-table">
            <tr>
                <!-- Col 1: Tagihan Kepada -->
                <td class="meta-grid-col">
                    <div class="grid-section-label">TAGIHAN KEPADA</div>
                    <div class="grid-primary-val">{{ strtoupper($booking->customer_name) }}</div>
                    <div class="grid-sub-val">{{ $booking->customer_phone }}</div>
                    @if($booking->customer_email && $booking->customer_email !== '-')
                        <div class="grid-muted-val">{{ $booking->customer_email }}</div>
                    @endif
                    @if($booking->event_type === 'khitanan' && $booking->child_name)
                        <div class="grid-muted-val">Ananda: {{ $booking->child_name }}</div>
                    @endif
                    @if(!$isSewaAlat && $booking->parents_name && $booking->parents_name !== '-')
                        <div class="grid-muted-val">Orang Tua: {{ $booking->parents_name }}</div>
                    @endif
                    @if(!$isSewaAlat && $booking->instagram_account && $booking->instagram_account !== '-')
                        <div class="grid-muted-val">Instagram: {{ $booking->instagram_account }}</div>
                    @endif
                </td>

                <!-- Col 2: Jadwal Acara -->
                <td class="meta-grid-col">
                    <div class="grid-section-label">{{ $isSewaAlat ? 'JADWAL SEWA ALAT' : 'JADWAL & OPERASIONAL' }}</div>
                    <div class="grid-primary-val">{{ $booking->formatted_event_date }}</div>
                    <div class="grid-sub-val">
                        Layanan: {{ $booking->event_type_label }}
                        @if($booking->event_time && $booking->event_time !== '-')
                            &bull; {{ $booking->event_time }}
                        @endif
                    </div>
                    <div class="grid-muted-val">
                        {{ $isSewaAlat ? 'Antar' : 'Pasang' }}: {{ $setupDateLabel }} &bull; {{ $isSewaAlat ? 'Ambil' : 'Bongkar' }}: {{ $teardownDateLabel }}
                    </div>
                </td>

                <!-- Col 3: Lokasi Pemasangan -->
                <td class="meta-grid-col">
                    <div class="grid-section-label">{{ $isSewaAlat ? 'LOKASI PENGIRIMAN' : 'LOKASI VENUE & LOGISTIK' }}</div>
                    <div class="grid-primary-val" style="font-size: 9.5px; font-weight: 600;">
                        {{ $booking->venue_address ?: 'Konfirmasi menyusul ke tim lapangan' }}
                    </div>
                    @if($booking->address_landmark && $booking->address_landmark !== '-')
                        <div class="grid-sub-val">Patokan: {{ $booking->address_landmark }}</div>
                    @endif
                    <div class="grid-muted-val">
                        @if(($booking->delivery_distance_km ?? 0) > 0)
                            Armada: {{ $booking->delivery_distance_km }} km dari Slawi Kulon
                        @else
                            Armada: Area Slawi (Gratis Ongkir s/d {{ $freeKm }} km)
                        @endif
                    </div>
                </td>
            </tr>
        </table>

        <!-- REKANAN VENDOR STRIP (JIKA ADA & BUKAN SEWA ALAT) -->
        @if(!$isSewaAlat && ($booking->vendor_name || $booking->vendor_mua || $booking->vendor_decor || $booking->vendor_photo_video || $booking->vendor_mc_wo))
        <div class="vendor-strip">
            <strong>Mitra Vendor:</strong>
            @php
                $vendorList = [];
                if ($booking->vendor_name) $vendorList[] = 'Rujukan: ' . $booking->vendor_name;
                if ($booking->vendor_mua && $booking->vendor_mua !== '-') $vendorList[] = 'MUA: ' . $booking->vendor_mua;
                if ($booking->vendor_decor && $booking->vendor_decor !== '-') $vendorList[] = 'Dekor: ' . $booking->vendor_decor;
                if ($booking->vendor_photo_video && $booking->vendor_photo_video !== '-') $vendorList[] = 'Dokumentasi: ' . $booking->vendor_photo_video;
                if ($booking->vendor_mc_wo && $booking->vendor_mc_wo !== '-') $vendorList[] = 'MC/WO: ' . $booking->vendor_mc_wo;
            @endphp
            {!! implode(' &nbsp;&bull;&nbsp; ', array_map('e', $vendorList)) !!}
        </div>
        @endif

        <!-- LINE ITEMS TABLE -->
        <table class="items-table">
            <thead>
                <tr>
                    <th class="col-no">No</th>
                    <th class="col-desc">Deskripsi Layanan &amp; Perlengkapan</th>
                    <th class="col-qty">Durasi / Qty</th>
                    <th class="col-price">Tarif Satuan</th>
                    <th class="col-total">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @php $no = 1; @endphp

                <!-- 1. Main Package (Disembunyikan untuk Sewa Alat Lepas Rp 0) -->
                @if(!$isSewaAlat)
                <tr>
                    <td class="text-center">{{ $no++ }}</td>
                    <td>
                        <div class="item-name">{{ $booking->package_name }}</div>
                        @php
                            $pkgDesc = trim((string) ($booking->package?->description ?? ''));
                            $tentSpecs = array_filter([
                                $booking->effective_tent_size ? 'Ukuran: ' . $booking->effective_tent_size : null,
                                ($booking->tent_color_theme && $booking->tent_color_theme !== '-') ? 'Tema Kain: ' . $booking->tent_color_theme : null,
                            ]);
                        @endphp
                        @if($pkgDesc !== '')
                            <div class="item-desc">{{ $pkgDesc }}</div>
                        @endif
                        @if(!empty($tentSpecs))
                            <div class="item-desc" style="color: #045D56; font-weight: 600; margin-top: 2px;">{{ implode(' · ', $tentSpecs) }}</div>
                        @endif
                    </td>
                    <td class="text-center">1 Paket</td>
                    <td class="text-right">Rp {{ number_format($booking->package_price, 0, ',', '.') }}</td>
                    <td class="text-right" style="font-weight: 700; color: #0F172A;">Rp {{ number_format($booking->package_price, 0, ',', '.') }}</td>
                </tr>
                @endif

                <!-- 2. Additional Day Charge (if any) -->
                @if($booking->additional_day_charge > 0)
                @php
                    $extraDaysCount = max(1, (int) $booking->duration_days - $standardDays);
                @endphp
                <tr>
                    <td class="text-center">{{ $no++ }}</td>
                    <td>
                        <div class="item-name">Tambahan Durasi Hari Acara</div>
                        <div class="item-desc">
                            Perpanjangan {{ $extraDaysCount }} hari melebihi durasi standar {{ $standardDays }} hari (tarif {{ $extraDayRatePct }}%/hari)
                        </div>
                    </td>
                    <td class="text-center">{{ $extraDaysCount }} Hari</td>
                    <td class="text-right">
                        Rp {{ number_format($booking->package_price * (\App\Models\Setting::getExtraDayChargeRateFloat()), 0, ',', '.') }}
                    </td>
                    <td class="text-right" style="font-weight: 700; color: #0F172A;">
                        Rp {{ number_format($booking->additional_day_charge, 0, ',', '.') }}
                    </td>
                </tr>
                @endif

                <!-- 3. Biaya Transport Muatan Tenda (if any) -->
                @if(($booking->transport_fee ?? 0) > 0)
                <tr>
                    <td class="text-center">{{ $no++ }}</td>
                    <td>
                        <div class="item-name">Biaya Transportasi Armada</div>
                        <div class="item-desc">
                            Pengiriman &amp; pengambilan armada ke lokasi (Jarak: {{ $booking->delivery_distance_km ?? 0 }} km dari Slawi Kulon)
                        </div>
                    </td>
                    <td class="text-center">1 Rute PP</td>
                    <td class="text-right">Rp {{ number_format($booking->transport_fee, 0, ',', '.') }}</td>
                    <td class="text-right" style="font-weight: 700; color: #0F172A;">
                        Rp {{ number_format($booking->transport_fee, 0, ',', '.') }}
                    </td>
                </tr>
                @endif

                <!-- 4. Addons / Item Sewa (Digabung otomatis bila nama & harga sama) -->
                @foreach($groupedAddons as $addon)
                <tr>
                    <td class="text-center">{{ $no++ }}</td>
                    <td>
                        <div class="item-name">{{ $isSewaAlat ? $addon['name'] : 'Tambahan: ' . $addon['name'] }}</div>
                        <div class="item-desc">{{ $isSewaAlat ? 'Sewa perlengkapan pesta lepas' : 'Perlengkapan tambahan pesanan di lokasi' }}</div>
                    </td>
                    <td class="text-center">{{ $addon['quantity'] }} Unit</td>
                    <td class="text-right">Rp {{ number_format($addon['unit_price'], 0, ',', '.') }}</td>
                    <td class="text-right" style="font-weight: 700; color: #0F172A;">Rp {{ number_format($addon['subtotal'], 0, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <!-- BOTTOM SECTION: PAYMENT INFO & SUMMARY TOTALS -->
        <table class="bottom-table">
            <tr>
                <!-- Sisi Kiri: Rekening Pembayaran Resmi & Ketentuan -->
                <td class="bottom-left-col">
                    <div class="section-label">REKENING RESMI PEMBAYARAN</div>
                    <div class="bank-accounts-block">
                        <div style="margin-bottom: 4px;">
                            <span class="bank-name">Bank BCA:</span> 
                            <span class="bank-no">{{ \App\Models\Setting::get('bca_account_number', '3620648107') }}</span><br>
                            <span style="font-size: 7.2px; color: #64748B;">a.n. {{ \App\Models\Setting::get('bca_account_name', 'EZZU RAKHMAT FIRDAUS') }}</span>
                        </div>
                        <div>
                            <span class="bank-name">Bank BRI:</span> 
                            <span class="bank-no">{{ \App\Models\Setting::get('bri_account_number', '344301026581532') }}</span><br>
                            <span style="font-size: 7.2px; color: #64748B;">a.n. {{ \App\Models\Setting::get('bri_account_name', 'EZZU RAKHMAT FIRDAUS') }}</span>
                        </div>
                    </div>

                    <div class="operational-notes-block">
                        <strong>Ketentuan Operasional Layanan:</strong><br>
                        @if($isSewaAlat)
                            1. Pengiriman perlengkapan dijadwalkan pada <strong>{{ $setupDateLabel }}</strong> dan pengambilan kembali pada <strong>{{ $teardownDateLabel }}</strong>.<br>
                        @else
                            1. Pemasangan tenda (loading) dijadwalkan pada <strong>{{ $setupDateLabel }}</strong> dan pembongkaran perlengkapan pada <strong>{{ $teardownDateLabel }}</strong>.<br>
                        @endif
                        2. @if($isCancelled)
                            Pesanan ini telah <strong>dibatalkan</strong>{{ $booking->dp_forfeited ? ' dengan ketentuan uang muka (DP) hangus' : '' }}.
                        @elseif((float) $booking->remaining_balance > 0)
                            Sisa pelunasan tagihan wajib diselesaikan maksimal <strong>{{ $dueDateLabel }} (H+1)</strong> setelah acara selesai.
                        @else
                            Pembayaran telah lunas secara penuh. Armada dan tim siap bertugas sesuai jadwal.
                        @endif<br>
                        3. Dokumen elektronik ini diterbitkan secara sah oleh sistem manajemen resmi NK Barokah tanpa tanda tangan basah.
                    </div>
                </td>

                <!-- Sisi Kanan: Summary Calculation -->
                <td class="bottom-right-col">
                    @php
                        $subtotalKotor = (float) $booking->package_price 
                            + (float) ($booking->addon_total_price ?? 0) 
                            + (float) ($booking->additional_day_charge ?? 0) 
                            + (float) ($booking->transport_fee ?? 0);
                    @endphp
                    <table class="summary-table">
                        <tr>
                            <td class="summary-lbl">Subtotal Biaya:</td>
                            <td class="summary-val">Rp {{ number_format($subtotalKotor, 0, ',', '.') }}</td>
                        </tr>

                        <!-- Diskon Rekanan Vendor -->
                        @if(($booking->discount_amount ?? 0) > 0)
                        <tr>
                            <td class="summary-lbl" style="color: #045D56;">
                                Diskon Rekanan Vendor{{ ($booking->discount_percent ?? 0) > 0 ? ' (' . rtrim(rtrim(number_format($booking->discount_percent, 1), '0'), '.') . '%)' : '' }}:
                            </td>
                            <td class="summary-val" style="color: #045D56;">-Rp {{ number_format($booking->discount_amount, 0, ',', '.') }}</td>
                        </tr>
                        @endif

                        <!-- Potongan Khusus Owner -->
                        @if(($booking->owner_discount ?? 0) > 0)
                        <tr>
                            <td class="summary-lbl" style="color: #9A7B38;">
                                Potongan Khusus Owner:
                            </td>
                            <td class="summary-val" style="color: #9A7B38;">-Rp {{ number_format($booking->owner_discount, 0, ',', '.') }}</td>
                        </tr>
                        @endif

                        <!-- GRAND TOTAL -->
                        <tr class="grand-total-row">
                            <td class="grand-total-lbl">TOTAL TAGIHAN:</td>
                            <td class="grand-total-val">Rp {{ number_format($booking->grand_total, 0, ',', '.') }}</td>
                        </tr>

                        <!-- Nominal Diterima -->
                        <tr>
                            <td class="summary-lbl" style="padding-top: 6px;">Pembayaran Diterima ({{ strtoupper($booking->payment_type) }}):</td>
                            <td class="summary-val" style="padding-top: 6px; color: #045D56; font-weight: 700;">
                                Rp {{ number_format($booking->amount_paid, 0, ',', '.') }}
                            </td>
                        </tr>

                        <!-- Sisa Pelunasan -->
                        <tr class="due-row">
                            <td class="summary-lbl">Sisa Pelunasan:</td>
                            <td class="summary-val">
                                @if($isCancelled)
                                    <span style="color: #64748B; font-weight: 700;">{{ $booking->dp_forfeited ? 'DIBATALKAN (DP HANGUS)' : 'DIBATALKAN' }}</span>
                                @elseif($isPaid)
                                    <span class="due-settled">LUNAS (100% TERBAYAR)</span>
                                @else
                                    <span class="due-pending">Rp {{ number_format($booking->remaining_balance, 0, ',', '.') }}</span>
                                @endif
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

        <!-- SIGNATURES SECTION (MANAGEMENT ONLY + OFFICIAL PAID STAMP) -->
        <table class="signatures-table">
            <tr>
                <td></td>
                <td>
                    <div class="sig-header">MANAJEMEN OPERASIONAL</div>
                    @if($isPaid)
                        <div><span class="paid-stamp">LUNAS</span></div>
                    @else
                        <div class="sig-spacer"></div>
                    @endif
                    <div class="sig-name">{{ strtoupper($ownerName) }}</div>
                    <div class="sig-role">NK Barokah Persewaan Tenda</div>
                </td>
            </tr>
        </table>

        <!-- FOOTER -->
        <div class="footer-line">
            NK BAROKAH PERSEWAAN TENDA &bull; SLAWI KULON, TEGAL, JAWA TENGAH &bull; WA: {{ $displayWa }} &bull; EMAIL: NKBAROKAH@GMAIL.COM<br>
            TERIMA KASIH ATAS KEPERCAYAAN ANDA MEMILIH NK BAROKAH TENDA.
        </div>

    </div>

</body>
</html>
