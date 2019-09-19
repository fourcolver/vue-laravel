<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateRealEstatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('real_estates', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('address_id')->unsigned()->default(0);
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('language')->nullable();
            $table->string('logo')->nullable();
            $table->integer('blank_pdf')->unsigned()->default(0);
            $table->integer('district_enable')->unsigned()->default(1);
            $table->integer('free_apartments_enable')->unsigned()->default(1);
            $table->string('free_apartments_url')->nullable();
            $table->string('cleanify_email')->nullable();
            $table->text('opening_hours')->nullable();
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
        Schema::drop('real_estates');
    }
}
