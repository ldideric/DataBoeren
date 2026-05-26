<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('extras', function (Blueprint $table) {
            $table->dropColumn('available');
            $table->unsignedSmallInteger('stock')->nullable()->after('price');
            $table->unsignedSmallInteger('max_per_booking')->nullable()->after('stock');
        });
    }

    public function down(): void
    {
        Schema::table('extras', function (Blueprint $table) {
            $table->dropColumn(['stock', 'max_per_booking']);
            $table->boolean('available')->default(true)->after('price');
        });
    }
};
