<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class StateSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Lista de estados brasileiros com suas siglas
        $states = [
            ['name' => 'Acre', 'uf' => 'AC'],
            ['name' => 'Alagoas', 'uf' => 'AL'],
            ['name' => 'Amapá', 'uf' => 'AP'],
            ['name' => 'Amazonas', 'uf' => 'AM'],
            ['name' => 'Bahia', 'uf' => 'BA'],
            ['name' => 'Ceará', 'uf' => 'CE'],
            ['name' => 'Distrito Federal', 'uf' => 'DF'],
            ['name' => 'Espírito Santo', 'uf' => 'ES'],
            ['name' => 'Goiás', 'uf' => 'GO'],
            ['name' => 'Maranhão', 'uf' => 'MA'],
            ['name' => 'Mato Grosso', 'uf' => 'MT'],
            ['name' => 'Mato Grosso do Sul', 'uf' => 'MS'],
            ['name' => 'Minas Gerais', 'uf' => 'MG'],
            ['name' => 'Pará', 'uf' => 'PA'],
            ['name' => 'Paraíba', 'uf' => 'PB'],
            ['name' => 'Paraná', 'uf' => 'PR'],
            ['name' => 'Pernambuco', 'uf' => 'PE'],
            ['name' => 'Piauí', 'uf' => 'PI'],
            ['name' => 'Rio de Janeiro', 'uf' => 'RJ'],
            ['name' => 'Rio Grande do Norte', 'uf' => 'RN'],
            ['name' => 'Rio Grande do Sul', 'uf' => 'RS'],
            ['name' => 'Rondônia', 'uf' => 'RO'],
            ['name' => 'Roraima', 'uf' => 'RR'],
            ['name' => 'Santa Catarina', 'uf' => 'SC'],
            ['name' => 'São Paulo', 'uf' => 'SP'],
            ['name' => 'Sergipe', 'uf' => 'SE'],
            ['name' => 'Tocantins', 'uf' => 'TO'],
        ];

        // Adicionar timestamps para 'created_at' e 'updated_at'
        $timestamp = Carbon::now();
        foreach ($states as &$state) {
            $state['created_at'] = $timestamp;
            $state['updated_at'] = $timestamp;
        }

        // Usar upsert para inserir ou atualizar os estados
        DB::table('states')->upsert($states, ['uf'], ['name', 'updated_at']);

        echo "Estados inseridos ou atualizados com sucesso.\n";
    }
}
