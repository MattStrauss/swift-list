<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Aisle;
use App\Category;
use App\Item;
use App\Recipe;
use App\ShoppingList;
use App\User;
use Illuminate\Support\Str;
use Faker\Generator as Faker;

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
    return [
        'name' => $faker->name,
        'email' => $faker->unique()->safeEmail,
        'email_verified_at' => now(),
        'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
        'remember_token' => Str::random(10),
    ];
});


$factory->define(Category::class, function (Faker $faker) {
    return [
        'name' => $faker->sentence($nbWords = 2, $variableNbWords = true),
    ];
});


$factory->define(Recipe::class, function (Faker $faker) {
    return [
        'name' => $faker->sentence($nbWords = 8, $variableNbWords = true),
        'instructions' => $faker->paragraph($nbSentences = 6, $variableNbSentences = true),
        'user_id' => User::first()->id ?? factory(User::class)->create()->id,
        'category_id' => Category::first()->id ?? factory(Category::class)->create()->id,
    ];
});


$factory->define(Aisle::class, function (Faker $faker) {
    return [
        'name' => $faker->sentence($nbWords = 2, $variableNbWords = true),
    ];
});


$factory->define(Item::class, function (Faker $faker) {
    return [
        'name' => $faker->word,
        'aisle_id' => Aisle::first()->id ?? factory(Aisle::class)->create()->id,
        'user_id' => User::first()->id ?? factory(User::class)->create()->id,
    ];
});


$factory->define(ShoppingList::class, function (Faker $faker) {
    return [
        'user_id' => User::first()->id ?? factory(User::class)->create()->id,
        'name' => $faker->dateTimeBetween($startDate = '-3 years', $endDate = 'now')->format("'l, M jS'"). ' List',
    ];
});
