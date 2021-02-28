<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use App\Traits\UploadTrait;

class ApiAuthController extends Controller
{
    private $user;
    private const SIGNUP_FAIL = '회원가입에 실패하셨습니다.';
    private const SIGNUP_SUCCESS = '회원가입에 성공하셨습니다.';
    private const LOGIN_FAIL_AC = '유저 아이디가 일치하지 않습니다.';
    private const LOGIN_FAIL_PW = '유저 패스워드가 일치하지 않습니다.';
    private const LOGIN_FAIL = '로그인에 실패하셨습니다.';
    private const LOGIN_SUCCESS = '로그인에 성공하셨습니다.';
    private const LOGOUT = '로그아웃 되었습니다.';
    private const USER_PROFILE = '프로필 정보 조회에 성공하셨습니다.';
    private const TOKEN_SUCCESS = '유효한 토큰입니다.';
    private const TOKEN_FAIL = '잘못된 접근입니다.';
    private const IMAGE_CHANGE_SUCCESS = '이미지가 변경되었습니다.';
    private const IMAGE_CHANGE_FAIL = '이미지 변경 실패했습니다.';
    private const PASSWORD_CHANGE_SUCCESS = '패스워드가 변경되었습니다.';
    private const PASSWORD_CHANGE_FAIL = '패스워드 변경 실패했습니다.';
    use UploadTrait;

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
    public function signup(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'user_account' => 'required|string|min:6|max:15|regex:/^[a-z]+[a-z0-9]{5,15}$/|unique:users',
            'user_password' => 'required|string|min:8|regex:/^(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-]).{7,}$/|confirmed',
            'user_nickname' => 'required|string|regex:/^[\w\Wㄱ-ㅎㅏ-ㅣ가-힣]{5,15}$/|unique:users',
            'user_picture.*'  => 'image|mimes:jpeg,png,jpg,gif|max:2048',
        ], [
            'user_account.regex' => '아이디를 다시 입력해주세요.',
            'user_password.regex' => '패스워드를 다시 입력해주세요.',
            'user_nickname.regex' => '닉네임을 다시 입력해주세요.'
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

        $request['user_password'] = Hash::make($request['user_password']);
        $request['remember_token'] = Str::random(10);

        $user_picture = "null";

        // 사진 입력
        if (($request->has('user_picture'))) {
            $name = Str::slug($request->input('user_account')).'_img';

            $folder = '/uploads/images/';

            $imgFile = $request->file('user_picture');
            $user_picture = $this->getImage($imgFile, $name, $folder);
        }

        $user_account = $request->input('user_account');
        $user_password = $request->input('user_password');
        $user_nickname = $request->input('user_nickname');

        $data = $this->user->createUserInfo($user_account,$user_password,$user_nickname,$user_picture);
//        $token = $data->createToken('Laravel Password Grant Client')->accessToken;
//
//        $response = ['token'=>$token];

        return $this->responseJson(
            self::SIGNUP_SUCCESS,
            [],
            201
        );
    }

    /**
     * 로그인
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function login(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'user_account' => 'required|string',
            'user_password' => 'required|string',
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
            if (Hash::check($request->user_password, $account->user_password)) {

                // 입력 받은 user_password, 저장된 user_password 일치 하는 경우
                $token = $account->createToken('Laravel Password Grant Client')->accessToken;
                $response = ['token' => $token];

                // 로그인 성공
                return $this->responseJson(
                    self::LOGIN_SUCCESS,
                    $response,
                    200
                );
            }

            $response_data = ["message" => "비밀번호가 일치하지 않습니다."];

            // 패스워드 불일치
            return $this->responseJson(
                self::LOGIN_FAIL_PW,
                $response_data,
                401
            );
        }

        $response_data = ["message" => "아이디가 존재하지 않습니다."];

        // 아이디 불일치
        return $this->responseJson(
            self::LOGIN_FAIL_AC,
            $response_data,
            401
        );
    }

    /**
     * 로그아웃
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function logout(Request $request): JsonResponse
    {
        $token = $request->user()->token();
        $token->revoke(); // 토큰 제거

        $response_data = ['message' => '로그아웃 되었습니다.'];

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
    public function profile(): JsonResponse
    {
        $user = Auth::guard('api')->user();

        $user_id = $user->getAttribute('id');
        $user_account = $user->getAttribute('user_account');
        $user_nickname = $user->getAttribute('user_nickname');
        $user_picture = $this->loadImage();
        $user_created_at = $user->getAttribute('created_at');


        return $this->responseJson(
            self::USER_PROFILE,
            [
                'id' => $user_id,
                'user_account' => $user_account,
                'user_nickname' => $user_nickname,
                'user_picture' => $user_picture,
                'created_at' => $user_created_at
            ]
            ,
            200
        );
    }

    /**
     * 회원정보 인증
     *
     * @return JsonResponse
     */
    public function user(): JsonResponse
    {
        $user = Auth::guard('api')->user();
        if (!$user) {
            return $this->responseJson(
                self::TOKEN_FAIL,
                [],
                401
            );
        }

        return $this->responseJson(
            self::TOKEN_SUCCESS,
            $user,
            200
        );
    }

