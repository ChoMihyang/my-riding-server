<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

//Route::middleware('auth:api')->get('/user', function (Request $request) {
//    return $request->user();
//});

// <<-- 회원 관리 -->>
Route::prefix("member")->group(function () {
    /*
     *  로그인 및 로그아웃
     *  회원가입
     *  프로필 조회
     *  회원 정보 인증
     *  -> MemberController
     */
    Route::post("login", "MemberController@login")->name("[사용자] 로그인");
    Route::post("logout", "MemberController@logout")->name("[사용자] 로그아웃");
    Route::post("signup", "MemberController@signUp")->name("[사용자] 회원가입");
    Route::get("profile", "MemberController@profile")->name("[사용자] 프로필 조회");
//    Route::get("auth", "MemberController@auth")->name("[사용자] 회원정보 인증");
});

// <<-- 대시 보드 관리 -->>
Route::prefix("dashboard")->group(function () {
    /**
     * 회원 정보, 통계, 알림 조회 -> memberController
     * 라이딩 알림 확인(X버튼) -> Notification
     */
    Route::get("/", "MemberController@dashboard")->name("[대시보드] 통계 조회");
    Route::patch("noti/{id}", "NotificationController@notiCheck")->name("[대시보드] 알림 확인");
    Route::get("/noti/{id}", "NotificationController@notiPageMove")->name("[대시보드] 해당 알림 페이지 이동");
});

// <<-- 라이딩 일지 관리-->>
Route::prefix("record")->group(function () {
    /*
     *  일지 조회 및 삭제
     *  일지 이름 수정
     *  거리, 시간, 평균 속도 통계 조회
     *  -> RecordController
     */
    Route::get("year", "RecordController@recordViewByYear")->name("[라이딩 일지] 연도 기준 조회");
    Route::get("week", "RecordController@recordViewByWeek")->name("[라이딩 일지] 주 기준 조회");
    Route::get("{id}", "RecordController@recordDetailView")->name("[라이딩 일지] 상세 조회");
    Route::patch("{id}", "RecordController@recordModify")->name("[라이딩 일지] 이름 수정");
    Route::delete("{id}", "RecordController@recordDelete")->name("[라이딩 일지] 기록 삭제");
});

// <<-- 라이딩 경로 관리-->>
Route::prefix("route")->group(function () {
    /*
     *  경로 목록 조회
     *  경로 목록 삭제
     *  경로 상세 조회
     *  경로 생성
     *  -> RouteController
     */
    Route::get("/", "RouteController@routeListView")->name("[라이딩 경로] 목록 조회");
    Route::delete("{id}", "RouteController@routeDelete")->name("[라이딩 경로] 경로 삭제");
    Route::post("{id}", "RouteController@routeDetailView")->name("[라이딩 경로] 상세 조회");
    Route::post("/", "RouteController@routeSave")->name("[라이딩 경로] 새로운 경로 저장");
});
