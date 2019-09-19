<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRequestAssigneeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('request_assignee', function(Blueprint $table) {
            $table->integer('user_id')->unsigned();
            $table->integer('request_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('request_id')->references('id')->on('service_requests')->onDelete('cascade');
            $table->primary(['user_id', 'request_id']);
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
