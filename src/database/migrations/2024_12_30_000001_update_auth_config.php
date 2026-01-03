<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class UpdateAuthConfig extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('json_key_values')) {
            DB::table('json_key_values')->updateOrInsert(
                ['key' => 'auth'], ['v' => '{"activate":"email","template":{"user":"default","mail":"default"},"point":{"register":"0","login":"0"}}']
            );
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {}
}
