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
            $table->uuid('profiled_by')->nullable();
            $table->uuid('vetted_by')->nullable();
            $table->uuid('data_entered_by')->nullable();
            $table->uuid('signed_by')->nullable();
            $table->uuid('dispatched_by')->nullable();
            $table->uuid('completed_by')->nullable();
            $table->uuid('declined_by')->nullable();
            $table->uuid('disputed_by')->nullable();
            $table->uuid('bursary_approved_by')->nullable();

            $table->foreign('profiled_by')->references('id')->on('users')->onUpdate('cascade')->onDelete('restrict');
            $table->foreign('vetted_by')->references('id')->on('users')->onUpdate('cascade')->onDelete('restrict');
            $table->foreign('data_entered_by')->references('id')->on('users')->onUpdate('cascade')->onDelete('restrict');
            $table->foreign('signed_by')->references('id')->on('users')->onUpdate('cascade')->onDelete('restrict');
            $table->foreign('dispatched_by')->references('id')->on('users')->onUpdate('cascade')->onDelete('restrict');
            $table->foreign('completed_by')->references('id')->on('users')->onUpdate('cascade')->onDelete('restrict');
            $table->foreign('declined_by')->references('id')->on('users')->onUpdate('cascade')->onDelete('restrict');
            $table->foreign('disputed_by')->references('id')->on('users')->onUpdate('cascade')->onDelete('restrict');
            $table->foreign('bursary_approved_by')->references('id')->on('users')->onUpdate('cascade')->onDelete('restrict');
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
            $table->dropForeign(['profiled_by']);
            $table->dropForeign(['vetted_by']);
            $table->dropForeign(['data_entered_by']);
            $table->dropForeign(['signed_by']);
            $table->dropForeign(['dispatched_by']);
            $table->dropForeign(['completed_by']);
            $table->dropForeign(['declined_by']);
            $table->dropForeign(['disputed_by']);
            $table->dropForeign(['bursary_approved_by']);

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
