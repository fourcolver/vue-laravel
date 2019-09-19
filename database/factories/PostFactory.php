<?php

use Faker\Generator as Faker;
use App\Models\Post;
use App\Models\User;

$factory->define(App\Models\Post::class, function (Faker $faker) {
    $us = [
        User::where('deleted_at', null)->first(),
        User::where('email', 'tenant@example.com')->first(),
    ];
    $u = $us[rand(0, 1)];
    $ret = [
        'user_id' => $u->id,
        'type' => Post::TypeArticle,
        'status' => Post::StatusNew,
        'visibility' => Post::VisibilityAll,
        'content' => $faker->paragraph(),
        'notify_email' => true,
    ];

    return $ret;
});
