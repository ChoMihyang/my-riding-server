<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Badge extends Model
{
    protected $table = 'badges';
    protected $fillable = ['badge_user_id', 'badge_type', 'badge_value', 'badge_name'];
    protected $hidden = ['created_at', 'updated_at'];

    // 통계 결과에 따라 배지를 달성한 경우 레코드 생성
    public function makeBadge(
        int $user_id,
        int $badge_type_code,
        int $badge_value_code,
        string $badge_name
    )
    {
        self::create([
            'badge_user_id' => $user_id,
            'badge_type' => $badge_type_code,
            'badge_value' => $badge_value_code,
            'badge_name' => $badge_name
        ]);
    }
}