    // 프로필 정보 모바일.. (수정중)
    public function profileMobile()
    {
        $user = Auth::guard('api')->user();

        $user_id = $user->getAttribute('id');
        $user_nickname = $user->getAttribute('user_nickname');
        $user_picture = $this->loadImage();
        $user_score_of_riding = $user->getAttribute('user_score_of_riding');

        // TODO stats 테이블의 통계 들어가야함!!!

        return $this->responseJson(
            self::USER_PROFILE,
            [
                'id' => $user_id,
                'user_nickname' => $user_nickname,
                'user_picture' => $user_picture,
                'user_score_of_riding' => $user_score_of_riding
            ],
            200
        );
    }

    /**
     * 프로필 이미지 수정
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function profileImageChange(Request $request)
    {
        // TODO 유저 사진 UPDATE
        // 1. 유저 사진 파일 validation 체크
        $user = Auth::guard('api')->user();

        $user_id = $user->getAttribute('id');
        $user_account = $user->getAttribute('user_account');

        $validator = Validator::make($request->all(), [
            'user_picture.*'  => 'image|mimes:jpeg,png,jpg,gif|max:2048',
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

        // 사진 입력
        if (($request->has('user_picture'))) {
            $name = Str::slug($user_account).'_img';

            $folder = '/uploads/images/';

            $imgFile = $request->file('user_picture');
            $user_picture = $this->getImage($imgFile, $name, $folder);


            // 기존 이미지 삭제
            $this->deleteImage($user_account.'_img');

            // 유저 이미지 변경
            $this->user->UserImageChange($user_id, $user_picture);

            return $this->responseJson(
                self::IMAGE_CHANGE_SUCCESS,
                [],
                200
            );
        }

        return $this->responseJson(
            self::IMAGE_CHANGE_FAIL,
            [],
            422
        );
    }

    /**
     * 비밀번호 변경
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function passwordUpdate(Request $request)
    {
        $user = Auth::guard('api')->user();

        $user_id = $user->getAttribute('id');
        $user_password_old = $user->getAttribute('user_password');

        $validator = Validator::make($request->all(), [
            'user_password_old' => 'required',
            'user_password_new' => 'required|string|min:8|regex:/^(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-]).{7,}$/|confirmed',
        ], [
            'user_password_old.regex' => '기존 패스워드를 다시 입력해주세요.',
            'user_password_new.regex' => '새로운 패스워드를 다시 입력해주세요.',
        ]);

        if ($validator->fails()) {
            $response_data = [
                'error' => $validator->errors(),
            ];

            return $this->responseJson(
                self::PASSWORD_CHANGE_FAIL,
                $response_data,
                422
            );
        }

        if (Hash::check($request['user_password_old'], $user_password_old)) {
            $user_password_new = Hash::make($request['user_password_new']);
            User::find($user_id)->update(['user_password' => $user_password_new]);

            return $this->responseJson(
              self::PASSWORD_CHANGE_SUCCESS,
              [],
              200
            );
        }
        else {
            return $this->responseJson(
                self::PASSWORD_CHANGE_FAIL,
                [],
                422
            );
        }
    }

    /**
     * 사용자 이미지 저장
     *
     * @param UploadedFile $uploadedFile
     * @param string $imgFileName
     * @param string $folderName
     * @return string
     */
    public function getImage(
        UploadedFile $uploadedFile,
        string $imgFileName,
        string $folderName
    )
    {
        $extension = $uploadedFile->extension();
        $storagePath = "{$folderName}/{$imgFileName}.{$extension}";

        Storage::putFileAs('public', $uploadedFile, $storagePath);

        return $storagePath;
    }

}
