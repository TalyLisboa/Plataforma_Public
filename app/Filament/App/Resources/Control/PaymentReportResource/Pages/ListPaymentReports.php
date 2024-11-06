<?php

namespace App\Filament\App\Resources\Control\PaymentReportResource\Pages;

use App\Filament\App\Resources\Control\PaymentReportResource;
use App\Models\PaymentReport;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Pages\ListRecords\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListPaymentReports extends ListRecords
{   
    protected static string $resource = PaymentReportResource::class;
    

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            'Todos' => Tab::make(),
            'Essa Semana' => Tab::make()
                ->modifyQueryUsing(fn (Builder $query) => $query->where('created_at', '>=', now()->subWeek()))
                ->badge(PaymentReport::query()->where('created_at', '>=', now()->subWeek())->count()),
            'Esse Mês' => Tab::make()
                ->modifyQueryUsing(fn (Builder $query) => $query->where('created_at', '>=', now()->subMonth()))
                ->badge(PaymentReport::query()->where('created_at', '>=', now()->subMonth())->count()),
            'Esse Ano' => Tab::make()
                ->modifyQueryUsing(fn (Builder $query) => $query->where('created_at', '>=', now()->subYear()))
                ->badge(PaymentReport::query()->where('created_at', '>=', now()->subYear())->count()),
        ];
    }

    /**
     * Retorna o título da página traduzido.
     *
     * @return string
     */
    public function getTitle(): string
    {
        return 'Listar Relatórios';
    }
}
