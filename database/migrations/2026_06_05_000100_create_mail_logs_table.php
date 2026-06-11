<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mail_logs', function (Blueprint $table) {
            $table->id();
            $table->string('event')->index();
            $table->string('mailable')->nullable()->index();
            $table->string('recipient')->nullable()->index();
            $table->string('subject')->nullable();
            $table->string('message_id')->nullable()->index();
            $table->string('job_id')->nullable()->index();
            $table->string('connection')->nullable();
            $table->string('queue')->nullable();
            $table->unsignedSmallInteger('attempt')->nullable();
            $table->text('error')->nullable();
            $table->json('context')->nullable();
            $table->timestamp('created_at')->nullable()->index();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mail_logs');
    }
};
