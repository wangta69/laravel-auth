<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class  CreateRoleTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    if (!Schema::hasTable('roles')) {
      Schema::create('roles', function (Blueprint $table) {
        $table->id();
        $table->string('name')->unique();
      });

      DB::table('roles')->insert(
        [
          ['name' => 'administrator'],
          ['name' => 'manager'],
          ['name' => 'user']
        ]
      );
    };

    if (!Schema::hasTable('user_roles')) {
      Schema::create('user_roles', function (Blueprint $table) {
        $table->bigInteger('user_id')->nullable()->unsigned();
        $table->bigInteger('role_id')->nullable()->unsigned();
        $table->unique(['user_id', 'role_id']);
        $table->index(['user_id']);
        $table->foreign('role_id')->references('id')->on('roles');
        $table->foreign('user_id')->references('id')->on('users');
      });
    };
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::table('user_roles', function($table) {
      $table->dropForeign('user_roles_role_id_foreign');
      $table->dropForeign('user_roles_user_id_foreign');
    });
    
    Schema::dropIfExists('user_roles');
    Schema::dropIfExists('roles');
      
  }
}
