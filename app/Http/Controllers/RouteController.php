<?php

namespace App\Http\Controllers;

use App\Route;
use Illuminate\Http\Request;

class RouteController extends Controller
{
    public function routeListView()
    {
        // 경로 목록 조회 -> Route
    }

    public function routeDetailView()
    {
        // 1. 선택한 경로 레코드 id 값 획득
        // 2. 경로 정보 조회 -> Route
        // 3. 라이딩 기록 조회 -> Record
    }

    public function routeDelete()
    {

        // 1. 삭제할 경로 레코드 id 값 획득
        // 2. 레코드 삭제 -> Route
    }

    public function routeSave()
    {
        // 새로 등록한 경로 정보 획득
        // 경로 저장 -> Route
    }
}
