<?php

namespace App\Filament\App\Resources\Control\PayrollResource\Pages;

use App\Filament\App\Resources\Control\PayrollResource;
use Filament\Resources\Pages\ViewRecord;
use Filament\Actions;

class ViewPayroll extends ViewRecord
{
    protected static string $resource = PayrollResource::class;

    protected function getActions(): array
    {
        return [
            Actions\EditAction::make()
                ->label('Editar')
                ->color('warning'),
            Actions\DeleteAction::make()
                ->label('Excluir')
                ->color('danger')
                ->successNotificationTitle('Folha de pagamento excluída com sucesso!')
                ->requiresConfirmation()
                ->modalHeading('Confirmar Exclusão')
                ->modalSubheading('Tem certeza de que deseja excluir esta folha de pagamento?'),
        ];
    }
}
