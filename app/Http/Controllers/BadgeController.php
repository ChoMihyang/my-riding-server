<?php

namespace App\Http\Controllers;

use App\Badge;
use App\Stats;
use Illuminate\Support\Facades\Auth;

class BadgeController extends Controller
{
    private $badge;
    private const PRINT_DETAIL_BADGE_SUCCESS = "배지 상세보기 출력을 성공하였습니다.";
    private const SAVE_DISTANCE_VALUE = '거리 뱃지 저장에 성공하였습니다.';
    private const SAVE_TIME_VALUE = '시간 뱃지 저장에 성공하였습니다.';
    private const SAVE_MAX_SPEED_VALUE = '속도 뱃지 저장에 성공하였습니다.';


    public function __construct()
    {
        $this->badge = new Badge();
        $this->state = new Stats();
    }

    // [APP] 배지 상세보기 화면
    public function viewDetailBadge()
    {
        $user_id = Auth::guard('api')->user()->getAttribute('id');

        $user_badge = $this->badge->showBadge($user_id);

        return $this->responseAppJson(
            self::PRINT_DETAIL_BADGE_SUCCESS,
            'badge', $user_badge,
            200
        );
    }


//  <<-- 배지 기록 수치별 코드 상수화
    // 거리
    const _DISTANCE_30 = 101;
    const _DISTANCE_50 = 102;
    const _DISTANCE_100 = 103;
    const _DISTANCE_150 = 104;
    const _DISTANCE_300 = 105;
    // 시간
    const _TIME_5 = 201;
    const _TIME_10 = 202;
    const _TIME_20 = 203;
    const _TIME_30 = 204;
    const _TIME_50 = 205;
    // 속도
    const _MAXSPEED_15 = 301;
    const _MAXSPEED_20 = 302;
    const _MAXSPEED_25 = 303;
    const _MAXSPEED_30 = 304;
    // 점수
    const _SCORE_1000 = 401;
    const _SCORE_5000 = 402;
    const _SCORE_10000 = 403;
    const _SCORE_50000 = 404;
    const _SCORE_100000 = 405;
    // -->>

    // 배지 메시지
    public function badgeMsg($value)
    {
        return "누적" . $value . "달성";
    }

    // badge 생성 전 (거리, 시간, 평균 속도, 최고 속도 체크)
    // distance badge 생성
    public function badgeDistance()
    {
        $user = Auth::guard('api')->user();
        $stat_user = $user->getAttribute('id');

        // distance 합계
        $sum_distance = $this->state->sumDistance($stat_user);

        // 1. 거리 (100)
        $distance_value = null;
        if ($sum_distance >= 300) {
            $distance_value .= "300km";
//            $badge_value_code = self::_DISTANCE_300;
        } elseif ($sum_distance >= 150) {
            $distance_value .= "150km";
//            $badge_value_code = self::_DISTANCE_150;
        } elseif ($sum_distance >= 100) {
            $distance_value .= "100km";
//            $badge_value_code = self::_DISTANCE_100;
        } elseif ($sum_distance >= 50) {
            $distance_value .= "50km";
//            $badge_value_code = self::_DISTANCE_50;
        } elseif ($sum_distance >= 30) {
            $distance_value .= "30km";
//            $badge_value_code = self::_DISTANCE_30;
        }

        if ($distance_value > 0) {
            $badge_name = $this->badgeMsg($distance_value);
            $badge_type_code = 100;
        }

        $this->badge->makeBadge($stat_user, $badge_type_code, $badge_name);

        return $this->responseAppJson(
            self::SAVE_DISTANCE_VALUE,
            'badgeSave',
            ['user' => $stat_user, 'badge_type' => $badge_type_code, 'badge_name' => $badge_name, 'sum_distance' => $sum_distance],
            201
        );
    }

    // time badge 생성
    public function badgeTime()
    {
        $user = Auth::guard('api')->user();
        $stat_user = $user->getAttribute('id');

        // time 합계
        $sum_time = $this->state->sumTime($stat_user);

        // 2. 시간 (200)
        $time_value = null;
        if ($sum_time >= 50) {
            $time_value .= "50시간";
//            $time_badge_value_code = self::_TIME_50;
        } elseif ($sum_time >= 30) {
            $time_value .= "30시간";
//            $time_badge_value_code = self::_TIME_30;
        } elseif ($sum_time >= 20) {
            $time_value .= "20시간";
//            $time_badge_value_code = self::_TIME_20;
        } elseif ($sum_time >= 10) {
            $time_value .= "10시간";
//            $time_badge_value_code = self::_TIME_10;
        } elseif ($sum_time >= 5) {
            $time_value .= "5시간";
//            $time_badge_value_code = self::_TIME_5;
        }

        if ($time_value > 0) {
            $badge_name = $this->badgeMsg($time_value);
            $badge_type_code = 200;
        }

        $this->badge->makeBadge($stat_user, $badge_type_code, $badge_name);

        return $this->responseAppJson(
            self::SAVE_TIME_VALUE,
            'badgeSave',
            ['user' => $stat_user, 'badge_type' => $badge_type_code, 'badge_name' => $badge_name, 'sum_time' => $sum_time],
            201
        );

    }

    // max speed badge 생성
    public function badgeSpeed()
    {
        $user = Auth::guard('api')->user();
        $stat_user = $user->getAttribute('id');

        // max_speed 최대값
        $sum_max_speed = $this->state->sumMaxSpeed($stat_user);
        $sum_max_speed_value = $sum_max_speed[0]['stat_max_speed'];

//        // avg_speed 평균값
//        $sum_avg_speed = $this->state->sumAvgSpeed($stat_user);

        // 3. 최고속도 (300)
        $max_speed_value = null;
        if ($sum_max_speed_value >= 30) {
            $max_speed_value .= "30km";
            //           $max_speed_badge_value_code = self::_MAXSPEED_30;
        } elseif ($sum_max_speed_value >= 25) {
            $max_speed_value .= "25km";
//            $max_speed_badge_value_code = self::_MAXSPEED_25;
        } elseif ($sum_max_speed_value >= 20) {
            $max_speed_value .= "20km";
//            $max_speed_badge_value_code = self::_MAXSPEED_20;
        } elseif ($sum_max_speed_value >= 15) {
            $max_speed_value .= "15km";
//            $max_speed_badge_value_code = self::_MAXSPEED_15;
        }

        if ($max_speed_value > 0) {
            $badge_name = $this->badgeMsg($max_speed_value);
            $badge_type_code = 300;
        }

        $this->badge->makeBadge($stat_user, $badge_type_code, $badge_name);

        return $this->responseAppJson(
            self::SAVE_MAX_SPEED_VALUE,
            'badgeSave',
            ['user' => $stat_user, 'badge_type' => $badge_type_code, 'badge_name' => $badge_name, 'sum_max_speed' => $sum_max_speed_value],
            201
        );
    }
}
