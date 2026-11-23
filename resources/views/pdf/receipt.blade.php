<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Kuitansi Resmi - {{ $booking->invoice_number }}</title>
    <style>
        @page {
            margin: 24px 28px;
        }
        * {
            box-sizing: border-box;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 10px;
            color: #1E293B;
            line-height: 1.45;
            background: #FFFFFF;
            margin: 0;
            padding: 0;
        }

        /* Outer Frame with Luxury Emerald Accent */
        .page-wrapper {
            border: 1px solid #D5E2DE;
            border-top: 4px solid #045D56;
            background: #FFFFFF;
            padding: 24px 26px 20px 26px;
            position: relative;
        }

        /* Header section */
        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 18px;
            padding-bottom: 14px;
            border-bottom: 1px solid #E2ECE8;
        }
        .brand-logo-cell {
            vertical-align: top;
            width: 58%;
        }
        .brand-logo-badge {
            display: inline-block;
            background: #045D56;
            color: #FFFFFF;
            font-weight: 800;
            font-size: 13px;
            letter-spacing: 1px;
            padding: 4px 8px;
            border-radius: 4px;
            margin-bottom: 6px;
        }
        .brand-title {
            font-size: 16px;
            font-weight: 800;
            letter-spacing: 0.5px;
            color: #045D56;
            margin: 0;
            text-transform: uppercase;
        }
        .brand-tagline {
            font-size: 8.5px;
            font-weight: 700;
            letter-spacing: 1.2px;
            color: #B8934A;
            text-transform: uppercase;
            margin: 2px 0 4px 0;
        }
        .brand-address {
            font-size: 8.5px;
            color: #64748B;
            margin: 0;
            line-height: 1.4;
        }

        .meta-cell {
            vertical-align: top;
            width: 42%;
            text-align: right;
        }
        .receipt-title {
            font-size: 15px;
            font-weight: 800;
            letter-spacing: 1px;
            color: #0F172A;
            margin: 0 0 3px 0;
            text-transform: uppercase;
        }
        .receipt-no {
            font-size: 11px;
            font-weight: 700;
            color: #0284C7;
            font-family: 'Courier New', Courier, monospace;
            margin: 0 0 2px 0;
        }
        .receipt-date {
            font-size: 8.5px;
            color: #64748B;
            margin: 0 0 6px 0;
        }
        .badge-verified {
            display: inline-block;
            background: #E6F7F0;
            color: #045D56;
            border: 1px solid #A8DAD3;
            font-weight: 800;
            font-size: 8.5px;
            letter-spacing: 0.5px;
            padding: 3px 10px;
            border-radius: 12px;
            text-transform: uppercase;
        }
        .badge-pending {
            display: inline-block;
            background: #FEF3C7;
            color: #92400E;
            border: 1px solid #FDE68A;
            font-weight: 800;
            font-size: 8.5px;
            letter-spacing: 0.5px;
            padding: 3px 10px;
            border-radius: 12px;
            text-transform: uppercase;
        }

        /* Client Info Box */
        .info-card {
            width: 100%;
            background: #F8FAF9;
            border: 1px solid #E2ECE8;
            border-radius: 6px;
            margin-bottom: 18px;
            padding: 10px 14px;
        }
        .info-table {
            width: 100%;
            border-collapse: collapse;
        }
        .info-table td {
            padding: 3px 4px;
            vertical-align: top;
            font-size: 9.5px;
        }
        .info-label {
            color: #64748B;
            font-size: 8px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 1px;
        }
        .info-val {
            font-size: 10px;
            font-weight: 700;
            color: #0F172A;
        }
        .info-val-event {
            font-size: 10.5px;
            font-weight: 800;
            color: #045D56;
        }

        /* Items Table */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 16px;
        }
        .items-table th {
            background: #045D56;
            color: #FFFFFF;
            font-size: 8.5px;
            font-weight: 700;
            letter-spacing: 0.8px;
            text-transform: uppercase;
            padding: 8px 10px;
            border: none;
        }
        .items-table th.col-qty { width: 14%; text-align: center; }
        .items-table th.col-rate { width: 22%; text-align: right; }
        .items-table th.col-total { width: 24%; text-align: right; }

        .items-table td {
            padding: 9px 10px;
            border-bottom: 1px solid #EDF2F0;
            font-size: 9.5px;
            vertical-align: middle;
        }
        .item-name {
            font-weight: 700;
            font-size: 10px;
            color: #0F172A;
            margin-bottom: 1px;
        }
        .item-desc {
            font-size: 8px;
            color: #64748B;
            line-height: 1.3;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-mono { font-family: 'Courier New', Courier, monospace; }

        /* Bottom Section: Stamp (Left) + Totals (Right) */
        .bottom-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 14px;
        }
        .bottom-table td {
            vertical-align: top;
        }
        .seal-cell {
            width: 48%;
            padding-right: 15px;
        }
        .summary-cell {
            width: 52%;
        }

        /* Official Digital Stamp */
        .official-stamp {
            border: 2px dashed #045D56;
            background: #F4F9F7;
            border-radius: 6px;
            padding: 10px 12px;
            text-align: center;
        }
        .stamp-badge {
            font-size: 7.5px;
            font-weight: 800;
            letter-spacing: 1px;
            color: #B8934A;
            text-transform: uppercase;
            margin-bottom: 2px;
        }
        .stamp-org {
            font-size: 10px;
            font-weight: 800;
            color: #045D56;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            margin-bottom: 2px;
        }
        .stamp-status {
            font-size: 8.5px;
            font-weight: 700;
            color: #065F46;
            margin-bottom: 4px;
        }
        .stamp-note {
            font-size: 7.5px;
            color: #475569;
            line-height: 1.35;
        }

        /* Summary Calculations Table */
        .summary-box {
            width: 100%;
            border-collapse: collapse;
            background: #FAFDFC;
            border: 1px solid #E2ECE8;
            border-radius: 6px;
            overflow: hidden;
        }
        .summary-box td {
            padding: 6px 12px;
            font-size: 9.5px;
        }
        .summary-row-alt {
            background: #F4F8F6;
        }
        .summary-label {
            color: #475569;
            font-weight: 600;
        }
        .summary-val {
            text-align: right;
            font-weight: 700;
            color: #0F172A;
        }
        .summary-grand {
            border-top: 1px solid #CBD5E1;
            border-bottom: 1px solid #CBD5E1;
            background: #FFFFFF;
        }
        .summary-grand .summary-label {
            font-size: 10.5px;
            font-weight: 800;
            color: #0F172A;
        }
        .summary-grand .summary-val {
            font-size: 12px;
            font-weight: 800;
            color: #0F172A;
        }
        .paid-val {
            color: #045D56;
            font-weight: 800;
        }
        .due-settled {
            color: #045D56;
            font-weight: 800;
            background: #E6F7F0;
            padding: 2px 6px;
            border-radius: 4px;
            display: inline-block;
        }
        .due-pending {
            color: #DC2626;
            font-weight: 800;
            background: #FEE2E2;
            padding: 2px 6px;
            border-radius: 4px;
            display: inline-block;
        }

        /* Notice & Watermark Box */
        .guarantee-bar {
            background: #F8FAF9;
            border-left: 3px solid #045D56;
            padding: 7px 12px;
            margin-top: 6px;
            margin-bottom: 14px;
            font-size: 8px;
            color: #475569;
            line-height: 1.4;
        }

        /* Footer */
        .footer-note {
            border-top: 1px solid #E2ECE8;
            padding-top: 10px;
            text-align: center;
            font-size: 8px;
            color: #94A3B8;
            line-height: 1.4;
        }
    </style>
