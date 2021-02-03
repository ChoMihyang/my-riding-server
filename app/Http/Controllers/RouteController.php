<?php

namespace App\Http\Controllers;

use App\Record;
use App\Route;
use Illuminate\Http\Request;

class RouteController extends Controller
{
    private $route;
    private $record;

    private const ROUTELIST_SUCCESS = "경로 정보 조회에 성공하셨습니다.";

    public function __construct()
    {
        $this->route  = new Route();
        $this->record = new Record();
    }

    // [라이딩 경로] 전체 목록 조회
    public function routeListView()
    {
        $routeValue = $this->route->routeValue();
        $responseData = [
            'routes' => $routeValue
        ];

        return $this->responseJson(
            self::ROUTELIST_SUCCESS,
            $responseData,
            200
        );
    }

    // [라이딩 경로] 경로 삭제
    public function routeDelete()
    {

    }

    // [라이딩 경로] 상세 조회
    public function routeDetailView()
    {

    }

    // [라이딩 경로] 새로운 경로 저장
    public function routeSave()
    {

    }
}
