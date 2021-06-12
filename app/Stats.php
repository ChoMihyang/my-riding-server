<?php

namespace App;

use DateTimeInterface;
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
            ->orderBy('stat_date')
            ->get();

        return $returnData;
    }


    /**
     * @param int $year
     * @param int $user_id
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
            ->orderBy('stat_date')
            ->get();

        return $returnData;
    }


    /**
     * 선택 연도와 주차에 해당하는 통계 조회
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
     * 사용자 랭킹 상세보기
     * @param int $rank_id
     * @return Collection
     */
    public function getUserDetailStats(int $rank_id)
    {
        $param = [
            'stat_distance as distance',
            'stat_time as time',
            'stats.stat_avg_speed as avg_speed',
            'stats.stat_max_speed as max_speed'
        ];

        $returnData = self::select($param)
            ->where('stat_user_id', $rank_id)
            ->get();

        return $returnData;
    }

    // 주차별 시작일과 마지막일
    public function get_start_end_date_of_week(int $year, int $week)
    {
        // 현재 날짜
        $today_date = date('Y-m-d');
        // 현재 연도
        $today_year = date('Y', strtotime($today_date));
        // 올해의 경우
        if ($year == $today_year) {
            // 현재 날짜의 요일
            $today_day = date('w', strtotime($today_date));
            // 현재 날짜의 주차
            $today_week = date('W', strtotime($today_date));
            // 현재 주차의 시작일
            $today_start_date = date('Y-m-d', strtotime($today_date . "-" . $today_day . "days + 1day"));
            // 현재 주차 - 요청 주차의 날짜 차이
            $week_difference = ($today_week - $week) * 7;
            // 현재 날짜로부터 n일 전으로 이동
            $start_of_requestedDate = date('Y-m-d', strtotime($today_start_date . "-" . $week_difference . "days"));
            $end_of_requestedDate = date('Y-m-d', strtotime($start_of_requestedDate . "+6days"));
        } else {
            // 이전 연도의 경우
            $last_date = $year . "-12-31";
            // YYYY-12-31 의 주차
            do {
                $last_date_week = date('W', strtotime($last_date));
                // 말일이 01주차로 넘어가는 경우 1주일 전의 날짜로 계산
                if ($last_date_week === '01') $last_date = $year . "-12-24";
                else                          break;
            } while (true);

            // YYYY-12-31 의 요일
            // 요일이 0(일요일)일 경우 웹의 월요일 ~ 일요일 기준 출력을 고려하여 7로 변경
            $last_day = date('w', strtotime($last_date));
            if ($last_day == 0) $last_day = 7;
            // YYYY-12-31 의 주차의 시작일
            $last_start_date = date('Y-m-d', strtotime($last_date . "-" . $last_day . "days + 1day"));
            // YYYY-12-31 로부터 요청 주차의 날짜 차이
            $last_week_difference = ($last_date_week - $week) * 7;
            // YYYY-12-31 로부터 n일 전으로 이동
            $start_of_requestedDate = date('Y-m-d', strtotime($last_start_date . "-" . $last_week_difference . "days"));
            $end_of_requestedDate = date('Y-m-d', strtotime($start_of_requestedDate . "+6days"));
        }

        return [$start_of_requestedDate, $end_of_requestedDate];
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
        string $today_year
    )
    {
        Stats::create([
            'stat_user_id' => $rec_user_id,
            'stat_date' => date("Y-m-d"),
            'stat_week' => $today_week,
            'stat_day' => $today_day,
            'stat_distance' => $rec_distance,
            'stat_time' => $rec_time,
            'stat_avg_speed' => $rec_avg_speed,
            'stat_max_speed' => $rec_max_speed,
            'stat_count' => 1,
            'stat_year' => $today_year,
            'created_at' => now('Asia/Seoul'),
            'updated_at' => now('Asia/Seoul')
        ]);

        return true;
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
            ->orderBy('rec_max_speed', 'DESC')
            ->first();


        Stats::where('stat_user_id', $rec_user_id)
            ->where('stat_date', 'like', $checkDate)
            ->update([
                'stat_distance' => $userAllDistanceSum,
                'stat_time' => $userAllTimeSum,
                'stat_avg_speed' => $userAllAvgSpeedAvg,
                'stat_max_speed' => $userRecord->rec_max_speed,
                'stat_count' => $userAllCount,
                'stat_year' => $today_year,
                'updated_at' => now('Asia/Seoul'),
            ]);

        return true;
    }

    public function select_profile_stat(
        int $user_id,
        string $start_date_range,
        string $last_date_range
    )
    {
        $param = [
            'stat_year as year',
            'stat_week as week',
            DB::raw('sum(stat_distance) as distance'),
            DB::raw('sum(stat_time) as time'),
            DB::raw('avg(stat_avg_speed) as avg_speed'),
            DB::raw('max(stat_max_speed) as max_speed')
        ];

        $returnData = Stats::select($param)
            ->groupBy('stat_year')
            ->groupBy('stat_week')
            ->where('stat_user_id', $user_id)
            ->whereBetween('stat_date', [$start_date_range, $last_date_range])
            ->orderByDesc('stat_year')
            ->orderByDesc('stat_week')
            ->get();

        return $returnData;
    }

    /**
     * @param int $user_id
     * @return Collection
     */
    public
    function select_stats_badge(
        int $user_id
    ): Collection
    {
        $param = [
            'stat_distance as distance',
            'stat_time as time',
            'stat_max_speed as max_speed',
            'stat_week as week'
        ];
        $returnData = self::select($param)
            ->where('stat_user_id', $user_id)
            ->get();

        return $returnData;
    }

    protected
    function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    // stat_distance 합계
    public function sumDistance(
        int $user_id
    )
    {
        return self::where('stat_user_id', $user_id)->
        sum('stat_distance');
    }

    // stat_time 합계
    public function sumTime(
        int $user_id
    )
    {
        return self::where('stat_user_id', $user_id)->
        sum('stat_time');
    }

    // sumAvgSpeed 평균값
    public function sumAvgSpeed(
        int $user_id
    )
    {
        $sumOfAvg = self::where('stat_user_id', $user_id)->
        sum('stat_avg_speed');

        $count = self::where('stat_user_id', $user_id)->
        get()->
        count();

        return $sumOfAvg / $count;
    }

    // sumMaxSpeed 최대값
    public function sumMaxSpeed(
        int $user_id
    )
    {
        return self::select('stat_max_speed')->
        where('stat_user_id', $user_id)->
        orderBy('stat_max_speed', 'DESC')->
        get()->
        take(1);
    }

}
