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
        Schema::table('disbursement_log', function (Blueprint $table) {
            $table->string('overhead_amount')->nullable()->change();
            $table->string('ict_amount')->nullable()->change();
            $table->string('undergraduate_amount')->nullable()->change();
            $table->string('postgraduate_amount')->nullable()->change();
            $table->string('tti_hub_amount')->nullable()->change();
            $table->enum('disbursement_type', ['client', 'business', 'acad_affairs'])->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('disbursement_log', function (Blueprint $table) {
            $table->string('overhead_amount')->change();
            $table->string('ict_amount')->change();
            $table->string('undergraduate_amount')->change();
            $table->string('postgraduate_amount')->change();
            $table->string('tti_hub_amount')->change();
            $table->dropColumn('disbursement_type');
        });
    }
};
