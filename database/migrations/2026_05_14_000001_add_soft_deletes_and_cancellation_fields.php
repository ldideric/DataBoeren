<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('campsites', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('coupons', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('extras', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('reservations', function (Blueprint $table) {
            $table->softDeletes();
            $table->timestamp('cancelled_at')->nullable()->after('status');
            $table->text('cancellation_reason')->nullable()->after('cancelled_at');
            $table->foreignUuid('cancelled_by_user_id')->nullable()->after('cancellation_reason')->constrained('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->dropForeign(['cancelled_by_user_id']);
            $table->dropColumn(['deleted_at', 'cancelled_at', 'cancellation_reason', 'cancelled_by_user_id']);
        });

        Schema::table('extras', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('coupons', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('campsites', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
};
