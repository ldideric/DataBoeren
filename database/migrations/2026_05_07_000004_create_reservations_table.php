<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reservations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('customer_id')->constrained()->restrictOnDelete();
            $table->foreignUuid('campsite_id')->constrained()->restrictOnDelete();
            $table->foreignUuid('booked_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('source');
            $table->date('check_in');
            $table->date('check_out');
            $table->unsignedSmallInteger('num_people');
            $table->unsignedSmallInteger('num_vehicles');
            $table->decimal('total_price', 10, 2);
            $table->string('status');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reservations');
    }
};
