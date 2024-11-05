<?php

namespace App\Filament\App\Pages\Tenancy;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Pages\Tenancy\EditTenantProfile;
use Illuminate\Database\Eloquent\Model;

class EditTeamProfile extends EditTenantProfile
{
      public static function getLabel(): string
      {
            return 'Perfil do Time';
      }

      public function form(Form $form): Form
      {
            return $form
                  ->schema([
                        TextInput::make('nome'),
                        TextInput::make('nicho'),
                  ]);
      }
}
