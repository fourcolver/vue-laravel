<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddProprertyManagerFormatInPropertyManagersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('property_managers', function (Blueprint $table) {
            $table->string('property_manager_format', 30)->after('user_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('property_managers', function (Blueprint $table) {
            $table->dropColumn('property_manager_format');
        });
    }
}
