<?php

namespace App\Http\Controllers;

use App\Record;
use App\Stats;
use Illuminate\Http\Request;
use phpDocumentor\Reflection\Types\Nullable;

class RecordController extends Controller
{
    private $stats;
    private $record;
    private const SELECT_BY_YEAR_SUCCESS = '연도 통계 조회를 성공하였습니다.';
    private const SELECT_BY_WEEK_SUCCESS = '주차 통계 조회를 성공하였습니다.';

    public function __construct()
    {
        $this->stats  = new Stats();
        $this->record = new Record();
    }

    // 연도별 라이딩 통계
    public function recordViewByYear(Request $request)
    {
        // TODO 토큰으로 사용자 정보 가져오기
        $user_id = $this->TEST_USER_ID;
        // 현재 날짜
        $today_date = date('Y-m-d');
        // 현재 연도
        $today_year = date('Y');

        // 요청받은 연도의 유효 범위
        $min_year = (int)$today_year - 3;
        $max_year = (int)$today_year;
        // 유효성 검사
        $requestedYear = $request->validate([
            'stat_year' => 'required | numeric | min:' . $min_year . '|max:' . $max_year
        ]);

        // 사용자가 요청한 연도 정보
        $requested_year = (int)$requestedYear['stat_year'];
        // 해당 연도의 라이딩 통계 조회
        $record_stats_by_year = $this->stats->select_stats_by_year($requested_year, $user_id);
        $temp_stats = $record_stats_by_year->groupBy('week')->toArray();

        // 현재 날짜의 주차
        $today_week = date('W', strtotime($today_date));
        // 현재 날짜의 요일
        $day_array = [0 => 6, 1 => 0, 2 => 1, 3 => 2, 4 => 3, 5 => 4, 6 => 5];
        $temp_day = date('w', strtotime($today_date));
        $today_day = $day_array[$temp_day];

        // 현재 주차의 시작일 (월요일 기준)
        $today_start_date = date('Y-m-d', strtotime($today_date . "-" . $today_day . "days"));
        $resultData = [];

        foreach ($temp_stats as $week => $values) {
            $date_difference = ((int)$today_week - (int)$week + 1) * 7;
            $start_date_requested = date('Y-m-d', (strtotime($today_start_date . "-" . $date_difference . "days")));
            $end_date_requested = date('Y-m-d', strtotime($start_date_requested . "+6days"));

            $resultData[$week] = [
                'week' => $week,
                'startDate' => $start_date_requested,
                'endDate' => $end_date_requested,
                'values' => $values
            ];
        }

        return $this->responseJson(
            "${today_year}" . self::SELECT_BY_YEAR_SUCCESS,
            ['stats' => $resultData],
            200
        );
    }

    // 주별 라이딩 통계
    public function recordViewByWeek(Request $request)
    {
        // TODO 사용자 토큰 정보 가져오기
        $user_id = $this->TEST_USER_ID;

        // 특정 연도 내 하나의 주차 통계 조회
        $record_stats_by_week = $this->record->select_stats_by_week($user_id, 2021, 1);

    }

    /**
     * 경로 저장
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function recordSave(Request $request)
    {
        // TODO 사용자 토큰 정보 가져오기
        $rec_user_id  = $request->rec_user_id;
        $rec_route_id = $request->rec_route_id;

        $rec_title               = $request->input('rec_title');
        $rec_distance            = $request->input('rec_distance');
        $rec_time                = $request->input('rec_time');
        $rec_score               = $request->input('rec_score');
        $rec_start_point_address = $request->input('rec_start_point_address');
        $rec_end_point_address   = $request->input('rec_end_point_address');
        $rec_avg_speed           = $request->input('rec_avg_speed');
        $rec_max_speed           = $request->input('rec_max_speed');

        // 경로 저장
        $this->record->createRecord(
            $rec_user_id,$rec_route_id,$rec_title,
            $rec_distance,$rec_time,$rec_score,
            $rec_start_point_address,$rec_end_point_address,
            $rec_avg_speed,$rec_max_speed
        );

        return $this->responseJson(
          "경로 저장 성공",
            [],
            201
        );
    }
}
