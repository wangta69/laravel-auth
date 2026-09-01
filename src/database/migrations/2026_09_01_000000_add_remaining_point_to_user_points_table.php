<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('user_points', function (Blueprint $table) {
            // 1. 잔액 관리를 위한 컬럼 추가
            // point 컬럼 바로 뒤에 생성하여 가독성을 높입니다.
            $table->bigInteger('remaining_point')
                ->default(0)
                ->after('point')
                ->comment('해당 적립 건에서 사용 가능한 잔액');

            // 2. FIFO 조회 최적화를 위한 복합 인덱스 추가
            // [유저ID -> 잔액여부 -> 만료일순]으로 스캔하도록 설계
            $table->index(['user_id', 'remaining_point', 'expires_at'], 'idx_user_points_remaining_search');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_points', function (Blueprint $table) {
            // 인덱스 먼저 삭제 후 컬럼 삭제
            $table->dropIndex('idx_user_points_remaining_search');
            $table->dropColumn('remaining_point');
        });
    }
};
