<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Route extends Model
{
    protected $table = 'routes';

    protected $fillable = [
        'route_user_id', 'route_title', 'route_distance',
        'route_image', 'route_time', 'route_avg_degree', 'route_num_of_try_count',
        'route_max_altitude', 'route_min_altitude', 'route_like', 'route_num_of_try_user',
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
        ?int $route_user_id
    )
    {
        if ($count == 1) {
            // 두 테이블 union
            $routeTableInfo = Route::select('id as route_id', 'route_user_id')
                ->where('route_user_id', $route_user_id);
            $routeLikeTableInfo = RouteLike::select('id as route_like_id', 'route_like_user as route_user_id')
                ->where('route_like_user', $route_user_id);
            $calValue = $routeTableInfo->union($routeLikeTableInfo)->get();

            // collection -> array
            $arrayCalValue = $calValue->toArray();
            $arr = array();
            // 경로 번호 뽑아냄
            for ($i = 0; $i < $calValue->count(); $i++) {
                $arr[$i] = $arrayCalValue[$i]["route_id"];
            }

            $routeInfo = Route::select(
                'id',
                'route_user_id',
                'created_at',
                'route_title',
                'route_distance',
                'route_time',
                'route_like',
                'route_num_of_try_count',
                'route_start_point_address',
                'route_end_point_address',
                'route_image')
                ->whereIn('id', $arr)
                ->orderBy('created_at', 'DESC')
                ->get();

            // 경로 이미지 출력
            $route_img = array();
            for ($i = 0; $i < $calValue->count(); $i++) {
                $route_img = $routeInfo[$i]->route_image;
                if (!($route_img == "null")) {
                    $data = Storage::get('public/' . $route_img);
                    $type = pathinfo('storage/' . $route_img, PATHINFO_EXTENSION);

                    $routeInfo[$i]['route_image'] = 'data:image/' . $type . ';base64,' . base64_encode($data);
                }
            }

        } elseif ($count == 2) {
            $routeInfo = self::select('id', 'route_title', 'route_distance', 'route_image', 'route_like')
                ->orderBy('route_like', 'DESC')
                ->get()
                ->take(5);

            // 경로 이미지 출력
            $route_img = array();
            for ($i = 0; $i < 5; $i++) {
                $route_img = $routeInfo[$i]->route_image;
                if (!($route_img == "null")) {
                    $data = Storage::get('public/' . $route_img);
                    $type = pathinfo('storage/' . $route_img, PATHINFO_EXTENSION);

                    $routeInfo[$i]['route_image'] = 'data:image/' . $type . ';base64,' . base64_encode($data);
                }
            }

        } elseif ($count == 3) {
            $routeInfo = self::select('id', 'route_user_id', 'route_title', 'route_like', 'route_distance', 'route_image',
                'route_time', 'route_start_point_address', 'route_end_point_address', 'created_at')
                ->where('route_user_id', $route_user_id)
                ->orderBy('id', 'DESC')
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
        $routeInfo = self::where('id', $route_id)->get();

        return $routeInfo;
    }

    /**
     * [라이딩 경로] 경로 삭제
     *
     * @param int $route_id
     * @param int $route_user_id
     * @return mixed
     */
    public function routeDelete(
        int $route_user_id,
        int $route_id
    )
    {
        return Route::where('route_user_id', $route_user_id)
            ->where('id', $route_id)
            ->delete();
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
            'route_user_id' => $route_user_id,
            'route_title' => $route_title,
            'route_image' => $route_image,
            'route_distance' => $route_distance,
            'route_time' => $route_time,
            'route_avg_degree' => $route_avg_degree,
            'route_max_altitude' => $route_max_altitude,
            'route_min_altitude' => $route_min_altitude,
            'route_start_point_address' => $route_start_point_address,
            'route_end_point_address' => $route_end_point_address,
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
     * [라이딩 경로] 좋아요 숫자 변동
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
     * [라이딩 경로] 코스 검색어 포함여부 체크
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
            ->orWhere('route_end_point_address', 'like', '%' . $word . '%');
    }

    /**
     * [라이딩 경로] 코스 정렬 기준 체크
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
        } elseif ($count == 2) {
            // 좋아요 순 정렬
            $routeInfo = 'route_like';
        } elseif ($count == 3) {
            // 거리 순 정렬
            $routeInfo = 'route_distance';
        } elseif ($count == 4) {
            // 소요 시간 순 정렬
            $routeInfo = 'route_time';
        } elseif ($count == 5) {
            // 라이딩 횟수 순 정렬
            $routeInfo = 'route_num_of_try_count';
        }

        return $routeInfo;
    }

    /**
     * [라이딩 경로] 코스 상세 페이지 값 조회
     *
     * @param int $route_id
     * @param int $route_like_user
     * @return mixed
     */
    public function routeDetailValue(
        int $route_id,
        int $route_like_user
    )
    {
        $routeLike = RouteLike::where('route_like_user', $route_like_user)
            ->where('route_like_obj', $route_id)
            ->get();

        // 좋아요 없을 때
        if (!($routeLike->isEmpty())) {
            $routeInfo = Route::join('route_likes', 'route_likes.route_like_user', '=', 'routes.route_user_id')
                ->select('routes.id',
                    'routes.route_user_id',
                    'routes.route_title',
                    'routes.route_like',
                    'routes.route_distance',
                    'routes.route_image',
                    'routes.route_time',
                    'routes.route_start_point_address',
                    'routes.route_end_point_address',
                    'routes.created_at',
                    'route_likes.route_like_user'
                )
                ->where('routes.id', $route_id)
                ->where('routes.route_user_id', $route_like_user)
                ->where('route_likes.route_like_obj', $route_id)
                ->get();
            // TODO 이미지 추가해야됨!!!

            return $routeInfo;
        }
        $routeInfo = Route::where('id', $route_id)
            ->where('route_user_id', $route_like_user)
            ->get();

        // 경로 이미지 출력
        $route_img = $routeInfo[0]->route_image;
        if (!($route_img == "null")) {
            $data = Storage::get('public/' . $route_img);
            $type = pathinfo('storage/' . $route_img, PATHINFO_EXTENSION);

            $routeInfo[0]['route_image'] = 'data:image/' . $type . ';base64,' . base64_encode($data);
        }

        return $routeInfo;
    }

    /**
     * 만들어진 경로의 시도 횟수
     *
     * @param int $rec_route_id
     * @return mixed
     */
    public function tryCountCheck(
        int $rec_route_id
    )
    {
        // rec_route_id 의 count 계산
        $tryCount = Record::where('rec_route_id', $rec_route_id)
            ->get()
            ->count();
        $param = [
            'route_num_of_try_count' => (int)$tryCount
        ];

        // 경로 가져옴
        return self::find($rec_route_id)->update($param);
    }

    /**
     * 만들어진 경로의 시도 인원수
     *
     * @param int $rec_route_id
     * @return int
     */
    public function tryUserCheck(
        int $rec_route_id
    )
    {
        // rec_user_id에 대한 값 반환
        $tryUser = Record::where('rec_route_id', $rec_route_id)
            ->pluck('rec_user_id');

        $tryUserArray = $tryUser->all();

        // rec_user_id 중복 제거된 값

        $count = count(array_unique($tryUserArray));

        $param = [
            'route_num_of_try_user' => $count
        ];

        // 경로 가져옴
        return self::find($rec_route_id)->update($param);
    }

    // 경로 저장 체크용
    public function routeSaveCheck(
        int $route_user_id,
        string $route_title
    )
    {
        return self::where('route_user_id', $route_user_id)
            ->where('route_title', $route_title)
            ->get();
    }
}
