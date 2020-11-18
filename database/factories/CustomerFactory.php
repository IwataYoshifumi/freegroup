<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Customer;
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

$factory->define( App\Models\Customer::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
        'kana' => $faker->kanaName,
        'tel'  => $faker->phoneNumber,
        'fax'  => $faker->phoneNumber,
        'mobile' => $faker->phoneNumber,
        'zip_code' => $faker->postcode,
        'prefecture' => $faker->prefecture,
        'city' => $faker->city,
        'street' => $faker->streetAddress,
        'birth_day' => $faker->dateTimeBetween( '-90 years', '-5years' )->format( 'Y-m-d' ),
        
        'email' => $faker->unique()->safeEmail,
        'email_verified_at' => now(),
        'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
        'remember_token' => Str::random(10),
    ];
});
