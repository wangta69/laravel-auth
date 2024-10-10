<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class  CreateSocialAccountTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    if (!Schema::hasTable('social_accounts')) {
      Schema::create('social_accounts', function (Blueprint $table) {
        $table->id();
        $table->bigInteger('user_id')->unsigned();
        $table->string('name', '50')->nullable();
        $table->string('email', '50')->nullable();
        $table->string('provider', '32');
        $table->string('provider_id', '191');
        $table->text('token')->nullable();
        $table->text('refresh_token')->nullable();
        $table->string('avatar', '191')->nullable();
        $table->string('info')->nullable();
        $table->timestamps();
        $table->index(['provider', 'provider_id']);
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
      
    Schema::dropIfExists('social_accounts');
      
  }
}
