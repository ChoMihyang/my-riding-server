<?php

namespace App\Http\Controllers;

use App\Record;
use App\Route;
use App\Stats;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class RecordController extends Controller
{
    private $stats;
    private $record;
    private $route;
    private const SELECT_BY_YEAR_SUCCESS = '년도 통계 조회를 성공하였습니다.';
    private const SELECT_BY_DAY_DETAIL_SUCCESS = '라이딩 일지 상세 정보 조회를 성공하였습니다.';
    private const SELECT_BY_DAY_SUCCESS = '홈 기록 조회를 성공하였습니다.';
    private const SAVE_RECORD_SUCCESS = '경로 저장에 성공했습니다';
    private const SAVE_RECORD_FAIL = '경로 저장에 실패했습니다.';

    public function __construct()
    {
        $this->stats = new Stats();
        $this->record = new Record();
        $this->route = new Route();
    }

    // [web] 연도별 라이딩 통계
    public function recordViewByYear(Request $request)
    {
        // 토큰으로 사용자 정보 가져오기
        $user_id = Auth::guard('api')->user()->getAttribute('id');

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

        // 사용자가 요청한 연도
        $requested_year = $requestedData['stat_year'];

        // 현재 날짜의 주차
        $today_week = date('W', strtotime($today_date));

        // 해당 연도의 라이딩 통계 조회
        $record_stats_by_year = $this->stats
            ->get_stats_by_year($user_id, $requested_year);
        $temp_stats = $record_stats_by_year->groupBy('week')->toArray();

        // 현재 날짜의 요일
        $temp_day = date('w', strtotime($today_date));
        $today_day = $temp_day === 0 ? 6 : $temp_day - 1;

        // 현재 주차의 시작일 (월요일 기준)
        $today_start_date = date('Y-m-d', strtotime($today_date . "-" . $today_day . "days"));

        $resultData = [];
        foreach ($temp_stats as $week => $values) {
            $date_difference = ($today_week - $week) * 7;
            $start_date_requested = date('Y-m-d', (strtotime($today_start_date . "-" . $date_difference . "days")));
            $end_date_requested = date('Y-m-d', strtotime($start_date_requested . "+6days"));

            $resultData[] = [
                'week' => $week,
                'startDate' => $start_date_requested,
                'endDate' => $end_date_requested,
                'values' => $values
            ];
        }

        return $this->responseJson(
            "${requested_year}" . self::SELECT_BY_YEAR_SUCCESS,
            ['stats' => $resultData],
            200
        );
    }

    // 주차별 라이딩 통계
    public function recordViewByWeek(Record $request)
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

        // 사용자 토큰 정보 가져오기
        $user_id = Auth::guard('api')->user()->getAttribute('id');

        // 연도 + 주차에 해당하는 레코드 조회
        $stats_by_year_week = $this->stats->get_stats_by_week($user_id, $year, $week);

        // Records 조회 -> 시작일, 종료일 범위 내 존재하는 필드 조회
        $records_of_week = $this->record->getRecordsByWeek($user_id, $start_date_requested, $end_date_requested);

        $result = [
            'stat' => [
                'startDate' => $start_date_requested,
                'endDate' => $end_date_requested,
                'values' => $stats_by_year_week,
            ],
            'records' => $records_of_week
        ];

        return $this->responseJson(
            "${year}년 ${week}주차 라이딩 통계 조회를 성공하였습니다.",
            $result,
            200
        );
    }

    // 라이딩 일지 일별 상세 조회
    public function recordDetailView(Record $record)
    {
        // 특정 날짜의 기록 레코드의 번호 요청 받기
        $record_id = $record['id'];

        // 사용자 토큰 가져오기
        $user_id = Auth::guard('api')->user()->getAttribute('id');
        $record_of_date = $this->record->getRecordOfDay($user_id, $record_id);

        $result = [
            'records' => $record_of_date,
            'path' => []
        ];

        return $this->responseJson(
            self::SELECT_BY_DAY_DETAIL_SUCCESS,
            $result,
            200
        );
    }

    //

    // [app] 홈 화면 - 연, 월, 일 요청 후 해당 기록 반환
    public function recordOfHome(Request $request)
    {
        // 사용자 토큰 정보 가져오기
        $user_id = Auth::guard('api')->user()->getAttribute('id');
        // 요청받은 정보 유효성 검사
        $requested_data = $request->validate([
            'year' => 'required | numeric',
            'month' => 'required | numeric | min:0 | max: 12',
            'day' => 'required | numeric | min:0 | max:31'
        ]);

        $year = $requested_data['year'];
        $month = $requested_data['month'];
        $day = $requested_data['day'];

        $resultData = $this->record->select_records_of_day($user_id, $year, $month, $day);

        return $this->responseAppJson(
            self::SELECT_BY_DAY_SUCCESS,
            "userRecord",
            $resultData,
            201);
    }

    /**
     * 기록 저장
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function recordSave(Request $request): JsonResponse
    {
        $user = Auth::guard('api')->user();
        // 유저 아이디 값
        $rec_user_id = $user->getAttribute('id');

        $validator = Validator::make($request->all(), [
            'rec_title' => 'required|string|min:3|max:25|regex:/^[\w\Wㄱ-ㅎㅏ-ㅣ가-힣]{5,15}$/|unique:records'
        ], [
            'rec_title.regex' => '경로 제목을 다시 입력해주세요.',
        ]);

        if ($validator->fails()) {
            $response_data = [
                'error' => $validator->errors(),
            ];

            return $this->responseJson(
                self::SAVE_RECORD_FAIL,
                $response_data,
                422
            );
        }

        // 경로 정보 있을 경우 가져오기
        $rec_route_id = $request->rec_route_id;

        $rec_title = $request->input('rec_title');
        $rec_distance = $request->input('rec_distance');
        $rec_time = $request->input('rec_time');
        $rec_score = 0;
        $rec_start_point_address = $request->input('rec_start_point_address');
        $rec_end_point_address = $request->input('rec_end_point_address');
        $rec_avg_speed = $request->input('rec_avg_speed');
        $rec_max_speed = $request->input('rec_max_speed');

        // 1. mysql 에 기록 저장
        $this->record->createRecord(
            $rec_user_id, $rec_route_id, $rec_title,
            $rec_distance, $rec_time, $rec_score,
            $rec_start_point_address, $rec_end_point_address,
            $rec_avg_speed, $rec_max_speed
        );

        // 2. 경로 ID 값으로 몽고에 기록, 경로 데이터 저장
        // 기록 저장 성공 여부 체크
        $recordSavePoints = $this->record->RecordSaveCheck($rec_route_id, $rec_title);
        // 가장 최근 기록의 ID 값 가져와 몽고의 drivingId로 넘겨줌
        $saveRecordId = $recordSavePoints[0]['id'];

        // 3. 몽고에 데이터 저장 후 값 뽑기
        // 몽고에 기록 데이터 저장 완료, 조회 완료
        $saveRecordMongo = $this->testSave($request, $saveRecordId);
        // 몽고에 경로 데이터 저장 완료, 조회 완료
        $savaRouteMongo = $this->myTestSave($request, $saveRecordId);

        // 주차, 요일 계산
        // 현재 연도
        $today_year = date('Y');
        // 현재 날짜의 주차
        $today_date = date('Y-m-d');
        $today_week = date('W', strtotime($today_date));
        // 현재 날짜의 요일
        $temp_day = date('w', strtotime($today_date));
        $today_day = $temp_day === 0 ? 6 : $temp_day - 1;


        if (!$recordSavePoints) {
            // TODO 기록 저장 실패한 경우?
            // app 문의?, 데이터 전송 실패?  (mysql, mongoDB)
            // msg 전송
            return $this->responseJson(
                self::SAVE_RECORD_FAIL,
                [],
                422
            );
        }

        // 기록 저장 성공한 경우
        $year = date("Y-m-d");

        // -> stats 테이블 select : 오늘 날짜의 기록 존재 여부 체크
        $statCheck = Stats::where('stat_user_id', $rec_user_id)
            ->where('stat_date', $year)
            ->first();

        if ($statCheck) {
            // -> 통계 존재하는 경우 : 기존 통계에서 update
            // TODO 수정할 부분 점수 계산
            $statResult = $this->stats->updateStat($rec_user_id);
        } else {
            // -> 통계 존재하지 않는 경우 : 통계 create
            $statResult = $this->stats->createStats(
                $rec_user_id, $today_week, $today_day,
                $rec_distance, $rec_time, $rec_avg_speed, $rec_max_speed, $today_year
            );
        }

//        if ($rec_route_id) {
//            // 만들어진 경로로 주행 한 경우에만
//            $this->tryCount($rec_route_id);
//        }
//
//        // 통계 레코드 생성을 성공한 경우 배지 달성 여부 판단
//        // 배지 타입 : 100 - 거리, 200 - 시간, 300 - 최고 속도, 400 - 점수, 500 - 랭킹, 600 - 연속
//
//        // TODO 달성 여부 판단 메서드 BadgeController 이동
//        if ($statResult) {
//            // 1. 거리 배지
//            // 통계 테이블 조회 -> 누적 거리 비교 (기준 - 30m / 50m / 100m)
//            $user_info = $this->stats->select_stats_badge($rec_user_id);
//
//            $sum_of_distance = $user_info->sum('distance');
//
//            $badge_msg = "";
//            if ($sum_of_distance >= 300) {
//                $badge_msg = "300km";
//            } elseif ($sum_of_distance >= 150) {
//                $badge_msg = "150km";
//            } elseif ($sum_of_distance >= 100) {
//                $badge_msg = "100km";
//            } elseif ($sum_of_distance >= 50) {
//                $badge_msg = "50km";
//            } elseif ($sum_of_distance >= 30) {
//                $badge_msg = "30km";
//            }
//            $badge_msg .= "달성";
//
//            // 위 기준과 같거나 넘을 경우
//            // -> badge Table 내 '배지 달성한 사용자 id', '배지 타입(100)', '달성 메시지("누적 거리 30m / 50m / 100m ... 달성")', '달성 날짜' 삽입
//
//
//            $sum_of_time = $user_info->sum('time');
//            $max_of_speed = $user_info->max('max_speed');
//        }


        return $this->responseJson(
            self::SAVE_RECORD_SUCCESS,
            [],
            201
        );
    }

    /**
     * routes - record 의 시도 횟수 맞추기
     *
     * @param int $rec_route_id
     */
    public function tryCount(
        int $rec_route_id
    )
    {
        // 만들어진 경로로 주행 한 경우에만 실행됨
        // 1. route_num_of_try_count 연산
        // -> record 테이블에서 rec_route_id 카운트 하기
        $this->route->tryCountCheck($rec_route_id);

        // 2. route_num_of_try_user 연산
        // -> record 테이블에서 rec_route_id 카운트 하기, rec_user_id 와 rec_route_id 가 중복되는 경우 제외
        $this->route->tryUserCheck($rec_route_id);
    }


    // TODO 몽고 연동 테스트 완료..
    // 라이딩 기록 몽고로 보내기
    public function testSave(Request $request, int $saveRecordId)
    {
        $response_data = $request->input('records');

        // TODO 기록 아이디로 바꾸기

        $response = \Illuminate\Support\Facades\Http::post("http://13.209.75.193:3000/api/record/$saveRecordId", [
            "records" => $response_data
        ]);

        return $response->json();
    }

    // 라이딩 기록 몽고에서 조회
    public function testShow(Request $request)
    {
        $record_id = $request->id;
        // TODO 기록 아이디로 바꾸기

        $response = \Illuminate\Support\Facades\Http::get("http://13.209.75.193:3000/api/record/$record_id");

        return $response->json();
    }

    // 라이딩 기록 삭제
    public function testDelete()
    {
        // TODO 기록 아이디로 바꾸기

        $response = \Illuminate\Support\Facades\Http::delete("http://13.209.75.193:3000/api/record/1");

        return $response->json();
    }

    // 경로 정보 저장
    public function myTestSave(Request $request, int $saveRecordId)
    {

        $response_data = $request->input('points');

        // TODO 기록 아이디로 바꾸기

        $response = \Illuminate\Support\Facades\Http::post("http://13.209.75.193:3000/api/route/$saveRecordId", [
            "points" => $response_data
        ]);

        return $response->json();
    }

//    // 경로 정보 조회
//    public function myTestShow(Request $request)
//    {
//        $route_id = $request->id;
//        // TODO 기록 아이디로 바꾸기
//
//        $response = \Illuminate\Support\Facades\Http::get("http://13.209.75.193:3000/api/route/$route_id");
//
//        return $response->json();
//    }

    // 경로 정보 삭제
    public function myTestDelete()
    {
        // TODO 기록 아이디로 바꾸기

        $response = \Illuminate\Support\Facades\Http::delete("http://13.209.75.193:3000/api/route/1");

        return $response->json();
    }
}
