<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\User;
use App\Stats;


class UserController extends Controller
{
    private $user;
    private $stats;

    // 모델 객체 생성
    public function __construct()
    {
        $this->user = new User();
        $this->stats = new Stats();
    }

    // 대시보드 페이지 출력
    public function dashboard()
    {
        // TODO : 토큰으로 사용자 정보 가져오기
        $user_id = $this->TEST_USER_ID;

//        // <<-- 프로필 시작
//        $users = User::all();
//        // 사용자 프로필 사진
//        $user_profile = $users->find($user_id)->user_picture;
//        //return response()->json($user_profile);
//
//        // 사용자 이름
//        $user_name = $users->find($user_id)->user_nickname;
//        //return response()->json($user_name);
//
//        // 사용자 라이딩 점수
//        $user_score = $users->find($user_id)->user_score_of_riding;
//        //return response()->json($user_score);

        // 사용자 총 라이딩 횟수
//        $user_stats = Stats::all()->where('stat_user_id', '=', $user_id);
//        $user_riding_count = $user_stats->count();
        //return response()->json($user_riding_count);

        // <<-- 사용자 정보 : 사진, 이름, 라이딩 점수, 총 라이딩 횟수, 최근 라이딩
        $user_info = $this->user->getDashboardUserInfo($user_id)->first();
        // TODO 사용자 이미지 가져오기!!!
        $user_img = $user_info['picture'];
        // 사용자 정보 -->>

        // <<-- 통계 : 올해 합계, 이번주 합계, 월 ~ 일 통계(거리, 시간, 평균속도)
        // 현재 연도 및 주차 계산
        // TODO 날짜 테스트 용 -> 현재 날짜로 변경
        $today_date = date("2020-10-27");
        $today_year = date("Y", strtotime($today_date));
        $today_week = date("W", strtotime($today_date));

        $user_stats = $this->stats->getDashboardStats($user_id, $today_year, $today_week);

        // 통계 -->>


        // <<-- 알림 : 읽지 않은 알림-->>


        // 사용자 최근 라이딩 날짜
        $user_date = DB::table('stats')->select('stat_date')->where('stat_user_id', '=', $user_id);
        $user_latest_riding_date = $user_date->orderByDesc('stat_date')->take(1)->get();
        //return response()->json($user_latest_riding_date);
        // 프로필 끝 -->>

        // <<-- 라이딩 통계 시작
        $stats = Stats::all();

        // 현재 연도 구하기
        $today_year = date("Y");
        // 현재 주차 구하기
        $today_week = date("Y-m-d");
        $today_week_count = date("W", strtotime($today_week));

        // 이번주 거리 통계 구하기
        $stats_distance = DB::table('stats')
            ->where('stat_user_id', '=', $user_id)
            ->where('stat_year', '=', $today_year)
            ->where('stat_week', '=', $today_week_count)
            ->sum('stat_distance');

        // 이번주 시간 통계 구하기
        $stats_time = DB::table('stats')
            ->where('stat_user_id', '=', $user_id)
            ->where('stat_year', '=', $today_year)
            ->where('stat_week', '=', $today_week_count)
            ->sum('stat_time');

        // 이번주 평균 속도 통계 구하기


        return response()->json([
            'message' => '사용자 정보',
            'data' => [
                'user' => [
                    'info' => $user_info,
                    'img' => $user_img
                ],
                'stats' => $user_stats,
                'notifications' => [

                ]
            ]
        ], 200);
        // <<-- 라이딩 요약 시작
        // <<-- 라이딩 알림 시작

    }
}
