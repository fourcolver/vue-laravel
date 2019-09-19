<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateUnitsTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('units', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('building_id')->unsigned()->default(0);
            $table->integer('type')->unsigned()->default(1);
            $table->string('name');
            $table->string('description');
            $table->integer('floor')->unsigned()->default(0);
            $table->decimal('monthly_rent')->default(0.0);
            $table->decimal('room_no')->default(0.0)->nullable();
            $table->integer('basement')->unsigned()->default(0);
            $table->integer('attic')->unsigned()->default(0);
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('building_id')->references('id')->on('buildings');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('units');
    }
}
