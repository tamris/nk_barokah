<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Kuitansi Reservasi - {{ $booking->invoice_number }}</title>
    <style>
        @page {
            margin: 20px 25px;
        }
        body { 
            font-family: 'Georgia', 'Times New Roman', serif; 
            font-size: 11px; 
            color: #2b2825; 
            line-height: 1.5; 
            padding: 10px;
        }
        .container {
            border: 2px solid #DEC69B;
            padding: 25px;
            background: #fff;
        }
        .header { 
            border-bottom: 2px solid #8C6D37; 
            padding-bottom: 15px; 
            margin-bottom: 20px; 
        }
        .brand-title { 
            font-size: 20px; 
            font-weight: bold; 
            letter-spacing: 2px; 
            text-transform: uppercase; 
            color: #191614; 
            margin: 0; 
        }
        .brand-subtitle { 
            font-size: 9px; 
            letter-spacing: 1px;
            color: #8C6D37; 
            text-transform: uppercase;
            margin: 3px 0 0 0; 
        }
        .brand-contact {
            font-family: Arial, sans-serif;
            font-size: 9px;
            color: #666;
            margin-top: 4px;
        }
        .invoice-meta { 
            float: right; 
            text-align: right; 
        }
        .invoice-meta h2 { 
            margin: 0; 
            font-size: 15px; 
            letter-spacing: 1px;
            color: #191614; 
        }
        .invoice-meta p { 
            margin: 2px 0; 
            font-size: 9px; 
            font-family: Arial, sans-serif;
            color: #555; 
        }
        .clear { clear: both; }
        
        .client-info { 
            margin-bottom: 20px; 
            padding: 12px 15px; 
            background: #FAF8F5; 
            border: 1px solid #EBDDC3; 
            border-radius: 4px; 
        }
        .client-info table { width: 100%; border-collapse: collapse; }
        .client-info td { font-size: 10px; padding: 3px 0; }
        
        .items-table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-bottom: 20px; 
        }
        .items-table th { 
            background: #191614; 
            color: #DEC69B; 
            text-align: left; 
            padding: 8px 10px; 
            font-size: 9px; 
            letter-spacing: 1px;
            text-transform: uppercase; 
        }
        .items-table td { 
            padding: 10px; 
            border-bottom: 1px solid #EFEAE2; 
            font-size: 10px; 
        }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        
        .summary-table { 
            width: 48%; 
            float: right; 
            border-collapse: collapse; 
        }
        .summary-table td { 
            padding: 5px 8px; 
            font-size: 10px; 
        }
        .summary-total { 
            font-weight: bold; 
            font-size: 12px; 
            border-top: 2px solid #8C6D37; 
            color: #191614;
        }
        
        .badge { 
            display: inline-block; 
            padding: 3px 8px; 
            font-family: Arial, sans-serif;
            font-weight: bold; 
            font-size: 9px; 
            border-radius: 3px; 
            letter-spacing: 0.5px;
        }
        .badge-pending { background: #FEF3C7; color: #92400E; border: 1px solid #FDE68A; }
        .badge-confirmed { background: #D1FAE5; color: #065F46; border: 1px solid #A7F3D0; }
        
        .watermark { 
            text-align: center; 
            margin-top: 25px; 
            padding: 10px; 
            border: 1px dashed #C59A4D; 
            background: #FAF8F5; 
            font-size: 9px; 
            color: #8C6D37; 
            font-style: italic;
        }
        .footer { 
            margin-top: 45px; 
            border-top: 1px solid #EBDDC3; 
            padding-top: 12px; 
            font-family: Arial, sans-serif;
            font-size: 8px; 
            color: #999; 
            text-align: center; 
        }
    </style>
</head>
<body>

    <div class="container">
        
        <!-- Header -->
        <div class="header">
            <div class="invoice-meta">
                <h2>{{ in_array($booking->status, ['confirmed', 'completed']) ? 'KUITANSI RESMI' : 'TANDA TERIMA RESERVASI' }}</h2>
                <p>No: <strong>{{ $booking->invoice_number }}</strong></p>
                <p>Tanggal Diterbitkan: {{ now()->format('d/m/Y H:i') }}</p>
                <div style="margin-top: 6px;">
                    <span class="badge {{ in_array($booking->status, ['confirmed', 'completed']) ? 'badge-confirmed' : 'badge-pending' }}">
                        STATUS: {{ in_array($booking->status, ['confirmed', 'completed']) ? 'TERVERIFIKASI (SAH)' : 'MENUNGGU VERIFIKASI' }}
                    </span>
                </div>
            </div>
            <div>
                <h1 class="brand-title">NK BAROKAH WEDDING ORGANIZER</h1>
                <p class="brand-subtitle">Professional Wedding Planner & Event Management</p>
                <p class="brand-contact">Jl. Ringroad Barat No. 88, D.I. Yogyakarta | WhatsApp: {{ config('wo.admin_whatsapp', '0812-3456-7890') }}</p>
            </div>
            <div class="clear"></div>
        </div>

        <!-- Client Info Box -->
        <div class="client-info">
            <table>
                <tr>
                    <td width="20%"><strong>Nama Mempelai:</strong></td>
                    <td width="35%">{{ $booking->customer_name }}</td>
                    <td width="20%"><strong>Tanggal Acara:</strong></td>
                    <td width="25%" style="color:#8C6D37; font-weight:bold;">
                        {{ \Carbon\Carbon::parse($booking->event_date)->isoFormat('dddd, D MMMM Y') }}
                    </td>
                </tr>
                <tr>
                    <td><strong>No. WhatsApp:</strong></td>
                    <td>{{ $booking->customer_phone }}</td>
                    <td><strong>Metode Bayar:</strong></td>
                    <td>Transfer Bank (BCA / BRI a.n Ezzu Rakhmat Firdaus - {{ strtoupper($booking->payment_type) }})</td>
                </tr>
            </table>
        </div>

        <!-- Services Breakdown Table -->
        <table class="items-table">
            <thead>
                <tr>
                    <th>Deskripsi Layanan & Fasilitas</th>
                    <th width="12%" class="text-center">Qty</th>
                    <th width="22%" class="text-right">Harga Satuan</th>
                    <th width="24%" class="text-right">Total Biaya</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <strong>{{ $booking->package_name }}</strong><br>
                        <span style="color:#777; font-size:8px;">Paket Utama Wedding Organizer & Kru Lapangan</span>
                    </td>
                    <td class="text-center">1 Paket</td>
                    <td class="text-right">Rp {{ number_format($booking->package_price, 0, ',', '.') }}</td>
                    <td class="text-right"><strong>Rp {{ number_format($booking->package_price, 0, ',', '.') }}</strong></td>
                </tr>
                @foreach($booking->addons as $addon)
                <tr>
                    <td>
                        <strong>Add-on: {{ $addon->addon_name }}</strong><br>
                        <span style="color:#777; font-size:8px;">Fasilitas Tambahan Pilihan</span>
                    </td>
                    <td class="text-center">{{ $addon->quantity }} pcs</td>
                    <td class="text-right">Rp {{ number_format($addon->unit_price, 0, ',', '.') }}</td>
                    <td class="text-right">Rp {{ number_format($addon->subtotal, 0, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Summary Totals -->
        <table class="summary-table">
            <tr>
                <td>Grand Total:</td>
                <td class="text-right"><strong>Rp {{ number_format($booking->grand_total, 0, ',', '.') }}</strong></td>
            </tr>
            <tr>
                <td>Nominal Diterima ({{ strtoupper($booking->payment_type) }}):</td>
                <td class="text-right" style="color:#059669; font-weight:bold;">
                    Rp {{ number_format($booking->amount_paid, 0, ',', '.') }}
                </td>
            </tr>
            <tr class="summary-total">
                <td>Sisa Pelunasan:</td>
                <td class="text-right" style="color: {{ $booking->remaining_balance > 0 ? '#DC2626' : '#191614' }};">
                    {{ $booking->remaining_balance > 0 ? 'Rp ' . number_format($booking->remaining_balance, 0, ',', '.') : 'LUNAS (100%)' }}
                </td>
            </tr>
        </table>
        <div class="clear"></div>

        <!-- Verification Seal Note -->
        <div class="watermark">
            @if(in_array($booking->status, ['confirmed', 'completed']))
                ✓ Kuitansi ini sah dan telah diverifikasi oleh bagian keuangan NK Barokah Wedding Organizer. Jadwal dan tim telah terkunci secara resmi.
            @else
                ⏳ Tanda terima digital sementara. Jadwal akan dikunci permanen pada Google Calendar setelah bukti transfer diverifikasi oleh admin.
            @endif
        </div>

        <!-- Footer -->
        <div class="footer">
            Dokumen ini diterbitkan secara otomatis dan sah oleh sistem reservasi NK Barokah Wedding Organizer. Simpan bukti ini untuk kelengkapan administrasi Anda.
        </div>

    </div>

</body>
</html>
