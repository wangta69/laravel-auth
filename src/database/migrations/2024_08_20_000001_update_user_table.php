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
        if (!Schema::hasColumn('users', 'mobile')) {
          Schema::table('users', function (Blueprint $table) {
            $table->string('mobile', '15')->nullable()->after('remember_token');
          });
        } else {
          // Schema::table('users', function (Blueprint $table) {
          //   $table->string('mobile', '15')->nullable()->after('remember_token')->change();
          // });
        }

        if (!Schema::hasColumn('users', 'point')) {
          Schema::table('users', function (Blueprint $table) {
            $table->integer('point')->unsigned()->nullable()->default(0)->after('remember_token');
          });
        } else {
          // Schema::table('users', function (Blueprint $table) {
          //   $table->integer('point')->nullable()->default(0)->after('remember_token')->change();
          // });
        }

        if (!Schema::hasColumn('users', 'active')) {
          Schema::table('users', function (Blueprint $table) {
            $table->tinyInteger('active')->unsigned()->nullable()->default(0)->after('remember_token')->comment('0: not active, 1: active, 2:prohibit  9:roll out ');
          });
        } else {
          // Schema::table('users', function (Blueprint $table) {
          //   $table->boolean('active')->nullable()->default(0)->after('remember_token')->comment('1: active, 0: not active')->change();
          // });
        }

        if (!Schema::hasColumn('users', 'logined_at')) {
          Schema::table('users', function (Blueprint $table) {
            $table->timestamp('logined_at')->nullable()->after('updated_at');
          });
        } else {
          // Schema::table('users', function (Blueprint $table) {
          //   $table->timestamp('logined_at')->nullable()->after('updated_at')->change();
          // });
        }

        if (!Schema::hasColumn('users', 'deleted_at')) {
          Schema::table('users', function (Blueprint $table) {
            $table->softDeletes()->after('updated_at');
            // $table->timestamp('deleted_at')->after('updated_at');
          });
        } else {
          // Schema::table('users', function (Blueprint $table) {
          //   $table->softDeletes()->after('updated_at')->change();
          // });
        }

        if (!Schema::hasColumn('users', 'password')) {
          Schema::table('users', function (Blueprint $table) {
            $table->string('password')->nullable();
          });
        } else {
          Schema::table('users', function (Blueprint $table) {
            $table->string('password')->nullable()->change();
          });
        }
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
