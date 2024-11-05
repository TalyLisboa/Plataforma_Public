<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DepartmentResource\Pages;
use App\Filament\Resources\DepartmentResource\RelationManagers;
use App\Models\Department;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DepartmentResource extends Resource
{
    // Modelo associado ao recurso de departamentos
    protected static ?string $model = Department::class;

    // Ícone de navegação
    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';

    // Rótulo de navegação e labels do recurso
    protected static ?string $navigationLabel = 'Departamento';
    protected static ?string $modelLabel = 'Departamento';

    // Grupo de navegação e ordenação
    protected static ?string $navigationGroup = 'Gestão do Sistema';
    protected static ?int $navigationSort = 4;

    /**
     * Define um emblema de navegação que mostra a contagem de departamentos.
     */
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    /**
     * Define a cor do emblema de navegação com base na contagem de departamentos.
     */
    public static function getNavigationBadgeColor(): string|array|null
    {
        return static::getModel()::count() > 10 ? 'warning' : 'success';
    }

    /**
     * Define o formulário de criação/edição do departamento.
     */
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Detalhes do Departamento')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nome')
                            ->required()
                            ->maxLength(255),
                    ])
            ]);
    }

    /**
     * Define a tabela de listagem dos departamentos.
     */
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nome')
                    ->searchable(),
                Tables\Columns\TextColumn::make('employees_count')
                    ->label('Número de Funcionários')
                    ->counts('employees'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Criado em')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Atualizado em')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                // Defina filtros adicionais, se necessário
            ])
            ->actions([
                Tables\Actions\ViewAction::make()->label('Visualizar'),
                Tables\Actions\EditAction::make()->label('Editar'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()->label('Excluir Selecionados'),
                ]),
            ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()->label('Criar Novo Departamento'),
            ]);
    }

    /**
     * Define a lista de informações do departamento.
     */
    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Informações do Departamento')
                    ->schema([
                        TextEntry::make('name')->label('Nome'),
                        TextEntry::make('employees_count')
                            ->label('Número de Funcionários')
                            ->state(function (Model $record): int {
                                return $record->employees()->count();
                            }),
                    ])->columns(2)
            ]);
    }

    /**
     * Define as relações do recurso, se houver.
     */
    public static function getRelations(): array
    {
        return [
            // Defina gerenciadores de relações aqui, se necessário
        ];
    }

    /**
     * Define as páginas disponíveis para o recurso Departamento.
     */
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDepartments::route('/'),
            'create' => Pages\CreateDepartment::route('/create'),
            //'view' => Pages\ViewDepartment::route('/{record}'),
            'edit' => Pages\EditDepartment::route('/{record}/edit'),
        ];
    }
}
