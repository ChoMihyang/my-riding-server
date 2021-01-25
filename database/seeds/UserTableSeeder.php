<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Faker\Factory;

class UserTableSeeder extends Seeder
{
    public function run()
    {
        // users 테이블 내 생성할 사용자 레코드 수
        $num_of_user = 20;

        // 임의 경로 설정
        $user_picture_paths = 'C:\user\profile\myPicture_';

        // Fake 객체 생성
        $faker = Factory::create('ko_kr');

        for ($count = 1; $count <= $num_of_user; $count++) {

            DB::table('users')->insert([
                'user_account' => $faker->name,
                'user_password' => Hash::make('user_password'),
                'user_nickname' => 'Rider_' . $count,
                'user_picture' => $user_picture_paths . $count,
                'user_num_of_riding' => $faker->numberBetween(0, 100),
                'user_score_of_riding' => $faker->numberBetween(0, 30000),
                'date_of_latest_riding' => $faker->dateTimeBetween($startDate = '-2 year', $endDate = 'now'),
                'created_at' => $faker->dateTimeBetween($startDate = '-3 year', $endDate = '-2 year'),
                'updated_at' => $faker->dateTimeBetween($startDate = '-2 year', $endDate = 'now')
            ]);
        }
    }
}
