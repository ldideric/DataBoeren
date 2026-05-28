<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::table('campsite_prices', function (Blueprint $table) {
            $table->integer('nightly_rate')->change();
            $table->integer('per_adult_rate')->change();
            $table->integer('per_child_rate')->default(0)->change();
        });

        Schema::table('order_summaries', function (Blueprint $table) {
            $table->integer('nightly_rate')->change();
            $table->integer('per_adult_rate')->change();
            $table->integer('per_child_rate')->default(0)->change();
            $table->integer('last_minute_discount')->nullable()->change();
            $table->integer('coupon_discount')->nullable()->change();
            $table->integer('extras_total')->nullable()->change();
            $table->integer('total')->change();
        });

        Schema::table('extras', function (Blueprint $table) {
            $table->integer('price')->change();
        });

        Schema::table('reservation_extras', function (Blueprint $table) {
            $table->integer('unit_price')->change();
            $table->integer('subtotal')->change();
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->integer('amount')->change();
        });
    }

    public function down(): void
    {
        Schema::table('campsite_prices', function (Blueprint $table) {
            $table->decimal('nightly_rate')->change();
            $table->decimal('per_adult_rate')->change();
            $table->decimal('per_child_rate')->default(0)->change();
        });

        Schema::table('order_summaries', function (Blueprint $table) {
            $table->decimal('nightly_rate')->change();
            $table->decimal('per_adult_rate')->change();
            $table->decimal('per_child_rate')->default(0)->change();
            $table->decimal('last_minute_discount')->nullable()->change();
            $table->decimal('coupon_discount')->nullable()->change();
            $table->decimal('extras_total')->nullable()->change();
            $table->decimal('total', 10)->change();
        });

        Schema::table('extras', function (Blueprint $table) {
            $table->decimal('price')->change();
        });

        Schema::table('reservation_extras', function (Blueprint $table) {
            $table->decimal('unit_price')->change();
            $table->decimal('subtotal')->change();
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->decimal('amount', 10)->change();
        });
    }
};
