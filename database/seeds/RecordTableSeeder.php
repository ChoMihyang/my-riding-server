<?php

use Illuminate\Database\Seeder;
use Faker\Factory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class RecordTableSeeder extends Seeder
{
    public function run()
    {
        $faker = Factory::create('ko_kr');

        // 사용자 한 명당 생성할 기록 레코드 수
        $num_of_record = 3;

        // 번호가 1 ~ 3 인 회원의 수만큼 반복
        for ($count_user = 0; $count_user < 3; $count_user++) {
            // 회원마다 3개씩 기록 레코드 생성
            for ($record_count = 0; $record_count < $num_of_record; $record_count++) {
                DB::table('records')->insert([
                    'id' => 0,
                    'rec_user_id' => $faker->numberBetween(1, 20),
                    'rec_route_id' => $faker->numberBetween(1, 20),
                    'rec_title' => '운동 끝_' . ($record_count + 1),
                    'rec_distance' => $faker->numberBetween(5, 100),
                    'rec_time' => $faker->numberBetween(50, 300),
                    'rec_score' => $faker->numberBetween(0, 500),
                    'rec_start_point_address' => $faker->address,
                    'rec_end_point_address' => $faker->address,
                    'rec_avg_speed' => $faker->numberBetween(10, 40),
                    'rec_max_speed' => $faker->numberBetween(20, 50),
                    'created_at' => $faker->dateTimeBetween($startDate = '-3 year', $endDate = '-2 year'),
                    'updated_at' => $faker->dateTimeBetween($startDate = '-2 year', $endDate = 'now')
                ]);
            }
        }
    }
}
