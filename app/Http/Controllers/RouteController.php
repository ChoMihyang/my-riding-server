<?php

namespace App\Http\Controllers;

use App\Record;
use App\Route;
use App\RouteLike;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RouteController extends Controller
{
    private $route;
    private $record;
    private $routeLike;

    private const ROUTELISTVIEW_SUCCESS   = "모든 경로 정보 조회에 성공하셨습니다.";
    private const ROUTEDETAILVIEW_SUCCESS = "선택한 경로 정보 조회에 성공하셨습니다.";
    private const ROUTESAVE_SUCCESS       = "경로 저장에 성공하셨습니다.";
    private const ROUTESAVE_FAIL          = "경로 저장에 실패하셨습니다.";
    private const ROUTEDELETE_SUCCESS     = "경로 삭제에 성공하셨습니다.";
    private const ROUTESEARCH_FAIL        = "검색어를 다시 입력하세요";
    private const ROUTESORT_SUCCESS       = "경로 정렬에 성공하셨습니다.";

    public function __construct()
    {
        $this->route     = new Route();
        $this->record    = new Record();
        $this->routeLike = new RouteLike();
    }

    /**
     * [라이딩 경로] WEB 전체 목록 조회
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function routeListView()
    {
        $routeValue = $this->route->routeListValue(1, null);
        $response_data = [
            'routes' => $routeValue
        ];

        return $this->responseJson(
            self::ROUTELISTVIEW_SUCCESS,
            $response_data,
            200
        );
    }

    /**
     * [라이딩 경로] WEB 경로 삭제
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function routeDelete(Request $request)
    {
        // TODO 토큰 값 가져오기
        $route_id = $request->id;

        $this->route->routeDelete($route_id);

        return $this->responseJson(
            self::ROUTEDELETE_SUCCESS,
            [],
            200
        );
    }

    /**
     * [라이딩 경로] WEB 상세 조회
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function routeDetailView(Request $request)
    {
        // TODO 토큰 값 가져오기
        $route_id = (int)$request->id;

        // TODO 상세조회 페이지에서 Record 부분 추가 해야됨
        // 요청한 Route 의 ID 값의 데이터 가져옴
        $routeValue = $this->route->routeDetailRouteValue($route_id);
        $response_data = [
            'route' => $routeValue
        ];

        return $this->responseJson(
            self::ROUTEDETAILVIEW_SUCCESS,
            $response_data,
            200
        );
    }

    /**
     * [라이딩 경로] WEB 새로운 경로 저장
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function routeSave(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'route_title'  => 'required|string|min:3|alpha_num|unique:routes',
        ]);

        if ($validator->fails()) {
            $response_data = [
                'error' => $validator->errors(),
            ];

            return $this->responseJson(
                self::ROUTESAVE_FAIL,
                $response_data,
                422
            );
        }

        $route_user_id             = $request->input('route_user_id');
        $route_title               = $request->input('route_title');
        $route_image               = $request->input('route_image');
        $route_distance            = $request->input('route_distance');
        $route_time                = $request->input('route_time');
        $route_avg_degree          = $request->input('route_avg_degree');
        $route_max_altitude        = $request->input('route_max_altitude');
        $route_min_altitude        = $request->input('route_min_altitude');
        $route_start_point_address = $request->input('route_start_point_address');
        $route_end_point_address   = $request->input('route_end_point_address');

        $this->route->routeSave(
            $route_user_id, $route_title, $route_image,
            $route_distance,$route_time,
            $route_avg_degree,$route_max_altitude,$route_min_altitude,
            $route_start_point_address,$route_end_point_address
        );

        return $this->responseJson(
            self::ROUTESAVE_SUCCESS,
            [],
            201
        );
    }

    /**
     * APP 인기 라이딩 경로 조회
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function routePopularity()
    {
        $routeValue = $this->route->routeListValue(2, 0);
        $response_data = [
            'routes' => $routeValue
        ];

        return $this->responseJson(
            self::ROUTELISTVIEW_SUCCESS,
            $response_data,
            200
        );
    }

    /**
     * APP 나의 경로 최신순 5개 조회
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function routeMyListLatest(Request $request)
    {
        // TODO 토큰 값 가져오기
        $route_user_id = (int)$request->route_user_id;
        // TODO 수정해야함 주석 부분 join 해야됨
        $routeValue = $this->route->routeListValue(3, $route_user_id)->take(5);
//        $routeLikeValue = $this->routeLike->likeSearch($route_user_id);

        $response_data = [
            'routes' => $routeValue,
//            'routeLikes' => $routeLikeValue
        ];

        return $this->responseJson(
            self::ROUTEDETAILVIEW_SUCCESS,
            $response_data,
            200
        );
    }

    /**
     * APP 나의 경로 최신순 모두 조회
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function routeMyListAll(Request $request)
    {
        // TODO 토큰 값 가져오기
        $route_user_id = (int)$request->route_user_id;

        $routeValue     = $this->route->routeListValue(3, $route_user_id);

        $response_data = [
            'routes' => $routeValue,
        ];

        return $this->responseJson(
            self::ROUTEDETAILVIEW_SUCCESS,
            $response_data,
            200
        );
    }

    // 라이딩 경로 검색 조회 , 일단 최신순...
    public function routeSearch(Request $request)
    {
        // TODO 검색어 입력받기
        $wordValue  = "";
        $word  = (string)$request->word;
        $count = (int)$request->count;

        // Default 화면, 최신순 정렬
        if ($count == null) {
            $count = 1;
        }

        // 검색 여부 체크..
        // if 검색할 경우
        if ($word) {
            $validator = Validator::make($request->all(), [
                'word'  => 'required|string|min:1|alpha_num',
            ]);

            if ($validator->fails()) {
                $response_data = [
                    'error' => $validator->errors(),
                ];

                return $this->responseJson(
                    self::ROUTESEARCH_FAIL,
                    $response_data,
                    422
                );
            }
            // 검색하면 검색하고 정렬 방법대로 정렬,
            $wordValue = $this->route->search($word);

            $pick = $this->route->sortNotSearchCount($count);

            $routeValue = $wordValue->sortByDesc($pick);
        }
        // 검색 안하면 바로 정렬 방법대로 정렬
        // 최신순 정렬 count = 1
        // 좋아요 순 정렬 count = 2
        // 거리순 count = 3
        // 소요시간 순 count = 4
        // 라이딩 횟수 count = 5

        // 검색 안 했을때...
        // -> 완료
        if ($wordValue == null) {
            // default 화면, 검색x, 최신순
            $pick = $this->route->sortNotSearchCount($count);

            $routeValue = Route::all()->sortByDesc($pick);
        }

        $response_data = [
            'routes' => $routeValue
        ];

        return $this->responseJson(
            self::ROUTESORT_SUCCESS,
            $response_data,
            200
        );
    }

    /**
     * 좋아요 생성
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function likePush(Request $request)
    {
        // TODO $route_like_user : 토큰으로 유저 확인 해야함
        $route_like_user = 2;
        $route_like_obj  = (int)$request->route_like_obj;

        // RouteLikes 테이블 새로운 레코드 추가
        $this->routeLike->likeUp($route_like_user, $route_like_obj);

        // RouteLikes 테이블 새로운 레코드 추가후 갯수 조회함
        $likeCount = $this->routeLike->selectLike($route_like_obj);

        $this->route->likeAlter($route_like_obj, $likeCount);

        return $this->responseJson(
            "좋아요 생성",
            [],
            201
        );
    }

    /**
     * 좋아요 삭제
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function likePull(Request $request)
    {
        // TODO $route_like_user : 토큰으로 유저 확인 해야함
        $route_like_user = 1;
        $route_like_obj  = $request->route_like_obj;

        // RouteLikes 테이블 레코드 삭제
        $this->routeLike->likeDown($route_like_user, $route_like_obj);

        // RouteLikes 테이블 새로운 레코드 삭제후 갯수 조회함
        $likeCount = $this->routeLike->selectLike();

        $this->route->likeAlter($route_like_obj, $likeCount);

        return $this->responseJson(
            "좋아요 삭제",
            [],
            200
        );
    }

    public function test(Request $request)
    {
        // 갯수 업데이트 테스트,,
        $routeID = $request->id;
        $routeValue = Route::find($routeID)->routeLike->count();
        // 좋아요 갯수 긁어 오는 것까지 함

        dd($routeValue);
    }
}
