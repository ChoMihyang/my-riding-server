<?php

namespace App;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class Stats extends Model
{
    protected $table = 'stats';

    protected $hidden = ['created_at', 'updated_at'];

    /**
     * 대시보드 통계 정보
     * @param int $user_id
     * @param int $today_year
     * @param int $today_week
     * @return Collection
     */
    public function getDashboardStats(
        int $user_id,
        int $today_year,
        int $today_week
    ): Collection
    {
        $user_stats = self::select([
            'stat_week as week',
            'stat_distance as distance',
            'stat_time as time',
            'stat_avg_speed as avg_speed'
        ])
            ->where('stat_user_id', $user_id)
            ->where('stat_year', $today_year)
            ->where('stat_week', $today_week)
            ->get();

        return $user_stats;
    }
}
