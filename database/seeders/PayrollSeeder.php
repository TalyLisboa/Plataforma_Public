<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Payroll;
use App\Models\Employee;
use App\Models\Team;
use Faker\Factory as Faker;

class PayrollSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();

        // Obter todos os times e funcionários
        $teams = Team::all();
        $employees = Employee::all();

        // Criar 100 folhas de pagamento fictícias
        foreach (range(1, 100) as $index) {
            $team = $teams->random();
            $employee = $employees->random();

            Payroll::create([
                'team_id' => $team->id,
                'employee_id' => $employee->id,
                'salary_amount' => $faker->randomFloat(2, 1000, 5000),
                'deductions' => $faker->randomFloat(2, 0, 500),
                'bonuses' => $faker->randomFloat(2, 0, 500),
                'net_pay' => $faker->randomFloat(2, 1000, 5000),
                'payment_date' => $faker->date(),
                'payment_method' => $faker->randomElement(['transfer', 'check', 'cash']),
                'comments' => $faker->sentence(),
            ]);
        }
    }
}
