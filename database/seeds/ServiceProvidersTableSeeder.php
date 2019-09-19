<?php

use App\Models\Role;
use App\Models\User;
use App\Models\UserSettings;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;

class ServiceProvidersTableSeeder extends Seeder
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
        $serviceRole = Role::where('name', 'service')->first();
        $settings = $this->getSettings();

        $serviceCategories = [
            'electrician',
            'heating_company',
            'lift',
            'sanitary',
            'key_service',
            'caretaker',
            'real_estate_service',
        ];

        foreach ($serviceCategories as $category) {

            //create User
            $email = $faker->email;
            $attr = [
                'name' => $faker->name,
                'email' => $email,
                'phone' => $faker->phoneNumber,
                'password' => bcrypt($email),
            ];
            $user = factory(User::class, 1)->create($attr)->first();

            $user->attachRole($serviceRole);

            $user->settings()->save($settings->replicate());

            $address = factory(App\Models\Address::class, 1)->create()->first();

            $attr = [
                'category' => $category,
                'user_id' => $user->id,
                'address_id' => $address->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
            ];

            factory(App\Models\ServiceProvider::class, 1)->create($attr);
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
