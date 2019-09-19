<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class FixFormats extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fix-formats';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix *_format of tables';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        dump('start correct _formats of tables');
        \App\Models\ServiceProvider::get(['id', 'created_at'])->each(function ($service_request) {
            $service_request->service_provider_format  = $service_request->getUniqueIDFormat($service_request->id, $service_request->created_at);
            $service_request->save();
        });
        \App\Models\Tenant::get(['id', 'created_at'])->each(function ($tenant) {
            $tenant->tenant_format  = $tenant->getUniqueIDFormat($tenant->id);
            $tenant->save();
        });
        \App\Models\Building::get(['id', 'created_at'])->each(function ($building) {
            $building->building_format  = $building->getUniqueIDFormat($building->id, $building->created_at);
            $building->save();
        });
        \App\Models\Unit::get(['id', 'created_at'])->each(function ($unit) {
            $unit->unit_format  = $unit->getUniqueIDFormat($unit->id, $unit->created_at);
            $unit->save();
        });
        \App\Models\District::get(['id', 'created_at'])->each(function ($district) {
            $district->district_format  = $district->getUniqueIDFormat($district->id, $district->created_at);
            $district->save();
        });
        \App\Models\ServiceRequest::get(['id', 'created_at'])->each(function ($service_request) {
            $service_request->service_request_format  = $service_request->getUniqueIDFormat($service_request->id, $service_request->created_at);
            $service_request->save();
        });
        \App\Models\PropertyManager::get(['id', 'created_at'])->each(function ($service_request) {
            $service_request->property_manager_format  = $service_request->getUniqueIDFormat($service_request->id, $service_request->created_at);
            $service_request->save();
        });
        dump('all _formats correct successfully');
    }
}
