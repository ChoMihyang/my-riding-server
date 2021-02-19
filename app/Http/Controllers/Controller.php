<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use \Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use PhpParser\Node\Scalar\String_;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected $member;

//    public function __construct()
//    {
//        $this->member = new Member();
//    }

    public static function makeResponseJson($msg, $statusCode, $data = null)
    {
        return response()->json([
            "message" => $msg,
            "data" => $data
        ], $statusCode);
    }

    /**
     * [WEB] json response 생성
     *
     * @param string $message
     * @param $data
     * @param int $http_code
     * @return JsonResponse
     */
    public function responseJson(
        string $message,
        $data,
        int $http_code
    ): JsonResponse
    {
        return response()->json([
            'message' => $message,
            'data' => $data
        ], $http_code);
    }

    /**
     * [APP] json response 생성
     *
     * @param string $message
     * @param string $type
     * @param $data
     * @param int $http_code
     * @return JsonResponse
     */
    public function responseAppJson(
        string $message,
        string $type,
        $data,
        int $http_code
    ): JsonResponse
    {
        return response()->json([
            'message' => $message,
            $type => $data
        ], $http_code
        );
    }
}
