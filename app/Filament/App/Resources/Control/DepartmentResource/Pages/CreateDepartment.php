<?php

namespace App\Filament\App\Resources\Control\DepartmentResource\Pages;

use App\Filament\App\Resources\Control\DepartmentResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateDepartment extends CreateRecord
{
    protected static string $resource = DepartmentResource::class;

    /**
     * Retorna o título da página traduzido.
     *
     * @return string
     */
    public function getTitle(): string
    {
        return 'Criar Departamento';
    }
}

