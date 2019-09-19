<?php

use App\Models\Role;
use App\Models\User;
use App\Models\UserSettings;
use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $superAdminRole = Role::where('name', 'super_admin')->first();

        $attr = [
            'name' => 'Super Admin',
            'email' => 'dev@example.com',
            'phone' => '5296711335',
            'password' => bcrypt('dev@example.com'),
        ];
        $user = factory(User::class, 1)->create($attr)->first();

        $settings = $this->getSettings();
        $user->settings()->save($settings->replicate());
        $user->attachRole($superAdminRole);

        $attr = [
            'name' => 'Propify',
            'email' => 'admin@propify.ch',
            'phone' => '5296711335',
            'password' => bcrypt('adprop19-1'),
        ];
        $user = factory(User::class, 1)->create($attr)->first();

        $settings = $this->getSettings();
        $user->settings()->save($settings->replicate());
        $user->attachRole($superAdminRole);

        if (App::environment('local')) {
            $roles = Role::where('name', '!=', 'super_admin')->get();
            factory(App\Models\User::class, 20)->create()->each(function ($user) use ($roles, $settings) {
                $settings->id = 0;
                $user->settings()->save($settings->replicate());

                $user->attachRole($roles->random());
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
