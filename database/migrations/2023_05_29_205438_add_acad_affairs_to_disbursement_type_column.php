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
            $table->string('disbursement_type')->nullable()->change();
            $table->double('acad_affairs_amount')->nullable();
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
            $table->dropColumn('disbursement_type');
            $table->dropColumn('acad_affairs_amount');
        });
    }
};
