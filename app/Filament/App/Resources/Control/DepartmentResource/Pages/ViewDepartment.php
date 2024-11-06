<?php

namespace App\Filament\App\Resources\Control\DepartmentResource\Pages;

use App\Filament\App\Resources\Control\DepartmentResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewDepartment extends ViewRecord
{
    protected static string $resource = DepartmentResource::class;

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
        return 'Visualizar Departamentos';
    }
}
