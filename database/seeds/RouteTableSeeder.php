<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory;
use App\User;
use App\RouteLike;

class RouteTableSeeder extends Seeder
{
    public function run()
    {
        $faker = Factory::create('ko_kr');

        // 사용자 한 명당 생성할 경로 레코드 수
        $num_of_route = 5;
        // 경로 이미지 저장 경로
        $route_picture_paths = 'C:\user\route\myPicture_';

        $temp_riding_title = "번째 라이딩";

        // id 가 1 ~ 10인 회원의 수만큼 반복
        for ($user_count = 0; $user_count < 3; $user_count++) {
            // 사용자마다 10개씩 경로 레코드 생성
            for ($route_count = 1; $route_count <= $num_of_route; $route_count++) {

                DB::table('routes')->insert([
                    'route_user_id' => $user_count + 1,
                    'route_title' => $route_count . $temp_riding_title,
                    'route_image' => $route_picture_paths . $route_count,
                    'route_distance' => $faker->numberBetween(1, 50),
                    'route_time' => $faker->numberBetween(1, 250),
                    'route_like' => $faker->numberBetween(0, 10),
                    'route_num_of_try_count' => $faker->numberBetween(0, 100),
                    'route_num_of_try_user' => $faker->numberBetween(0, 100),
                    'route_start_point_address' => $faker->address,
                    'route_end_point_address' => $faker->address,
                    'route_avg_degree' => $faker->numberBetween(0, 30),
                    'route_max_altitude' => $faker->numberBetween(0, 30),
                    'route_min_altitude' => $faker->numberBetween(0, 30),
                    'created_at' => $faker->dateTimeBetween($startDate = '-3 year', $endDate = 'now'),
                    'updated_at' => $faker->dateTimeBetween($startDate = '-2 year', $endDate = 'now')
                ]);
            }
        }
    }
}

