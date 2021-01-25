<?php

use Illuminate\Database\Seeder;
use App\RouteLike;
use App\Route;
use App\User;
use Illuminate\Support\Facades\DB;
use Faker\Factory;

class RouteLikeTableSeeder extends Seeder
{
    public function run()
    {
        $faker = Factory::create('ko_kr');

        // route_likes 테이블 내 생성할 레코드 수
        $num_of_route_like = 30;

        for ($count = 0; $count < $num_of_route_like; $count++) {
            DB::table('route_likes')->insert([

                'id' => 0,
                'route_like_user' => $faker->numberBetween(1, 20),
                'route_like_obj' => $faker->numberBetween(1, 100),
                'created_at' => $faker->dateTimeBetween($startDate = '-3 year', $endDate = 'now'),
                'updated_at' => null
            ]);
        }
    }
}
