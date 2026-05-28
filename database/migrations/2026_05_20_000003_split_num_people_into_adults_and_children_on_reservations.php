<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->renameColumn('num_people', 'num_adults');
        });

        Schema::table('reservations', function (Blueprint $table) {
            $table->unsignedSmallInteger('num_children')->default(0)->after('num_adults');
        });
    }

    public function down(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->dropColumn('num_children');
        });

        Schema::table('reservations', function (Blueprint $table) {
            $table->renameColumn('num_adults', 'num_people');
        });
    }
};
