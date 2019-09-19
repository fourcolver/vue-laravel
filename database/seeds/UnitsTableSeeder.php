<?php

use App\Models\Building;
use Illuminate\Database\Seeder;

class UnitsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (App::environment('local')) {
            $buildings = Building::get();
            foreach ($buildings as $building) {
                $unitTotal = $building->floor_nr * 2;

                for ($i = 1; $i <= $unitTotal; $i++) {
                    $attr = [
                        'name' => sprintf('B%s - Unit %s', $building->id, $i),
                        'building_id' => $building->id,
                    ];

                    factory(App\Models\Unit::class, 1)->create($attr);
                }
            }
        }
    }
}
