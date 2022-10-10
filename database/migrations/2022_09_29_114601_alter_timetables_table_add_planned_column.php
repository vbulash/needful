<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
		Schema::table('timetables', function (Blueprint $table) {
			$table->integer('planned')->comment('Плановое количество практикантов');
		});
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
		Schema::table('timetables', function (Blueprint $table) {
			$table->dropColumn('planned');
		});
    }
};
