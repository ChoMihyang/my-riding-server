<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNotificationsTable extends Migration
{
    /**
     * notification Table
     *
     * id - 알림 레코드 번호
     * noti_user_id - 현 알림을 받은 사용자 id
     * noti_type - 알림의 유형
     * noti_msg - 알림의 유형에 따른 메시지
     * noti_url - 알림의 유형에 따른 이동 페이지 주소
     * noti_check - 사용자의 현 알림 확인 유뮤
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('noti_user_id');
            $table->foreign('noti_user_id')->references('id')->on('users');
            $table->string('noti_type');
            $table->string('noti_msg');
            $table->string('noti_url');
            $table->boolean('noti_check');
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
        Schema::dropIfExists('notifications');
    }
}
