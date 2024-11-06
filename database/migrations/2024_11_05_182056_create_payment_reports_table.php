<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentReportsTable extends Migration
{
    /**
     * Executa as migrações.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payment_reports', function (Blueprint $table) {
            $table->id();
            // Define o relacionamento com employees usando chave estrangeira
            $table->foreignId('employee_id')->constrained('employees')->onDelete('cascade');
            // Define o relacionamento com teams usando chave estrangeira
            $table->foreignId('team_id')->constrained('teams')->onDelete('cascade');
            
            $table->unsignedTinyInteger('month'); // 1-12
            $table->unsignedSmallInteger('year');
            $table->decimal('amount', 10, 2);
            $table->enum('status', ['pendente', 'pago', 'cancelado'])->default('pendente');
            $table->timestamps();

            // Índices para melhorar as consultas
            $table->index(['month', 'year']);
            $table->index('status');
        });
    }

    /**
     * Reverte as migrações.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('payment_reports');
    }
}
