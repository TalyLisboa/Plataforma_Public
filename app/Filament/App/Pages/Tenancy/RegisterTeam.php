<?php

namespace App\Filament\App\Pages\Tenancy;

use App\Models\Team;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Pages\Tenancy\RegisterTenant;
use Illuminate\Database\Eloquent\Model;

class RegisterTeam extends RegisterTenant
{
      public static function getLabel(): string
      {
            return 'Registrar time';
      }

      public function form(Form $form): Form
      {
            return $form
                  ->schema([
                        TextInput::make('name') 
                              ->label('Nome') 
                              ->required()
                              ->maxLength(255),

                        TextInput::make('slug')
                              ->label('Nicho')
                              ->required()
                              ->maxLength(255),
                  ]);
      }

      protected function handleRegistration(array $data): Team
      {
            $team = Team::create($data);

            $team->members()->attach(auth()->user());

            return $team;
      }
}
