<?php

use Faker\Generator as Faker;

$factory->define(\App\Comment::class, function (Faker $faker) {
    return [
        'user_id' => rand(1, 100),
        'tweet_id' => rand(1, 400),
        'body' => $faker->sentence
    ];
});
