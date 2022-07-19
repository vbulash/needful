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
        Schema::create('schools', function (Blueprint $table) {
            $table->id();
			$table->string('name')->comment('Наименование учебного заведения');
			$table->tinyInteger('type')->default(2)->comment('Тип учебного заведения');
			$table->tinyInteger('status')->default(0)->comment('Статус активности объекта');
			$table->string('contact')->comment('Контактное лицо')->nullable();
			$table->string('phone')->comment('Телефон')->nullable();
			$table->string('email')->comment('Электронная почта')->nullable();
			$table->string('inn')->comment('ИНН')->nullable();
			$table->string('kpp')->comment('КПП')->nullable();
			$table->string('ogrn')->comment('ОГРН / ОГРНИП')->nullable();
			$table->string('official_address')->comment('Юридический адрес')->nullable();
			$table->string('post_address')->comment('Почтовый адрес')->nullable();
			//
			$table->unsignedBigInteger('user_id')->comment('Связанный пользователь');
			$table->foreign('user_id')->references('id')->on('users');
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
        Schema::dropIfExists('schools');
    }
};
