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
		Schema::table('histories', function (Blueprint $table) {
			$table->dropForeign(['student_id']);
			$table->dropColumn('student_id');
			$table->dropColumn('status');
		});
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
		Schema::table('histories', function (Blueprint $table) {
			$table->unsignedBigInteger('student_id')->comment('Связанный практикант');
			$table->foreign('student_id')->references('id')->on('students')->cascadeOnDelete();
			$table->enum('status', ['Планируется', 'Выполняется', 'Закрыта'])->comment('Статус практики');
		});
    }
};
