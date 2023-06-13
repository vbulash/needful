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
        Schema::table('answers', function (Blueprint $table) {
			$table->unsignedBigInteger('contract_id')->nullable()->comment('Связанный договор');
			$table->foreign('contract_id')->references('id')->on('contracts')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
		Schema::table('answers', function (Blueprint $table) {
			$table->dropForeign(['contract_id']);
			$table->dropColumn('contract_id');
        });
    }
};
