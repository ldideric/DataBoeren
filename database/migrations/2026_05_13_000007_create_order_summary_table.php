<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('order_summaries', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('reservation_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('season_name');
            $table->unsignedSmallInteger('num_nights');
            $table->decimal('nightly_rate', 8, 2);
            $table->decimal('per_person_rate', 8, 2);
            $table->boolean('last_minute_applied')->default(false);
            $table->decimal('last_minute_discount', 8, 2)->nullable();
            $table->decimal('coupon_discount', 8, 2)->nullable();
            $table->decimal('extras_total', 8, 2)->nullable();
            $table->decimal('total', 10, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_summaries');
    }
};
