<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPaymentDateAndMethodToPaymentReportsTable extends Migration
{
    /**
     * Executa as migrações.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('payment_reports', function (Blueprint $table) {
            // Adiciona a coluna 'payment_date' se não existir
            if (!Schema::hasColumn('payment_reports', 'payment_date')) {
                $table->date('payment_date')->after('year')->nullable();
            }

            // Adiciona a coluna 'payment_method' se não existir
            if (!Schema::hasColumn('payment_reports', 'payment_method')) {
                $table->string('payment_method')->after('payment_date')->nullable();
            }

            // Adiciona a coluna 'notes' se não existir (opcional)
            if (!Schema::hasColumn('payment_reports', 'notes')) {
                $table->text('notes')->nullable()->after('payment_method');
            }

            // Atualiza os campos monetários para não permitirem NULL (exceto onde necessário)
            $columnsToChange = [
                'amount',
                // Adicione outros campos monetários aqui, se necessário
            ];

            foreach ($columnsToChange as $column) {
                if (Schema::hasColumn('payment_reports', $column)) {
                    $table->decimal($column, 15, 2)->default(0.00)->nullable(false)->change();
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
        Schema::table('payment_reports', function (Blueprint $table) {
            // Remove as colunas adicionadas
            if (Schema::hasColumn('payment_reports', 'payment_date')) {
                $table->dropColumn('payment_date');
            }

            if (Schema::hasColumn('payment_reports', 'payment_method')) {
                $table->dropColumn('payment_method');
            }

            if (Schema::hasColumn('payment_reports', 'notes')) {
                $table->dropColumn('notes');
            }

            // Reverte os campos monetários para permitir NULL, se necessário
            $columnsToRevert = [
                'amount',
                // Adicione outros campos monetários aqui, se necessário
            ];

            foreach ($columnsToRevert as $column) {
                if (Schema::hasColumn('payment_reports', $column)) {
                    $table->decimal($column, 15, 2)->default(0.00)->nullable()->change();
                }
            }
        });
    }
}
