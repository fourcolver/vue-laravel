<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateLocAddressesTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('loc_addresses', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('country_id')->unsigned()->default(210);
            $table->integer('state_id')->unsigned()->default(0);
            $table->string('city');
            $table->string('street');
            $table->string('street_nr');
            $table->string('zip');
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('country_id')->references('id')->on('loc_countries');
            $table->foreign('state_id')->references('id')->on('loc_states');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('loc_addresses');
    }
}
