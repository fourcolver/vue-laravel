<?php

use App\Models\District;
use App\Repositories\DistrictRepository;
use Faker\Factory as Faker;

trait MakeDistrictTrait
{
    /**
     * Create fake instance of District and save it in database
     *
     * @param array $districtFields
     * @return District
     */
    public function makeDistrict($districtFields = [])
    {
        /** @var DistrictRepository $districtRepo */
        $districtRepo = App::make(DistrictRepository::class);
        $theme = $this->fakeDistrictData($districtFields);
        return $districtRepo->create($theme);
    }

    /**
     * Get fake data of District
     *
     * @param array $postFields
     * @return array
     */
    public function fakeDistrictData($districtFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'name' => $fake->word,
            'description' => $fake->text,
            'created_at' => $fake->word,
            'updated_at' => $fake->word
        ], $districtFields);
    }

    /**
     * Get fake instance of District
     *
     * @param array $districtFields
     * @return District
     */
    public function fakeDistrict($districtFields = [])
    {
        return new District($this->fakeDistrictData($districtFields));
    }
}
