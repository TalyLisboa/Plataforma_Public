<?php

namespace App\Filament\App\Resources\Control\PaymentReportResource\Pages;

use App\Filament\App\Resources\Control\PaymentReportResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditPaymentReport extends EditRecord
{
    protected static string $resource = PaymentReportResource::class;

    /**
     * Retorna as ações de cabeçalho da página.
     *
     * @return array
     */
    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }

    /**
     * Retorna a notificação exibida após salvar o registro.
     *
     * @return \Filament\Notifications\Notification|null
     */
    protected function getSavedNotification(): ?Notification
    {
        return Notification::make()
            ->success() 
            ->title('Relatório Atualizado!')
            ->body('As informações do relatório foram atualizadas com sucesso.');
    }

    /**
     * Retorna o título da página traduzido.
     *
     * @return string
     */
    public function getTitle(): string 
    {
        return 'Editar Relatório';
    }
}
