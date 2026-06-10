<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::table('campsites', function (Blueprint $table) {
            $table->dropColumn('max_vehicles');
        });

        Schema::table('reservations', function (Blueprint $table) {
            $table->dropColumn('num_vehicles');
        });
    }

    public function down(): void
    {
        Schema::table('campsites', function (Blueprint $table) {
            $table->unsignedSmallInteger('max_vehicles')->after('max_people');
        });

        Schema::table('reservations', function (Blueprint $table) {
            $table->unsignedSmallInteger('num_vehicles')->after('num_children');
        });
    }
};
