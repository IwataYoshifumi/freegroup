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
    ];
});
