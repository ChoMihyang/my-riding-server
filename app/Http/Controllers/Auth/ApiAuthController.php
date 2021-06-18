<?php

namespace App\Http\Controllers\Auth;

use App\Badge;
use App\Http\Controllers\BadgeController;
use App\Http\Controllers\Controller;
use App\Stats;
use App\Traits\UploadTrait;
use App\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ApiAuthController extends Controller
{
    private $user;
    private $stat;
    private $badge;
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
        $this->stat = new Stats();
        $this->badge = new Badge();
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
            'user_picture.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
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
            $name = Str::slug($request->input('user_account')) . '_img';

            $folder = '/uploads/images/';

            $imgFile = $request->file('user_picture');
            $user_picture = $this->getImage($imgFile, $name, $folder);
        }

        $user_account = $request->input('user_account');
        $user_password = $request->input('user_password');
        $user_nickname = $request->input('user_nickname');

        $data = $this->user->createUserInfo($user_account, $user_password, $user_nickname, $user_picture);
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
        $user['user_picture'] = $this->loadImage();

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

    // [APP] 회원 정보(프로필, 통계, 배지) 조회
    public function profileMobile(): JsonResponse
    {
        $user = Auth::guard('api')->user();

        $user_id = $user->getAttribute('id');
        $user_nickname = $user->getAttribute('user_nickname');
        $user_picture = $this->loadImage();
        $user_score_of_riding = $user->getAttribute('user_score_of_riding');
        $user_num_of_riding = $user->getAttribute('user_num_of_riding');

        // 오늘 날짜
        $today_date = date('Y-m-d');
        // 오늘 요일
        $temp_day = date('w', strtotime($today_date));
        $today_day = $temp_day == 0 ? 6 : $temp_day - 1;
        // 이번 주의 시작일
        $today_start_date = date('Y-m-d', strtotime($today_date . "-" . $today_day . "days"));
        $last_date_range = date('Y-m-d', strtotime($today_start_date . "+6day"));

        // 91일 전 날짜
        $start_date_range = date('Y-m-d', strtotime($today_start_date . "-91days"));
        // stats 테이블에서 날짜 범위에 해당하는 주차 조회
        $profile_stat = $this->stat->select_profile_stat(
            $user_id,
            $start_date_range,
            $last_date_range
        );
        // 올해
        $today_year = date('Y');
        // 이번주 주차
        $today_week = date('W', strtotime($today_date));

        $returnRecord = [];
        // 통계가 존재하지 않는 사용자(신규 유저)
        // TODO : 현재 "year" -> 2021년으로 고정
        if (count($profile_stat) == 0) {
            for ($week = $today_week; $week > $today_week - 12; $week--) {
                $returnRecord[] = [
                    "year" => $today_year,
                    "week" => $week,
                    "distance" => 0,
                    "time" => 0,
                    "avg_speed" => 0,
                    "max_speed" => 0
                ];
            }
        } else {
            // 통계가 존재하는 사용자
            $temp = 0;
            for ($week = $today_week; $week > $today_week - 12; $week--) {
                for ($index = $temp; $index < count($profile_stat); $index++) {
                    // 해당 주에 기록이 존재하는 경우
                    if ($profile_stat[$index]->week == $week) {
                        $returnRecord[] = [
                            "year" => $profile_stat[$index]->year,
                            "week" => $profile_stat[$index]->week,
                            "distance" => $profile_stat[$index]->distance,
                            "time" => $profile_stat[$index]->time,
                            "avg_speed" => $profile_stat[$index]->avg_speed,
                            "max_speed" => $profile_stat[$index]->max_speed
                        ];
                        $week--;
                    } else {
                        // 해당 주에 기록이 존재하지 않는 경우
                        $returnRecord[] = [
                            "year" => $profile_stat[$index]->year,
                            "week" => $week,
                            "distance" => 0,
                            "time" => 0,
                            "avg_speed" => 0,
                            "max_speed" => 0
                        ];
                        $temp = $index;
                        break;
                    }
                    // 기록이 있는 주차를 넘어갈 경우 인덱스 오류 처리
                    if ($index == count($profile_stat) - 1) {
                        $week++;
                    }
                }
            }
        }
        // 배지 보유 현황
        $badge = new BadgeController();
        $profile_badge = $badge->badgeCheck();

        return $this->responseAppJson(
            self::USER_PROFILE,
            'profile',
            ['id' => $user_id,
                'user_nickname' => $user_nickname,
                'user_picture' => $user_picture,
                'user_score_of_riding' => $user_score_of_riding,
                'user_num_of_riding' => $user_num_of_riding,
                'stat' => $returnRecord,
                'badge' => $profile_badge],
            200
        );
    }

    /**
     * 프로필 이미지 수정
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function profileImageChange(Request $request): JsonResponse
    {
        // 1. 유저 사진 파일 validation 체크
        $user = Auth::guard('api')->user();

        $user_id = $user->getAttribute('id');
        $user_account = $user->getAttribute('user_account');

        $validator = Validator::make($request->all(), [
            'user_picture.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
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
            $name = Str::slug($user_account) . '_img';

            $folder = '/uploads/images/';

            $imgFile = $request->file('user_picture');
            $user_picture = $this->getImage($imgFile, $name, $folder);


            // 기존 이미지 삭제
            $this->deleteImage($user_account . '_img');

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
    public
    function passwordUpdate(Request $request): JsonResponse
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
        } else {
            return $this->responseJson(
                self::PASSWORD_CHANGE_FAIL,
                [],
                422
            );
        }
    }
}
