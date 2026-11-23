<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->string('tent_color_theme', 100)->nullable()->after('package_price');
            $table->string('custom_tent_size', 150)->nullable()->after('tent_color_theme');
            $table->text('operational_notes')->nullable()->after('custom_tent_size');
            $table->string('job_source', 100)->nullable()->after('vendor_mc_wo');
            $table->date('setup_date')->nullable()->after('event_end_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn([
                'tent_color_theme',
                'custom_tent_size',
                'operational_notes',
                'job_source',
                'setup_date',
            ]);
        });
    }
};
