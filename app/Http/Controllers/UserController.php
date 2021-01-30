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
        $user_info = $this->user->getDashboardUserInfo($user_id)->first();
        // TODO 사용자 이미지 가져오기
        $user_img = $user_info['picture'];
        // 사용자 정보 -->>

        // <<-- 통계 : 올해 합계, 이번주 합계, 월 ~ 일 통계(거리, 시간, 평균속도)
        // 현재 연도 및 주차 계산
        // TODO 날짜 테스트 용 -> 현재 날짜로 변경 / 날짜 함수 리펙토링
        $today_date = date('Y-m-d');
        $today_year = date("Y", strtotime($today_date));
        $today_month = date("m", strtotime($today_date));
        $today_day = date("d", strtotime($today_date));

        $make_date = $today_year . "-" . $today_month . "-" . $today_day;
        $today_week = date('w', strtotime($make_date)); // 현재 요일
        // 해당 주의 시작일
        $start_date = date('Y-m-d', strtotime($today_date . " -" . $today_week . "days"));
        // 해당 주의 마지막일
        $end_date = date('Y-m-d', strtotime($start_date . '+6days'));
        $user_stats = $this->stats->getDashboardStats($user_id, 2020, 1);

        // 통계 -->>


        // <<-- 알림 : 읽지 않은 알림-->>
        $user_noti = $this->notifications->getDashboardNoti($user_id);

        return response()->json([
            'message' => '사용자 정보',
            'data' => [
                'user' => [
                    'info' => $user_info,
                    'img' => $user_img
                ],
                'stats' => [
                    'start_date' => $start_date,
                    'end_date' => $end_date,
                    'values' => $user_stats,
                ],
                'notifications' => [
                    $user_noti
                ]
            ]
        ], 200);
    }
}
