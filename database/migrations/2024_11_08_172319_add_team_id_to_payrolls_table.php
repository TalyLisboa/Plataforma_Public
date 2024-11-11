<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTeamIdToPayrollsTable extends Migration
{
    public function up()
    {
        Schema::table('payrolls', function (Blueprint $table) {
            if (!Schema::hasColumn('payrolls', 'team_id')) {
                $table->foreignId('team_id')->after('employee_id')->constrained('teams')->onDelete('cascade');
            }
        });
    }

    public function down()
    {
        Schema::table('payrolls', function (Blueprint $table) {
            // Remove a chave estrangeira e a coluna team_id
            $table->dropForeign(['team_id']);
            $table->dropColumn('team_id');
        });
    }
}
