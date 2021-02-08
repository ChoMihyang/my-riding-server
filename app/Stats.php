<?php

namespace App;

use http\Env\Request;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Stats extends Model
{
    protected $table = 'stats';

    protected $hidden = ['created_at', 'updated_at'];

    // TODO 웹에서 주차 선택 시 ,해당 통계 조회

    /**
     * 대시보드 통계 정보(UserController - dashboard)
     * @param int $user_id
     * @param int $today_year
     * @param int $today_week
     * @return Collection
     */
    public function getDashboardStats(
        int $user_id,
        int $today_year,
        int $today_week
    ): Collection
    {
        $param = [
            'stat_day as day',
            'stat_distance as distance',
            'stat_time as time',
            'stat_avg_speed as avg_speed'
        ];

        $user_stats = self::select($param)
            ->where('stat_user_id', $user_id)
            ->where('stat_year', $today_year)
            ->where('stat_week', $today_week)
            ->orderBy('stat_day')
            ->get();

        return $user_stats;
    }


    /**
     * 특정 연도의 통계 조회
     *
     * @param int $year
     * @param int $user_id
     * @return Collection
     */
    public function select_stats_by_year(
        int $year,
        int $user_id
    ): Collection
    {
        $param = [
            'stat_week as week',
            'stat_day as day',
            'stat_distance as distance',
            'stat_time as time',
            'stat_avg_speed as avg_speed'
        ];

        $record_stats_by_year = Stats::select($param)
            ->where('stat_user_id', $user_id)
            ->where('stat_year', $year)
            ->orderBy('stat_week')
            ->get();

        return $record_stats_by_year;
    }
}
