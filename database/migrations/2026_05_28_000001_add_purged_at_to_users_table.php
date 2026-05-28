<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('first_name')->nullable()->change();
            $table->string('last_name')->nullable()->change();
            $table->string('email')->nullable()->change();
            $table->string('password')->nullable()->change();
            $table->timestamp('purged_at')->nullable()->after('updated_at');
        });
    }

    public function down(): void
    {
        DB::table('users')
            ->whereNull('first_name')
            ->update(['first_name' => 'Purged']);

        DB::table('users')
            ->whereNull('last_name')
            ->update(['last_name' => 'User']);

        DB::table('users')
            ->whereNull('password')
            ->update(['password' => 'purged']);

        DB::table('users')
            ->select('id')
            ->whereNull('email')
            ->orderBy('id')
            ->each(function (object $user): void {
                DB::table('users')
                    ->where('id', $user->id)
                    ->update(['email' => "purged+{$user->id}@example.invalid"]);
            });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('purged_at');
            $table->string('email')->nullable(false)->change();
            $table->string('last_name')->nullable(false)->change();
            $table->string('first_name')->nullable(false)->change();
            $table->string('password')->nullable(false)->change();
        });
    }
};
