<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory;

class BadgeTableSeeder extends Seeder
{
    /**
     * Badge Table
     *
     * @return void
     */
    public function run()
    {

        $numOfBadge = 5;
        $faker = Factory::create('ko_kr');

        // 배지 유형
        $badge_type = [
            1 => '가입',
            2 => '연속',
            3 => '거리',
            4 => '속도',
            5 => '시간',
            6 => '점수',
        ];
        // 배지 이름
        $badge_name = [];
        // [가입] 배지 이름
        $badge_name_of_sign = '최초 가입';

        // [연속] 배지 이름
        $badge_name_of_continue = '주 연속 기록';

        // [거리] 배지 이름
        $badge_name_of_distance = 'km 달성';
        for ($count = 0; $count < $numOfBadge; $count++) {
            DB::table('badges')->insert([
                'id' => 0,
                'badge_id' => 1,
                'badge_type' => 1,
                'badge_name' => [],
                'created_at' => $faker->dateTimeBetween($startDate = '-3 year', $endDate = '-2 year'),
                'updated_at' => $faker->dateTimeBetween($startDate = '-2 year', $endDate = 'now')
            ]);
        }
    }
}
