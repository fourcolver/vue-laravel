<?php

use Faker\Factory as Faker;
use App\Models\Tenant;
use App\Repositories\TenantRepository;

trait MakeTenantTrait
{
    /**
     * Create fake instance of Tenant and save it in database
     *
     * @param array $tenantFields
     * @return Tenant
     */
    public function makeTenant($tenantFields = [])
    {
        /** @var TenantRepository $tenantRepo */
        $tenantRepo = App::make(TenantRepository::class);
        $theme = $this->fakeTenantData($tenantFields);
        return $tenantRepo->create($theme);
    }

    /**
     * Get fake instance of Tenant
     *
     * @param array $tenantFields
     * @return Tenant
     */
    public function fakeTenant($tenantFields = [])
    {
        return new Tenant($this->fakeTenantData($tenantFields));
    }

    /**
     * Get fake data of Tenant
     *
     * @param array $postFields
     * @return array
     */
    public function fakeTenantData($tenantFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'user_id' => $fake->randomDigitNotNull,
            'address_id' => $fake->randomDigitNotNull,
            'building_id' => $fake->randomDigitNotNull,
            'unit_id' => $fake->randomDigitNotNull,
            'title' => $fake->word,
            'first_name' => $fake->word,
            'last_name' => $fake->word,
            'birthrate' => $fake->randomDigitNotNull,
            'mobile_phone' => $fake->word,
            'private_phone' => $fake->word,
            'work_phone' => $fake->word,
            'created_at' => $fake->word,
            'updated_at' => $fake->word
        ], $tenantFields);
    }
}
