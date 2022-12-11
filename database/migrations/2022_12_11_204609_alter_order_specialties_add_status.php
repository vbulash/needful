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
		Schema::table('orders_specialties', function (Blueprint $table) {
			$table->tinyInteger('status')->default(0)->comment('Статус специальности в заявке на практику');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
		Schema::table('orders_specialties', function (Blueprint $table) {
			$table->dropColumn('status');
        });
    }
};
