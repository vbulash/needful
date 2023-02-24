<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		Schema::table('histories', function (Blueprint $table) {
			$table->unsignedBigInteger('school_id')->nullable()->comment('Связанное учебное заведение');
			$table->foreign('school_id')->references('id')->on('schools')->nullOnDelete();
			$table->tinyInteger('initiator')->default(0);
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down() {
		Schema::table('histories', function (Blueprint $table) {
			$table->dropColumn('initiator');
			$table->dropForeign('school_id');
		});
	}
};