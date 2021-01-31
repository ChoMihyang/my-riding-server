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
        // $today_date = date('Y-m-d');
        $today_date = '2020-01-03';

        // 연도, 월, 일 추출
        $today_year = date("Y", strtotime($today_date));
        $today_month = date("m", strtotime($today_date));
        $today_day = date("d", strtotime($today_date));

        $make_date = $today_year . "-" . $today_month . "-" . $today_day;
        $today_week = date('W', strtotime($make_date));             // 현재 주차
        $day_of_week = date('w', strtotime($make_date));            // 현재 요일

        // 해당 주의 시작일
        $start_date = date('Y-m-d', strtotime($today_date . " -" . $day_of_week . "days"));
        // 해당 주의 마지막일
        $end_date = date('Y-m-d', strtotime($start_date . '+6days'));

        $user_stats = $this->stats->getDashboardStats($user_id, $today_year, $today_week);
        // 통계 -->>

        // <<-- 알림 : 읽지 않은 알림-->>
        // TODO created_at 포맷 변경
        $user_noti = $this->notifications->getDashboardNoti($user_id);


        return response()->json([
            'message' => '사용자 정보, 통계, 알림 조회를 성공하였습니다.',
            'data' => [
                'user' => $user_info,
                'stats' => [
                    'year' => $today_year,
                    'week' => $today_week,
                    'start_date' => $start_date,
                    'end_date' => $end_date,
                    'values' => $user_stats,
                ],
                'notifications' => $user_noti
            ]
        ], 200);
    
}
