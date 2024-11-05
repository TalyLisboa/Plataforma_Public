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
        // Limpa a tabela de employees antes de rodar o seeder
        DB::table('employees')->truncate();

        // Cria 100 funcionários fictícios
        Employee::factory()->count(100)->create();
    }
}
