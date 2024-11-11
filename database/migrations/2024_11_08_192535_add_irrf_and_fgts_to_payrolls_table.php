<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIrrfAndFgtsToPayrollsTable extends Migration
{
    public function up()
    {
        Schema::table('payrolls', function (Blueprint $table) {
            // Adicionar 'irrf' se não existir
            if (!Schema::hasColumn('payrolls', 'irrf')) {
                $table->decimal('irrf', 10, 2)->default(0)->after('inss');
            }

            // Adicionar 'fgts' se não existir
            if (!Schema::hasColumn('payrolls', 'fgts')) {
                $table->decimal('fgts', 10, 2)->default(0)->after('irrf');
            }
        });
    }

    public function down()
    {
        Schema::table('payrolls', function (Blueprint $table) {
            // Remover 'irrf' se existir
            if (Schema::hasColumn('payrolls', 'irrf')) {
                $table->dropColumn('irrf');
            }

            // Remover 'fgts' se existir
            if (Schema::hasColumn('payrolls', 'fgts')) {
                $table->dropColumn('fgts');
            }
        });
    }
}
