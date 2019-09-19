<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateBuildingsTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('buildings', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('description')->nullable();
            $table->string('label')->nullable();
            $table->integer('address_id')->unsigned()->default(210);
            $table->integer('floor_nr')->unsigned()->default(0);
            $table->integer('basement')->unsigned()->default(0);
            $table->integer('attic')->unsigned()->default(0);
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('address_id')->references('id')->on('loc_addresses');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('buildings');
    }
}
