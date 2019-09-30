<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->uuid('user_id');
            $table->uuid('sekolah_id')->nullable();
			$table->string('name');
            $table->string('nisn')->nullable();
			$table->string('nuptk')->nullable();
			$table->string('email')->unique();
            $table->string('password');
			$table->string('password_dapo');
            $table->rememberToken();
			$table->timestamp('last_login_at')->nullable();
			$table->string('last_login_ip')->nullable();
			$table->string('photo')->nullable();
			$table->integer('active')->default('1');
			$table->uuid('peserta_didik_id')->nullable();
			$table->uuid('guru_id')->nullable();
            $table->timestamps();
			$table->softDeletes();
			$table->timestamp('last_sync');
			$table->primary('user_id');
			$table->foreign('sekolah_id')->references('sekolah_id')->on('sekolah')
				->onUpdate('RESTRICT')->onDelete('RESTRICT');
			$table->foreign('peserta_didik_id')->references('peserta_didik_id')->on('peserta_didik')
				->onUpdate('RESTRICT')->onDelete('RESTRICT');
			$table->foreign('guru_id')->references('guru_id')->on('guru')
				->onUpdate('RESTRICT')->onDelete('RESTRICT');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
