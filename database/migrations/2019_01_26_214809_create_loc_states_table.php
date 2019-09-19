<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateLocStatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('loc_states', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('country_id')->unsigned()->default(0);
            $table->string('code');
            $table->string('name');
            $table->string('name_de');
            $table->string('name_fr');
            $table->string('name_it');
            $table->string('name_rm');
            $table->foreign('country_id')->references('id')->on('loc_countries');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('loc_states');
    }
}
