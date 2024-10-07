<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class  CreateUserLogTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    if (!Schema::hasTable('user_logs')) {
      Schema::create('user_logs', function(BluePrint $table) {
        $table->id();
        $table->bigInteger('user_id')->nullable()->unsigned()->index();
        $table->string('http_user_agent', '255')->nullable();
        $table->string('http_referer', '255')->nullable();
        $table->string('http_origin', '255')->nullable();
        $table->string('remote_addr', '255')->nullable();
        $table->timestamp('created_at');
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
    Schema::dropIfExists('user_logs');
  }
}
