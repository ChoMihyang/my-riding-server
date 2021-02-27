<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RouteLike extends Model
{
    protected $table = 'route_likes';

    protected $fillable = ['route_like_user', 'route_like_obj'];

    public function user()
    {
        // User  <-> RouteLike 모델 다대다 관계 선언
        return $this->belongsToMany(User::class);
    }

    public function route()
    {
        // Route <-> RouteLike 모델 일대다 관계 선언
        return $this->belongsToMany(Route::class);
    }

    /**
     * 좋아요 생성
     *
     * @param int $route_like_user
     * @param int $route_like_obj
     */
    public function likeUp(
        int $route_like_user,
        int $route_like_obj
    )
    {
        self::create([
            'route_like_user' => $route_like_user,
            'route_like_obj' => $route_like_obj
        ]);
    }

    /**
     * 좋아요 누른 사람 수 조회
     *
     * @return mixed
     */
    public function selectLike(
        int $route_like_obj
    )
    {
        return self::select('route_like_obj')
            ->where('route_like_obj', $route_like_obj)
            ->get()
            ->count();
    }

    /**
     * 좋아요 삭제
     *
     * @param int $route_like_user
     * @param int $route_like_obj
     * @return mixed
     */
    public function likeDown(
        int $route_like_user,
        int $route_like_obj
    )
    {
        $idCheck = self::where('route_like_user', $route_like_user)
            ->where('route_like_obj', $route_like_obj)
            ->get();

        $pickId = $idCheck->pluck('id');
        if ($pickId->isEmpty()) {
            return [];
        }
        return self::where('id', $pickId)->delete();
    }
}
