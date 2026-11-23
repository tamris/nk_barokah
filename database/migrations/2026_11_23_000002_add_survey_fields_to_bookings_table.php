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
            $table->boolean('survey_requested')->default(false)->after('instagram_account');
            $table->date('survey_date')->nullable()->after('survey_requested');
            $table->string('survey_time_slot', 50)->nullable()->after('survey_date'); // morning, afternoon, flexible
            $table->text('survey_notes')->nullable()->after('survey_time_slot');
            $table->string('survey_status', 50)->default('none')->after('survey_notes'); // none, requested, scheduled, completed
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn([
                'survey_requested',
                'survey_date',
                'survey_time_slot',
                'survey_notes',
                'survey_status',
            ]);
        });
    }
};
