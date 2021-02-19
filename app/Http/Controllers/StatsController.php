<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Stats;

class StatsController extends Controller
{
    public function test()
    {
//        $today = date('Y-m-d');
        $today = '2021-01-04';

        // 해당 날짜의 전년도
        $last_year = date('Y', strtotime($today . "-1 year"));

        // 해당 날짜의 작년 말의 주차
        $last_year_week = date('W', strtotime($last_year . "-12-31"));

        // 해당 날짜의 주차
        $today_week = date('W', strtotime($today));

        // 작년 말일의 주차와 지정 날짜의 주차가 같은 경우
        //
        if ($today_week === $last_year_week) {
            $today = date('Y-m-d', strtotime($today . "-1 years"));

        }
        // 현재 날짜의 요일
//        $day_array = [0 => 6, 1 => 0, 2 => 1, 3 => 2, 4 => 3, 5 => 4, 6 => 5];
        $temp_day = date('w', strtotime($today));
        $today_day = $temp_day === 0 ? 6 : $temp_day - 1;

        // 현재 주차의 시작일 (월요일 기준)
        $today_start_date = date('Y-m-d', strtotime($my_date . "-" . $today_day . "days"));
        $end_start_date = date('Y-m-d', strtotime($today_start_date . "+6days"));

        dd($today_start_date . ":" . $end_start_date);
    }

}
