<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateServiceRequestsTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('service_requests', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('category_id')->unsigned()->default(0);
            $table->integer('unit_id')->unsigned()->default(0);
            $table->integer('tenant_id')->unsigned()->default(0);
            $table->integer('service_id')->unsigned()->nullable();
            $table->string('title');
            $table->text('description');
            $table->integer('status')->unsigned()->default(1);
            $table->integer('priority')->unsigned()->default(1);
            $table->integer('qualification')->unsigned()->default(0);
            $table->date('due_date')->nullable();
            $table->dateTime('solved_date')->nullable();
            $table->boolean('is_public')->default(false);
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('category_id')->references('id')->on('service_request_categories');
            $table->foreign('unit_id')->references('id')->on('units');
            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
            $table->foreign('service_id')->references('id')->on('service_providers');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('service_requests');
    }
}