</head>
<body>

    <div class="page-wrapper">
        
        <!-- HEADER -->
        <table class="header-table">
            <tr>
                <td class="brand-logo-cell">
                    <div>
                        <span class="brand-logo-badge">NK</span>
                    </div>
                    <h1 class="brand-title">NK Barokah Wedding Organizer</h1>
                    <div class="brand-tagline">Exclusive Wedding Planner & Tent Management</div>
                    <p class="brand-address">
                        Jl. Ringroad Barat No. 88, D.I. Yogyakarta &bull; Kontak Resmi: {{ \App\Models\Setting::get('admin_whatsapp', '6282255969313') }}<br>
                        Email: info@nkbarokah.com &bull; Website: nkbarokah.com
                    </p>
                </td>
                <td class="meta-cell">
                    <div class="receipt-title">{{ in_array($booking->status, ['confirmed', 'completed']) ? 'KUITANSI PEMBAYARAN' : 'TANDA TERIMA RESERVASI' }}</div>
                    <div class="receipt-no">#{{ $booking->invoice_number }}</div>
                    <div class="receipt-date">Diterbitkan: {{ now()->translatedFormat('d F Y, H:i') }} WIB</div>
                    <div>
                        @if(in_array($booking->status, ['confirmed', 'completed']))
                            <span class="badge-verified">STATUS: TERVERIFIKASI RESMI</span>
                        @else
                            <span class="badge-pending">STATUS: MENUNGGU VERIFIKASI</span>
                        @endif
                    </div>
                </td>
            </tr>
        </table>

        <!-- CLIENT & EVENT INFORMATION CARD -->
        <div class="info-card">
            <table class="info-table">
                <tr>
                    <td width="50%">
                        <div class="info-label">Nama Mempelai / Pemesan</div>
                        <div class="info-val">{{ strtoupper($booking->customer_name) }}</div>
                    </td>
                    <td width="50%">
                        <div class="info-label">Jadwal & Durasi Acara</div>
                        <div class="info-val-event">{{ $booking->formatted_event_date }}</div>
                    </td>
                </tr>
                <tr>
                    <td style="padding-top: 8px;">
                        <div class="info-label">Kontak WhatsApp Klien</div>
                        <div class="info-val font-mono">{{ $booking->customer_phone }}</div>
                    </td>
                    <td style="padding-top: 8px;">
                        <div class="info-label">Metode Pembayaran</div>
                        <div class="info-val">
                            Transfer Bank &bull; {{ strtoupper($booking->payment_type) }} 
                            ({{ $booking->payment_type === 'lunas' ? 'Pelunasan 100%' : 'Uang Muka / DP' }})
                        </div>
                    </td>
                </tr>
                @if($booking->venue_address)
                <tr>
                    <td colspan="2" style="padding-top: 8px; border-top: 1px dashed #E2ECE8;">
                        <div class="info-label">Lokasi Pemasangan Tenda / Venue</div>
                        <div class="info-val">
                            {{ $booking->venue_address }} 
                            @if($booking->address_landmark) <span style="font-weight: normal; color: #64748B;">(Patokan: {{ $booking->address_landmark }})</span>@endif
                            @if(($booking->delivery_distance_km ?? 0) > 0) <span style="font-weight: normal; color: #045D56;"> &bull; Estimasi Jarak: {{ $booking->delivery_distance_km }} km dari Slawi</span>@endif
                        </div>
                    </td>
                </tr>
                @endif
            </table>
        </div>

        <!-- LINE ITEMS TABLE -->
        <table class="items-table">
            <thead>
                <tr>
                    <th>Rincian Layanan & Fasilitas</th>
                    <th class="col-qty">Durasi / Qty</th>
                    <th class="col-rate">Harga Satuan</th>
                    <th class="col-total">Total Biaya</th>
                </tr>
            </thead>
            <tbody>
                <!-- Main Package -->
                <tr>
                    <td>
                        <div class="item-name">{{ $booking->package_name }}</div>
                        <div class="item-desc">Paket Utama Wedding Organizer, Kru Lapangan, dan Fasilitas Tenda Standar</div>
                    </td>
                    <td class="text-center font-mono">1 Paket</td>
                    <td class="text-right font-mono">Rp {{ number_format($booking->package_price, 0, ',', '.') }}</td>
                    <td class="text-right font-mono" style="font-weight: 800; color: #045D56;">Rp {{ number_format($booking->package_price, 0, ',', '.') }}</td>
                </tr>

                <!-- Additional Day Charge (if any) -->
                @if($booking->additional_day_charge > 0)
                <tr style="background: #FAFCFB;">
                    <td>
                        <div class="item-name">Charge Tambahan Durasi Acara</div>
                        <div class="item-desc">
                            Penambahan durasi {{ max(1, $booking->duration_days - (int) \App\Models\Setting::get('standard_package_max_days', 2)) }} hari melebihi batas standar paket
                        </div>
                    </td>
                    <td class="text-center font-mono">{{ max(1, $booking->duration_days - (int) \App\Models\Setting::get('standard_package_max_days', 2)) }} Hari</td>
                    <td class="text-right font-mono">
                        Rp {{ number_format($booking->package_price * (\App\Models\Setting::getExtraDayChargeRateFloat()), 0, ',', '.') }}
                    </td>
                    <td class="text-right font-mono" style="font-weight: 800; color: #B8934A;">
                        Rp {{ number_format($booking->additional_day_charge, 0, ',', '.') }}
                    </td>
                </tr>
                @endif

                <!-- Biaya Transport Muatan Tenda (if any) -->
                @if(($booking->transport_fee ?? 0) > 0)
                <tr style="background: #FAFCFB;">
                    <td>
                        <div class="item-name">Biaya Transport Muatan Tenda</div>
                        <div class="item-desc">
                            Pengiriman armada tenda &amp; muatan logistik lokasi acara (Jarak: {{ $booking->delivery_distance_km ?? 0 }} km dari Slawi)
                        </div>
                    </td>
                    <td class="text-center font-mono">1 Rute</td>
                    <td class="text-right font-mono">Rp {{ number_format($booking->transport_fee, 0, ',', '.') }}</td>
                    <td class="text-right font-mono" style="font-weight: 800; color: #045D56;">
                        Rp {{ number_format($booking->transport_fee, 0, ',', '.') }}
                    </td>
                </tr>
                @endif

                <!-- Addons (if any) -->
                @foreach($booking->addons as $addon)
                <tr>
                    <td>
                        <div class="item-name">Tambahan: {{ $addon->addon_name }}</div>
                        <div class="item-desc">Fasilitas dan perlengkapan ekstra pilihan pemesan</div>
                    </td>
                    <td class="text-center font-mono">{{ $addon->quantity }} Unit</td>
                    <td class="text-right font-mono">Rp {{ number_format($addon->unit_price, 0, ',', '.') }}</td>
                    <td class="text-right font-mono" style="font-weight: 700;">Rp {{ number_format($addon->subtotal, 0, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <!-- BOTTOM SECTION: STAMP (LEFT) & SUMMARY TOTALS (RIGHT) -->
        <table class="bottom-table">
            <tr>
                <td class="seal-cell">
                    <div class="official-stamp">
                        <div class="stamp-badge">&bull; DIGITAL SEAL &bull;</div>
                        <div class="stamp-org">NK Barokah Wedding Organizer</div>
                        <div class="stamp-status">
                            @if(in_array($booking->status, ['confirmed', 'completed']))
                                [ VALID &amp; VERIFIED ]
                            @else
                                [ PENDING VERIFICATION ]
                            @endif
                        </div>
                        <div class="stamp-note">
                            Dokumen kuitansi ini sah diterbitkan oleh sistem resmi NK Barokah.<br>
                            Jadwal tanggal acara telah terkunci dan disinkronkan ke kalender operasional tim.
                        </div>
                    </div>
                </td>
                <td class="summary-cell">
                    <table class="summary-box">
                        @if(($booking->discount_amount ?? 0) > 0)
                        <tr style="background: #F0FDF8;">
                            <td class="summary-label" style="color: #045D56; font-weight: 700;">
                                Diskon Vendor Rekanan{{ ($booking->discount_percent ?? 0) > 0 ? ' (' . rtrim(rtrim(number_format($booking->discount_percent, 1), '0'), '.') . '%)' : '' }}:
                            </td>
                            <td class="summary-val font-mono" style="color: #045D56; font-weight: 800;">-Rp {{ number_format($booking->discount_amount, 0, ',', '.') }}</td>
                        </tr>
                        @endif
                        @if(($booking->owner_discount ?? 0) > 0)
                        <tr style="background: #FFFDF5;">
                            <td class="summary-label" style="color: #92400E; font-weight: 700;">
                                Potongan Khusus Owner:
                            </td>
                            <td class="summary-val font-mono" style="color: #92400E; font-weight: 800;">-Rp {{ number_format($booking->owner_discount, 0, ',', '.') }}</td>
                        </tr>
                        @endif
                        <tr class="summary-grand">
                            <td class="summary-label">Grand Total Biaya:</td>
                            <td class="summary-val font-mono">Rp {{ number_format($booking->grand_total, 0, ',', '.') }}</td>
                        </tr>
                        <tr class="summary-row-alt">
                            <td class="summary-label">Nominal Diterima ({{ strtoupper($booking->payment_type) }}):</td>
                            <td class="summary-val font-mono paid-val">Rp {{ number_format($booking->amount_paid, 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <td class="summary-label">Sisa Pelunasan:</td>
                            <td class="summary-val">
                                @if($booking->remaining_balance <= 0 || strtolower($booking->payment_type) === 'lunas')
                                    <span class="due-settled">LUNAS (100% TERBAYAR)</span>
                                @else
                                    <span class="due-pending font-mono">Rp {{ number_format($booking->remaining_balance, 0, ',', '.') }}</span>
                                @endif
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

        <!-- GUARANTEE & SERVICE NOTE -->
        <div class="guarantee-bar">
            <strong>Catatan Operasional &amp; Jaminan Layanan:</strong>
            Pemasangan tenda (loading) dijadwalkan pada H-2 dan pembongkaran perlengkapan pada H+1 setelah acara selesai.
            @if($booking->remaining_balance > 0)
                Sisa pelunasan tagihan wajib diselesaikan maksimal H-7 sebelum hari pelaksanaan acara.
            @else
                Pembayaran telah lunas secara penuh. Tim operasional siap bertugas sesuai jadwal yang telah ditentukan.
            @endif
        </div>

        <!-- FOOTER -->
        <div class="footer-note">
            Kuitansi ini dibuat secara elektronik dan berlaku sebagai tanda bukti transaksi yang sah tanpa memerlukan tanda tangan basah.<br>
            NK Barokah Wedding Organizer &bull; Yogyakarta, Indonesia &bull; Terima kasih atas kepercayaan Anda bersama kami.
        </div>

    </div>

</body>
</html>