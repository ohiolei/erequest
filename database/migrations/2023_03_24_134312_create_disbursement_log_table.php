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
        Schema::create('disbursement_log', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('disbursed_by');
            $table->foreign('disbursed_by')->references('id')->on('users')->onUpdate('cascade')->onDelete('restrict');
            $table->datetime('disbursement_date');
            $table->double('overhead_amount');
            $table->double('ict_amount');
            $table->double('undergraduate_amount');
            $table->double('postgraduate_amount');
            $table->double('tti_hub_amount');
            $table->double('total_amount');
            $table->string('disbursed_month');
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
        Schema::dropIfExists('disbursement_log');
    }
};
