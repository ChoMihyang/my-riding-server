<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStatsTable extends Migration
{
    /**
     * stats Table
     *
     * id - 통계 레코드 번호
     * stat_user_id - 현 사용자 id
     * stat_date - 현 통계에 해당하는 주행 날짜
     * stat_week - 현 통계에 해당하는 날짜의 주차
     * stat_day - 현 통계에 해당하는 날짜의 요일
     * stat_distance - 현 통계에 해당하는 주행 거리
     * stat_time - 현 통계에 해당하는 주행 시간
     * stat_avg_speed - 현 통계에 해당하는 주행 기록 중 평균 속도
     * stat_max_speed - 현 통계에 해당하는 주행 기록 중 최고 속도
     * stat_count - 현 사용자의 주행 통계 수
     * stat_year - 현 통계에 해당하는 날짜의 연도
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stats', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('stat_user_id');
            $table->foreign('stat_user_id')->references('id')->on('users');
            $table->dateTime('stat_date');
            $table->unsignedBigInteger('stat_week');
            $table->unsignedBigInteger('stat_day');
            $table->unsignedDouble('stat_distance');
            $table->unsignedBigInteger('stat_time');
            $table->unsignedDouble('stat_avg_speed');
            $table->unsignedDouble('stat_max_speed');
            $table->unsignedBigInteger('stat_count');
            $table->unsignedBigInteger('stat_year');
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
        Schema::dropIfExists('stats');
    }
}
