<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TeamsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('teams')->insert([
            [
                'name' => 'Making',
                'slug' => Str::slug('Making'),
                'description' => 'Equipe de administradores e desenvolvimento',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Equipe Alpha',
                'slug' => Str::slug('Equipe Alpha'),
                'description' => 'Equipe Alpha description',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Equipe Beta',
                'slug' => Str::slug('Equipe Beta'),
                'description' => 'Equipe Beta description',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
