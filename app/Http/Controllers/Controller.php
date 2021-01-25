<?php

namespace App\Http\Controllers;

use App\Member;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

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


}
