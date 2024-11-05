<?php

namespace App\Filament\Widgets;

use App\Models\Employee;
use App\Models\Team;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsAdminOverview extends BaseWidget
{
    /**
     * Define as estatísticas exibidas no widget de visão geral administrativa.
     */
    protected function getStats(): array
    {
        return [
            Stat::make('Usuários', User::query()->count())
                ->description('Todos os usuários do banco de dados')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->chart([7, 2, 10, 3, 15, 4, 17])
                ->color('success'),

            Stat::make('Equipes', Team::query()->count())
                ->description('Todas as equipes do banco de dados')
                ->descriptionIcon('heroicon-m-arrow-trending-down')
                ->color('danger'),

            Stat::make('Funcionários', Employee::query()->count())
                ->description('Todos os funcionários do banco de dados')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success'),
        ];
    }
}
