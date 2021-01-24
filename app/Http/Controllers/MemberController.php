<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MemberController extends Controller
{
    private const SIGNUP_SUCCESS = "회원가입에 성공하였습니다.";
    private const SIGNUP_FAIL = "회원가입에 실패하였습니다.";

    public function login()
    {
        /*.
         * 1. 회원 아이디 비밀번호 검사 -> Member
         * 2. 회원 토큰 발급 -> Passport
         */

        return response()->json([
            "message" => "login"
        ], 200);
    }

    public function logout()
    {
        /*
         * 1. 회원 토큰 삭제 -> Passport
         */
        return response()->json([
            "message" => "logout"
        ], 200);
    }

    public function signUp(Request $request)
    {
        /*
         * 1. 회원정보 추가 -> Member 모델의 create_member_info()
         */
        $rules = [];
        $request->validate($rules);

        $account = $request->input('account');
        $password = $request->input('password');
        $is_signup_success = $this->member->create_member_info($account, $password);

        // 실패 했을 경우
        if (!$is_signup_success) {
            return self::makeResponseJson(self::SIGNUP_FAIL, 422);
        }

        return
            self::makeResponseJson(self::SIGNUP_SUCCESS, 200);
    }

    public function profile()
    {
        /*
         * 1. 회원정보 조회 -> Member
         */
        return response()->json([
            "message" => "profile"
        ], 200);
    }

    public function auth()
    {
        /*
         * 1. 회원정보 토큰 검사 -> Passport
         */
        return response()->json([
            "message" => "auth"
        ], 200);
    }


    public function dashboard()
    {
        /**
         *  대시보드 조회
         *  param : X
         *  Request : 회원 정보 토큰
         *  Response
         *    <<-- 조회 성공 시 -->>
         *    [
         *         "message" => 조회에 성공하였습니다.
         *         "data"    => [
         *             "memberInfo" : (Member)사진, 이름, 라이딩 점수, 라이딩 횟수, 최근 라이딩 날짜
         *             "statsData"  : (Stats)이번 주 및 올해 총 거리, 속도, 시간
         *             "notiData"   : (Notification)알림 종류, 알림 메시지, 상세 알림 주소
         *         ]
         *    ], 200
         *    <<-- 조회 실패 시 -->>
         *    [
         *         "message" => 조회에 실패하였습니다.
         *         "data"    => null
         *    ], 403
         */

    }
}
