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
        Schema::create('transactions', function (Blueprint $table) {
            $table->uuid('trans_request_id')->primary();
            $table->uuid('request_id');

            $table->string('ogun_pay_code')->nullable();
            $table->string('ogun_pay_url')->nullable();
            $table->string('ogun_pay_reference')->nullable();

            $table->string('payment_description');

            $table->uuid('service_payable_id');
            $table->foreign('service_payable_id')->references('id')->on('service_payables')->onUpdate('cascade')->onDelete('restrict');

            $table->double('amount');
            $table->datetime('transaction_date')->nullable();
            $table->enum('status', ['approved', 'declined', 'pending'])->default('pending');

            $table->text('json_response')->nullable();

            $table->foreign('request_id')->references('id')->on('requests')->onUpdate('cascade')->onDelete('restrict');
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
        Schema::dropIfExists('transactions');
    }
};
