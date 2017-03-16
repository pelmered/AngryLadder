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

$factory->define(App\User::class, function ($faker) {
    return [
        'name' => $faker->name,
        'email' => $faker->email,
    ];
});

$factory->define(App\Player::class, function ($faker) {
    return [
        'name' => $faker->name,
        'email' => $faker->email,
    ];
});

$factory->define(App\Match::class, function ($faker) {
    return [
        'name' => $faker->name,
        'email' => $faker->email,
    ];
});


$factory->define(App\LadderPeriod::class, function ($faker) {
    return [
        'ladder'        => $ladderKey,
        'active'        => 1,
        'period_start'  => LadderPeriod::getCurrentStartDate( $ladder),
        'period_end'    => LadderPeriod::getNextEndDate( $ladder ),
    ];
});



