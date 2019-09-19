<?php

use Faker\Generator as Faker;

$factory->define(App\Models\Address::class, function (Faker $faker) {
    $country = (new App\Models\Country)->where('name', 'Switzerland')->first();
    $state = (new App\Models\State)->where('country_id', $country->id)->inRandomOrder()->first();

    return [
        'country_id' => $country->id,
        'state_id' => $state->id,
        'city' => $faker->city,
        'street' => $faker->streetAddress,
        'street_nr' => $faker->buildingNumber,
        'zip' => 3172,
    ];
});
