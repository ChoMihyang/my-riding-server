<?php

namespace App;

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

        $returnData = self::select($param)
            ->where('stat_user_id', $user_id)
            ->where('stat_year', $today_year)
            ->where('stat_week', $today_week)
            ->orderBy('stat_day')
            ->get();

        return $returnData;
    }


    /**
     * @param int $year
     * @param int $user_id
     * @return Collection
     */
    public function select_stats(
        int $user_id,
        int $year
    ): Collection
    {
        $param = [
            'stat_week as week',
            'stat_day as day',
            'stat_distance as distance',
            'stat_time as time',
            'stat_avg_speed as avg_speed'
        ];

        $returnData = Stats::select($param)
            ->where('stat_user_id', $user_id)
            ->where('stat_year', $year)
            ->whereNotIn('stat_week', [53])
            ->orderBy('stat_week')
            ->get();

        return $returnData;
    }

    // 선택 연도와 주차에 해당하는 통계 조회

    /**
     *
     * @param int $user_id
     * @param int $year
     * @param int $week
     * @return Collection
     */
    public function get_stats_by_week(
        int $user_id,
        int $year,
        int $week
    ): Collection
    {
        $param = [
            'stat_day as day',
            'stat_distance as distance',
            'stat_time as time',
            'stat_avg_speed as avg_speed'
        ];

        $returnData = Stats::select($param)
            ->where('stat_user_id', $user_id)
            ->where('stat_year', $year)
            ->where('stat_week', $week)
            ->get();

        return $returnData;
    }

    /**
     * 사용자 랭킹 기록 상세 보기
     * TODO users 테이블 조인 -> 사진, 닉네임, 점수 추가 반환
     * @param int $user_id
     * @return Collection
     */
    public function getUserDetailRank(
        int $user_id
    ): Collection
    {
        $param = [
            'stat_distance as distance',
            'stat_time as time',
            'stat_avg_speed as avg_speed',
            'stat_max_speed as max_speed'
        ];
        $returnData = Stats::select($param)
            ->where('stat_user_id', $user_id)
            ->get();

        return $returnData;
    }

    // 특정 주차의 통계 조회
    public function select_stats_by_week()
    {

    }
}
