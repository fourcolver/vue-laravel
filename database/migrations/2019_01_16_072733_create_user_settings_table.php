<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateUserSettingsTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_settings', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned()->default(0);
            $table->string('language')->default('en');
            $table->string('summary')->default('daily');
            $table->integer('admin_notification')->unsigned()->default(1);
            $table->integer('news_notification')->unsigned()->default(1);
            $table->integer('marketplace_notification')->unsigned()->default(1);
            $table->integer('service_notification')->unsigned()->default(1);
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('user_settings');
    }
}
