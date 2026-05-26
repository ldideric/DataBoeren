<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('campsite_prices', function (Blueprint $table) {
            $table->renameColumn('per_person_rate', 'per_adult_rate');
        });

        Schema::table('campsite_prices', function (Blueprint $table) {
            $table->decimal('per_child_rate')->default(0)->after('per_adult_rate');
        });
    }

    public function down(): void
    {
        Schema::table('campsite_prices', function (Blueprint $table) {
            $table->dropColumn('per_child_rate');
        });

        Schema::table('campsite_prices', function (Blueprint $table) {
            $table->renameColumn('per_adult_rate', 'per_person_rate');
        });
    }
};
