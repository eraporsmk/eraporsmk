<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Asesor extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('asesor', function (Blueprint $table) {
			$table->uuid('asesor_id');
			$table->uuid('sekolah_id');
            $table->uuid('guru_id');
			$table->uuid('dudi_id');
			$table->timestamps();
			$table->softDeletes();
			$table->timestamp('last_sync');
			$table->foreign('sekolah_id')->references('sekolah_id')->on('sekolah')
                ->onUpdate('NO ACTION')->onDelete('NO ACTION');
			$table->foreign('guru_id')->references('guru_id')->on('guru')
                ->onUpdate('NO ACTION')->onDelete('NO ACTION');
			$table->foreign('dudi_id')->references('dudi_id')->on('dudi')
                ->onUpdate('NO ACTION')->onDelete('NO ACTION');
			$table->primary('asesor_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('asesor');
    }
}
