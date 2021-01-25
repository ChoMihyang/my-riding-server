<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call('UserTableSeeder');
        $this->call('StatsTableSeeder');
        $this->call('RouteTableSeeder');
        $this->call('RouteLikeTableSeeder');
        $this->call('RecordTableSeeder');
        $this->call('NotificationTableSeeder');
    }
}
