<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Database\Seeders\StateSeeder;
use Database\Seeders\CitySeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Cria ou atualiza o usuário administrador Felipe
        User::firstOrCreate([
            'email' => 'felipe@makingpublicidade.com.br',
        ], [
            'name' => 'Felipe',
            'email_verified_at' => now(),
            'password' => bcrypt('123456789'), 
            'is_admin' => true,
        ]);

        // Chama os seeders para popular outras tabelas
        $this->call([
            StateSeeder::class,
            CitySeeder::class,
            TeamsTableSeeder::class,
            TeamUserTableSeeder::class,
            DepartmentsTableSeeder::class,
            EmployeesTableSeeder::class,
            PaymentReportSeeder::class,
        ]);
    }
}
