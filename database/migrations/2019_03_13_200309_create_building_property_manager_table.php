<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBuildingPropertyManagerTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('building_property_manager', function (Blueprint $table) {
            $table->integer('building_id')->unsigned();
            $table->integer('property_manager_id')->unsigned();

            $table->foreign('building_id')->references('id')->on('buildings')
                ->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('property_manager_id')->references('id')->on('property_managers')
                ->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('building_property_manager', function (Blueprint $table) {
            Schema::drop('building_property_manager');
        });
    }
}
