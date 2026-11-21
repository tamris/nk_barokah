<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->date('event_end_date')->nullable()->after('event_date');
            $table->unsignedTinyInteger('duration_days')->default(1)->after('event_end_date');
            $table->index(['event_end_date', 'status']);
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropIndex(['event_end_date', 'status']);
            $table->dropColumn(['event_end_date', 'duration_days']);
        });
    }
};
