<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRoutesTable extends Migration
{
    /**
     * routes Table
     *
     * id - 경로 레코드 번호
     * route_user_id - 현 경로를 생성한 사용자
     * route_title - 현 경로의 이름
     * route_distance - 현 경로의 출발점과 도착점 사이의 거리
     * route_time - 현 경로의 주행 시 걸리는 시간
     * route_like - 현 경로의 좋아요 수
     * route_num_of_try_count - 사용자들이 현 경로를 주행한 (누적) 수
     * route_num_of_try_user - 현 경로를 주행한 사용자 수
     * route_start_point_address - 현 경로의 출발점
     * route_end_point_address - 현 경로의 도착점
     * route_avg_degree - 현 경로의 평균 경사도
     * route_max_altitude - 현 경로의 최고 고도
     * route_min_altitude - 현 경로의 최저 고도
     *
     */
    public function up()
    {
        Schema::create('routes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('route_user_id');
            $table->foreign('route_user_id')->references('id')->on('users');
            $table->string('route_title');
            $table->string('route_image');
            $table->unsignedDouble('route_distance');
            $table->unsignedBigInteger('route_time');
            $table->unsignedBigInteger('route_like');
            $table->unsignedBigInteger('route_num_of_try_count');
            $table->unsignedBigInteger('route_num_of_try_user');
            $table->string('route_start_point_address');
            $table->string('route_end_point_address');
            $table->unsignedDouble('route_avg_degree');
            $table->unsignedDouble('route_max_altitude');
            $table->unsignedDouble('route_min_altitude');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('routes');
    }
}
