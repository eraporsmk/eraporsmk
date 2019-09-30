<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePekerjaanTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ref.pekerjaan', function (Blueprint $table) {
			$table->increments('pekerjaan_id');
			$table->string('nama', 25);
			$table->timestamps();
			$table->softDeletes();
			$table->timestamp('last_sync');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ref.pekerjaan');
    }
}
