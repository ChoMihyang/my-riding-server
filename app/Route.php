<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use phpDocumentor\Reflection\Types\Self_;

class Route extends Model
{
    protected $table = 'routes';

    protected $fillable = [
        'route_user_id', 'route_title', 'route_distance',
        'route_image', 'route_time', 'route_avg_degree',
        'route_max_altitude', 'route_min_altitude', 'route_like',
        'route_start_point_address', 'route_end_point_address'
    ];

    /**
     * [라이딩 경로] 라이딩 경로 조회 (조건)
     *
     * $count = 1 : [web] 모든 경로 조회
     * $count = 2 : [app] 좋아요 순 정렬, 경로 5개 출력
     * $count = 3 : [app] 나의 경로 최신순 정렬 경로 5개 출력
     * @return mixed
     */
    public function routeListValue(
        int $count,
        int $route_user_id
    )
    {
        if ($count == 1) {
            // TODO routeLike 테이블 조인 -> 좋아요 숫자 가져오기
            $routeInfo = self::get(
                [
                    'id',
                    'created_at',
                    'route_title',
                    'route_distance',
                    'route_time',
                    'route_like',
                    'route_num_of_try_count',
                    'route_end_point_address',
                    'route_image'
                ]
            );
        }
        elseif ($count == 2) {
            $routeInfo = self::select('id','route_title','route_distance','route_like')
                ->orderBy('route_like','DESC')
                ->get()
                ->take(5);
        }
        elseif ($count == 3) {
            // TODO routes 테이블과 route_likes 테이블 조인하여 가장 최신 날짜 5개 조회, 210211 join 실패..
            $routeInfo = self::select('id','route_user_id','route_title','route_like','route_distance',
                                      'route_time','route_start_point_address','route_end_point_address','created_at')
                ->where('route_user_id', $route_user_id)
                ->orderBy('id','DESC')
                ->get();
        }

        return $routeInfo;
    }

    /**
     * [라이딩 경로] 상세 조회
     *
     * $count = 1 : [web] 경로 상세 조회
     * $count = 2 : [app] 경로 상세 조회
     *
     * @param int $route_id
     * @return mixed
     */
    public function routeDetailRouteValue(
        int $route_id
    )
    {
        $routeInfo = self::where('id',$route_id)->get();

        return $routeInfo;
    }

    /**
     * [라이딩 경로] 경로 삭제
     *
     * @param int $id
     * @return mixed
     */
    public function routeDelete(
        int $id
    )
    {
        return self::find($id)->delete();
    }

    /**
     * [라이딩 경로] 경로 저장
     *
     * @param int $route_user_id
     * @param string $route_title
     * @param string $route_image
     * @param float $route_distance
     * @param int $route_time
     * @param float $route_avg_degree
     * @param float $route_max_altitude
     * @param float $route_min_altitude
     * @param string $route_start_point_address
     * @param string $route_end_point_address
     * @return mixed
     */
    public function routeSave(
        int $route_user_id,
        string $route_title,
        string $route_image,
        float $route_distance,
        int $route_time,
        float $route_avg_degree,
        float $route_max_altitude,
        float $route_min_altitude,
        string $route_start_point_address,
        string $route_end_point_address
    )
    {
        return self::create([
            'route_user_id'=>$route_user_id,
            'route_title'=>$route_title,
            'route_image'=>$route_image,
            'route_distance'=>$route_distance,
            'route_time'=>$route_time,
            'route_avg_degree'=>$route_avg_degree,
            'route_max_altitude'=>$route_max_altitude,
            'route_min_altitude'=>$route_min_altitude,
            'route_start_point_address'=>$route_start_point_address,
            'route_end_point_address'=>$route_end_point_address,
        ]);
    }

    /**
     * User  <-> Route 모델 다대다 관계 선언
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function user()
    {
        return $this->belongsToMany(User::class);
    }

    /**
     * Route <-> RouteLike 모델 일대다 관계 선언
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function routelike()
    {
        return $this->belongsToMany(RouteLike::class, 'route_like_obj');
    }

    /**
     * 좋아요 숫자 변동
     *
     * @param int $route_id
     * @param int $likeCount
     * @return mixed
     */
    public function likeAlter(
        int $route_id,
        int $likeCount
    )
    {
        $param = [
            'route_like' => $likeCount
        ];

        return self::find($route_id)->update($param);
    }

    /**
     * 코스 검색어 포함여부 체크
     *
     * @param string $word
     * @return mixed
     */
    public function search(
        string $word
    )
    {
        return self::where('route_title', 'like', '%' . $word . '%')
            ->orWhere('route_start_point_address', 'like', '%' . $word . '%')
            ->orWhere('route_end_point_address', 'like', '%' . $word . '%')->get();
    }

    /**
     * 코스 정렬 기준 체크
     *
     * @param int $count
     * @return string
     */
    public function sortSearchCount(
        int $count
    )
    {
        if ($count == 1) {
            // 최신 순 정렬
            $routeInfo = 'id';
        }
        elseif ($count == 2) {
            // 좋아요 순 정렬
            $routeInfo = 'route_like';
        }
        elseif ($count == 3) {
            // 거리 순 정렬
            $routeInfo = 'route_distance';
        }
        elseif ($count == 4) {
            // 소요 시간 순 정렬
            $routeInfo = 'route_time';
        }
        elseif ($count == 5) {
            // 라이딩 횟수 순 정렬
            $routeInfo = 'route_num_of_try_count';
        }

        return $routeInfo;
    }
}
