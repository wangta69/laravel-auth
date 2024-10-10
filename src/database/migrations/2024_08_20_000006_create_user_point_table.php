<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class  CreateUserPointTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    if (!Schema::hasTable('user_points')) {
      Schema::create('user_points', function(BluePrint $table) {
        $table->id();
        $table->bigInteger('user_id')->index()->unsigned();
        $table->bigInteger('point')->default(0)->unsigned();
        $table->bigInteger('cur_sum')->default(0)->unsigned()->comment('users.point + users.hold_point 와 동일한 값이 되어야 함');
        $table->string('item', '20')->comment('이벤트, 구매포인트');
        $table->string('sub_item', '20')->nullable()->comment('item의 세부정, buy, event..');
        $table->bigInteger('rel_item')->nullable()->unsigned()->comment('주로 참조 테이블 아이디');
        $table->timestamp('created_at')->index();
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
      
    Schema::dropIfExists('user_points');
      
  }
}
