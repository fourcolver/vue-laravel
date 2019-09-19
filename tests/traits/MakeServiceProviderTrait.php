<?php

use Faker\Factory as Faker;
use App\Models\ServiceProvider;
use App\Repositories\ServiceProviderRepository;

trait MakeServiceProviderTrait
{
    /**
     * Create fake instance of ServiceProvider and save it in database
     *
     * @param array $serviceProviderFields
     * @return ServiceProvider
     */
    public function makeServiceProvider($serviceProviderFields = [])
    {
        /** @var ServiceProviderRepository $serviceProviderRepo */
        $serviceProviderRepo = App::make(ServiceProviderRepository::class);
        $theme = $this->fakeServiceProviderData($serviceProviderFields);
        return $serviceProviderRepo->create($theme);
    }

    /**
     * Get fake instance of ServiceProvider
     *
     * @param array $serviceProviderFields
     * @return ServiceProvider
     */
    public function fakeServiceProvider($serviceProviderFields = [])
    {
        return new ServiceProvider($this->fakeServiceProviderData($serviceProviderFields));
    }

    /**
     * Get fake data of ServiceProvider
     *
     * @param array $postFields
     * @return array
     */
    public function fakeServiceProviderData($serviceProviderFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'user_id' => $fake->randomDigitNotNull,
            'address_id' => $fake->randomDigitNotNull,
            'category' => $fake->word,
            'name' => $fake->word,
            'email' => $fake->word,
            'phone' => $fake->word,
            'created_at' => $fake->word,
            'updated_at' => $fake->word
        ], $serviceProviderFields);
    }
}
