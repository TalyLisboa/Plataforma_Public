<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class ModifyPayrollsTable extends Migration
{
    /**
     * Executa as migrações.
     *
     * @return void
     */
    public function up()
    {
        // Adicionar novas colunas se não existirem
        Schema::table('payrolls', function (Blueprint $table) {
            // Adicionar coluna 'payment_date' apenas se não existir
            if (!Schema::hasColumn('payrolls', 'payment_date')) {
                $table->date('payment_date')->after('net_pay');
            }

            // Adicionar coluna 'payment_method' apenas se não existir
            if (!Schema::hasColumn('payrolls', 'payment_method')) {
                $table->string('payment_method')->after('payment_date');
            }

            // Adicionar coluna 'comments' apenas se não existir
            if (!Schema::hasColumn('payrolls', 'comments')) {
                $table->text('comments')->nullable()->after('payment_method');
            }
        });

        // Lista de colunas monetárias para modificar
        $columnsToChange = [
            'salary_amount',
            'inss',
            'irrf',
            'fgts',
            'deductions',
            'other_deductions',
            'bonuses',
            'net_pay',
        ];

        // Atualizar valores NULL para 0 antes de alterar colunas para não-nuláveis (exceto 'other_deductions')
        foreach ($columnsToChange as $column) {
            if ($column !== 'other_deductions' && Schema::hasColumn('payrolls', $column)) {
                DB::table('payrolls')->whereNull($column)->update([$column => 0]);
            }
        }

        // Alterar as colunas para decimal(10,2) com valores padrão de 0
        Schema::table('payrolls', function (Blueprint $table) use ($columnsToChange) {
            foreach ($columnsToChange as $column) {
                if (Schema::hasColumn('payrolls', $column)) {
                    if ($column === 'other_deductions') {
                        // Manter como nullable para permitir outros descontos
                        $table->decimal($column, 10, 2)->default(0)->nullable()->change();
                    } else {
                        // Definir como não-nulável
                        $table->decimal($column, 10, 2)->default(0)->nullable(false)->change();
                    }
                }
            }
        });
    }

    /**
     * Reverte as migrações.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('payrolls', function (Blueprint $table) {
            // Lista de colunas monetárias para reverter
            $columnsToRevert = [
                'salary_amount',
                'inss',
                'irrf',
                'fgts',
                'deductions',
                'other_deductions',
                'bonuses',
                'net_pay',
            ];

            foreach ($columnsToRevert as $column) {
                if (Schema::hasColumn('payrolls', $column)) {
                    if ($column === 'other_deductions') {
                        // Reverter para nullable se necessário
                        $table->decimal($column, 10, 2)->default(0)->nullable()->change();
                    } else {
                        // Reverter para não-nullable sem valor padrão
                        $table->decimal($column, 10, 2)->default(0)->nullable(false)->change();
                    }
                }
            }

            // Opcionalmente, remover colunas adicionadas
            if (Schema::hasColumn('payrolls', 'payment_date')) {
                $table->dropColumn('payment_date');
            }

            if (Schema::hasColumn('payrolls', 'payment_method')) {
                $table->dropColumn('payment_method');
            }

            if (Schema::hasColumn('payrolls', 'comments')) {
                $table->dropColumn('comments');
            }
        });
    }
}
