<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddServiceProviderFormatInServiceProvidersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('service_providers', function (Blueprint $table) {
            $table->string('service_provider_format', 30)->after('address_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('service_providers', function (Blueprint $table) {
            $table->dropColumn('service_provider_format');
        });
    }
}
