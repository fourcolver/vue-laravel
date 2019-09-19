<?php

use App\Models\Country;
use App\Models\State;
use Illuminate\Database\Seeder;

class StatesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $json = File::get('database/data/states.json');
        $data = json_decode($json);
        if (!$data) {
            return;
        }

        $country = Country::where('code', 'CH')->first();
        foreach($data as $obj) {
            State::create(
                [
                    'country_id' => $country->id,
                    'code' => $obj->abbreviation,
                    'name' => $obj->name->en,
                    'name_de' => $obj->name->de,
                    'name_fr' => $obj->name->fr,
                    'name_it' => $obj->name->it,
                    'name_rm' => $obj->name->rm,
                ]
            );
        }
    }
}
