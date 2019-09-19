<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddUserConversationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('conversations', function (Blueprint $table) {
            $table->increments('id');
            $table->morphs('conversationable');
            $table->timestamps();
            $table->softDeletes();
        });
        Schema::create('conversation_user', function(Blueprint $table) {
            $table->integer('conversation_id')->unsigned();
            $table->integer('user_id')->unsigned();

            $table->foreign('conversation_id')->references('id')->on('conversations')
                ->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')
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
        Schema::dropIfExists('conversations');
        Schema::dropIfExists('conversation_user');
    }
}
