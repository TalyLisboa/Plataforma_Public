<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUniqueConstraintToUfInStatesTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('states', function (Blueprint $table) {
            $table->unique('uf');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('states', function (Blueprint $table) {
            $table->dropUnique(['uf']);
        });
    }
}
