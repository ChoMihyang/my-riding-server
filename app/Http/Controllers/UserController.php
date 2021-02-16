<?php

namespace App\Http\Controllers;

use App\Notification;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\User;
use App\Stats;


class UserController extends Controller
{
    private $user;
    private $stats;
    private $notifications;

    private const PRINT_USER_PROFILE_SUCCESS = "사용자 정보, 통계, 알림 조회를 성공하였습니다.";
    private const PRINT_USER_RANK = "사용자 랭킹 조회를 성공하였습니다.";

    // 모델 객체 생성
    public function __construct()
    {
        $this->user = new User();
        $this->stats = new Stats();
        $this->notifications = new Notification();
    }

    // 대시보드 페이지 출력
    public function dashboard()
    {
        // TODO : 토큰으로 사용자 정보 가져오기
        $user_id = $this->TEST_USER_ID;

        // <<-- 사용자 정보 : 사진, 이름, 라이딩 점수, 총 라이딩 횟수, 최근 라이딩
        // TODO 사용자 이미지 CONCAT 하기
        // TODO last_riding 포맷 변경
        $user_info = $this->user->getDashboardUserInfo($user_id)->first();
        // 사용자 정보 -->>

        // <<-- 통계 정보 : 올해 합계, 이번주 합계, 월 ~ 일 통계(거리, 시간, 평균속도)
        // TODO 날짜 테스트 용 -> 현재 날짜로 변경
        // 현재 연도 및 주차 계산
//        $today_date = date('Y-m-d');
        $today_date = '2021-02-07';

        // 연도, 월, 일 추출
        $today_year = date("Y", strtotime($today_date));
        $today_month = date("m", strtotime($today_date));
        $today_day = date("d", strtotime($today_date));

        $make_date = $today_year . "-" . $today_month . "-" . $today_day;
        $today_week = date('W', strtotime($make_date));             // 현재 주차

//        $day_array = [0 => 6, 1 => 0, 2 => 1, 3 => 2, 4 => 3, 5 => 4, 6 => 5];
        $temp_day = date('w', strtotime($today_date));
        $day_of_week = $temp_day === 0 ? 6 : $temp_day - 1; // 현재 요일


        // 해당 주의 시작일
        $start_date = date('Y-m-d', strtotime($today_date . " -" . $day_of_week . "days"));

        // 해당 주의 마지막일
        $end_date = date('Y-m-d', strtotime($start_date . '+6days'));

        $user_stats = $this->stats->getDashboardStats($user_id, $today_year, $today_week);

        // 통계 -->>

        // <<-- 알림 : 읽지 않은 알림-->>
        // TODO created_at 포맷 변경
        // TODO 알림 확인 API
        // TODO 알림 페이지 URL 전송 API
        $user_noti = $this->notifications->getDashboardNoti($user_id);

        $dateData = [
            'year' => $today_year,
            'week' => $today_week,
            'startDate' => $start_date,
            'endDate' => $end_date,
            'values' => $user_stats
        ];
        $returnData = [
            'user' => $user_info,
            'stats' => $dateData,
            'notifications' => $user_noti
        ];

        return $this->responseJson(
            self::PRINT_USER_PROFILE_SUCCESS,
            $returnData,
            201);
    }


    // 전체 랭킹 출력
    // TODO id값 넘겨주기
    public function viewUserRank()
    {
        $rank_of_all_users = $this->user->getUserRank();

        return $this->responseJson(
            self::PRINT_USER_RANK,
            $rank_of_all_users,
            201);
    }

    // 사용자 랭킹 상세 보기
    public function viewDetailRank($arg_user_id)
    {
        $rank_of_user = $this->stats->getUserDetailRank($arg_user_id);

        $sum_of_time = $rank_of_user->sum('time');
        $sum_of_distance = $rank_of_user->sum('distance');
        $avg_of_speed = $rank_of_user->avg('avg_speed');
        $max_of_speed = $rank_of_user->max('max_speed');

        $result_data = [
            "sum_of_time" => $sum_of_time,
            "sum_of_distance" => $sum_of_distance,
            "avg_of_speed" => $avg_of_speed,
            "max_of_speed" => $max_of_speed
        ];

        return $this->responseJson(
            self::PRINT_USER_RANK,
            $result_data,
            201
        );
    }
}
