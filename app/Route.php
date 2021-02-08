<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class Route extends Model
{
    protected $table = 'routes';

    protected $fillable = [
        'route_user_id', 'route_title', 'route_distance',
        'route_image', 'route_time', 'route_avg_degree',
        'route_max_altitude', 'route_min_altitude',
        'route_start_point_address', 'route_end_point_address'
    ];

    /**
     * 모든 라이딩 경로 조회
     *
     * @return mixed
     */
    public function routeListValue()
    {
        // routeLike 테이블 조인 -> 좋아요 숫자 가져오기
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

        return $routeInfo;
    }

    /**
     * [라이딩 경로] 상세 조회 - route 정보
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

    public function routeDelete(
        int $id
    )
    {
        // 해당 레코드 id 값 조회 후 삭제
        return self::find($id)->delete();
    }

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
        // 경로 정보 전달 받은 후
        // 새 경로 레코드 생성
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
}
