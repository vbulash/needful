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
        Schema::create('employers', function (Blueprint $table) {
            $table->id();
			$table->string('name');
			$table->string('contact')->nullable();
			$table->string('address')->nullable();
			$table->string('phone')->nullable();
			$table->string('email', 50)->nullable();
			$table->string('inn')->nullable();
			$table->string('kpp')->nullable();
			$table->string('ogrn')->nullable();
			$table->string('offficial_address')->nullable();
			$table->string('post_address')->nullable();
			$table->text('description')->nullable();
			$table->text('expectation')->nullable();
			$table->string('nda')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('employers');
    }
};
