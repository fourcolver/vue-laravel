<?php

use Faker\Factory as Faker;
use App\Models\ServiceRequest;
use App\Repositories\ServiceRequestRepository;

trait MakeServiceRequestTrait
{
    /**
     * Create fake instance of ServiceRequest and save it in database
     *
     * @param array $serviceRequestFields
     * @return ServiceRequest
     */
    public function makeServiceRequest($serviceRequestFields = [])
    {
        /** @var ServiceRequestRepository $serviceRequestRepo */
        $serviceRequestRepo = App::make(ServiceRequestRepository::class);
        $theme = $this->fakeServiceRequestData($serviceRequestFields);
        return $serviceRequestRepo->create($theme);
    }

    /**
     * Get fake instance of ServiceRequest
     *
     * @param array $serviceRequestFields
     * @return ServiceRequest
     */
    public function fakeServiceRequest($serviceRequestFields = [])
    {
        return new ServiceRequest($this->fakeServiceRequestData($serviceRequestFields));
    }

    /**
     * Get fake data of ServiceRequest
     *
     * @param array $postFields
     * @return array
     */
    public function fakeServiceRequestData($serviceRequestFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'category_id' => $fake->randomDigitNotNull,
            'subject_id' => $fake->randomDigitNotNull,
            'tenant_id' => $fake->randomDigitNotNull,
            'agent_id' => $fake->randomDigitNotNull,
            'service_id' => $fake->randomDigitNotNull,
            'title' => $fake->word,
            'description' => $fake->text,
            'status' => $fake->randomDigitNotNull,
            'priority' => $fake->randomDigitNotNull,
            'due_date' => $fake->word,
            'defect_location' => $fake->word,
            'created_at' => $fake->word,
            'updated_at' => $fake->word
        ], $serviceRequestFields);
    }
}
