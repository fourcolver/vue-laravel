<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class FillTenantFormatInTenantsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \App\Models\Tenant::get(['id', 'created_at'])->each(function ($tenant) {
            $tenant->tenant_format  = $tenant->getUniqueIDFormat($tenant->id);
            $tenant->save();
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
