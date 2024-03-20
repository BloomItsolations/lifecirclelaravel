<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterRewardsAddTdsAdminCharges extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('rewards', function (Blueprint $table) {
            $table->decimal('tds',10,2)->default(0)->after('amount');
            $table->decimal('admin_charges',10,2)->default(0)->after('tds');
            $table->decimal('credit',10,2)->default(0)->after('admin_charges');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('rewards', function (Blueprint $table) {
            $table->dropColumn(['tds','admin_charges','credit']);
        });
    }
}
