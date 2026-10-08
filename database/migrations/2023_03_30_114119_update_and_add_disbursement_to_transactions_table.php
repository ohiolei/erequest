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
        Schema::table('transactions', function (Blueprint $table) {
            $table->renameColumn('disbursement_status', 'business_disbursement_status');
            $table->enum('client_disbursement_status', ['yes', 'no'])->default('no');
        });
        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->renameColumn('business_disbursement_status', 'disbursement_status');
            $table->dropColumn('client_disbursement_status');
        });
    }
};
