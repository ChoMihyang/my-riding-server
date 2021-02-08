<?php

namespace App\Http\Controllers;

use App\Members;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use \Illuminate\Http\JsonResponse;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected $TEST_USER_ID = 1;
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
     * json response 생성
     *
     * @param string $message
     * @param array $data
     * @param int $http_code
     * @return JsonResponse
     */
    public function responseJson(
        string $message,
        array $data,
        int $http_code
    ): JsonResponse
    {
        return response()->json([
            'message' => $message,
            'data' => $data
        ], $http_code);
    }


}
