<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBuildingServiceProviderTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('building_service_provider', function (Blueprint $table) {
            $table->integer('building_id')->unsigned();
            $table->integer('service_provider_id')->unsigned();

            $table->foreign('building_id')->references('id')->on('buildings')
                ->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('service_provider_id')->references('id')->on('service_providers')
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
        Schema::table('building_service_provider', function (Blueprint $table) {
            Schema::drop('building_service_provider');
        });
    }
}
