<?php

use App\Models\Address;
use App\Models\Building;
use App\Models\District;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;

class BuildingsTableSeeder extends Seeder
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
        $addresses = Address::get();
        foreach ($addresses as $k => $address) {
            $hasDistrict = $faker->boolean;
            $attr = [
                'name' => sprintf('Building %s', $k + 1),
            ];
            if ($hasDistrict) {
                $attr['district_id'] = District::inRandomOrder()->first()->id;
            }

            factory(Building::class, 1)->create($attr)->each(function ($building) use ($address) {
                $building->address_id = $address->id;
                $building->save();
            });
        }
    }
}
