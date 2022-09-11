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
		Schema::table('employers', function (Blueprint $table) {
			$table->string('short', 40)->nullable()->comment('Краткое наименование работодателя');
		});
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
		Schema::table('employers', function (Blueprint $table) {
			$table->dropColumn('short');
		});
    }
};
