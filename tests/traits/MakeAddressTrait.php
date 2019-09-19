<?php

use Faker\Factory as Faker;
use App\Models\Address;
use App\Repositories\AddressRepository;

trait MakeAddressTrait
{
    /**
     * Create fake instance of Address and save it in database
     *
     * @param array $addressFields
     * @return Address
     */
    public function makeAddress($addressFields = [])
    {
        /** @var AddressRepository $addressRepo */
        $addressRepo = App::make(AddressRepository::class);
        $theme = $this->fakeAddressData($addressFields);
        return $addressRepo->create($theme);
    }

    /**
     * Get fake instance of Address
     *
     * @param array $addressFields
     * @return Address
     */
    public function fakeAddress($addressFields = [])
    {
        return new Address($this->fakeAddressData($addressFields));
    }

    /**
     * Get fake data of Address
     *
     * @param array $postFields
     * @return array
     */
    public function fakeAddressData($addressFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'country_id' => $fake->randomDigitNotNull,
            'state_id' => $fake->randomDigitNotNull,
            'city' => $fake->word,
            'address' => $fake->word,
            'address_nr' => $fake->word,
            'zip' => $fake->word,
            'created_at' => $fake->word,
            'updated_at' => $fake->word
        ], $addressFields);
    }
}
