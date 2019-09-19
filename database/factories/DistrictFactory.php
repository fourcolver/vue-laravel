<?php

use Faker\Generator as Faker;

$factory->define(App\Models\District::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
        'description' => $faker->paragraph,
    ];
});
