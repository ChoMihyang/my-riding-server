<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory;

class BadgeTableSeeder extends Seeder
{

    public function run()
    {
        $faker = Factory::create('ko_kr');

        // badges 테이블 내 생성할 배지 레코드 수
        $num_of_badge = 20;

        // 연속 배지 종류
        $badge_of_continue = ['1주', '2주', '3주', '4주', '1개월', '3개월'];
        // 거리 배지 종류
        $badge_distance = ['누적 10', '누적 30', '누적 50', '누적 70', '누적 100', '최장 거리 라이딩 123'];
        // 시간 배지 종류
        $badge_time = ['누적 3', '누적 5', '누적 10', '누적 15', '누적 20', '최장 시간 라이딩 123'];
        // 속도 배지 종류
        $badge_speed = ['평균 속도 20', '평균 속도 25', '평균 속도 30', '평균 속도 35', '평균 속도 40', '최고 속도 라이딩 123'];
        // 점수 배지 종류
        $badge_score = ['내 점수 1,000', '내 점수 3,000', '내 점수 5,000', '내 점수 7,000', '내 점수 10,000', '내 점수 15,000'];

        // 배지 이름
        $badge_name_type = [
            1 => $badge_of_continue,
            2 => $badge_distance,
            3 => $badge_time,
            4 => $badge_speed,
            5 => $badge_score
        ];

        // 배지 달성
        $badge_sen_type = [
            1 => ' 연속 기록',
            2 => 'km 달성',
            3 => '시간 달성',
            4 => 'km/h 달성',
            5 => '점 달성'
        ];

        for ($badge_count = 0; $badge_count < $num_of_badge; $badge_count++) {

            $badge_type = random_int(0, 5);
            $badge_name = $badge_type == 0 ? '최초 가입' : $badge_name_type[$badge_type][$badge_type];
            $badge_sen = $badge_type == 0 ? '' : $badge_sen_type[$badge_type];

            DB::table('badges')->insert([
                'id' => 0,
                'badge_id' => $faker->numberBetween(1, 100),
                'badge_type' => $badge_type,
                'badge_name' => $badge_name . $badge_sen,
                'created_at' => $faker->dateTimeBetween($startDate = '-3 year', $endDate = '-2 year'),
                'updated_at' => $faker->dateTimeBetween($startDate = '-2 year', $endDate = 'now')
            ]);
        }
    }

}
