<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('service_payables', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('service_id')->nullable();
            $table->foreign('service_id')->references('id')->on('services')->onUpdate('cascade')->onDelete('restrict');

            $table->uuid('payment_head_id');
            $table->foreign('payment_head_id')->references('id')->on('payment_heads')->onUpdate('cascade')->onDelete('restrict');

            $table->uuid('payment_revenue_head_id');
            $table->foreign('payment_revenue_head_id')->references('id')->on('payment_revenue_heads')->onUpdate('cascade')->onDelete('restrict');

            $table->double('amount');

            $table->enum('payment_duration', ['annually', 'fixed'])->default('fixed');

            $table->enum('fee_bearer', ['merchant', 'client'])->default('client');

            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->enum('compulsory', ['yes', 'no'])->default('yes');
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
        Schema::dropIfExists('service_payables');
    }
};
