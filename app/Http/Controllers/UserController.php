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
    private const PRINT_USER_RANK_SUCCESS = "사용자 랭킹 조회를 성공하였습니다.";

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
        // TODO 사용자 이미지 CONCAT 하기
        // TODO last_riding 포맷 변경
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

        // TODO 알림 페이지 URL 전송 API
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
            201
        );
    }

    // 알림 확인 버튼 클릭 시
    //1. noti_check 필드 값 -> 0으로 업데이트
    //2.updated_at 필드 값-> 확인 날짜 데이터 삽입
    public function notificationCheck(Notification $notification)
    {
        $user_id = Auth::guard('api')->user()->getAttribute('id');

        $this->notifications->checkNotification($user_id, $notification);
    }

    // 전체 랭킹 출력
    public function viewUserRank()
    {
        $rank_of_all_users = $this->user->getUserRank();

        return $this->responseAppJson(
            self::PRINT_USER_RANK_SUCCESS,
            "ranks",
            $rank_of_all_users,
            200
        );
    }

    // 사용자 랭킹 상세 보기
    // 요청하는 값 -> ? 랭킹 번호 + 사용자 닉네임 ??
    // 현재 상태 : 요청한 사용자 id값 + 닉네임
    public function viewDetailRank(User $name)
    {
//        $rank_user_name = $request['name'];
        dd($name['name']);
        // 요청한 사용자의 id 값과 닉네임으로 정보 조회
        $info_of_user = $this->stats->getUserDetailRank($rank_user_name);

        // 반환할 데이터 계산
        $picture = $info_of_user->pluck('picture')->first();
        $score = $info_of_user->pluck('score')->first();
        $sum_of_time = $info_of_user->sum('time');
        $sum_of_distance = $info_of_user->sum('distance');
        $avg_of_speed = $info_of_user->avg('avg_speed');
        $max_of_speed = $info_of_user->max('max_speed');

        // 반환할 데이터 배열 저장
        $result_data = [
            "nickname" => $rank_user_name,
            "picture" => $picture,
            "score" => $score,
            "sum_of_time" => $sum_of_time,
            "sum_of_distance" => $sum_of_distance,
            "avg_of_speed" => $avg_of_speed,
            "max_of_speed" => $max_of_speed
        ];

        return $this->responseAppJson(
            self::PRINT_USER_RANK_SUCCESS,
            "ranks",
            $result_data,
            200
        );
    }
}
