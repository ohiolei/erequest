<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStatesLgasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    //states
    public function up()
    {
        Schema::create('states', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('stname');
            $table->timestamps();
        });


        Schema::create('state_lgas', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('state_id')->nullable();
            $table->foreign('state_id')->references('id')->on('states')->onUpdate('cascade')->onDelete('restrict');
            $table->string('lganame');
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
        Schema::dropIfExists('state_lgas');
        Schema::dropIfExists('states');
    }
}
