<?php 

namespace App\Filament\Widgets;

use App\Models\User;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;

class UserAdminChart extends ChartWidget
{
    // Título do gráfico
    protected static ?string $heading = 'Gráfico de Usuários';

    // Define a posição do widget no painel
    protected static ?int $sort = 2;

    /**
     * Obtém os dados para o gráfico de usuários.
     */
    protected function getData(): array
    {
        $data = Trend::model(User::class)
            ->between(
                start: now()->startOfMonth(),
                end: now()->endOfMonth(),
            )
            ->perDay()
            ->count();

        return [
            'datasets' => [
                [
                    'label' => 'Usuários',
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
        return 'bar';
    }
}
