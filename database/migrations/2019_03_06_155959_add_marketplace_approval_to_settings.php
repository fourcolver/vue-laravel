<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddMarketplaceApprovalToSettings extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('real_estates', function (Blueprint $table) {
            $table->integer('marketplace_approval_enable')->unsigned()->default(0);
            $table->integer('news_approval_enable')->unsigned()->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('real_estates', function (Blueprint $table) {
            //
        });
    }
}
