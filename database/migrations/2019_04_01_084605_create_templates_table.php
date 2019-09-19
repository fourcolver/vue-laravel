<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateTemplatesTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('templates', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('category_id')->unsigned()->default(0);
            $table->integer('type')->unsigned()->default(1);
            $table->string('name');
            $table->string('subject');
            $table->mediumText('body')->nullable();
            $table->boolean('default')->default(0);
            $table->boolean('system')->default(0);
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('category_id')->references('id')->on('template_categories');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('templates');
    }
}
