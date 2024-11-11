<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMissingColumnsToPayrollsTable extends Migration
{
    public function up()
    {
        Schema::table('payrolls', function (Blueprint $table) {
            // Add 'irrf' column if it doesn't exist
            if (!Schema::hasColumn('payrolls', 'irrf')) {
                $table->decimal('irrf', 10, 2)->default(0)->after('inss');
            }

            // Add 'fgts' column if it doesn't exist
            if (!Schema::hasColumn('payrolls', 'fgts')) {
                $table->decimal('fgts', 10, 2)->default(0)->after('irrf');
            }

            // Add any other missing columns similarly
            // ...
        });
    }

    public function down()
    {
        Schema::table('payrolls', function (Blueprint $table) {
            // Drop 'irrf' column if it exists
            if (Schema::hasColumn('payrolls', 'irrf')) {
                $table->dropColumn('irrf');
            }

            // Drop 'fgts' column if it exists
            if (Schema::hasColumn('payrolls', 'fgts')) {
                $table->dropColumn('fgts');
            }

            // Drop other columns if necessary
            // ...
        });
    }
}
