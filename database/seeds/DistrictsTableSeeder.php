<?php

use Illuminate\Database\Seeder;

class DistrictsTableSeeder extends Seeder
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

        for ($i = 1; $i <= 10; $i++) {
            factory(App\Models\District::class, 1)->create([
                'name' => 'District ' . $i,
            ]);
        }
    }
}
