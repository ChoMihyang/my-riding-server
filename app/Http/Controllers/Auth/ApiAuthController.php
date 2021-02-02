<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use phpDocumentor\Reflection\Types\Self_;

class ApiAuthController extends Controller
{
    private $user;
    private const SIGNUP_FAIL    = '회원가입에 실패하셨습니다.';
    private const SIGNUP_SUCCESS = '회원가입에 성공하셨습니다.';
    private const LOGIN_FAIL_AC  = '유저 아이디가 일치하지 않습니다.';
    private const LOGIN_FAIL_PW  = '유저 패스워드가 일치하지 않습니다.';
    private const LOGIN_FAIL     = '로그인에 실패하셨습니다.';
    private const LOGIN_SUCCESS  = '로그인에 성공하셨습니다.';
    private const LOGOUT         = '로그아웃 되었습니다.';
    private const USER_PROFILE   = '프로필 정보 조회에 성공하셨습니다.';
    private const TOKEN_SUCCESS  = '유효한 토큰입니다.';
    private const TOKEN_FAIL     = '잘못된 접근입니다.';

    public function __construct()
    {
        $this->user = new User();
    }

    /**
     * 회원가입
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function register(Request $request):JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'user_account'  => 'required|string|max:255',
            'user_password' => 'required|string|min:6|confirmed',
            'user_nickname' => 'required|String|min:12',
            'user_picture'  => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            $response_data = [
                'error' => $validator->errors(),
            ];

            return $this->responseJson(
                self::SIGNUP_FAIL,
                $response_data,
                422
            );
        }

        $request['user_password']  = Hash::make($request['user_password']);
        $request['remember_token'] = Str::random(10);

        // TODO 사진 입력 부분 추가 해야됨

        $user_account  = $request->input('user_account');
        $user_password = $request->input('user_password');
        $user_nickname = $request->input('user_nickname');
        $user_picture  = $request->input('user_picture');

        $newUser = $this->user->createUserInfo($user_account,$user_password,$user_nickname,$user_picture);
        $response_data = [
            'userInfo' => $newUser
        ];

        $token = $newUser->createToken('Laravel Password Grant Client')->accessToken;
        $response = ['token'=>$token];


        return $this->responseJson(
            self::SIGNUP_SUCCESS,
            [
                $response,
                $response_data,
            ],
            201
        );
    }

    /**
     * 로그인
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function login(Request $request):JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'user_account'  => 'required|string|max:255',
            'user_password' => 'required|string|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            $response_data = [
              'error' => $validator->errors(),
            ];

            return $this->responseJson(
              self::LOGIN_FAIL,
              $response_data,
              422
            );
        }


        // 입력 받은 user_account 정보와 저장된 user_account 정보 일치 여부 확인
        $account = User::where('user_account', $request->user_account)->first();

        // 유효성 검사 성공, user_account 정보 일치
        if ($account) {
            // 입력 받은 user_password, 저장된 user_password 일치 여부 확인
            if (Hash::check($request->user_password, $account->user_password)){

                // 입력 받은 user_password, 저장된 user_password 일치 하는 경우
                $token    = $account->createToken('Laravel Password Grant Client')->accessToken;
                $response = ['token'=>$token];

                // 로그인 성공
                return $this->responseJson(
                    self::LOGIN_SUCCESS,
                    $response,
                    200
                );
            }

            $response_data = ["message"=>"Password mismatch"];
            // 패스워드 불일치
            return $this->responseJson(
                self::LOGIN_FAIL_PW,
                $response_data,
                422
            );
        }
        $response_data = ["message"=>"User does not exist"];
        // 아이디 불일치
        return $this->responseJson(
            self::LOGIN_FAIL_AC,
            $response_data,
            422
        );
    }

    /**
     * 로그아웃
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function logout(Request $request):JsonResponse
    {
        $token = $request->user()->token();
        $token->revoke(); // 토큰 제거
        $response_data = ['message'=>'You have been successfully logged out!'];

        return $this->responseJson(
            self::LOGOUT,
            $response_data,
            200
        );
    }

    /**
     * 프로필 정보
     *
     * @param User $id
     * @return JsonResponse
     */
    public function profile(User $id):JsonResponse
    {
        $user_account    = $id->getAttribute('user_account');
        $user_nickname   = $id->getAttribute('user_nickname');
        $user_picture    = $id->getAttribute('user_picture');
        $user_created_at = $id->getAttribute('created_at');

        return $this->responseJson(
            self::USER_PROFILE,
            [
                $user_account,
                $user_nickname,
                $user_picture,
                $user_created_at
            ],
            200
        );
    }

    /**
     * 회원정보 인증
     *
     * @return JsonResponse
     */
    public function auth():JsonResponse
    {
        if ((Auth::guard('api')->user()) == null) {
            return $this->responseJson(
                self::TOKEN_FAIL,
                [Auth::guard('api')->user()],
                401
            );
        }

        return $this->responseJson(
            self::TOKEN_SUCCESS,
            [Auth::guard('api')->user()],
            200
        );
    }
}
