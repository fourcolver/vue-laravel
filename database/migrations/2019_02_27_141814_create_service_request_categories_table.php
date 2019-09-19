<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateServiceRequestCategoriesTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('service_request_categories', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('parent_id')->unsigned()->nullable();
            $table->string('name');
            $table->text('description')->nullable();
            $table->boolean('has_qualifications')->default(false);
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('parent_id')->references('id')->on('service_request_categories');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('service_request_categories');
    }
}
