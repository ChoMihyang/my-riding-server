<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\User;
use App\RouteLike;
use App\Route;
use Faker\Factory;

class StatsController extends Controller
{

    public function statsTest()
    {
        $faker = Factory::create('ko_kr');

        for ($i = 0; $i < 10; $i++) {
            $notiCheck = $faker->boolean;
            $tempDate = $faker->dateTimeBetween($startDate = '-2 year', $endDate = 'now');
//            var_dump($tempDate);
            $updatedCheck = $notiCheck == true ? $tempDate : 'False';
            dump($notiCheck . ":" . $updatedCheck);
        }
    }
}
