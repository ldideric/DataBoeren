<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::table('mail_logs', function (Blueprint $table) {
            $table->string('trace_id')->nullable()->index()->after('message_id');
        });

        // Inefficient but this is a one-off migration and the table is small
        DB::table('mail_logs')->whereNull('trace_id')->orderBy('id')->chunkById(500, function ($rows) {
            foreach ($rows as $row) {
                DB::table('mail_logs')->where('id', $row->id)->update([
                    'trace_id' => filled($row->job_id) ? 'legacy-job-'.$row->job_id : 'legacy-row-'.$row->id,
                ]);
            }
        });
    }

    public function down(): void
    {
        Schema::table('mail_logs', function (Blueprint $table) {
            $table->dropIndex(['trace_id']);
            $table->dropColumn('trace_id');
        });
    }
};
