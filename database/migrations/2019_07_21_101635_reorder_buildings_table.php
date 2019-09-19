<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ReorderBuildingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \Illuminate\Support\Facades\DB::statement('ALTER TABLE `buildings`   
  CHANGE `district_id` `district_id` INT(10) UNSIGNED NULL  AFTER `id`,
  CHANGE `address_id` `address_id` INT(10) UNSIGNED DEFAULT 210  NOT NULL  AFTER `district_id`;
');
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
