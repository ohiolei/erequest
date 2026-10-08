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
        Schema::table('requests', function (Blueprint $table) {
            $table->uuid('programme_id')->nullable();
            $table->foreign('programme_id')->references('id')->on('programmes')->onUpdate('cascade')->onDelete('restrict');
            $table->datetime('date_declined')->nullable();
            $table->longText('decline_comment')->nullable();


        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('requests', function (Blueprint $table) {
            $table->dropColumn('programme_id'); // Remove "active" field
            $table->dropColumn('date_declined'); // Remove "active" field
            $table->dropColumn('decline_comment'); // Remove "active" field


        });
    }
};
