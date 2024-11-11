<?php

namespace App\Filament\App\Resources\Control\PayrollResource\Pages;

use App\Filament\App\Resources\Control\PayrollResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;

class CreatePayroll extends CreateRecord
{
    protected static string $resource = PayrollResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Folha de pagamento criada com sucesso!';
    }
}
