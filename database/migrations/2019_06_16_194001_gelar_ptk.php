<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class GelarPtk extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('gelar_ptk', function (Blueprint $table) {
			$table->uuid('gelar_ptk_id');
			$table->uuid('sekolah_id');
			$table->integer('gelar_akademik_id');
			$table->uuid('guru_id');
			$table->uuid('ptk_id');
			$table->timestamps();
			$table->softDeletes();
			$table->timestamp('last_sync');
			$table->foreign('sekolah_id')->references('sekolah_id')->on('sekolah')
                ->onUpdate('RESTRICT')->onDelete('RESTRICT');
			$table->foreign('guru_id')->references('guru_id')->on('guru')
                ->onUpdate('RESTRICT')->onDelete('RESTRICT');
			$table->foreign('gelar_akademik_id')->references('gelar_akademik_id')->on('ref.gelar_akademik')
                ->onUpdate('RESTRICT')->onDelete('RESTRICT');
            $table->primary('gelar_ptk_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('gelar_ptk');
    }
}
