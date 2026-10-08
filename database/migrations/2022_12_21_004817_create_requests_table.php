<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('requests', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id');

            $table->uuid('service_id');
            $table->foreign('service_id')->references('id')->on('services')->onUpdate('cascade')->onDelete('restrict');

            $table->uuid('department_id')->nullable();
            $table->foreign('department_id')->references('id')->on('departments')->onUpdate('cascade')->onDelete('restrict');

            $table->string('graduation_year')->nullable();
            $table->string('entry_mode')->nullable();

            // $table->enum('entry_mode', ['undergraduate_fulltime', 'undergraduate_parttime', 'postgraduate_fulltime', 'postgraduate_parttime', 'prelim'])->nullable();

            $table->json('request_data')->nullable();
            $table->text('transcript_exist')->nullable();
            $table->text('transcript_url_tams')->nullable();
            $table->datetime('date_submitted')->nullable();
            $table->datetime('date_profiled')->nullable();
            $table->datetime('date_vetted')->nullable();
            $table->datetime('date_signed')->nullable();
            $table->datetime('date_disputed')->nullable();
            $table->datetime('date_resolved')->nullable();
            $table->datetime('date_bursary_approved')->nullable();
            $table->datetime('date_dispatched')->nullable();
            $table->enum('status', ['pending','profiling','data_entry', 'vetting', 'sign', 'bursary_approval', 'dispatch', 'dispute', 'completed', 'declined'])->default('pending');
            $table->longText('profiling_comment')->nullable();
            $table->longText('data_entry_comment')->nullable();
            $table->longText('vetting_comment')->nullable();
            $table->longText('signature_comment')->nullable();
            $table->longText('dispute_comment')->nullable();
            $table->json('general_comments')->nullable();
            $table->longText('id_card')->nullable();
            $table->longText('statement_of_result')->nullable();
            $table->foreign('user_id')->references('id')->on('users')->onUpdate('cascade')->onDelete('restrict');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('requests');
    }
};
