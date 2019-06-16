<?php

use Faker\Generator as Faker;

$factory->define(App\Message::class, function (Faker $faker) {
    do{
        $sender = rand(1, 100);
        $receiver = rand(1, 100);
    }while(($receiver - $sender) === 0);

    return [
        'sender_id' => $sender,
        'receiver_id' => $receiver,
        'body' => $faker->sentence,
        'seen' => 0
    ];
});
