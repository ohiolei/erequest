<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('requests', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id');
            $table->uuid('service_id');
            $table->uuid('department_id')->nullable();
            $table->string('graduation_year')->nullable();
            $table->string('entry_mode')->nullable();
            $table->json('request_data')->nullable();
            $table->text('transcript_exist')->nullable();
            $table->text('transcript_url_tams')->nullable();
            $table->dateTime('date_submitted')->nullable();
            $table->dateTime('date_profiled')->nullable();
            $table->dateTime('date_vetted')->nullable();
            $table->dateTime('date_signed')->nullable();
            $table->dateTime('date_disputed')->nullable();
            $table->dateTime('date_resolved')->nullable();
            $table->dateTime('date_bursary_approved')->nullable();
            $table->dateTime('date_dispatched')->nullable();
            $table->enum('status', [
                'pending',
                'profiling',
                'data_entry',
                'vetting',
                'sign',
                'bursary_approval',
                'dispatch',
                'dispute',
                'completed',
                'declined',
            ])->default('pending');
            $table->longText('profiling_comment')->nullable();
            $table->longText('data_entry_comment')->nullable();
            $table->longText('vetting_comment')->nullable();
            $table->longText('signature_comment')->nullable();
            $table->longText('dispute_comment')->nullable();
            $table->json('general_comments')->nullable();
            $table->longText('id_card')->nullable();
            $table->longText('statement_of_result')->nullable();
            $table->timestamps();

            $table->foreign('user_id')
                ->references('id')->on('users')
                ->onUpdate('cascade')
                ->onDelete('restrict');
            $table->foreign('service_id')
                ->references('id')->on('services')
                ->onUpdate('cascade')
                ->onDelete('restrict');
            $table->foreign('department_id')
                ->references('id')->on('departments')
                ->onUpdate('cascade')
                ->onDelete('restrict');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('requests');
    }
};
