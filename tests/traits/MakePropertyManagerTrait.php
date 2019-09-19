<?php

use App\Models\PropertyManager;
use App\Repositories\PropertyManagerRepository;
use Faker\Factory as Faker;

trait MakePropertyManagerTrait
{
    /**
     * Create fake instance of PropertyManager and save it in database
     *
     * @param array $propertyManagerFields
     * @return PropertyManager
     */
    public function makePropertyManager($propertyManagerFields = [])
    {
        /** @var PropertyManagerRepository $propertyManagerRepo */
        $propertyManagerRepo = App::make(PropertyManagerRepository::class);
        $theme = $this->fakePropertyManagerData($propertyManagerFields);
        return $propertyManagerRepo->create($theme);
    }

    /**
     * Get fake data of PropertyManager
     *
     * @param array $postFields
     * @return array
     */
    public function fakePropertyManagerData($propertyManagerFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'user_id' => $fake->randomDigitNotNull,
            'building_id' => $fake->randomDigitNotNull,
            'created_at' => $fake->word,
            'updated_at' => $fake->word
        ], $propertyManagerFields);
    }

    /**
     * Get fake instance of PropertyManager
     *
     * @param array $propertyManagerFields
     * @return PropertyManager
     */
    public function fakePropertyManager($propertyManagerFields = [])
    {
        return new PropertyManager($this->fakePropertyManagerData($propertyManagerFields));
    }
}
