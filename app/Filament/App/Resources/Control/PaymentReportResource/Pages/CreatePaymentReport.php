<?php

namespace App\Filament\App\Resources\Control\PaymentReportResource\Pages;

use App\Filament\App\Resources\Control\PaymentReportResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;

class CreatePaymentReport extends CreateRecord
{   
    protected static string $resource = PaymentReportResource::class;

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Relatório Gerado!')
            ->body('O Relatório foi gerado com sucesso.');
    }

    /**
     * Retorna o título da página traduzido.
     *
     * @return string
     */
    public function getTitle(): string
    {
        return 'Gerar Relatório';
    }
    
}
