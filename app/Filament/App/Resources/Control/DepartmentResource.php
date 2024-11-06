<?php 

namespace App\Filament\App\Resources\Control;

use App\Filament\App\Resources\Control\DepartmentResource\Pages;
use App\Filament\App\Resources\Control\DepartmentResource\RelationManagers;
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
        public static function getModelLabel(): string
    {
        return 'Departamento';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Departamentos';
    }

    // Modelo associado ao recurso
    protected static ?string $model = Department::class;

    // Ícone de navegação (alterado para um ícone mais coerente)
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    /**
     * Define o formulário de criação/edição do departamento.
     */
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Detalhes do Departamento') // Seção principal
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nome')
                            ->required()
                            ->maxLength(255),
                    ])
            ]);
    }

    /**
     * Define a tabela de listagem de departamentos.
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
                // Defina filtros personalizados, se necessário
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label('Visualizar'),
                Tables\Actions\EditAction::make()
                    ->label('Editar'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->label('Excluir Selecionados'),
                ]),
            ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()
                    ->label('Criar Novo Departamento'),
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
                        TextEntry::make('name')
                            ->label('Nome'),
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
            // Relacione outros recursos, se necessário
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
            'view' => Pages\ViewDepartment::route('/{record}'),
            'edit' => Pages\EditDepartment::route('/{record}/edit'),
        ];
    }
}
