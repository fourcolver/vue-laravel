<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCommentsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('comments', function (Blueprint $table) {
            $table->increments('id');
            $table->morphs('commentable');
            $table->unsignedInteger('parent_id')->nullable();
            $table->text('comment');
            $table->boolean('is_approved')->default(false);
            $table->unsignedInteger('user_id')->nullable();
            $table->timestamps();

            $table->foreign('parent_id')->references('id')->on('comments')
                ->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('comments');
    }
}
