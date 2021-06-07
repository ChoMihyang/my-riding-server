<?php

namespace App\Http\Controllers;

use App\Notification;
use App\Record;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\User;
use App\Stats;


class UserController extends Controller
{
    private $user;
    private $stats;
    private $record;
    private $notifications;

    private const PRINT_USER_PROFILE_SUCCESS = "사용자 정보, 통계, 알림 조회를 성공하였습니다.";
    private const PRINT_STATS_OF_ANOTHER_WEEK_SUCCESS = "통계 정보 조회를 성공하였습니다.";
    private const PRINT_USER_RANK_SUCCESS = "사용자 랭킹 조회를 성공하였습니다.";
    private const PRINT_USER_RANK_DETAIL_SUCCESS = "사용자 상세 랭킹 조회를 성공하였습니다.";
    private const PRINT_NOTIFICATION_CHECK_SUCCESS = "대시보드 알림 확인을 성공하였습니다.";

    // 모델 객체 생성
    public function __construct()
    {
        $this->user = new User();
        $this->stats = new Stats();
        $this->record = new Record();
        $this->notifications = new Notification();
    }

    // 대시보드 페이지 출력
    public function dashboard()
    {
        // 사용자 정보 토큰 가져오기
        $user_id = Auth::guard('api')->user()->getAttribute('id');

        // <<-- 사용자 정보 : 사진, 이름, 라이딩 점수, 총 라이딩 횟수, 최근 라이딩
        $user_info = $this->user->getDashboardUserInfo($user_id);
        // 사용자 정보 -->>

        // <<-- 통계 정보 : 올해 합계, 이번주 합계, 월 ~ 일 통계(거리, 시간, 평균속도)
        // 현재 연도 및 주차 계산
        $today_date = date('Y-m-d');

        // 연도, 월, 일 추출
        $today_year = date("Y", strtotime($today_date));
        $today_month = date("m", strtotime($today_date));
        $today_day = date("d", strtotime($today_date));

        $make_date = $today_year . "-" . $today_month . "-" . $today_day;
        $today_week = date('W', strtotime($make_date));             // 현재 주차

        $temp_day = date('w', strtotime($today_date));
        $day_of_week = $temp_day === 0 ? 6 : $temp_day - 1; // 현재 요일

        // 해당 주의 시작일
        $start_date = date('Y-m-d', strtotime($today_date . " -" . $day_of_week . "days"));
        // 해당 주의 마지막일
        $end_date = date('Y-m-d', strtotime($start_date . '+6days'));

        $user_stats = $this->stats->getDashboardStats($user_id, $today_year, $today_week);

        // 올해 누적 통계 (거리, 시간, 평균속도)
        $year_value = $this->record->getTodayYearStats($user_id, $today_year);

        $today_year_distance = $year_value->sum('distance');
        $today_year_time = $year_value->sum('time');
        $today_year_avg_speed = round($year_value->avg('avg_speed'), 1);

        // 통계 -->>

        // <<-- 알림 : 읽지 않은 알림-->>
        $user_noti = $this->notifications->getDashboardNoti($user_id);

        $dateData = [
            'year' => $today_year,
            'week' => $today_week,
            'startDate' => $start_date,
            'endDate' => $end_date,
            'values' => $user_stats
        ];

        $yearData = [
            'distance' => $today_year_distance,
            'time' => $today_year_time,
            'avg_speed' => $today_year_avg_speed
        ];

        $returnData = [
            'user' => $user_info,
            'stat' => $dateData,
            'notifications' => $user_noti,
            'year' => $yearData
        ];

        return $this->responseJson(
            self::PRINT_USER_PROFILE_SUCCESS,
            $returnData,
            200
        );
    }

    // 대시보드 주차 이동 시 통계 가져오기
    public function moveWeekOfStat(Request $request)
    {
        $user_id = Auth::guard('api')->user()->getAttribute('id');
        // 주차 validation
        // TODO 연도 범위?
        $requestedData = $request->validate([
            'year' => 'required | numeric',
            'week' => 'required | numeric | min:0 | max:54'
        ]);

        $year = $requestedData['year'];
        $week = $requestedData['week'];

        // 해당 연도-주차의 통계
        $stat = $this->stats->getDashboardStats($user_id, $year, $week);

        // 해당 연도-주차의 시작일과 마지막일
        $getDate = $this->stats->get_start_end_date_of_week($year, $week);

        $returnData = [
            'year' => $year,
            'week' => $week,
            'startDate' => $getDate[0],
            'endDate' => $getDate[1],
            'values' => $stat
        ];

        return $this->responseJson(
            $year . "년 " . $week . "주차의 " . self::PRINT_STATS_OF_ANOTHER_WEEK_SUCCESS,
            $returnData,
            200);
    }

    // 알림 확인 버튼 클릭 시
    //1. noti_check 필드 값 -> 1으로 업데이트
    //2.updated_at 필드 값-> 확인 날짜 데이터 삽입
    public function notificationCheck(Notification $notification)
    {
//        $noti_id = $notification['id'];
        $user_id = Auth::guard('api')->user()->getAttribute('id');

        $this->notifications->checkNotification($user_id, $notification);

        return $this->responseJson(
            self::PRINT_NOTIFICATION_CHECK_SUCCESS,
            [],
            200
        );
    }

    // 전체 랭킹 출력
    public function viewUserRank()
    {
//        $user = Auth::guard('api')->user();

//        $user_img = $user->getAttribute('user_picture');
//        if ($user_img == "null") {
//            return "null";
//        }
//
//        $loadImg = $this->getBase64Img($user_img);
//        dd($loadImg);
        // 랭킹 10위 조회
        $rank_users = $this->user->getUserRank();

        // id 순회하며 프로필 사진, 닉네임, 점수 획득
        $return = [];
        foreach ($rank_users as $value) {
            $user_id = $value->id;
            $user_nickname = $value->user_nickname;
            $user_score = $value->user_score_of_riding;
            $user_picture = $this->loadImage($user_id);

            $return[] = [
                'id' => $user_id,
                'nickname' => $user_nickname,
                'score' => $user_score,
                'picture' => $user_picture
            ];
        }

        return $this->responseAppJson(
            self::PRINT_USER_RANK_SUCCESS,
            "ranks",
            $return,
            200
        );
    }

    // 사용자 랭킹 상세보기
    public function viewUserDetailRank(User $rank_id)
    {
        // 사용자 id 값
        $rank_id = $rank_id['id'];
        // 해당 사용자의 누적 거리, 시간, 평균 속도, 최고 속도 반환
        $rank_user_stats = $this->stats->getUserDetailStats($rank_id);

        // 반환할 데이터 계산
        $sum_of_time = $rank_user_stats->sum('time');
        $sum_of_distance = $rank_user_stats->sum('distance');
        $avg_of_speed = round($rank_user_stats->avg('avg_speed'), 1);
        $max_of_speed = $rank_user_stats->max('max_speed');

        $result = [
            'time' => $sum_of_time,
            'distance' => $sum_of_distance,
            'avg_speed' => $avg_of_speed,
            'max_speed' => $max_of_speed
        ];

        return $this->responseAppJson(
            self::PRINT_USER_RANK_DETAIL_SUCCESS,
            'stat',
            $result,
            200);
    }
}
