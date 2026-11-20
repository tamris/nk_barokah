<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number', 50)->unique(); // INV-YYYYMMDD-XXXXX
            $table->string('customer_name', 150);
            $table->string('customer_phone', 25);
            $table->string('customer_email', 100)->nullable();
            $table->date('event_date');
            
            // Relasi & Detail Snapshot Paket
            $table->foreignId('package_id')->constrained('packages');
            $table->string('package_name'); 
            $table->decimal('package_price', 15, 2);

            // Ringkasan Add-on & Finansial
            $table->decimal('addon_total_price', 15, 2)->default(0);
            $table->decimal('grand_total', 15, 2);
            $table->decimal('amount_paid', 15, 2);
            $table->decimal('remaining_balance', 15, 2);
            $table->enum('payment_type', ['dp', 'lunas']);
            $table->string('payment_proof_path');

            // Status Siklus Hidup Transaksi
            $table->enum('status', [
                'pending_verification', 
                'confirmed', 
                'rejected', 
                'completed'
            ])->default('pending_verification');

            // Integrasi Status Eksternal
            $table->boolean('synced_to_sheets')->default(false);
            $table->string('google_calendar_event_id')->nullable();

            $table->timestamps();

            // Index untuk kecepatan query pengecekan tanggal
            $table->index(['event_date', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};

