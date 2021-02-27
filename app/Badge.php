<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Badge extends Model
{
    protected $table = 'badges';
    protected $hidden = ['created_at', 'updated_at'];

    public function makeBadge(
        int $user_id,
        int $badge_type_code,
        int $badge_value_code
    )
    {
        $param = [
            'badge_type as type_code',
            'badge_value as value_code',
            'badge_name as name',
        ];

        self::create([
            'badge_type' => $badge_type_code,
            ''

        ]);


    }
}
