<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDistrictProperyManagerTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('district_property_manager', function(Blueprint $table) {
            $table->integer('district_id')->unsigned();
            $table->integer('property_manager_id')->unsigned();
            $table->foreign('district_id')->references('id')->on('districts');
            $table->foreign('property_manager_id')->references('id')->on('property_managers');
            $table->primary(['district_id', 'property_manager_id'], 'district_property_manager_primary');
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
