<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterPinListsAddPlacementSide extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pin_lists', function (Blueprint $table) {
            $table->enum('side', ['left', 'right'])->default('left')->after('pin_number');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pin_lists', function (Blueprint $table) {
            $table->dropColumn( 'side' );
        });
    }
}
