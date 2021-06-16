<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    protected $table = 'users';

    protected $fillable = [
        'user_account', 'user_password',
        'user_nickname', 'user_picture',
        'user_num_of_riding', 'user_score_of_riding',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'user_password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * 대시보드 사용자 정보(UserController - dashboard)
     * @param int $user_id
     * @return array
     */
    public function getDashboardUserInfo(
        $user_id
    ): array
    {
        $param_user = [
            'user_nickname as nickname',
            'user_score_of_riding as score',
            'user_num_of_riding as count',
            'date_of_latest_riding as last_riding',
            'user_picture as picture'
        ];

        $user_info = self::select($param_user)
            ->where('id', $user_id)
            ->get()
            ->first()
            ->toArray();

        $user_img = $user_info['picture'];
        if (!($user_img == "null")) {
            $data = Storage::get('public/' . $user_img);
            $type = pathinfo('storage/' . $user_img, PATHINFO_EXTENSION);

            $user_info['picture'] = 'data:image/' . $type . ';base64,' . base64_encode($data);
        }

        $latest_riding_date = $user_info['last_riding'];

        $latest_riding_id = Record::select('id')
            ->where('rec_user_id', $user_id)
            ->whereDate('created_at', DB::raw("date('$latest_riding_date')"))
            ->first();

        // 최근 라이딩 날짜의 통계를 배열에 추가
        $user_info['last_riding_id'] = $latest_riding_id === null
            ? 0 : $latest_riding_id['id'];

        return $user_info;
    }

    /**
     * 유저 생성
     *
     * @param string $user_account
     * @param string $user_password
     * @param string $user_nickname
     * @param string $user_picture
     * @return mixed
     */
    public function createUserInfo(
        string $user_account,
        string $user_password,
        string $user_nickname,
        string $user_picture
    )
    {
        return self::create([
            'user_account' => $user_account,
            'user_password' => $user_password,
            'user_nickname' => $user_nickname,
            'user_picture' => $user_picture
        ]);
    }

    // -->> User  <-> Route 모델 다대다 관계 선언
    public function route()
    {
        // User  <-> Route 모델 다대다 관계 선언
        return $this->belongsToMany(Route::class, 'route_user_id');
    }

    // User  <-> RouteLike 모델 다대다 관계 선언
    public function routelike()
    {
        // User  <-> RouteLike 모델 다대다 관계 선언
        return $this->belongsToMany(RouteLike::class, 'route_like_user');
    }

    // User  <-> Record 모델 다대다 관계 선언
    public function record()
    {
        // User  <-> Record 모델 다대다 관계 선언
        return $this->belongsToMany(Record::class, 'rec_user_id');
    }

    // Users 테이블 점수 업데이트
    public function scoreUpdate(int $user_id, int $score)
    {
        $user = User::find($user_id);
        // 기존 점수
        $existed_score = $user->user_score_of_riding;
        $score += $existed_score;
        // 점수 필드 업데이트
        $user->user_score_of_riding = $score;

        // 기존 라이딩 횟수
        $existed_number_of_riding = $user->user_num_of_riding;
        $number = $existed_number_of_riding + 1;

        // 라이딩 횟수 필드 업데이트
        $user->user_num_of_riding = $number;
        $user->save();
    }

    // 사용자 랭킹 (10순위) 조회
    public function getUserRank()
    {
        $param = ['id', 'user_nickname', 'user_score_of_riding'];
        $returnData = User::select($param)
            ->orderByDesc('user_score_of_riding')
            ->take(2)
            ->get();

        return $returnData;
    }

    // 최근 라이딩 업데이트
    public function updateLatestRidingDate(int $user_id, $date)
    {
        $user = User::find($user_id);
        $user->date_of_latest_riding = $date;

        $user->save();
    }

    public function updateRecordCount(int $user_id, int $record_count)
    {
        $user = User::find($user_id);
        $user->user_num_of_riding = $record_count;

        $user->save();
    }

    // 이미지 url 업데이트
    public function UserImageChange(
        int $user_id,
        string $user_picture
    )
    {
        self::where('id', $user_id)
            ->update(['user_picture' => $user_picture]);
    }
}
