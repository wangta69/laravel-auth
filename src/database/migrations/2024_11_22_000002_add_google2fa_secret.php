<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddGoogle2faSecret extends Migration
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
        if (!Schema::hasColumn('users', 'google2fa_secret')) {
          Schema::table('users', function (Blueprint $table) {
            $table->string('google2fa_secret')->nullable()->after('remember_token');
          });
        } else {
          // Schema::table('users', function (Blueprint $table) {
          //   $table->string('google2fa_secret')->nullable()->after('remember_token')->change();
          // });
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
    Schema::table('users', function($table) {
      $table->dropColumn(['google2fa_secret']);
    });
  }
}
