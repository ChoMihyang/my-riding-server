<?php

use Illuminate\Database\Seeder;
use Faker\Factory;
use Illuminate\Support\Facades\DB;

class IpNumberTableSeeder extends Seeder
{
    /**
     * badges Table
     *
     * @return void
     */
    public function run()
    {
        $num_of_ip = 20;
        $faker = Factory::create('ko_kr');

        for ($count = 0; $count < $num_of_ip; $count++) {
            DB::table('ip_numbers')->insert([
                'id' => 0,
                'ip_user_id' => $faker->numberBetween(1, 20),
                'ip_num_front' => $faker->ipv6,
                'ip_num_back' => $faker->ipv6,
                'ip_num_port' => $faker->numberBetween(0, 1023),
                'created_at' => $faker->dateTimeBetween($startDate = '-3 year', $endDate = '-2 year'),
                'updated_at' => $faker->dateTimeBetween($startDate = '-2 year', $endDate = 'now')
            ]);
        }
    }
}
