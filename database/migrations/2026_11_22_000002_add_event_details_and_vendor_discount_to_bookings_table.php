<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->text('venue_address')->nullable()->after('event_end_date');
            $table->string('address_landmark')->nullable()->after('venue_address');
            $table->string('parents_name')->nullable()->after('customer_name');
            $table->string('instagram_account')->nullable()->after('customer_email');
            $table->string('event_time')->nullable()->after('address_landmark');
            $table->string('vendor_name')->nullable()->after('event_time');
            $table->decimal('discount_amount', 15, 2)->default(0)->after('addon_total_price');
            $table->string('vendor_mua')->nullable()->after('vendor_name');
            $table->string('vendor_decor')->nullable()->after('vendor_mua');
            $table->string('vendor_photo_video')->nullable()->after('vendor_decor');
            $table->string('vendor_mc_wo')->nullable()->after('vendor_photo_video');
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn([
                'venue_address',
                'address_landmark',
                'parents_name',
                'instagram_account',
                'event_time',
                'vendor_name',
                'discount_amount',
                'vendor_mua',
                'vendor_decor',
                'vendor_photo_video',
                'vendor_mc_wo',
            ]);
        });
    }
};
