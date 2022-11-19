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
		Schema::table('internships', function (Blueprint $table) {
			$table->text('short')->nullable()->comment('Краткое содержание программы практики');
		});
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
		Schema::table('internships', function (Blueprint $table) {
			$table->dropColumn('short');
		});
    }
};
