<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ApiAuthController extends Controller
{
    // login/logout test

    // 회원가입
    public function register (Request $request) {
        // 유효성 검사
        $validator = Validator::make($request->all(), [
            'user_account'  => 'required|string|max:255',
            'user_password' => 'required|string|min:6|confirmed',
            'user_nickname' => 'required|String|min:12',
            'user_picture'  => 'required|string|max:255',
        ]);

        // 유효성 검사 실패시
        if ($validator->fails())
        {
            return response(['errors'=>$validator->errors()->all()], 422);
        }

        // 유효성 검사 성공시
        $request['user_password']  = Hash::make($request['user_password']);
        $request['remember_token'] = Str::random(10);
        $user  = User::create($request->toArray());
        $token = $user->createToken('Laravel Password Grant Client')->accessToken;
        $response = ['token'=>$token];

        return response($response, 200);
    }

    // 로그인
    public function login(Request $request) {
        // 유효성 검사
        $validator = Validator::make($request->all(), [
            'user_account'  => 'required|string|max:255',
            'user_password' => 'required|string|min:6|confirmed',
        ]);

        // 유효성 검사 실패시
        if ($validator->fails())
        {
            return response(['errors'=>$validator->errors()->all()], 422);
        }

        // 입력받은 user_account 정보와 저장된 user_account 정보 일치여부 확인
        $user = User::where('user_account', $request->user_account);

        // 유효성 검사 성공, user_account 정보 일치
        if ($user) {
            // 입력받은 user_password, 저장된 user_password 일치여부 확인
            if (Hash::check($request->user_password, $user->user_password)){

                // 입력받은 user_password, 저장된 user_password 일치하는 경우
                $token    = $user->createToken('Laravel Password Grant Client')->accessToken;
                $response = ['token'=>$token];

                return response($response, 200);
            } else {

                // 입력받은 user_password, 저장된 user_password 일치하지 않는 경우
                $response = ["message" => "Password mismatch"];

                return response($response, 422);
            }
        }
        else {
            // 유효성 검사 성공, user_account 정보 불일치
            $response = ["message" => "User does not exist"];

            return response($response, 422);
        }
    }

}
