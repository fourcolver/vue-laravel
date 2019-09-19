<?php

use Illuminate\Database\Seeder;
use OwenIt\Auditing\Models\Audit;
use App\Models\User;

class AuditsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (App::environment('local')) {
            Audit::where('user_id', null)->update([
                'user_type' => User::class,
                'user_id' => User::where('email', 'dev@example.com')->first()->id,
            ]);
        }
    }
}
