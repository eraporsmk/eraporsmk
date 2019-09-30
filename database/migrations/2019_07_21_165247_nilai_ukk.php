<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class NilaiUkk extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('nilai_ukk', function (Blueprint $table) {
			$table->uuid('nilai_ukk_id');
			$table->uuid('sekolah_id');
            $table->uuid('rencana_ukk_id');
			$table->uuid('anggota_rombel_id');
			$table->uuid('peserta_didik_id');
			$table->integer('nilai');
			$table->timestamps();
			$table->softDeletes();
			$table->timestamp('last_sync');
			$table->foreign('sekolah_id')->references('sekolah_id')->on('sekolah')
                ->onUpdate('NO ACTION')->onDelete('NO ACTION');
			$table->foreign('rencana_ukk_id')->references('rencana_ukk_id')->on('rencana_ukk')
                ->onUpdate('NO ACTION')->onDelete('NO ACTION');
			$table->foreign('anggota_rombel_id')->references('anggota_rombel_id')->on('anggota_rombel')
                ->onUpdate('NO ACTION')->onDelete('NO ACTION');
			$table->foreign('peserta_didik_id')->references('peserta_didik_id')->on('peserta_didik')
                ->onUpdate('NO ACTION')->onDelete('NO ACTION');
			$table->primary('nilai_ukk_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('nilai_ukk');
    }
}
