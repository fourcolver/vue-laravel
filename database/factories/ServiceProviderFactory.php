<?php

use Faker\Generator as Faker;

$factory->define(App\Models\ServiceProvider::class, function (Faker $faker, array $attr) {
    $serviceCategories = [
        'electrician',
        'heating_company',
        'lift,sanitary',
        'key_service',
        'caretaker',
        'real_estate_service',
    ];
    $randomCat = $faker->numberBetween(1, count($serviceCategories)-1);

    $category = isset($attr['category']) ? $attr['category']: $serviceCategories[$randomCat];
    $user_id = isset($attr['user_id']) ? $attr['user_id'] : 1;
    $address_id = isset($attr['address_id']) ? $attr['address_id'] : 1;

    return [
        'user_id' => $user_id,
        'address_id' => $address_id,      
        'category' => $category,
        'name' => $faker->name,
        'email' => $faker->email,       
        'phone' => $faker->phoneNumber,       
    ];
});
 