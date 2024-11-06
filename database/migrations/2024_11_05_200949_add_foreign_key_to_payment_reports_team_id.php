<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeyToPaymentReportsTeamId extends Migration
{
    public function up()
    {
        Schema::table('payment_reports', function (Blueprint $table) {
            // Adiciona a chave estrangeira após garantir que todos os team_id são válidos
            $table->foreign('team_id')->references('id')->on('teams')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('payment_reports', function (Blueprint $table) {
            $table->dropForeign(['team_id']);
        });
    }
}
