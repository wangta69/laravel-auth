<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserPointTable extends Migration
{
    public function up()
    {
        if (! Schema::hasTable('user_points')) {
            Schema::create('user_points', function (Blueprint $table) {
                $table->id();
                $table->bigInteger('user_id')->index()->unsigned();
                $table->bigInteger('point')->default(0); // 증감액 (+/-)
                $table->bigInteger('cur_sum')->default(0)->unsigned()->comment('변동 후 잔액');
                $table->string('item', 20)->comment('구분 (charge, use, event)');
                $table->string('sub_item', 20)->nullable()->comment('상세 구분');
                $table->bigInteger('rel_item')->nullable()->unsigned()->comment('참조 ID');

                // [고도화 추가] 유료 여부 및 만료일
                $table->boolean('is_paid')->default(false)->comment('true:유상, false:무상');
                $table->timestamp('expires_at')->nullable()->comment('소멸 예정일');

                $table->timestamp('created_at')->index();
            });
        }
        // 이미 테이블이 있다면 컬럼 추가 (패키지 업데이트 대응)
        else {
            Schema::table('user_points', function (Blueprint $table) {
                if (! Schema::hasColumn('user_points', 'is_paid')) {
                    $table->boolean('is_paid')->default(false)->after('rel_item');
                }
                if (! Schema::hasColumn('user_points', 'expires_at')) {
                    $table->timestamp('expires_at')->nullable()->after('is_paid');
                }
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('user_points');
    }
}
