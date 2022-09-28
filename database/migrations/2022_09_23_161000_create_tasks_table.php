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
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
			$table->uuid('uuid')->comment('Глобальный идентификатор задачи');
			$table->string('title')->comment('Заголовок задачи');
			$table->text('description')->comment('Текст описания задачи');
			$table->string('route')->nullable()->comment('Маршрут объекта задачи');
			//
			$table->unsignedBigInteger('from_id')->nullable()->comment('Пользователь-источник задачи');
			$table->foreign('from_id')->references('id')->on('users')->cascadeOnDelete();
			//
			$table->unsignedBigInteger('to_id')->nullable()->comment('Пользователь-приёмник задачи');
			$table->foreign('to_id')->references('id')->on('users')->cascadeOnDelete();
			//
			$table->boolean('fromadmin')->default(false)->comment('Пользователь-автор задачи = администратор');
			$table->boolean('toadmin')->default(false)->comment('Пользователь-приёмник задачи = администратор');
			$table->tinyInteger('type')->default(0)->comment('Тип задачи');
			$table->boolean('read')->default(false)->comment('Признак прочтенного сообщения задачи');
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
        Schema::dropIfExists('tasks');
    }
};
