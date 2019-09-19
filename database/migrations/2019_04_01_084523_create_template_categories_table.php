<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateTemplateCategoriesTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('template_categories', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('parent_id')->unsigned()->nullable();
            $table->string('name');
            $table->text('description');
            $table->string('subject');
            $table->text('body');
            $table->text('tag_map')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('parent_id')->references('id')->on('template_categories');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('template_categories');
    }
}
