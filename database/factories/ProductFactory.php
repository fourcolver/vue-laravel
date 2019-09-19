<?php

use Faker\Generator as Faker;
use App\Models\Product;
use App\Models\User;

$factory->define(App\Models\Product::class, function (Faker $f) {
    $u = [
        User::where('deleted_at', null)->first(),
        User::where('email', 'tenant@example.com')->first(),
    ][rand(0, 1)];
    $t = [Product::TypeSell, Product::TypeLend][rand(0, 1)];

    $ret = [
        'user_id' => $u->id,
        'type' => $t,
        'status' => Product::StatusPublished,
        'visibility' => Product::VisibilityAll,
        'content' => $f->paragraph(),
        'title' => $f->sentence(),
        'published_at' => \Carbon\Carbon::now(),
        'contact' => implode(" ", [$f->firstName, $f->lastName, $f->phoneNumber]),
        'price' => '1.10',
    ];

    if ($u->tenant && $u->tenant->building) {
        $ret['address_id'] = $u->tenant->building->address_id;
        $ret['district_id'] = $u->tenant->building->district_id;
    }

    return $ret;
});
