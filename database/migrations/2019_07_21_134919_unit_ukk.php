<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UnitUkk extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ref.unit_ukk', function (Blueprint $table) {
			$table->uuid('unit_ukk_id');
			$table->uuid('sekolah_id')->nullable();
			$table->uuid('paket_ukk_id');
			$table->string('kode_unit');
			$table->string('nama_unit');
			$table->timestamps();
			$table->softDeletes();
			$table->timestamp('last_sync');
			$table->foreign('sekolah_id')->references('sekolah_id')->on('sekolah')
                ->onUpdate('NO ACTION')->onDelete('NO ACTION');
			$table->foreign('paket_ukk_id')->references('paket_ukk_id')->on('ref.paket_ukk')
                ->onUpdate('NO ACTION')->onDelete('NO ACTION');
			$table->primary('unit_ukk_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ref.unit_ukk');
    }
}
