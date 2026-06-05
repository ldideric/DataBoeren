<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->timestamp('payment_reminder_sent_at')->nullable()->after('cancelled_by_user_id');
            $table->timestamp('arrival_reminder_sent_at')->nullable()->after('payment_reminder_sent_at');
        });
    }

    public function down(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->dropColumn(['payment_reminder_sent_at', 'arrival_reminder_sent_at']);
        });
    }
};
