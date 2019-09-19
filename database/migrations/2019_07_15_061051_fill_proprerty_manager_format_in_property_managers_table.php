<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class FillProprertyManagerFormatInPropertyManagersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \App\Models\PropertyManager::get(['id', 'created_at'])->each(function ($service_request) {
            $service_request->property_manager_format  = $service_request->getUniqueIDFormat($service_request->id, $service_request->created_at);
            $service_request->save();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
    }
}
