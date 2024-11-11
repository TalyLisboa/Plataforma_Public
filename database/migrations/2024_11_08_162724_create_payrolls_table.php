<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePayrollsTable extends Migration
{
    /**
     * Executa as migrações.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payrolls', function (Blueprint $table) {
            $table->id();

            // Chaves estrangeiras
            $table->foreignId('employee_id')->constrained('employees')->onDelete('cascade');
            $table->foreignId('team_id')->constrained('teams')->onDelete('cascade');

            // Campos monetários armazenados como inteiros (centavos)
            $table->unsignedBigInteger('salary_amount')->default(0);
            $table->unsignedBigInteger('inss')->default(0);
            $table->unsignedBigInteger('irrf')->default(0);
            $table->unsignedBigInteger('fgts')->default(0);
            $table->unsignedBigInteger('deductions')->default(0);
            $table->unsignedBigInteger('other_deductions')->default(0)->nullable();
            $table->unsignedBigInteger('bonuses')->default(0);
            $table->unsignedBigInteger('net_pay')->default(0);

            // Outros campos
            $table->date('payment_date');
            $table->string('payment_method');
            $table->text('comments')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverte as migrações.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('payrolls');
    }
}
