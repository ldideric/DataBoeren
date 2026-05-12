<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('campsite_prices', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('campsite_id')->constrained()->cascadeOnDelete();
            $table->date('starts_at');
            $table->date('ends_at');
            $table->decimal('nightly_rate', 8, 2);
            $table->decimal('per_person_rate', 8, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('campsite_prices');
    }
};
