<?php

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

// <<-- 회원 관리 -->>
Route::middleware('auth:api')->group(function () {
    Route::post('auth/logout', 'Auth\ApiAuthController@logout')->name('[사용자] 로그아웃');
});
Route::group(['middleware' => ['cors', 'json.response']], function () {
    Route::prefix("auth")->group(function () {
        Route::post('/signup', 'Auth\ApiAuthController@signup')->name('[사용자] 회원가입');
        Route::post('/login', 'Auth\ApiAuthController@login')->name('[사용자] 로그인');
    });

    Route::group(['middleware' => ['usertoken']], function () {
        Route::get("/address", "RouteController@getAddress")->name("[라이딩 경로] 주소 요청");
        Route::prefix("auth")->group(function () {
            Route::get("/load/img", "Auth\ApiAuthController@loadImage")->name("[사용자] 이미지 로드");
            Route::patch('/update/password', 'Auth\ApiAuthController@passwordUpdate')->name('[사용자] 프로필 비밀번호 변경');
            Route::post('/update/image', 'Auth\ApiAuthController@profileImageChange')->name('[사용자] 프로필 이미지 변경');
            Route::get('/profilemobile', 'Auth\ApiAuthController@profileMobile')->name('모바일 사용자 프로필 테스트');
            Route::get('/profile', 'Auth\ApiAuthController@profile')->name('[사용자] 프로필 조회');
            Route::get('/', 'Auth\ApiAuthController@user')->name('[사용자] 회원정보 인증');
        });

        // <<-- 대시 보드 관리 -->>
        Route::prefix("dashboard")->group(function () {
            /**
             * 회원 정보, 통계, 알림 조회 -> UserController
             * 라이딩 알림 확인(X버튼) -> Notification
             */
            Route::get("/", "UserController@dashboard")->name("[대시보드] 통계 조회");
            Route::patch("/notification/check/{notification}", "UserController@notificationCheck")->name("[대시보드] 알림 확인");
            Route::get("/stat", "UserController@moveWeekOfStat")->name("[대시보드] 통계 요약 주차 이동");
        });

        // <<-- 라이딩 일지 관리-->>
        Route::prefix("record")->group(function () {
            /*
             *  일지 조회 및 삭제
             *  일지 이름 수정
             *  거리, 시간, 평균 속도 통계 조회
             *  -> RecordController
             */
            Route::get("/detail/{record}", "RecordController@recordAppDetailPage")->name("[라이딩 일지] 앱 상세조회 페이지");
            Route::get("/year", "RecordController@recordViewByYear")->name("[라이딩 일지] 연도 기준 조회");
            Route::get("/week", "RecordController@recordViewByWeek")->name("[라이딩 일지] 주 기준 조회");
            Route::get('/home', 'RecordController@recordOfHome')->name("[홈화면] 날짜별 조회");
            Route::get("/{record}", "RecordController@recordDetailView")->name("[라이딩 일지] 상세 조회");
            Route::patch("/{record}", "RecordController@recordModify")->name("[라이딩 일지] 이름 수정");
            Route::delete("/{record}", "RecordController@recordDelete")->name("[라이딩 일지] 기록 삭제");
            Route::post("/", "RecordController@recordSave")->name("[라이딩 일지] 라이딩 기록 저장");
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
            Route::get("/popularity", "RouteController@routePopularity")->name("[라이딩 경로] 인기 라이딩 경로 조회");
            Route::get("/mylistlatest", "RouteController@routeMyListLatest")->name("[라이딩 경로] 내 라이딩 경로, 좋아요 한 경로 일부 조회");
            Route::get("/mylistall", "RouteController@routeMyListAll")->name("[라이딩 경로] 내 라이딩 경로, 좋아요 한 경로 모두 조회");
            Route::get("/search", "RouteController@routeSearch")->name("[라이딩 경로] 경로 검색 (Default 최신순)");
            Route::get("/mylist/{id}", "RouteController@routeMyListDetail")->name("[라이딩 경로] 앱 경로 상세 페이지 조회");

            Route::get('/path/{id}', "RouteController@routeListViewMongo")->name("[라이딩 경로] 목록 조회 몽고 데이터 조회");
            Route::get("/", "RouteController@routeListView")->name("[라이딩 경로] 목록 조회");
            Route::delete("/{id}", "RouteController@routeDelete")->name("[라이딩 경로] 경로 삭제");
            Route::get("/{id}", "RouteController@routeDetailView")->name("[라이딩 경로] 상세 조회");
            Route::post("/", "RouteController@routeSave")->name("[라이딩 경로] 새로운 경로 저장");
        });
        Route::prefix("routelike")->group(function () {
            Route::post("/likeup", "RouteController@likePush")->name("좋아요 증가");
            Route::delete("/likedown", "RouteController@likePull")->name("좋아요 감소");
        });
    });

    Route::get('/rank', 'UserController@viewUserRank')->name('[랭킹] 사용자 랭킹 출력');
    Route::get('/rank/{rank_id}', 'UserController@viewUserDetailRank')->name('[랭킹] 사용자 랭킹 상세 정보');
    Route::get('/badge', 'BadgeController@viewDetailBadge')->name('[배지] 배지 상세보기');
    Route::get('/testBadge', 'BadgeController@badgeSpeed')->name('배치 테스트');
});

Route::get('/dashboardtest', 'StatsController@test');
// 몽고 경로 테스트 중..
Route::get("/test/post/record", "RecordController@mongoRecordSave")->name("라이딩 기록 몽고로 데이터 전송");
Route::get("/test/get/record/{id}", "RecordController@mongoRecordShow")->name("라이딩 기록 몽고에서 조회");
Route::get("/test/delete/record", "RecordController@mongoRecordDelete")->name("라이딩 기록 몽고에서 삭제");
// 몽고 기록 테스트 중
Route::get("/test/post/route", "RouteController@mongoRouteSave")->name("라이딩 경로 몽고로 데이터 전달");
Route::get("/test/get/route/{routeId}", "RouteController@mongoRouteShow")->name("라이딩 경로 몽고에서 조회");
//Route::get("/test/delete/route", "RouteController@mongoRouteDelete")->name("라이딩 경로 몽고에서 삭제");

// 경로 이미지 조회 테스트
Route::get("/test/img/{route}", "RouteController@loadRouteImage")->name("이미지로드 테스트");

