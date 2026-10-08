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
            $table->string('email1')->nullable();
            $table->string('email2')->nullable();
            $table->text('address1')->nullable();
            $table->text('address2')->nullable();
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
            $table->dropColumn('email1');
            $table->dropColumn('email2');
            $table->dropColumn('address1');
            $table->dropColumn('address2');
        });
    }
};
