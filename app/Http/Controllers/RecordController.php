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
    private const SELECT_BY_DAY_DETAIL_SUCCESS = '라이딩 일지 상세 정보 조회를 성공하였습니다.';
    private const SELECT_BY_DAY_SUCCESS = '기록 조회를 성공하였습니다.';
    private const SELECT_BY_WEEK_SUCCESS = '주차 통계 조회를 성공하였습니다.';

    public function __construct()
    {
        $this->stats  = new Stats();
        $this->record = new Record();
    }

    // 연도별 라이딩 통계 (WEB)
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
        $requestedData = $request->validate([
            'stat_year' => 'required | numeric | min:' . $min_year . '|max:' . $max_year,
        ]);

        // 사용자가 요청한 정보
        $requested_year = $requestedData['stat_year'];

        // 해당 연도의 라이딩 통계 조회
        $record_stats_by_year = $this->stats
            ->select_stats($user_id, $requested_year);
        $temp_stats = $record_stats_by_year->groupBy('week')->toArray();

        // 현재 날짜의 주차
        $today_week = date('W', strtotime($today_date));
        // 현재 날짜의 요일
//        $day_array = [0 => 6, 1 => 0, 2 => 1, 3 => 2, 4 => 3, 5 => 4, 6 => 5];
        $temp_day = date('w', strtotime($today_date));
        $today_day = $temp_day === 0 ? 6 : $temp_day - 1;

        // 현재 주차의 시작일 (월요일 기준)
        $today_start_date = date('Y-m-d', strtotime($today_date . "-" . $today_day . "days"));

        $resultData = [];
        foreach ($temp_stats as $week => $values) {
            $date_difference = ($today_week - $week + 1) * 7;
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
        $today_year = date('Y');
        // 요청받은 연도의 유효 범위
        $min_year = (int)$today_year - 3;
        $max_year = (int)$today_year;

        // TODO 올해 주차 범위 함수화
        $requestedData = $request->validate([
            'year' => 'required | numeric | min: ' . $min_year . '|max: ' . $max_year,
            'week' => 'required | numeric | min:0 | max:53'
        ]);

        $year = $requestedData['year'];
        $week = $requestedData['week'];

        // 해당 연도, 주차의 시작일, 종료일 조회
        // 현재 날짜의 주차
        $today_date = date('Y-m-d');
//        $today_date = '2021-02-14';
        $today_week = date('W', strtotime($today_date));
        // 현재 날짜의 요일
        $temp_day = date('w', strtotime($today_date));
        $today_day = $temp_day === 0 ? 6 : $temp_day - 1;

        // 현재 주차의 시작일
        $today_start_date = date('Y-m-d', strtotime($today_date . "-" . $today_day . "days"));

        $date_difference = $today_week - $week;
        // 작년일 경우 = $date_difference 값이 음수일 경우
        if ($date_difference < 0) {
            $today_year -= 1;
            // 2. 작년 12월 31일이 몇주차인지 계산한다.
            $last_year_week = date('W', strtotime($today_year . "-12-31"));
            // 3. 현재 주차 + (마지막주차 - 구하고자 하는 주차)
            $date_difference = ($today_week + ($last_year_week - $week)) * 7;
        } else {
            // 올해일 경우
            $date_difference *= 7;
        }

        // 요청받은 주차의 시작일
        $start_date_requested = date('Y-m-d', (strtotime($today_start_date . "-" . $date_difference . "days")));
        // 요청받은 주차의 마지막일
        $end_date_requested = date('Y-m-d', strtotime($start_date_requested . "+6days"));

        // TODO 사용자 토큰 정보 가져오기
        $user_id = $this->TEST_USER_ID;

        // 연도 + 주차에 해당하는 레코드 조회
        $stats_by_year_week = $this->stats->get_stats_by_week($user_id, $year, $week);

        // Records 조회 -> 시작일, 종료일 범위 내 존재하는 필드 조회
        $records_of_week = $this->record->getRecordsByWeek($user_id, $start_date_requested, $end_date_requested);

        $result = [
            'stats' => [
                'startDate' => $start_date_requested,
                'endDate' => $end_date_requested,
                'values' => $stats_by_year_week,
                'records' => $records_of_week
            ]
        ];

        return $this->responseJson(
            "${year}년 ${week}주차 라이딩 통계 조회를 성공하였습니다.",
            $result,
            201);
    }

    // 라이딩 일지 일별 상세 조회
    public function recordDetailView($record_id)
    {
        $user_id = $this->TEST_USER_ID;
        $record_of_date = $this->record->getRecordOfDay($user_id, $record_id);

        $result = [
            'records' => $record_of_date,
            'path' => []
        ];

        return $this->responseJson(
            self::SELECT_BY_DAY_DETAIL_SUCCESS,
            $result,
            201);
    }

    // [app] 홈 화면 - 연, 월, 일 요청 후 해당 통계 반환
    public function recordOfHome(Request $request)
    {
        // TODO 사용자 토큰 가져오기
        $user_id = $this->TEST_USER_ID;
        // 요청받은 정보 유효성 검사
        $requested_data = $request->validate([
            'year' => 'required | numeric',
            'month' => 'required | numeric | min:0 | max: 12',
            'day' => 'required | numeric | min:0 | max:31'
        ]);

        $year = $requested_data['year'];
        $month = $requested_data['month'];
        $day = $requested_data['day'];

        $resultData = $this->record->select_records_of_day($user_id, $year, $month, $day)->first();
        $date = $resultData['date'];

        return $this->responseJson("${date} " . self::SELECT_BY_DAY_SUCCESS, $resultData, 201);
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
        $rec_user_id = $request->rec_user_id;
        $rec_route_id = $request->rec_route_id;

        $rec_title = $request->input('rec_title');
        $rec_distance = $request->input('rec_distance');
        $rec_time = $request->input('rec_time');
        $rec_score = $request->input('rec_score');
        $rec_start_point_address = $request->input('rec_start_point_address');
        $rec_end_point_address = $request->input('rec_end_point_address');
        $rec_avg_speed = $request->input('rec_avg_speed');
        $rec_max_speed = $request->input('rec_max_speed');

        // 경로 저장
        $this->record->createRecord(
            $rec_user_id, $rec_route_id, $rec_title,
            $rec_distance, $rec_time, $rec_score,
            $rec_start_point_address, $rec_end_point_address,
            $rec_avg_speed, $rec_max_speed
        );

        return $this->responseJson(
            "경로 저장 성공",
            [],
            201
        );
    }


    public function recordSort()
    {

    }
}
