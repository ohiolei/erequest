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
        Schema::create('transcript_data', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id');
            $table->enum('vetting_status', ['yes', 'no'])->default('no');
            $table->enum('submit_status', ['yes', 'no'])->default('no');
            $table->string('entry_mode')->unique()->nullable();
            $table->uuid('submitted_by')->nullable();
            $table->uuid('entry_year')->nullable();
            $table->foreign('entry_year')->references('id')->on('school_sessions')->onUpdate('cascade')->onDelete('restrict');
            $table->uuid('grad_year')->nullable();
            $table->foreign('grad_year')->references('id')->on('school_sessions')->onUpdate('cascade')->onDelete('restrict');
            $table->uuid('programme_id');
            $table->foreign('programme_id')->references('id')->on('programmes')->onUpdate('cascade')->onDelete('restrict');
            $table->string('department')->nullable();
            $table->string('programme')->nullable();
            $table->json('result')->nullable();
            $table->string('total_no_of_units')->nullable();
            $table->string('total_credit_points')->nullable();
            $table->string('degree_awarded')->nullable();
            $table->string('cummulative_grade_point')->nullable();
            $table->string('class_of_degree')->nullable();
            $table->foreign('submitted_by')->references('id')->on('users')->onUpdate('cascade')->onDelete('restrict');
            $table->timestamps();

            $table->unique(['user_id', 'programme_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('transcript_data');
    }
};
