<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateTenantsTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tenants', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id')->nullable();
            $table->unsignedInteger('address_id')->nullable();
            $table->unsignedInteger('building_id')->nullable();
            $table->unsignedInteger('unit_id')->nullable();
            $table->string('title');
            $table->string('company')->nullable();
            $table->string('first_name');
            $table->string('last_name');
            $table->date('birth_date');
            $table->string('mobile_phone')->nullable();
            $table->string('private_phone')->nullable();
            $table->string('work_phone')->nullable();
            $table->integer('status')->unsigned()->default(1);
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('address_id')->references('id')->on('loc_addresses');
            $table->foreign('building_id')->references('id')->on('buildings');
            $table->foreign('unit_id')->references('id')->on('units');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('tenants');
    }
}
