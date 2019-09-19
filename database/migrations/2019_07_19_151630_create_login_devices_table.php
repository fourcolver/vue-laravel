<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLoginDevicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('login_devices', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('user_id')->unsigned();
            $table->integer('tenant_id')->unsigned()->nullable();
            $table->integer('mobile')->unsigned()->default(0);
            $table->integer('desktop')->unsigned()->default(0);
            $table->integer('tablet')->unsigned()->default(0);
            $table->date('created_by');
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('tenant_id')->references('id')->on('tenants');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('login_devices');
    }
}
