<?php

namespace App\Http\Controllers;

class BadgeController extends Controller
{
//  <<-- 배지 기록 수치별 코드 상수화
    const _DISTANCE_30 = 101;
    const _DISTANCE_50 = 102;
    const _DISTANCE_100 = 103;
    const _DISTANCE_150 = 104;
    const _DISTANCE_300 = 105;

    const _TIME_5 = 201;
    const _TIME_10 = 202;
    const _TIME_20 = 203;
    const _TIME_30 = 204;
    const _TIME_50 = 205;

    const _MAXSPEED_15 = 301;
    const _MAXSPEED_20 = 302;
    const _MAXSPEED_25 = 303;
    const _MAXSPEED_30 = 304;

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
    // 통계 테이블 조회 후 전달받은 값으로
    // 각 배지 달성 여부 판단
    public function checkBadge(
        int $distance,
        int $time,
        float $max_speed
    )
    {
        $badge_result = [];

        // 1. 거리 (100)
        $distance_value = null;
        if ($distance >= 300) {
            $distance_value .= "300km";
        } elseif ($distance >= 150) {
            $distance_value .= "150km";
        } elseif ($distance >= 100) {
            $distance_value .= "100km";
        } elseif ($distance >= 50) {
            $distance_value .= "50km";
        } elseif ($distance >= 30) {
            $distance_value .= "30km";
        }

        if ($distance_value > 0) {
            $badge_result[] = $this->badgeMsg($distance_value);
        }

        // 2. 시간 (200)
        $time_value = null;
        if ($time >= 50) {
            $time_value .= "50시간";
        } elseif ($time >= 30) {
            $time_value .= "30시간";
        } elseif ($time >= 20) {
            $time_value .= "20시간";
        } elseif ($time >= 10) {
            $time_value .= "10시간";
        } elseif ($time >= 5) {
            $time_value .= "5시간";
        }

        if ($time_value > 0) {
            $badge_result[] = $this->badgeMsg($time_value);
        }

        // 3. 최고속도 (300)
        $max_speed_value = null;
        if ($max_speed >= 30) {
            $max_speed_value .= "30km";
        } elseif ($max_speed >= 25) {
            $max_speed_value .= "25km";
        } elseif ($max_speed >= 20) {
            $max_speed_value .= "20km";
        } elseif ($max_speed >= 15) {
            $max_speed_value .= "15km";
        }

        if ($max_speed_value > 0.0) {
            $badge_result[] = $this->badgeMsg($max_speed_value);
        }

        // 반환값 : 달성한 배지의 코드, 타입 안에서 달성 수치값에 대한 정보
        // 4. 점수 (400)
        // 5. 랭킹 (500)
        // 6. 연속 (600)

        return $badge_result;
    }
}
