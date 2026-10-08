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
            $table->uuid('profiled_by')->nullable()->onUpdate('cascade')->onDelete('restrict');
            $table->uuid('vetted_by')->nullable()->onUpdate('cascade')->onDelete('restrict');
            $table->uuid('data_entered_by')->nullable()->onUpdate('cascade')->onDelete('restrict');
            $table->uuid('signed_by')->nullable()->onUpdate('cascade')->onDelete('restrict');
            $table->uuid('dispatched_by')->nullable()->onUpdate('cascade')->onDelete('restrict');
            $table->uuid('completed_by')->nullable()->onUpdate('cascade')->onDelete('restrict');
            $table->uuid('declined_by')->nullable()->onUpdate('cascade')->onDelete('restrict');
            $table->uuid('disputed_by')->nullable()->onUpdate('cascade')->onDelete('restrict');
            $table->uuid('bursary_approved_by')->nullable()->onUpdate('cascade')->onDelete('restrict');

            $table->foreign('profiled_by')->references('id')->on('users');
            $table->foreign('vetted_by')->references('id')->on('users');
            $table->foreign('data_entered_by')->references('id')->on('users');
            $table->foreign('signed_by')->references('id')->on('users');
            $table->foreign('dispatched_by')->references('id')->on('users');
            $table->foreign('completed_by')->references('id')->on('users');
            $table->foreign('declined_by')->references('id')->on('users');
            $table->foreign('disputed_by')->references('id')->on('users');
            $table->foreign('bursary_approved_by')->references('id')->on('users');
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
            $table->dropColumn('profiled_by');
            $table->dropColumn('vetted_by');
            $table->dropColumn('data_entered_by');
            $table->dropColumn('signed_by');
            $table->dropColumn('dispatched_by');
            $table->dropColumn('completed_by');
            $table->dropColumn('declined_by');
            $table->dropColumn('disputed_by');
            $table->dropColumn('bursary_approved_by');
        });
    }
};
