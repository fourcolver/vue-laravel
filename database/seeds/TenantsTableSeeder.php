<?php

use App\Models\Building;
use App\Models\Role;
use App\Models\ServiceProvider;
use App\Models\Tenant;
use App\Models\Unit;
use App\Models\User;
use App\Models\UserSettings;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;

class TenantsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (!App::environment('local')) {
            return;
        }

        $faker = Faker::create();
        $units = Unit::inRandomOrder()->limit(20)->get();
        foreach ($units as $key => $unit) {
            $building = Building::where('id', $unit->building_id)->first();

            $email = $faker->email;
            if ($key == 0) {
                $email = 'tenant@example.com';
                $services = ServiceProvider::select('id')->limit(4)->inRandomOrder()->get();
                $building->serviceProviders()->attach($services);
            }

            factory(App\Models\Tenant::class, 1)->create()->each(function ($tenant) use ($building, $unit, $email) {
                // create user attached to tenant
                $registeredRole = Role::where('name', 'registered')->first();

                $attr = [
                    'name' => $tenant->first_name . ' ' . $tenant->last_name,
                    'email' => $email,
                    'phone' => $tenant->mobile_phone,
                    'password' => bcrypt($email),
                ];
                $user = factory(User::class, 1)->create($attr)->first();

                $user->attachRole($registeredRole);

                $settings = $this->getSettings();
                $user->settings()->save($settings->replicate());

                $tenant->user_id = $user->id;
                $tenant->title = $user->title;
                // some are homeless, some not
                if ($email == 'tenant@example.com' || rand(0, 1)) {
                    $tenant->address_id = $building->address_id;
                    $tenant->building_id = $building->id;
                    $tenant->unit_id = $unit->id;
                    $tenant->status = Tenant::StatusActive;
                }
                $tenant->save();
                $tenant->setCredentialsPDF($user->email);
            });
        }
    }

    private function getSettings()
    {
        $settings = new UserSettings();
        $settings->language = 'en';
        $settings->summary = 'daily';
        $settings->admin_notification = 1;
        $settings->news_notification = 1;
        $settings->marketplace_notification = 1;
        $settings->service_notification = 1;

        return $settings;
    }
}
