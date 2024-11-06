<?php

namespace App\Filament\App\Resources\Control\PaymentReportResource\Pages;

use App\Filament\App\Resources\Control\PaymentReportResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditPaymentReport extends EditRecord
{
    protected static string $resource = PaymentReportResource::class;

    protected function getHeaderActions(): array{

        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }

        protected function getSavedNotification(): ?Notification
    {
            return Notification::make()
            ->sucess()
            ->title('Relatório Atualizado!')
            ->body('As informações do relatório foram atualizadas com sucesso');
    }


/**
 *  Retorna o título da página traduzido
 * 
 * @return string
 */
    public function getTitle(): string 
    {
        return 'Editar Relatório';
    }
}