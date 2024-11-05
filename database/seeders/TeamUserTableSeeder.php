<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Team;

class TeamUserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Criar usuários fictícios se não existirem, verificando pelo e-mail
        $adminUser = User::firstOrCreate(
            ['email' => 'felipe@makingpublicidade.com.br'],
            [
                'name' => 'Felipe',
                'password' => bcrypt('123456789'),
                'is_admin' => true,
            ]
        );

        $memberUser = User::firstOrCreate(
            ['email' => 'member@example.com'],
            [
                'name' => 'Talita',
                'password' => bcrypt('password'),
                'is_admin' => false,
            ]
        );

        // Obtendo o ID do time 'Making'
        $teamId = DB::table('teams')->where('name', 'Making')->value('id');      

        // Adicionando usuários ao time 'Making'
        if ($teamId) {
            $users = [
                [
                    'team_id' => $teamId,
                    'user_id' => $adminUser->id,
                    'role' => 'admin',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'team_id' => $teamId,
                    'user_id' => $memberUser->id,
                    'role' => 'member',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ];

            foreach ($users as $userTeam) {
                DB::table('team_user')->updateOrInsert(
                    ['team_id' => $userTeam['team_id'], 'user_id' => $userTeam['user_id']],
                    $userTeam
                );
            }
        }
    }
}
