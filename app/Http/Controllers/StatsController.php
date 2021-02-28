<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Stats;
use Illuminate\Support\Facades\DB;

class StatsController extends Controller
{
    protected $stat;

    public function __construct()
    {
        $this->stat = new Stats();
    }

    public function test()
    {

        // 1. 이번주의 시작일 구하기
        $today_date = date('Y-m-d');

        $temp_day = date('w', strtotime($today_date));
        $today_day = $temp_day == 0 ? 6 : $temp_day - 1;

        $today_start_date = date('Y-m-d', strtotime($today_date . "-" . $today_day . "days"));
        $last_date_range = date('Y-m-d', strtotime($today_start_date . "-1day"));

        // 91일 전 날짜 구하기
        $start_date_range = date('Y-m-d', strtotime($today_start_date . "-91days"));

        // stats 모델에서 날짜 범위에 해당하는 통계 조회하기

        $param = [
            'stat_year as year',
            'stat_week as week',
            DB::raw('sum(stat_distance) as distance'),
            DB::raw('sum(stat_time) as time'),
            DB::raw('avg(stat_avg_speed) as avg_speed'),
            DB::raw('max(stat_max_speed) as max_speed')
        ];

        $stats = Stats::select($param)
            ->groupBy('stat_year')
            ->groupBy('stat_week')
            ->where('stat_user_id', 23)
            ->whereBetween('stat_date', [$start_date_range, $last_date_range])
            ->orderByDesc('stat_year')
            ->orderByDesc('stat_week')
            ->get();

        return $this->responseAppJson("통계 출력을 성공하였습니다.", 'stat', $stats, 200);
    }
}
