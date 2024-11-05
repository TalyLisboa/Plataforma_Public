<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCitiesTable extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('cities')) {
            Schema::create('cities', function (Blueprint $table) {
                $table->id(); // Identificador único da cidade (chave primária)
                $table->string('name', 150); // Nome da cidade
                $table->unsignedBigInteger('state_id'); // Chave estrangeira para states
                $table->integer('codigo_ibge')->unique(); // Código IBGE da cidade, deve ser único
                $table->decimal('latitude', 10, 8)->nullable(); // Latitude da cidade
                $table->decimal('longitude', 11, 8)->nullable(); // Longitude da cidade
                $table->boolean('capital')->default(false); // Se é capital (true ou false)
                $table->integer('codigo_uf'); // Código da unidade federativa
                $table->integer('siafi_id')->nullable(); // Código SIAFI da cidade
                $table->integer('ddd')->nullable(); // Código DDD da cidade
                $table->string('fuso_horario', 50)->nullable(); // Fuso horário da cidade
                $table->timestamps(); // Campos 'created_at' e 'updated_at'

                // Definir a chave estrangeira com a ação 'on delete cascade'
                $table->foreign('state_id')
                    ->references('id')
                    ->on('states')
                    ->onDelete('cascade')
                    ->onUpdate('cascade');

                // Garantir unicidade do nome da cidade dentro de um mesmo estado
                $table->unique(['name', 'state_id']);

                // Índices para melhorar a performance de consultas
                $table->index('name');
                $table->index('state_id');
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('cities');
    }
}
