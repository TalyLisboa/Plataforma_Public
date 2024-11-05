<?php

namespace App\Filament\App\Widgets;

use App\Models\Department;
use App\Models\Employee;
use App\Models\Team;
use Filament\Facades\Filament;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsAppOverview extends BaseWidget
{
    /**
     * Define as estatísticas exibidas no widget de visão geral.
     */
    protected function getStats(): array
    {
        return [
            Stat::make('Usuários', Team::find(Filament::getTenant())->first()->members->count())
                ->description('Todos os usuários do banco de dados')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->chart([7, 2, 10, 3, 15, 4, 17])
                ->color('success'),

            Stat::make('Departamentos', Department::query()->whereBelongsTo(Filament::getTenant())->count())
                ->description('Todos os departamentos do banco de dados')
                ->descriptionIcon('heroicon-m-arrow-trending-down')
                ->color('danger'),

            Stat::make('Funcionários', Employee::query()->whereBelongsTo(Filament::getTenant())->count())
                ->description('Todos os funcionários do banco de dados')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success'),
        ];
    }
}
