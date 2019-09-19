<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class FillUnitFormatInUnitsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \App\Models\Unit::get(['id', 'created_at'])->each(function ($unit) {
            $unit->unit_format  = $unit->getUniqueIDFormat($unit->id, $unit->created_at);
            $unit->save();
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
