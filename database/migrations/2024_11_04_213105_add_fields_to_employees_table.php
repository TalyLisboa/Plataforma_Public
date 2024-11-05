<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            // Adicionando novos campos
            $table->enum('contract_type', ['CLT', 'CNPJ'])->after('email')->default('CLT');
            $table->string('phone')->nullable()->after('contract_type');
            $table->enum('marital_status', ['single', 'married', 'divorced', 'widowed'])->nullable()->after('phone');
            $table->string('nationality')->nullable()->after('marital_status');
            
            // Tornando 'middle_name' opcional
            $table->string('middle_name')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            // Removendo os campos adicionados
            $table->dropColumn(['contract_type', 'phone', 'marital_status', 'nationality']);
            
            // Revertendo 'middle_name' para ser obrigatório
            $table->string('middle_name')->nullable(false)->change();
        });
    }
};
