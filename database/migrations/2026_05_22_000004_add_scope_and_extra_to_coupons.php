<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::table('coupons', function (Blueprint $table) {
            $table->string('scope')->default('accommodation')->after('code');
            $table->foreignUuid('extra_id')->nullable()->after('scope')
                ->constrained()->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('coupons', function (Blueprint $table) {
            $table->dropConstrainedForeignId('extra_id');
            $table->dropColumn('scope');
        });
    }
};
