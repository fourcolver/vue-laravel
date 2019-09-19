<?php

use App\Models\Building;
use App\Models\PropertyManager;
use App\Models\Role;
use App\Models\User;
use App\Models\UserSettings;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;

class PropertyManagerTableSeeder extends Seeder
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
        $managerRole = Role::where('name', 'manager')->first();
        $settings = $this->getSettings();

        $buildings = Building::all()->count();
        $totalManagers = $faker->numberBetween($buildings, $buildings * 2);

        for ($i = 0; $i < $totalManagers; $i++) {
            $firstName = $faker->firstName;
            $lastName = $faker->lastName;

            $email = $faker->email;
            $attr = [
                'name' => sprintf('%s %s', $firstName, $lastName),
                'email' => $email,
                'phone' => $faker->phoneNumber,
                'password' => bcrypt($email),
            ];
            $user = factory(User::class, 1)->create($attr)->first();

            $user->attachRole($managerRole);

            $user->settings()->save($settings->replicate());
            $attr = [
                'user_id' => $user->id,
                'title' => $user->title,
            ];

            $manager = factory(PropertyManager::class, 1)->create($attr)->first();

            $building = Building::inRandomOrder()->first();
            $manager->buildings()->attach($building);
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
