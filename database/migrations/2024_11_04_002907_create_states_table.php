<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('states')) {
            Schema::create('states', function (Blueprint $table) {
                $table->id(); // Identificador único do estado (chave primária)
                $table->string('name', 100)->unique(); // Nome do estado, limitado a 100 caracteres e único
                $table->string('uf', 2)->unique(); // Sigla do estado (UF), deve ser única
                $table->timestamps(); // Campos 'created_at' e 'updated_at'

                // Adicionar índices adicionais se necessário
                $table->index('name'); // Índice para facilitar busca por nome
                $table->index('uf'); // Índice para facilitar busca por UF (já que é único)
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('states');
    }
};
