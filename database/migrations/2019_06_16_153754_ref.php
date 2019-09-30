<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Pacuna\Schemas\Facades\PGSchema;
class Ref extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        PGSchema::create('ref');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        PGSchema::drop('ref');
    }
}
