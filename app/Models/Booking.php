<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Booking extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'event_date'        => 'date',
        'event_end_date'    => 'date',
        'duration_days'     => 'integer',
        'package_price'         => 'decimal:2',
        'additional_day_charge' => 'decimal:2',
        'addon_total_price'     => 'decimal:2',
        'discount_amount'       => 'decimal:2',
        'discount_percent'      => 'decimal:2',
        'owner_discount'        => 'decimal:2',
        'delivery_distance_km'  => 'decimal:2',
        'transport_fee'         => 'decimal:2',
        'grand_total'       => 'decimal:2',
        'amount_paid'       => 'decimal:2',
        'remaining_balance' => 'decimal:2',
        'synced_to_sheets'  => 'boolean',
        'survey_requested'  => 'boolean',
        'survey_date'       => 'date',
        'setup_date'        => 'date',
    ];

    protected $appends = [
        'formatted_phone',
        'formatted_event_date',
        'formatted_setup_date',
        'event_type_label',
        'whatsapp_message',
        'whatsapp_url',
    ];

    /**
     * Label representasi jenis acara untuk invoice, SPK, dan admin.
     */
    public function getEventTypeLabelAttribute(): string
    {
        return match ($this->event_type) {
            'khitanan'    => 'Khitanan / Sunatan',
            'tasyakuran'  => 'Tasyakuran / Pengajian',
            'instansi'    => 'Acara Instansi / Event',
            'sewa_alat'   => 'Sewa Alat Lepas',
            default       => 'Pernikahan',
        };
    }

    /**
     * Tanggal efektif pemasangan tenda (H-2 otomatis bila belum diset spesifik).
     */
    public function getEffectiveSetupDateAttribute(): \Carbon\Carbon
    {
        if ($this->setup_date) {
            return \Carbon\Carbon::parse($this->setup_date);
        }
        return \Carbon\Carbon::parse($this->event_date)->subDays(2);
    }

    /**
     * Format tanggal pemasangan / loading H-2 yang mudah dibaca tim lapangan.
     */
    public function getFormattedSetupDateAttribute(): string
    {
        return $this->effective_setup_date->isoFormat('ddd, D MMM Y');
    }

    public function package(): BelongsTo
    {
        return $this->belongsTo(Package::class);
    }

    public function addons(): HasMany
    {
        return $this->hasMany(BookingAddon::class);
    }

    /**
     * Format nomor telepon WhatsApp klien menjadi format internasional (62xxx).
     */
    public function getFormattedPhoneAttribute(): string
    {
        $phone = preg_replace('/[^\d]/', '', $this->customer_phone ?? '');
        return preg_replace('/^0/', '62', $phone);
    }

    /**
     * Format tanggal acara (1 hari atau rentang multi-hari acara).
     */
    public function getFormattedEventDateAttribute(): string
    {
        $start = \Carbon\Carbon::parse($this->event_date)->locale('id');
        $duration = $this->duration_days ?: 1;
        $suffix = $this->event_type === 'sewa_alat' ? "{$duration} Hari" : "{$duration} Hari Acara";

        if ($this->event_end_date && !$start->isSameDay($this->event_end_date)) {
            $end = \Carbon\Carbon::parse($this->event_end_date)->locale('id');
            $dateRange = $start->isSameMonth($end)
                ? $start->isoFormat('D') . ' - ' . $end->isoFormat('D MMMM Y')
                : $start->isoFormat('D MMMM') . ' - ' . $end->isoFormat('D MMMM Y');
            return "{$dateRange} ({$suffix})";
        }
        return $start->isoFormat('dddd, D MMMM Y') . " ({$suffix})";
    }

    /**
     * Template pesan WhatsApp profesional & dinamis sesuai jenis pesanan dan status pembayaran.
     */
    public function getWhatsAppMessageAttribute(): string
    {
        $brandName = 'NK Barokah Tenda';
        $dateFormatted = $this->formatted_event_date;
        $grandTotalFormatted = 'Rp ' . number_format($this->grand_total, 0, ',', '.');
        $paidFormatted = 'Rp ' . number_format($this->amount_paid, 0, ',', '.');
        $remainingFormatted = 'Rp ' . number_format($this->remaining_balance, 0, ',', '.');
        $receiptUrl = route('booking.receipt', $this->invoice_number);

        $eventType = $this->event_type ?: 'pernikahan';
        $isEquipmentOnly = $eventType === 'sewa_alat';

        $orderSubject = match ($eventType) {
            'sewa_alat'  => 'sewa alat & perlengkapan acara',
            'khitanan'   => $this->child_name ? "sewa tenda acara khitanan {$this->child_name}" : 'sewa tenda acara khitanan',
            'tasyakuran' => 'sewa tenda acara tasyakuran / pengajian',
            'instansi'   => 'sewa tenda acara instansi / event',
            default      => 'sewa tenda acara pernikahan',
        };

        $dateLabel = $isEquipmentOnly ? 'Tanggal Pemakaian' : 'Tanggal Acara';
        $packageLabel = $isEquipmentOnly ? 'Layanan' : 'Paket Pilihan';

        $surveyInfo = '';
        if ($this->survey_requested && $this->survey_date) {
            $slotLabel = match($this->survey_time_slot) {
                'morning'   => 'Pagi (09:00 - 12:00)',
                'afternoon' => 'Siang-Sore (13:00 - 17:00)',
                'flexible'  => 'Fleksibel',
                default     => $this->survey_time_slot ?: 'Fleksibel',
            };
            $surveyDateStr = \Carbon\Carbon::parse($this->survey_date)->locale('id')->isoFormat('dddd, D MMMM Y');
            $surveyInfo = "• Request Survey Lokasi: *{$surveyDateStr}* ({$slotLabel})\n";
            if ($this->survey_notes) {
                $surveyInfo .= "• Catatan Survey: _{$this->survey_notes}_\n";
            }
        }

        // Kasus 1: Pesanan Selesai (Completed)
        if ($this->status === 'completed') {
            $closingCompleted = match ($eventType) {
                'pernikahan' => "Keluarga besar {$brandName} mengucapkan selamat menempuh hidup baru, semoga menjadi keluarga yang sakinah, mawaddah, warahmah. Terima kasih sebesar-besarnya telah mempercayakan kebutuhan tenda & perlengkapan acara kepada kami! 🙏",
                'khitanan'   => "Semoga ananda menjadi anak yang sholeh, sehat, dan berbakti kepada orang tua. Terima kasih telah mempercayakan kebutuhan tenda & perlengkapan acara kepada {$brandName}! 🙏",
                default      => "Terima kasih sebesar-besarnya telah mempercayakan kebutuhan tenda & perlengkapan acara kepada {$brandName}. Semoga sukses dan lancar selalu! 🙏",
            };

            return "Halo Kak *{$this->customer_name}*, salam hangat dari *{$brandName}*! ✨\n\n" .
                "Alhamdulillah, pesanan {$orderSubject} atas nama Kak *{$this->customer_name}* pada *{$dateFormatted}* telah selesai dilaksanakan dengan baik dan lancar.\n\n" .
                "📋 *Rincian Pesanan:*\n" .
                "• No. Invoice: *#{$this->invoice_number}*\n" .
                "• Jenis Layanan: *{$this->event_type_label}*\n" .
                "• {$packageLabel}: *{$this->package_name}*\n" .
                "• Status Pembayaran: *LUNAS (100%)*\n\n" .
                $closingCompleted;
        }

        // Kasus 2: Terkonfirmasi LUNAS 100%
        if ($this->status === 'confirmed' && ($this->remaining_balance <= 0 || strtolower($this->payment_type) === 'lunas')) {
            return "Halo Kak *{$this->customer_name}*, salam hangat dari *{$brandName}*! ✨\n\n" .
                "Kami mengonfirmasi bahwa pembayaran *PELUNASAN* untuk pesanan {$orderSubject} Anda telah berhasil kami terima dan verifikasi. Status pembayaran Anda kini telah *LUNAS (100%)*.\n\n" .
                "📋 *Rincian Pesanan:*\n" .
                "• No. Invoice: *#{$this->invoice_number}*\n" .
                "• Jenis Layanan: *{$this->event_type_label}*\n" .
                "• {$packageLabel}: *{$this->package_name}*\n" .
                "• {$dateLabel}: *{$dateFormatted}*\n" .
                $surveyInfo .
                "• Total Tagihan: *{$grandTotalFormatted}*\n" .
                "• Total Terbayar: *{$paidFormatted}*\n" .
                "• Sisa Tagihan: *Rp 0 (LUNAS 100%)*\n\n" .
                "📄 *Kuitansi Resmi Pelunasan:*\n" .
                "Kuitansi resmi dapat dilihat dan diunduh melalui tautan berikut:\n" .
                "{$receiptUrl}\n\n" .
                "Terima kasih banyak atas kepercayaannya kepada {$brandName}. Jika ada koordinasi jadwal pengiriman atau pemasangan, silakan balas pesan ini! 🙏";
        }

        // Kasus 3: Terkonfirmasi Down Payment (DP) - Ada Sisa Pelunasan
        if ($this->status === 'confirmed') {
            return "Halo Kak *{$this->customer_name}*, salam hangat dari *{$brandName}*! ✨\n\n" .
                "Kami mengonfirmasi bahwa pembayaran *Down Payment (DP)* untuk pesanan {$orderSubject} Anda telah berhasil kami verifikasi. Jadwal pesanan Anda telah resmi terdaftar dan terkunci di sistem kami.\n\n" .
                "📋 *Rincian Pesanan:*\n" .
                "• No. Invoice: *#{$this->invoice_number}*\n" .
                "• Jenis Layanan: *{$this->event_type_label}*\n" .
                "• {$packageLabel}: *{$this->package_name}*\n" .
                "• {$dateLabel}: *{$dateFormatted}*\n" .
                $surveyInfo .
                "• Total Tagihan: *{$grandTotalFormatted}*\n" .
                "• Pembayaran DP Diterima: *{$paidFormatted}*\n" .
                "• Sisa Tagihan Pelunasan: *{$remainingFormatted}*\n\n" .
                "📄 *Kuitansi Resmi DP:*\n" .
                "Anda dapat melihat dan mengunduh kuitansi resmi melalui tautan berikut:\n" .
                "{$receiptUrl}\n\n" .
                "Terima kasih atas kepercayaannya kepada {$brandName}. Jika ada pertanyaan atau koordinasi persiapan pengiriman/pemasangan, silakan balas pesan ini! 🙏";
        }

        // Kasus 4: Menunggu Verifikasi Pembayaran (Pending Verification)
        if ($this->status === 'pending_verification') {
            return "Halo Kak *{$this->customer_name}*, salam hangat dari *{$brandName}*! ✨\n\n" .
                "Kami telah menerima pengajuan pesanan {$orderSubject} Anda dengan rincian:\n" .
                "• No. Invoice: *#{$this->invoice_number}*\n" .
                "• Jenis Layanan: *{$this->event_type_label}*\n" .
                "• {$packageLabel}: *{$this->package_name}*\n" .
                "• {$dateLabel}: *{$dateFormatted}*\n" .
                $surveyInfo .
                "• Nominal Transfer: *{$paidFormatted}* (" . strtoupper($this->payment_type) . ")\n\n" .
                "Saat ini bukti transfer sedang dalam proses verifikasi oleh tim kami. Kami akan segera mengabari Anda setelah pembayaran terverifikasi. Terima kasih! 🙏";
        }

        // Kasus 5: Pesanan Ditolak (Rejected)
        if ($this->status === 'rejected') {
            return "Halo Kak *{$this->customer_name}*, kami dari *{$brandName}*.\n\n" .
                "Mohon maaf, pengajuan pesanan dengan No. Invoice *#{$this->invoice_number}* belum dapat kami setujui karena kendala verifikasi bukti transfer pembayaran.\n\n" .
                "Silakan hubungi kami untuk informasi lebih lanjut atau pengajuan ulang. Terima kasih. 🙏";
        }

        // Fallback default
        return "Halo Kak *{$this->customer_name}*, kami dari *{$brandName}* ingin mengonfirmasi detail pesanan {$orderSubject} Anda (#{$this->invoice_number}). Terima kasih!";
    }

    /**
     * URL tautan langsung ke WhatsApp dengan pesan pre-filled ter-encode rapi.
     */
    public function getWhatsAppUrlAttribute(): string
    {
        return "https://wa.me/{$this->formatted_phone}?text=" . urlencode($this->whatsapp_message);
    }

    /**
     * Hitung ulang seluruh total biaya reservasi (addon, durasi, transport, diskon, sisa bayar).
     */
    public function recalculateTotals(): void
    {
        $this->load('addons');
        $addonTotal = (float) $this->addons->sum('subtotal');
        $basePrice = (float) $this->package_price;
        $extraCharge = (float) ($this->additional_day_charge ?? 0);
        $transportFee = (float) ($this->transport_fee ?? 0);

        $subtotal = $basePrice + $addonTotal + $extraCharge + $transportFee;

        // Hitung ulang diskon vendor rekanan jika ada persentase
        $vendorDiscount = (float) ($this->discount_amount ?? 0);
        if ((float) ($this->discount_percent ?? 0) > 0) {
            $vendorDiscount = min($subtotal, round(($this->discount_percent / 100) * $subtotal));
        } else {
            $vendorDiscount = min($vendorDiscount, $subtotal);
        }

        // Special Owner Discount (potongan harga langsung nominal rupiah dari owner)
        $ownerDiscount = (float) ($this->owner_discount ?? 0);
        $ownerDiscount = min($ownerDiscount, max(0, $subtotal - $vendorDiscount));

        $totalDiscount = $vendorDiscount + $ownerDiscount;
        $grandTotal = max(0, $subtotal - $totalDiscount);

        $amountPaid = (float) $this->amount_paid;
        $remainingBalance = max(0, $grandTotal - $amountPaid);

        $this->update([
            'addon_total_price' => $addonTotal,
            'discount_amount'   => $vendorDiscount,
            'owner_discount'    => $ownerDiscount,
            'grand_total'       => $grandTotal,
            'amount_paid'       => $amountPaid,
            'remaining_balance' => $remainingBalance,
        ]);
    }
}

