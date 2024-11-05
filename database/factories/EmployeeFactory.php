<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Team;
use App\Models\State;
use App\Models\City;
use App\Models\Department;

class EmployeeFactory extends Factory
{
    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        // Seleciona aleatoriamente um time
        $team = Team::inRandomOrder()->first();
        // Seleciona aleatoriamente um estado
        $state = State::inRandomOrder()->first();
        // Seleciona uma cidade que pertence ao estado selecionado
        $city = City::where('state_id', $state->id)->inRandomOrder()->first();
        // Seleciona aleatoriamente um departamento
        $department = Department::inRandomOrder()->first();

        return [
            'team_id' => $team ? $team->id : 1,
            'state_id' => $state ? $state->id : 1,
            'city_id' => $city ? $city->id : 1,
            'department_id' => $department ? $department->id : 1,
            'first_name' => $this->faker->firstName(),
            'last_name' => $this->faker->lastName(),
            'email' => $this->faker->unique()->safeEmail(),
            'middle_name' => $this->faker->firstName(),
            'address' => $this->faker->address(),
            'zip_code' => $this->faker->postcode(),
            'date_of_birth' => $this->faker->date('Y-m-d', '2000-01-01'),
            'date_hired' => $this->faker->date('Y-m-d', 'now'),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
