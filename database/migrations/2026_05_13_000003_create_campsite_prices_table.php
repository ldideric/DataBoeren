<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('campsite_prices', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('campsite_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('season_id')->constrained()->cascadeOnDelete();
            $table->decimal('nightly_rate');
            $table->decimal('per_person_rate');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('campsite_prices');
    }
};
