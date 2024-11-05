<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DepartmentsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obtém o ID do time 'Making'
        $makingTeam = DB::table('teams')->where('name', 'Making')->first();

        DB::table('departments')->insert([
            [
                'team_id' => $makingTeam->id ?? 1, // Usa 1 como fallback
                'name' => 'Recursos Humanos',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'team_id' => $makingTeam->id ?? 1,
                'name' => 'Desenvolvimento',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'team_id' => $makingTeam->id ?? 1,
                'name' => 'Marketing',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'team_id' => $makingTeam->id ?? 1,
                'name' => 'Financeiro',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'team_id' => $makingTeam->id ?? 1,
                'name' => 'Vendas',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'team_id' => $makingTeam->id ?? 1,
                'name' => 'Suporte Técnico',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
