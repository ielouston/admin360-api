<?php

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| Here you may define all of your model factories. Model factories give
| you a convenient way to create models for testing and seeding your
| database. Just tell the factory how a default model should look.
|
*/

use Muebleria\User;
use Muebleria\Client;

$factory->define(User::class, function (Faker\Generator $faker) {
    return [
        'name' => $faker->name,
        'email' => $faker->safeEmail,
        'password' => bcrypt('fuckyou'),
        'first_name' => $faker->name,
        'last_name' => $faker->name,
        'region' => $faker->numberBetween(1, 5), 
        'state' => 'active',
        'remember_token' => str_random(10),
    ];
});

$factory->define(Client::class, function (Faker\Generator $faker) {
    return [
        'full_name' => $faker->name(),
        'telephone' => $faker->phoneNumber(),
        'telephone2' => $faker->phoneNumber(),
        'avatar' => $faker->imageUrl(70, 70),
        'street' => $faker->address(),
        'message' => $faker->text(),
        'address' => $faker->address(),
        'region' => $faker->numberBetween(1, 3),
        'license' => $faker->numberBetween(0, 4),
        'popularity' => $faker->numberBetween(0, 1500),
        'send_service' => $faker->numberBetween(1, 2),
        'category_id' => $faker->numberBetween(1, 10)
    ];
});

$factory->define(Category::class, function (Faker\Generator $faker) {
    return [
        'name' => $faker->word(),
        'type' => $faker->numberBetween(1, 5)
    ];
});

$factory->define(Article::class, function (Faker\Generator $faker) {
    return [
        'name' => $faker->name(),
        'description' => $faker->paragraph(),
        'photo_one' => $faker->imageUrl(1024, 680).",".$faker->imageUrl(800, 600).",".$faker->imageUrl(80, 80),
        'photo_two' => $faker->imageUrl(1024, 680).",".$faker->imageUrl(800, 600).",".$faker->imageUrl(80, 80),
        'photo_three' => $faker->imageUrl(1024, 680).",".$faker->imageUrl(800, 600).",".$faker->imageUrl(80, 80),
        'price' => $faker->numberBetween(0, 1000),
        'popularity' => $faker->numberBetween(0, 1000),
        'category' => $faker->word(),
        'subcategory' => $faker->word(),
        'bussiness_id' => $faker->numberBetween(1, 1000)
    ];
});
