<?php

namespace App\Filament\Widgets;

use App\Models\Employee;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class LatestAdminEmployees extends BaseWidget
{   protected static ?string $heading = 'Últimos Funcionários';
    // Define a ordem de exibição do widget no painel
    protected static ?int $sort = 4;

    // Define o espaçamento das colunas
    protected int | string | array $columnSpan = 'full';

    /**
     * Configura a tabela de exibição dos últimos funcionários.
     */
    public function table(Table $table): Table
    {
        return $table
            ->query(Employee::query())
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('first_name')
                    ->label('Primeiro Nome'),
                Tables\Columns\TextColumn::make('last_name')
                    ->label('Último Nome'),
            ]);
    }
}
