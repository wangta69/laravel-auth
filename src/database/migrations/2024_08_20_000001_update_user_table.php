<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class  UpdateUserTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    if (Schema::hasTable('users')) {
      Schema::table('users', function($table)
      {
        $table->string('mobile', '15')->nullable()->after('remember_token');
        $table->integer('point')->nullable()->default(0)->after('remember_token');
        $table->tinyInteger('active')->nullable()->default(0)->after('remember_token')->comment('1: active, 0: not active');
        $table->timestamp('logined_at')->nullable()->after('updated_at');
        $table->softDeletes()->after('updated_at');

        $table->string('password')->nullable()->change();
      });
      // FULLTEXT INDEX `full` (`title`, `content`)
    }
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::table('users', function($table) {
      $table->dropColumn(['mobile', 'point', 'active', 'logined_at', 'deleted_at']);
      $table->string('password')->change();
      //password field nullable (for social login)
    });
    
      
  }
}
