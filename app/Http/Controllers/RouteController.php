<?php

namespace App\Http\Controllers;

use App\Record;
use App\Route;
use App\RouteLike;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class RouteController extends Controller
{
    private $route;
    private $record;
    private $routeLike;

    private const ROUTELISTVIEW_SUCCESS = "모든 경로 정보 조회에 성공하셨습니다.";
    private const ROUTEDETAILVIEW_SUCCESS = "선택한 경로 정보 조회에 성공하셨습니다.";
    private const ROUTESAVE_SUCCESS = "경로 저장에 성공하셨습니다.";
    private const ROUTESAVE_FAIL = "경로 저장에 실패하셨습니다.";
    private const ROUTEDELETE_SUCCESS = "경로 삭제에 성공하셨습니다.";
    private const ROUTEDELETE_FAIL = "경로 삭제에 실패하셨습니다.";
    private const ROUTESEARCH_FAIL = "검색어를 다시 입력하세요";
    private const ROUTESORT_SUCCESS = "경로 정렬에 성공하셨습니다.";

    public function __construct()
    {
        $this->route = new Route();
        $this->record = new Record();
        $this->routeLike = new RouteLike();
    }

    /**
     * [WEB] 경로 목록 조회 (생성, 좋아요 누른 경로만)
     *
     * @return JsonResponse
     */
    public function routeListView(): JsonResponse
    {
        $user = Auth::guard('api')->user();
        $route_user_id = $user->getAttribute('id');


        $routeValue = $this->route->routeListValue(1, $route_user_id);
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
     * [WEB] 경로 삭제
     *
     * @param Route $id
     * @return JsonResponse
     */
    public function routeDelete(Route $id): JsonResponse
    {
        // 경로 번호
        $route_id = $id->id;

        // 유저 아이디 값
        $user = Auth::guard('api')->user();
        $route_user_id = $user->getAttribute('id');

        // 몽고 경로 정보 삭제
        $this->mongoRouteDelete($route_id);
        // 경로 좋아요 삭제
        $this->routeLike->likeDown($route_user_id, $route_id);
        // 경로 삭제
        $deleteValue = $this->route->routeDelete($route_user_id, $route_id);

        if (!$deleteValue) {
            return $this->responseJson(
                self::ROUTEDELETE_FAIL,
                [],
                422
            );
        }

        return $this->responseJson(
            self::ROUTEDELETE_SUCCESS,
            [],
            200
        );
    }

    /**
     * [WEB] 경로 상세 조회
     *
     * @param Route $id
     * @return JsonResponse
     */
    public function routeDetailView(Route $id): JsonResponse
    {
        $route_id = $id->id;

        // 경로 정보 조회
        $routeValue = $this->route->routeDetailRouteValue($route_id);

        $user = Auth::guard('api')->user();
        $route_user_id = $user->getAttribute('id');

        // 요청한 Route 의 ID 값의 record 데이터(기록순 정렬) 가져옴
        $recordValue = $this->record->rankSort($route_id)->take(3);
        // 랭킹 데이터
        $rankValues = $this->record->myRecord($route_id, $route_user_id);
        // 몽고 데이터 조회
        $routeMongo = $this->mongoRouteShow($route_id);

        $response_data = [
            'route' => $routeValue,
            'record' => $recordValue,
            'rankvalue' => $rankValues,
            'routedata' => $routeMongo
        ];

        return $this->responseJson(
            self::ROUTEDETAILVIEW_SUCCESS,
            $response_data,
            200
        );
    }

    /**
     * [WEB] 새로운 경로 저장
     *
     * @param Request $request
     * @return JsonResponse
     * @throws Exception
     */
    public function routeSave(Request $request, Record $record): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'route_title' => 'required|string|min:3|alpha_num|unique:routes',
            'route_image.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
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

        $user = Auth::guard('api')->user();
        $route_user_id = $user->getAttribute('id');

        // 경로 이미지 추가
        $name = Str::slug($route_user_id).'_'.($request->input('route_title')).'_img';

        $folder = '/uploads/images/';

        $imgFile = $request->file('route_image');
        $routePicture = $this->getImage($imgFile, $name, $folder);


        $route_title = $request->input('route_title');
        $route_image = $routePicture;
        $route_distance = $request->input('route_distance');
        $route_time = $request->input('route_time');
        $route_avg_degree = $request->input('route_avg_degree');
        $route_max_altitude = $request->input('route_max_altitude');
        $route_min_altitude = $request->input('route_min_altitude');
        $route_start_point_address = $request->input('route_start_point_address');
        $route_end_point_address = $request->input('route_end_point_address');

        // mysql 에 경로 데이터 먼저 저장 후
        $this->route->routeSave(
            $route_user_id, $route_title, $route_image,
            $route_distance, $route_time,
            $route_avg_degree, $route_max_altitude, $route_min_altitude,
            $route_start_point_address, $route_end_point_address
        );

        // 저장된 경로의 id 값 가져와 몽고로 전송하기
        // 경로 저장 체크용
        $routeSavePoints = $this->route->RouteSaveCheck($route_user_id, $route_title);
        $saveRouteId = $routeSavePoints[0]['id'];

        // 몽고에 경로 데이터 저장 완료, 조회 완료
        $savaRouteMongo = $this->mongoRouteSave($request, $saveRouteId);

//        if ($savaRouteMongo["message"] == "잘못된 요청 값입니다.") {
//
//        }

        return $this->responseJson(
            self::ROUTESAVE_SUCCESS,
            [],
            201
        );
    }

    /**
     * [APP] 인기 라이딩 경로 조회
     *
     * @return JsonResponse
     */
    public function routePopularity(): JsonResponse
    {
        $routeValue = $this->route->routeListValue(2, 0);
        $response_data = $routeValue;

        return $this->responseAppJson(
            self::ROUTELISTVIEW_SUCCESS,
            "routes",
            $response_data,
            200
        );
    }

    /**
     * [APP] 나의 경로, 좋아요한 경로 최신순 5개 조회
     *
     *
     * @return JsonResponse
     */
    public function routeMyListLatest(): JsonResponse
    {
        $user = Auth::guard('api')->user();
        $route_user_id = $user->getAttribute('id');

        $routeValue = $this->route->routeListValue(1, $route_user_id)->take(5);
        $response_data = $routeValue;

        return $this->responseAppJson(
            self::ROUTEDETAILVIEW_SUCCESS,
            "routes",
            $response_data,
            200
        );
    }

    /**
     * [APP] 나의 경로 최신순 모두 조회
     *
     * @return JsonResponse
     */
    public function routeMyListAll(): JsonResponse
    {
        $user = Auth::guard('api')->user();
        $route_user_id = $user->getAttribute('id');

        $routeValue = $this->route->routeListValue(1, $route_user_id);
        $response_data = $routeValue;

        return $this->responseAppJson(
            self::ROUTEDETAILVIEW_SUCCESS,
            "routes",
            $response_data,
            200
        );
    }

    /**
     * [App] 라이딩 경로 검색 조회 (default 최신순)
     *
     * count = 1 : 최신순 정렬
     * count = 2 : 좋아요순 정렬
     * count = 3 : 거리순 정렬
     * count = 4 : 소요시간순 정렬
     * count = 5 : 라이딩 횟수순 정렬
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function routeSearch(Request $request): JsonResponse
    {
        $wordValue = "";
        $word = (string)$request->word;
        $count = (int)$request->count;

        // Default 화면, 최신순 정렬
        if ($count == null) {
            $count = 1;
        }

        // if 검색할 경우 (검색 여부 체크)
        if ($word) {
            $validator = Validator::make($request->all(), [
                'word' => 'required|string|min:1|alpha_num',
            ]);

            if ($validator->fails()) {
                $response_data = [
                    'error' => $validator->errors(),
                ];

                return $this->responseAppJson(
                    self::ROUTESEARCH_FAIL,
                    "routes",
                    $response_data,
                    422
                );
            }
            // TODO 검색부분 수정해야됨
            // 검색 하면 검색어 조회 후 사용자 입력 방법으로 정렬,
            $wordValue = $this->route->search($word);

            $pick = $this->route->sortSearchCount($count);

            $routeValue = $wordValue->orderBy($pick)->get();
        }
        // 검색 안하면 바로 사용자 입력 방법으로 정렬

        if ($wordValue == null) {
            // TODO 검색부분 수정해야됨
            $pick = $this->route->sortSearchCount($count);

            $routeValue = Route::orderBy($pick)->get();
        }
        // 경로 이미지 출력
        $route_img = array();
        for ($i = 0; $i < $routeValue->count(); $i++) {
            $route_img = $routeValue[$i]['route_image'];
            if (!($route_img == "null")) {
                $data = Storage::get('public/' . $route_img);
                $type = pathinfo('storage/' . $route_img, PATHINFO_EXTENSION);

                $routeValue[$i]['route_image'] = 'data:image/' . $type . ';base64,' . base64_encode($data);
            }
        }
        $response_data = $routeValue;

        return $this->responseAppJson(
            self::ROUTESORT_SUCCESS,
            "routes",
            $response_data,
            200
        );
    }

    /**
     * [WEB, APP] 좋아요 생성
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function likePush(Request $request): JsonResponse
    {
        $user = Auth::guard('api')->user();
        $route_like_user = $user->getAttribute('id');

        $route_like_obj = (int)$request->route_like_obj;

        // RouteLikes 테이블 새로운 레코드 추가
        $this->routeLike->likeUp($route_like_user, $route_like_obj);

        // RouteLikes 테이블 새로운 레코드 추가후 갯수 조회함
        $likeCount = $this->routeLike->selectLike($route_like_obj);
        // 레코드 갯수 조회후 갯수 업데이트a
        $this->route->likeAlter($route_like_obj, $likeCount);

        return $this->responseJson(
            "좋아요 생성",
            [],
            201
        );
    }

    /**
     * [WEB, APP] 좋아요 삭제
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function likePull(Request $request): JsonResponse
    {
        $user = Auth::guard('api')->user();
        $route_like_user = $user->getAttribute('id');
        $route_like_obj = $request->route_like_obj;

        // RouteLikes 테이블 레코드 삭제
        $this->routeLike->likeDown($route_like_user, $route_like_obj);

        // RouteLikes 테이블 새로운 레코드 삭제후 갯수 조회함
        $likeCount = $this->routeLike->selectLike($route_like_obj);
        // 레코드 갯수 조회후 갯수 업데이트
        $this->route->likeAlter($route_like_obj, $likeCount);

        return $this->responseJson(
            "좋아요 삭제",
            [],
            201
        );
    }

    /**
     * [APP] 경로 상세 페이지
     *
     * @param Route $id
     * @return JsonResponse
     */
    public function routeMyListDetail(Route $id): JsonResponse
    {
        $user = Auth::guard('api')->user();
        $route_like_user = $user->getAttribute('id');
        // 경로 종류 -> 인기 경로, 좋아요 누른 경로, 내가 만든 경로, 검색한 경로
        // 이전 페이지에서 route 의 id 받음
        $route_id = $id->id;

        // 경로 종류별로 들어온 route_id
        $routeValue = $this->route->routeDetailValue($route_id, $route_like_user);

        $responseData = $routeValue;

        return $this->responseAppJson(
            self::ROUTEDETAILVIEW_SUCCESS,
            "routes",
            $responseData,
            200
        );
    }

    // 경로 정보 조회
    public function mongoRouteShow(int $routeId)
    {
        $response = \Illuminate\Support\Facades\Http::get("http://13.209.75.193:3000/api/route/$routeId");

        return $response->json();
    }

    // 경로 정보 저장
    public function mongoRouteSave(Request $request, int $routeId)
    {
        $response_data = $request->input('points');

        $response = \Illuminate\Support\Facades\Http::post("http://13.209.75.193:3000/api/route/$routeId", [
            "points" => $response_data
        ]);

        return $response->json();
    }

    // 경로 정보 삭제
    public function mongoRouteDelete(int $routeId)
    {
        $response = \Illuminate\Support\Facades\Http::delete("http://13.209.75.193:3000/api/route/$routeId");

        return $response->json();
    }

    // TODO 경로 이미지 조회
    public function loadRouteImage(Route $route): string
    {
        $routeImg = $route['route_image'];
        if ($routeImg == "null") {
            return "null";
        }

        return $loadImg = $this->getBase64Img($routeImg);
    }
}
