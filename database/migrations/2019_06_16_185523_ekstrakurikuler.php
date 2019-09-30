<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Ekstrakurikuler extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ekstrakurikuler', function (Blueprint $table) {
			$table->uuid('ekstrakurikuler_id');
			$table->uuid('sekolah_id');
			$table->string('semester_id', 5);
			$table->uuid('guru_id');
			$table->string('nama_ekskul');
			$table->string('nama_ketua')->nullable();
			$table->string('nomor_kontak')->nullable();
			$table->string('alamat_ekskul')->nullable();
			$table->string('is_dapodik')->default('0')->nullable();
			$table->uuid('id_kelas_ekskul')->nullable();
			$table->uuid('rombongan_belajar_id');
			$table->uuid('ekstrakurikuler_id_migrasi')->nullable();
			$table->timestamps();
			$table->softDeletes();
			$table->timestamp('last_sync');
			$table->foreign('sekolah_id')->references('sekolah_id')->on('sekolah')
                ->onUpdate('RESTRICT')->onDelete('RESTRICT');
			$table->foreign('semester_id')->references('semester_id')->on('ref.semester')
                ->onUpdate('RESTRICT')->onDelete('RESTRICT');
			$table->foreign('guru_id')->references('guru_id')->on('guru')
                ->onUpdate('RESTRICT')->onDelete('RESTRICT');
			$table->foreign('rombongan_belajar_id')->references('rombongan_belajar_id')->on('rombongan_belajar')
                ->onUpdate('RESTRICT')->onDelete('RESTRICT');
			$table->primary('ekstrakurikuler_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ekstrakurikuler');
    }
}
