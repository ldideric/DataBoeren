<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('reservations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('customer_id')->constrained('users')->restrictOnDelete();
            $table->foreignUuid('campsite_id')->constrained()->restrictOnDelete();
            $table->foreignUuid('booked_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignUuid('coupon_id')->nullable()->constrained()->nullOnDelete();
            $table->string('source');
            $table->date('check_in');
            $table->date('check_out');
            $table->unsignedSmallInteger('num_people');
            $table->unsignedSmallInteger('num_vehicles');
            $table->string('status');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reservations');
    }
};
