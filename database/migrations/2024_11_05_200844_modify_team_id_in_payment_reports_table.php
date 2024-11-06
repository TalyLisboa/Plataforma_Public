<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyTeamIdInPaymentReportsTable extends Migration
{
    public function up()
    {
        Schema::table('payment_reports', function (Blueprint $table) {
            // Remover o valor padrão 'NULL' e manter 'NOT NULL'
            $table->unsignedBigInteger('team_id')->nullable(false)->change();
        });
    }

    public function down()
    {
        Schema::table('payment_reports', function (Blueprint $table) {
            // Reverter para permitir 'NULL' se necessário
            $table->unsignedBigInteger('team_id')->nullable()->change();
        });
    }
}
