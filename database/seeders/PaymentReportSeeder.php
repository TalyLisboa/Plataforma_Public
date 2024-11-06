<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PaymentReport;
use App\Models\Employee;
use App\Models\Team;
use Faker\Factory as Faker;

class PaymentReportSeeder extends Seeder
{
    public function run()
    {
        // Obtém todos os funcionários e equipes existentes
        $employees = Employee::all();
        $teams = Team::all();
        $faker = Faker::create();

        // Cria 100 relatórios fictícios de pagamento
        foreach (range(1, 1000) as $index) {
            PaymentReport::create([
                'team_id' => $teams->random()->id,
                'employee_id' => $employees->random()->id,
                'month' => $faker->numberBetween(1, 12),
                'year' => $faker->numberBetween(2000, 2024),
                'amount' => $faker->randomFloat(2, 1000, 10000), // Valores entre 1000 e 10000
                'status' => $faker->randomElement(['pendente', 'pago', 'cancelado']),
            ]);
        }
    }
}
