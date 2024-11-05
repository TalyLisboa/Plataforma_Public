<?php

namespace App\Filament\App\Resources\Control\EmployeeResource\Pages;

use App\Filament\App\Resources\Control\EmployeeResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateEmployee extends CreateRecord
{
    protected static string $resource = EmployeeResource::class;

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Funcionário Criado!')
            ->body('O funcionário foi criado com sucesso.');
    }

    /**
     * Retorna o título da página traduzido.
     *
     * @return string
     */
    public function getTitle(): string
    {
        return 'Criar Funcionário';
    }
}
