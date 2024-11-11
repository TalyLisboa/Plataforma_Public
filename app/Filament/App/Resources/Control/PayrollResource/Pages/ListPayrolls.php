<?php

namespace App\Filament\App\Resources\Control\PayrollResource\Pages;

use App\Filament\App\Resources\Control\PayrollResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPayrolls extends ListRecords
{
    protected static string $resource = PayrollResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Criar Nova Folha de Pagamento'),
        ];
    }
}
