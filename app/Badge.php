<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class Badge extends Model
{
    protected $table = 'badges';
    protected $fillable = ['badge_user_id', 'badge_type', 'badge_value', 'badge_name'];
    protected $hidden = ['created_at', 'updated_at'];

    // 앱 프로필 화면 '내 배지' 현황 출력

    /**
     * @param int $user_id
     * @return Collection
     */
    public function showBadge(int $user_id)
    {
        $param = [
            'badge_type as type',
            'badge_name as name'
        ];

        $returnData = self::select($param)
            ->where('badge_user_id', $user_id)
            ->get();

        return $returnData;
    }

    // 통계 결과에 따라 배지를 달성한 경우 레코드 생성
    public function makeBadge(
        int $user_id,
        int $badge_type_code,
        string $badge_name
    )
    {
        self::create([
            'badge_user_id' => $user_id,
            'badge_type' => $badge_type_code,
            'badge_name' => $badge_name
        ]);
    }

    // 시간 배지 조회
    public function checkTimeBadge(
        int $user_id
    )
    {
        return self::where('badge_user_id', $user_id)
            ->where('badge_name', 'like', '%' . '시간' . '%')
            ->get()->count();
    }

    // 거리 배지 조회
    public function checkDisBadge(
        int $user_id
    )
    {
        return self::where('badge_user_id', $user_id)
            ->where('badge_name', 'like', '%' . 'km달성' . '%')
            ->get()->count();
    }

    // 최고속도 메달 조회
    public function checkMaxSpeedBadge(
        int $user_id
    )
    {
        return self::where('badge_user_id', $user_id)
            ->where('badge_name', 'like', '%' . '속도' . '%')
            ->get()->count();
    }
}
