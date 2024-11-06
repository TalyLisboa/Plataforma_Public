<?php

namespace App\Filament\App\Resources\Control\DepartmentResource\Pages;

use App\Filament\App\Resources\Control\DepartmentResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDepartments extends ListRecords
{
    protected static string $resource = DepartmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
    /**
     * Retorna o título da página traduzido.
     *
     * @return string
     */
    public function getTitle(): string
    {
        return 'Listar Departamentos';
    }
}
