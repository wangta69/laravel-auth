<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddEmailStatusToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // 테이블이 존재할 때만 실행
        if (Schema::hasTable('users')) {
            Schema::table('users', function (Blueprint $table) {
                // 1. 반송 처리용 (기존 데이터는 true로 시작)
                if (! Schema::hasColumn('users', 'email_valid')) {
                    $table->boolean('email_valid')->default(true)->after('email');
                }

                // 2. 수신 거부 처리용 (기존 데이터는 수신 동의 상태인 true로 시작)
                if (! Schema::hasColumn('users', 'is_subscribed')) {
                    $table->boolean('is_subscribed')->default(true)->after('email_valid');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasTable('users')) {
            Schema::table('users', function (Blueprint $table) {
                $columns = [];
                if (Schema::hasColumn('users', 'email_valid')) {
                    $columns[] = 'email_valid';
                }
                if (Schema::hasColumn('users', 'is_subscribed')) {
                    $columns[] = 'is_subscribed';
                }

                if (! empty($columns)) {
                    $table->dropColumn($columns);
                }
            });
        }
    }
}
