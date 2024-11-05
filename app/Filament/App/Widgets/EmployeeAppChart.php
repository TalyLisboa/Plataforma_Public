<?php

namespace App\Filament\App\Widgets;

use App\Models\Employee;
use Filament\Facades\Filament;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;

class EmployeeAppChart extends ChartWidget
{
    // Título do gráfico
    protected static ?string $heading = 'Gráfico de Funcionários';

    // Define a posição do widget no painel
    protected static ?int $sort = 3;

    // Cor do gráfico
    protected static string $color = 'warning';

    /**
     * Obtém os dados para o gráfico de funcionários.
     */
    protected function getData(): array
    {
        $data = Trend::query(Employee::query()->whereBelongsTo(Filament::getTenant()))
            ->between(
                start: now()->startOfMonth(),
                end: now()->endOfMonth(),
            )
            ->perDay()
            ->count();

        return [
            'datasets' => [
                [
                    'label' => 'Funcionários',
                    'data' => $data->map(fn (TrendValue $value) => $value->aggregate),
                ],
            ],
            'labels' => $data->map(fn (TrendValue $value) => $value->date),
        ];
    }

    /**
     * Define o tipo de gráfico.
     */
    protected function getType(): string
    {
        return 'line';
    }
}
