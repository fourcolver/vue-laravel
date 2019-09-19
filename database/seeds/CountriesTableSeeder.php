<?php

use Illuminate\Database\Seeder;
use App\Models\Country;

class CountriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $json = File::get('database/data/countries.json');
        $data = json_decode($json);
        if (!$data) {
            return;
        }

        foreach($data as $obj) {
            Country::create(
                [
                    'name' => $obj->name,
                    'code' => $obj->code,
                ]
            );
        }
    }
}
