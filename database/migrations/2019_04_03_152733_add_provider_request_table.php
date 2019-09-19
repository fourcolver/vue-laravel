<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddProviderRequestTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('request_provider', function(Blueprint $table) {
            $table->integer('provider_id')->unsigned();
            $table->integer('request_id')->unsigned();
            $table->foreign('provider_id')->references('id')->on('service_providers')->onDelete('cascade');
            $table->foreign('request_id')->references('id')->on('service_requests')->onDelete('cascade');
            $table->primary(['provider_id', 'request_id']);
        });
        Schema::table('service_requests', function (Blueprint $table) {
            $table->dropForeign('service_requests_service_id_foreign');
            $table->dropColumn('service_id');
        });
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
