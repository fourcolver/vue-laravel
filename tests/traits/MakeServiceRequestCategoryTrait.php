<?php

use Faker\Factory as Faker;
use App\Models\ServiceRequestCategory;
use App\Repositories\ServiceRequestCategoryRepository;

trait MakeServiceRequestCategoryTrait
{
    /**
     * Create fake instance of ServiceRequestCategory and save it in database
     *
     * @param array $serviceRequestCategoryFields
     * @return ServiceRequestCategory
     */
    public function makeServiceRequestCategory($serviceRequestCategoryFields = [])
    {
        /** @var ServiceRequestCategoryRepository $serviceRequestCategoryRepo */
        $serviceRequestCategoryRepo = App::make(ServiceRequestCategoryRepository::class);
        $theme = $this->fakeServiceRequestCategoryData($serviceRequestCategoryFields);
        return $serviceRequestCategoryRepo->create($theme);
    }

    /**
     * Get fake instance of ServiceRequestCategory
     *
     * @param array $serviceRequestCategoryFields
     * @return ServiceRequestCategory
     */
    public function fakeServiceRequestCategory($serviceRequestCategoryFields = [])
    {
        return new ServiceRequestCategory($this->fakeServiceRequestCategoryData($serviceRequestCategoryFields));
    }

    /**
     * Get fake data of ServiceRequestCategory
     *
     * @param array $postFields
     * @return array
     */
    public function fakeServiceRequestCategoryData($serviceRequestCategoryFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'parent_id' => $fake->randomDigitNotNull,
            'name' => $fake->word,
            'description' => $fake->text,
            'created_at' => $fake->word,
            'updated_at' => $fake->word
        ], $serviceRequestCategoryFields);
    }
}
