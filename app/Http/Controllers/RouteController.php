<?php

namespace App\Http\Controllers;

use App\Record;
use App\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RouteController extends Controller
{
    private $route;
    private $record;

    private const ROUTELISTVIEW_SUCCESS   = "모든 경로 정보 조회에 성공하셨습니다.";
    private const ROUTEDETAILVIEW_SUCCESS = "선택한 경로 정보 조회에 성공하셨습니다.";
    private const ROUTESAVE_SUCCESS       = "경로 저장에 성공하셨습니다.";
    private const ROUTESAVE_FAIL          = "경로 저장에 실패하셨습니다.";
    private const ROUTEDELETE_SUCCESS     = "경로 삭제에 성공하셨습니다.";

    public function __construct()
    {
        $this->route  = new Route();
        $this->record = new Record();
    }

    /**
     * [라이딩 경로] WEB 전체 목록 조회
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function routeListView()
    {
        $routeValue = $this->route->routeListValue(2);
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
            'route_title'  => 'required|string|min:3|unique:routes',
        ]);

        if ($validator->fails() || strpos($request['route_title'], ' ')) {
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

        $newRoute = $this->route->routeSave(
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
        $routeValue = $this->route->routeListValue(1);
        $response_data = [
            'routes' => $routeValue
        ];

        return $this->responseJson(
            self::ROUTELISTVIEW_SUCCESS,
            $response_data,
            200
        );
    }
}
