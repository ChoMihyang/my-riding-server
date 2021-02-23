<?php

namespace App;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Stats extends Model
{
    protected $table = 'stats';
    protected $hidden = ['created_at', 'updated_at'];
    protected $fillable = [
        'stat_user_id', 'stat_date', 'stat_week', 'stat_day',
        'stat_distance', 'stat_time', 'stat_avg_speed', 'stat_max_speed',
        'stat_count', 'stat_year', 'created_at', 'updated_at'
    ];

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
     * @param int $same_week
     * @return Collection
     */
    public function get_stats_by_year(
        int $user_id,
        int $year
    ): Collection
    {

        $param = [
            'stat_date as date',
            'stat_week as week',
            'stat_day as day',
            'stat_distance as distance',
            'stat_time as time',
            'stat_avg_speed as avg_speed'
        ];

        $returnData = Stats::select($param)
            ->where('stat_user_id', $user_id)
            ->where('stat_year', $year)
            ->orderByDesc('stat_week')
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
    public
    function get_stats_by_week(
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
            ->orderBy('stat_day')
            ->get();

        return $returnData;
    }

    /**
     * 사용자 랭킹 기록 상세 보기
     * TODO users 테이블 조인 -> 사진, 닉네임, 점수 추가 반환
     * @param string $rank_user_name
     * @param int $user_id
     * @return Collection
     */
    public
    function getUserDetailRank(
        int $user_id,
        string $rank_user_name
    ): Collection
    {
        $param = [
            'users.user_picture as picture',
            'users.user_nickname as name',
            'users.user_score_of_riding as score',
            'stat_distance as distance',
            'stat_time as time',
            'stat_avg_speed as avg_speed',
            'stat_max_speed as max_speed'
        ];
        $returnData = Stats::select($param)
            ->join('users', 'stat_user_id', 'users.id')
            ->where('users.id', $user_id)
            ->where('users.user_nickname', $rank_user_name)
            ->get();

        return $returnData;
    }

// 특정 주차의 통계 조회
    public
    function select_stats_by_week()
    {

    }

    // 통계 저장
    public function createStats(
        int $rec_user_id,
        int $today_week,
        int $today_day,
        float $rec_distance,
        int $rec_time,
        float $rec_avg_speed,
        float $rec_max_speed,
        String $today_year
    )
    {
        Stats::create([
            'stat_user_id' => $rec_user_id,
            'stat_date' => now(),
            'stat_week' => $today_week,
            'stat_day' => $today_day,
            'stat_distance' => $rec_distance,
            'stat_time' => $rec_time,
            'stat_avg_speed' => $rec_avg_speed,
            'stat_max_speed' => $rec_max_speed,
            'stat_count' => 1,
            'stat_year' => $today_year,
            'created_at' => now(),
        ]);
    }

    // 통계 업데이트
    public function updateStat(
        int $rec_user_id
    )
    {
        $checkDate = date("Y-m-d");
        $today_year = date('Y');
        $userRecord = Record::where('rec_user_id', $rec_user_id)
            ->where('created_at', 'like', '%' . $checkDate . '%')
            ->get();

        // 횟수
        $userAllCount = $userRecord->count();

        // 거리
        $userAllDistance = array_column($userRecord->toArray(), 'rec_distance');
        // 선택한 날 거리 총 합계
        $userAllDistanceSum = array_sum($userAllDistance);

        // 시간
        $userAllTime = array_column($userRecord->toArray(), 'rec_time');
        // 선택한 날 기록 시간 총 합계
        $userAllTimeSum = array_sum($userAllTime);

        // 평균 속도
        $userAllAvgSpeed = array_column($userRecord->toArray(), 'rec_avg_speed');
        // 선택한 날 기록 평균 속도 총 합계
        $userAllAvgSpeedSum = array_sum($userAllAvgSpeed);

        // 나의 모든 기록 평균
        $userAllAvgSpeedAvg = (int)round(($userAllAvgSpeedSum / $userAllCount)); // 반환할 값

        $userRecord = Record::where('rec_user_id', $rec_user_id)
            ->where('created_at', 'like', '%' . $checkDate . '%')
            ->orderBy('rec_max_speed','DESC')
            ->first();


        Stats::where('stat_user_id',$rec_user_id)
            ->where('stat_date', 'like', '%' . $checkDate . '%')
            ->update([
                'stat_distance' => $userAllDistanceSum,
                'stat_time' => $userAllTimeSum,
                'stat_avg_speed' => $userAllAvgSpeedAvg,
                'stat_max_speed' => $userRecord->rec_max_speed,
                'stat_count' => $userAllCount,
                'stat_year' => $today_year,
                'updated_at' => now(),
            ]);
    }
}
