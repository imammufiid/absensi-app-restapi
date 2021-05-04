<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Config;
use App\Model;
use Faker\Generator as Faker;

$factory->define(Config::class, function (Faker $faker) {
    $dataConfig = json_encode([
        "name"              => "office",
        "address"           => "Blitar City",
        "latitude"          => "-8.137035168730755",
        "longitude"         => "112.13864366932964",
        "time"              => "07:30:00",
        "attendance_point"  => 2,
        "task_point"        => 2
    ]);
    return [
        "name"      => "office",
        "configuration" => $dataConfig,
        "status"    => 1
    ];
});
