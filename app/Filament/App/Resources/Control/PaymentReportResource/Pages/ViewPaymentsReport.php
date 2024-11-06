<?php

namespace App\Filament\App\Resources\Control\PaymentReportResource\Pages;

use App\Filament\App\Resources\Control\PaymentReportResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewPaymentsReport extends ViewRecord
{
    protected static string $resource = PaymentReportResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }

    /**
     * Retorna o título da página traduzido.
     *
     * @return string
     */
    public function getTitle(): string
    {
        return 'Visualizar Relatórios';
    }
}
