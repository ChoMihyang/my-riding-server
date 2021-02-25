<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BadgeController extends Controller
{
    // 통계 테이블 조회 후 전달받은 값으로
    // 각 배지 달성 여부 판단
    public function checkBadge()
    {
        // 1. 거리 (100)
        // 2. 시간 (200)
        // 3. 최고속도 (300)
        // 4. 점수 (400)
        // 5. 랭킹 (500)
        // 6. 연속 (600)
    }
}
