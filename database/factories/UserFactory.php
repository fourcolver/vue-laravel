<?php

use App\Models\User;
use Faker\Generator as Faker;
use Illuminate\Support\Str;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/

$factory->define(User::class, function (Faker $faker) {
    $title = User::Title[$faker->numberBetween(0, count(User::Title) - 1)];

    return [
        'title' => $title,
        'name' => $faker->firstName . ' ' . $faker->lastName,
        'email' => $faker->unique()->safeEmail,
        'phone' => $faker->phoneNumber,
        'email_verified_at' => now(),
        'password' => '$2y$10$TKh8H1.PfQx37YgCzwiKb.KjNyWgaHb9cbcoQgdIVFlYg7B77UdFm', // secret
        'remember_token' => Str::random(10)
    ];
});
