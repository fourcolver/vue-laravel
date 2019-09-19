<?php

use App\Models\RealEstate;
use Illuminate\Database\Seeder;

class RealEstateTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $realEstate = new RealEstate();
        $realEstate->name = env('RL_NAME', 'Test Estate');
        $realEstate->email = env('RL_EMAIL', 'test@example.com');
        $realEstate->phone = env('RL_PHONE', '071112244');
        $realEstate->language = env('RL_LANG', 'en');
        $realEstate->cleanify_email = env('CLEANIFY_EMAIL', '');

        $realEstate->free_apartments_enable = false;
        $realEstate->opening_hours = json_encode($this->getOpeningHours());
        $realEstate->news_receiver_ids = [];

        $address = factory(App\Models\Address::class, 1)->create()[0];
        $address->zip = 3172;
        $address->save();

        $realEstate->address_id = $address->id;

        $realEstate->save();
    }

    private function getOpeningHours()
    {
        $openingHoursList = [];

        $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];

        foreach ($days as $day) {
            $openingHours = new stdClass();
            $openingHours->day = $day;
            $openingHours->closed = false;
            $openingHours->start_hour = 8;
            $openingHours->start_min = 0;
            $openingHours->end_hour = 16;
            $openingHours->end_min = 50;
            if ($day > 4) {
                $openingHours->closed = false;
            }
            $openingHoursList[] = $openingHours;
        }

        return $openingHoursList;
    }
}
