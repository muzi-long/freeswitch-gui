<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Faker\Generator as Faker;

$factory->define(\App\Models\Customer::class, function (Faker $faker) {
    return [
        'uuid' => create_customer_num(),
        'name' => $faker->company,
        'contact_name' => $faker->firstName,
        'contact_phone' => $faker->phoneNumber,
        'created_user_id' => 1,
        'created_user_nickname' => 'root',
        'owner_user_id' => 1,
        'owner_user_nickname' => 'root',
        'owner_department_id' => 0,
        'status' => 1,
        'status_time' => date('Y-m-d H:i:s'),

    ];
});
