<?php

use Faker\Generator as Faker;

$factory->define(App\Models\Building::class, function (Faker $faker) {
    return [
        'name' => $faker->sentence(3) ,
        'description' => $faker->sentence(5),
        'label' => $faker->sentence(3),
        'address_id' => 1,
        'floor_nr' => $faker->numberBetween(1, 30),
        'basement' => $faker->numberBetween(0,1),
        'attic' => $faker->numberBetween(0,1),
    ];
});
