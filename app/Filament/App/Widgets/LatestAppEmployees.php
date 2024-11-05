<?php

namespace App\Filament\App\Widgets;

use App\Models\Employee;
use Filament\Facades\Filament;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class LatestAppEmployees extends BaseWidget
{   protected static ?string $heading = 'Últimos Funcionários';

    // Define a posição do widget no painel
    protected static ?int $sort = 3;

    /**
     * Configura a tabela de exibição dos últimos funcionários.
     */
    public function table(Table $table): Table
    {
        return $table
            ->query(Employee::query()->whereBelongsTo(Filament::getTenant()))
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('first_name')
                    ->label('Primeiro Nome'),
                Tables\Columns\TextColumn::make('last_name')
                    ->label('Último Nome'),
            ]);
    }
}
