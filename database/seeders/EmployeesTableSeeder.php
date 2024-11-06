<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Employee;
use Illuminate\Support\Facades\DB;

class EmployeesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Desabilita as restrições de chave estrangeira temporariamente
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Limpa a tabela de employees antes de rodar o seeder
        DB::table('employees')->truncate();

        // Reativa as restrições de chave estrangeira
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Cria 100 funcionários fictícios
        Employee::factory()->count(100)->create();
    }
}
