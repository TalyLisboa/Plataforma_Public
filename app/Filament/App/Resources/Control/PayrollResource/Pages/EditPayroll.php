<?php

namespace App\Filament\App\Resources\Control\PayrollResource\Pages;

use App\Filament\App\Resources\Control\PayrollResource;
use Filament\Resources\Pages\EditRecord;

class EditPayroll extends EditRecord
{
    protected static string $resource = PayrollResource::class;

    protected function getSavedNotificationTitle(): ?string
    {
        return 'Folha de pagamento atualizada com sucesso!';
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
