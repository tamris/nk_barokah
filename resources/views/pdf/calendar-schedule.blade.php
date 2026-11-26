<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>{{ $modeTitle }} — {{ $monthLabel }} | NK Barokah Tenda</title>
    <style>
        @page {
            size: a4 landscape;
            margin: 11mm 12mm 12mm 12mm;
        }
        * {
            box-sizing: border-box;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 9px;
            color: #0F172A;
            line-height: 1.4;
            background: #FFFFFF;
            margin: 0;
            padding: 0;
        }
        .header-table {
            width: 100%;
            border-collapse: collapse;
            border-bottom: 2px solid #045D56;
            padding-bottom: 8px;
            margin-bottom: 10px;
        }
        .header-table td {
            vertical-align: top;
            padding-bottom: 8px;
        }
        .brand-wrap {
            width: 56%;
        }
        .brand-logo {
            height: 36px;
            width: auto;
            vertical-align: middle;
            margin-right: 8px;
        }
        .brand-name {
            font-size: 14px;
            font-weight: 800;
            color: #045D56;
            letter-spacing: 0.3px;
        }
        .brand-sub {
            font-size: 8px;
            color: #64748B;
            margin-top: 2px;
        }
        .doc-meta {
            width: 44%;
            text-align: right;
        }
        .doc-title {
            font-size: 14px;
            font-weight: 800;
            color: #0F172A;
            letter-spacing: 0.6px;
            margin: 0;
        }
        .doc-period {
            font-size: 11px;
            font-weight: 700;
            color: #045D56;
            margin-top: 2px;
        }
        .doc-printed {
            font-size: 7.5px;
            color: #64748B;
            margin-top: 3px;
        }

        .summary-bar {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
            background: #F8FAFA;
            border: 1px solid #E2E8F0;
        }
        .summary-bar td {
            padding: 6px 10px;
            font-size: 8.5px;
            color: #334155;
            border-right: 1px solid #E2E8F0;
        }
        .summary-bar td:last-child {
            border-right: none;
        }
        .summary-bar strong {
            font-size: 11px;
            color: #0F172A;
            margin-left: 4px;
        }

        table.schedule-table {
            width: 100%;
            border-collapse: collapse;
        }
        table.schedule-table th {
            background: #045D56;
            color: #FFFFFF;
            font-size: 8px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            text-align: left;
            padding: 7px 7px;
            border: 1px solid #045D56;
        }
        table.schedule-table td {
            padding: 7px 7px;
            border: 1px solid #CBD5E1;
            vertical-align: top;
            font-size: 8.5px;
        }
        table.schedule-table tbody tr:nth-child(even) {
            background: #F8FAFC;
        }

        .date-day {
            font-weight: 700;
            color: #0F172A;
            font-size: 9px;
        }
        .date-num {
            color: #475569;
            font-size: 8px;
            margin-top: 1px;
        }

        .badge {
            display: inline-block;
            padding: 3px 6px;
            border-radius: 4px;
            font-size: 7.5px;
            font-weight: 800;
            letter-spacing: 0.3px;
            text-transform: uppercase;
            white-space: nowrap;
        }
        .badge-setup       { background: #DBEAFE; color: #1E40AF; border: 1px solid #93C5FD; }
        .badge-teardown    { background: #FEF3C7; color: #92400E; border: 1px solid #FCD34D; }
        .badge-survey      { background: #F3E8FF; color: #6B21A8; border: 1px solid #D8B4FE; }
        .badge-survey_done { background: #D1FAE5; color: #065F46; border: 1px solid #6EE7B7; }
        .badge-event       { background: #D1FAE5; color: #065F46; border: 1px solid #6EE7B7; }
        .badge-completed   { background: #E2E8F0; color: #334155; border: 1px solid #CBD5E1; }

        .client-name {
            font-weight: 800;
            color: #0F172A;
            font-size: 9px;
        }
        .sub-meta {
            color: #475569;
            font-size: 7.8px;
            margin-top: 2px;
        }
        .service-name {
            font-weight: 700;
            color: #0F172A;
        }
        .addons-list {
            margin-top: 3px;
            font-size: 7.6px;
            color: #334155;
        }
        .landmark {
            margin-top: 2px;
            font-size: 7.6px;
            color: #045D56;
            font-weight: 600;
        }
        .notes-text {
            font-size: 8px;
            color: #1E293B;
        }

        .empty-box {
            border: 1px dashed #94A3B8;
            background: #F8FAFC;
            padding: 28px;
            text-align: center;
            color: #475569;
            font-size: 10px;
            margin-top: 10px;
        }

        .footer-note {
            margin-top: 10px;
            font-size: 7.5px;
            color: #64748B;
            text-align: right;
        }
    </style>
</head>
<body>
    <table class="header-table">
        <tr>
            <td class="brand-wrap">
                <table>
                    <tr>
                        <td style="padding: 0; border: none; vertical-align: middle;">
                            @php
                                $logoPath = public_path('images/logo.png');
                            @endphp
                            @if(file_exists($logoPath))
                                <img src="{{ $logoPath }}" alt="NK Barokah" class="brand-logo">
                            @endif
                        </td>
                        <td style="padding: 0; border: none; vertical-align: middle;">
                            <div class="brand-name">NK BAROKAH TENDA</div>
                            <div class="brand-sub">Persewaan Tenda &amp; Alat Pesta &bull; {{ $settings['warehouse_address'] ?? 'Slawi Kulon, Kec. Slawi, Kab. Tegal' }}</div>
                        </td>
                    </tr>
                </table>
            </td>
            <td class="doc-meta">
                <p class="doc-title">{{ $modeTitle }}</p>
                <div class="doc-period">Periode: {{ $monthLabel }}</div>
                <div class="doc-printed">Dicetak: {{ $printedAt }}</div>
            </td>
        </tr>
    </table>

    <table class="summary-bar">
        <tr>
            @if($mode === 'operations')
                <td>Total Tugas Bulan Ini: <strong>{{ $counts['total'] }}</strong></td>
                <td>Survei Lokasi: <strong>{{ $counts['survey'] }}</strong></td>
                <td>Pasang Tenda (H-2): <strong>{{ $counts['setup'] }}</strong></td>
                <td>Bongkar Tenda (H+1): <strong>{{ $counts['teardown'] }}</strong></td>
            @else
                <td>Total Acara Bulan Ini: <strong>{{ $counts['events'] }}</strong></td>
                <td>Dokumen Khusus Operasional (Tanpa Nominal Harga)</td>
            @endif
        </tr>
    </table>

    @if(empty($rows))
        <div class="empty-box">
            Tidak ada jadwal terkonfirmasi pada periode <strong>{{ $monthLabel }}</strong>.
        </div>
    @else
        <table class="schedule-table">
            <thead>
                <tr>
                    <th style="width: 11%;">Tanggal</th>
                    <th style="width: 11%;">Tugas / Status</th>
                    <th style="width: 17%;">Klien &amp; Kontak</th>
                    <th style="width: 22%;">Paket, Tenda &amp; Tambahan</th>
                    <th style="width: 23%;">Alamat Lokasi &amp; Patokan</th>
                    <th style="width: 16%;">Catatan Lapangan</th>
                </tr>
            </thead>
            <tbody>
                @foreach($rows as $row)
                    <tr>
                        <td>
                            <div class="date-day">{{ $row['day_name'] }}</div>
                            <div class="date-num">{{ $row['date_label'] }}</div>
                        </td>
                        <td>
                            <span class="badge badge-{{ $row['type'] }}">{{ $row['type_label'] }}</span>
                        </td>
                        <td>
                            <div class="client-name">{{ $row['client'] }}</div>
                            <div class="sub-meta">#{{ $row['invoice'] }}</div>
                            <div class="sub-meta">HP: {{ $row['phone'] ?: '-' }}</div>
                        </td>
                        <td>
                            <div class="service-name">{{ $row['service'] }}</div>
                            @if(!empty($row['tent_size']))
                                <div class="sub-meta"><strong>Ukuran:</strong> {{ $row['tent_size'] }}</div>
                            @endif
                            @if(!empty($row['tent_theme']))
                                <div class="sub-meta"><strong>Tema:</strong> {{ $row['tent_theme'] }}</div>
                            @endif
                            @if(!empty($row['addons']))
                                <div class="addons-list"><strong>Item:</strong> {{ $row['addons'] }}</div>
                            @endif
                        </td>
                        <td>
                            <div>{{ $row['address'] }}</div>
                            @if(!empty($row['landmark']))
                                <div class="landmark">Patokan: {{ $row['landmark'] }}</div>
                            @endif
                        </td>
                        <td>
                            <div class="notes-text">{{ $row['notes'] ?: '-' }}</div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <div class="footer-note">
        Dokumen jadwal internal kru lapangan NK Barokah Tenda &bull; Cek pembaruan terbaru dengan Koordinator Lapangan / Admin sebelum keberangkatan armada.
    </div>
</body>
</html>
