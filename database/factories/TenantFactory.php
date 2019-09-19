<?php

use App\Models\Tenant;
use Faker\Generator as Faker;

$factory->define(App\Models\Tenant::class, function (Faker $faker) {
    $title = Tenant::Title[$faker->numberBetween(0, count(Tenant::Title) - 1)];

    $company = null;
    if ($title == 'company') {
        $company = $faker->company;
    }

    return [
        'user_id' => 1,
        'title' => $title,
        'company' => $company,
        'first_name' => $faker->firstName,
        'last_name' => $faker->lastName,
        'birth_date' => $faker->date(),
        'mobile_phone' => $faker->phoneNumber,
        'private_phone' => $faker->phoneNumber,
        'work_phone' => $faker->phoneNumber,
        'status' => $faker->numberBetween(Tenant::StatusActive, Tenant::StatusNotActive),
    ];
});
