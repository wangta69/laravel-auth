<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class InsertMenuForPondolAuth extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('manage_menus')) {
            DB::table('manage_menus')->updateOrInsert(
                ['type' => 'lnb', 'title' => 'pondol-auth'], ['component' => 'pondol-auth::lnb-partial', 'order' => '1']
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
