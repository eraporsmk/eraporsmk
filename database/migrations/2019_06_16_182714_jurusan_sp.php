<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class JurusanSp extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('jurusan_sp', function (Blueprint $table) {
			$table->uuid('jurusan_sp_id');
			$table->uuid('jurusan_sp_id_dapodik');
			$table->uuid('sekolah_id');
			$table->string('jurusan_id');
			$table->string('nama_jurusan_sp');
			$table->timestamps();
			$table->softDeletes();
			$table->timestamp('last_sync');
			$table->primary('jurusan_sp_id');
			$table->foreign('sekolah_id')->references('sekolah_id')->on('sekolah')
				->onUpdate('RESTRICT')->onDelete('RESTRICT');
			$table->foreign('jurusan_id')->references('jurusan_id')->on('ref.jurusan')
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
        Schema::dropIfExists('jurusan_sp');
    }
}
