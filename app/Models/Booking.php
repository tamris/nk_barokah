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
        'package_price'     => 'decimal:2',
        'addon_total_price' => 'decimal:2',
        'grand_total'       => 'decimal:2',
        'amount_paid'       => 'decimal:2',
        'remaining_balance' => 'decimal:2',
        'synced_to_sheets'  => 'boolean',
    ];

    protected $appends = [
        'formatted_phone',
        'whatsapp_message',
        'whatsapp_url',
    ];

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
     * Template pesan WhatsApp profesional & lengkap sesuai status pembayaran dan reservasi.
     */
    public function getWhatsAppMessageAttribute(): string
    {
        $dateFormatted = \Carbon\Carbon::parse($this->event_date)->isoFormat('dddd, D MMMM Y');
        $grandTotalFormatted = 'Rp ' . number_format($this->grand_total, 0, ',', '.');
        $paidFormatted = 'Rp ' . number_format($this->amount_paid, 0, ',', '.');
        $remainingFormatted = 'Rp ' . number_format($this->remaining_balance, 0, ',', '.');
        $receiptUrl = route('booking.receipt', $this->invoice_number);

        // Kasus 1: Acara Selesai (Completed)
        if ($this->status === 'completed') {
            return "Halo Kak *{$this->customer_name}*, salam hangat dari *NK Barokah Wedding Organizer*! 💍✨\n\n" .
                "Alhamdulillah, seluruh rangkaian acara pernikahan Kak *{$this->customer_name}* pada hari *{$dateFormatted}* telah selesai dilaksanakan dengan baik dan lancar.\n\n" .
                "📋 *Rincian Acara:*\n" .
                "• No. Invoice: *#{$this->invoice_number}*\n" .
                "• Paket Pilihan: *{$this->package_name}*\n" .
                "• Status Pembayaran: *LUNAS (100%)*\n\n" .
                "Keluarga besar NK Barokah Wedding Organizer mengucapkan selamat menempuh hidup baru. Semoga menjadi keluarga yang sakinah, mawaddah, warahmah. Terima kasih sebesar-besarnya telah mempercayakan momen bahagia Kakak bersama kami! 🙏🥰";
        }

        // Kasus 2: Terkonfirmasi LUNAS 100%
        if ($this->status === 'confirmed' && ($this->remaining_balance <= 0 || strtolower($this->payment_type) === 'lunas')) {
            return "Halo Kak *{$this->customer_name}*, salam hangat dari *NK Barokah Wedding Organizer*! 💍✨\n\n" .
                "Kami mengonfirmasi bahwa pembayaran *PELUNASAN* untuk reservasi pernikahan Anda telah berhasil kami terima dan verifikasi. Status pembayaran Anda kini telah *LUNAS (100%)*.\n\n" .
                "📋 *Rincian Reservasi:*\n" .
                "• No. Invoice: *#{$this->invoice_number}*\n" .
                "• Paket Pilihan: *{$this->package_name}*\n" .
                "• Tanggal Acara: *{$dateFormatted}*\n" .
                "• Total Nilai Paket: *{$grandTotalFormatted}*\n" .
                "• Total Terbayar: *{$paidFormatted}*\n" .
                "• Sisa Tagihan: *Rp 0 (LUNAS 100%)*\n\n" .
                "📄 *Kuitansi Resmi Pelunasan:*\n" .
                "Kuitansi resmi dapat dilihat dan diunduh melalui tautan berikut:\n" .
                "{$receiptUrl}\n\n" .
                "Terima kasih banyak atas kepercayaannya bersama NK Barokah Wedding Organizer. Sampai jumpa di hari bahagia! 🙏😊";
        }

        // Kasus 3: Terkonfirmasi Down Payment (DP) - Ada Sisa Pelunasan
        if ($this->status === 'confirmed') {
            return "Halo Kak *{$this->customer_name}*, salam hangat dari *NK Barokah Wedding Organizer*! 💍✨\n\n" .
                "Kami mengonfirmasi bahwa pembayaran *Down Payment (DP)* untuk reservasi pernikahan Anda telah berhasil kami verifikasi secara sah. Jadwal acara pernikahan Anda telah resmi terdaftar dan terkunci di sistem kami.\n\n" .
                "📋 *Rincian Reservasi:*\n" .
                "• No. Invoice: *#{$this->invoice_number}*\n" .
                "• Paket Pilihan: *{$this->package_name}*\n" .
                "• Tanggal Acara: *{$dateFormatted}*\n" .
                "• Total Nilai Paket: *{$grandTotalFormatted}*\n" .
                "• Pembayaran DP Diterima: *{$paidFormatted}*\n" .
                "• Sisa Tagihan Pelunasan: *{$remainingFormatted}*\n\n" .
                "📄 *Kuitansi Resmi DP:*\n" .
                "Anda dapat melihat dan mengunduh kuitansi resmi melalui tautan berikut:\n" .
                "{$receiptUrl}\n\n" .
                "Terima kasih atas kepercayaannya bersama NK Barokah Wedding Organizer. Jika ada pertanyaan atau koordinasi persiapan acara, silakan balas pesan ini. Semoga persiapannya lancar hingga hari H! 🙏😊";
        }

        // Kasus 4: Menunggu Verifikasi Pembayaran (Pending Verification)
        if ($this->status === 'pending_verification') {
            return "Halo Kak *{$this->customer_name}*, salam hangat dari *NK Barokah Wedding Organizer*! ✨\n\n" .
                "Kami telah menerima reservasi Anda dengan rincian:\n" .
                "• No. Invoice: *#{$this->invoice_number}*\n" .
                "• Paket Pilihan: *{$this->package_name}*\n" .
                "• Tanggal Acara: *{$dateFormatted}*\n" .
                "• Nominal Transfer: *{$paidFormatted}* (" . strtoupper($this->payment_type) . ")\n\n" .
                "Saat ini bukti transfer sedang dalam proses verifikasi mutasi oleh tim keuangan kami. Kami akan segera mengabari Anda setelah pembayaran terverifikasi sah. Terima kasih atas kesabarannya! 🙏";
        }

        // Kasus 5: Reservasi Ditolak (Rejected)
        if ($this->status === 'rejected') {
            return "Halo Kak *{$this->customer_name}*, kami dari *NK Barokah Wedding Organizer*.\n\n" .
                "Mohon maaf, pengajuan reservasi dengan No. Invoice *#{$this->invoice_number}* belum dapat kami setujui karena kendala verifikasi bukti transfer pembayaran.\n\n" .
                "Silakan hubungi kami untuk informasi lebih lanjut atau melakukan pengajuan ulang. Terima kasih. 🙏";
        }

        // Fallback default
        return "Halo Kak *{$this->customer_name}*, kami dari *NK Barokah Wedding Organizer* ingin mengonfirmasi detail reservasi pernikahan Anda (#{$this->invoice_number}). Terima kasih!";
    }

    /**
     * URL tautan langsung ke WhatsApp dengan pesan pre-filled ter-encode rapi.
     */
    public function getWhatsAppUrlAttribute(): string
    {
        return "https://wa.me/{$this->formatted_phone}?text=" . urlencode($this->whatsapp_message);
    }
}

