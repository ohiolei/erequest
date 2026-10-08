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
        Schema::create('payment_revenue_heads', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('revenue_head_name');
            $table->string('revenue_head_code');
            $table->string('bank_name');
            $table->string('bank_account_number');
            $table->uuid('payment_gateway_id');
            $table->foreign('payment_gateway_id')->references('id')->on('payment_gateways')->onUpdate('cascade')->onDelete('restrict');

            $table->enum('status', ['active', 'inactive'])->default('active');
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
        Schema::dropIfExists('payment_revenue_heads');
    }
};
