<?php

use Illuminate\Database\Seeder;
use Faker\Factory;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use App\Stats;
use App\User;


class StatsTableSeeder extends Seeder
{
    public function run()
    {
        $faker = Factory::create('ko_kr');

        // 사용자 한 명당 내 생성할 통계 레코드 수
        $num_of_stats = 5;

        // 번호가 1 ~ 3  인 회원의 수만큼 반복
        for ($count_user = 0; $count_user < 3; $count_user++) {
            // 회원마다 10개씩 통계 레코드 생성
            for ($count_stats = 0; $count_stats < $num_of_stats; $count_stats++) {

                $temp_date = $faker->date('Y-m-d');                     // 임의의 날짜 생성
                $date_y = date('Y', strtotime($temp_date));             // 위 날짜의 '연도' 가져오기
                $date_m = date('m', strtotime($temp_date));             // 위 날짜의 '월' 가져오기
                $date_d = date('d', strtotime($temp_date));             // 위 날짜의 '일' 가져오기

                $check_week = $date_y . "-" . $date_m . "-" . $date_d;
                $mk_date = strtotime($check_week);                          // 날짜 형식에 맞춰 생성
                $date_week = date('W', $mk_date);                   // 몇주차인지 저장
                $date_day = date('w', $mk_date);                    // 무슨 요일인지 저장

                DB::table('stats')->insert([
                    'id' => 0,
                    'stat_user_id' => $count_user + 1,
                    'stat_date' => $temp_date,
                    'stat_week' => $date_week,
                    'stat_day' => $date_day,
                    'stat_distance' => $faker->numberBetween(5, 50),
                    'stat_time' => $faker->numberBetween(0, 250),
                    'stat_avg_speed' => $faker->numberBetween(10, 40),
                    'stat_max_speed' => $faker->numberBetween(10, 40),
                    'stat_count' => $faker->numberBetween(1, 20),
                    'stat_year' => $date_y,
                    'created_at' => $temp_date,
                    'updated_at' => null
                ]);
            }
        }
    }
}
