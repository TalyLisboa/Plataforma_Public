<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StateResource\Pages;
use App\Filament\Resources\StateResource\RelationManagers;
use App\Filament\Resources\StateResource\RelationManagers\CitiesRelationManager;
use App\Filament\Resources\StateResource\RelationManagers\EmployeesRelationManager;
use App\Models\State;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class StateResource extends Resource
{
    // Modelo associado ao recurso de estados
    protected static ?string $model = State::class;

    // Ícone de navegação
    protected static ?string $navigationIcon = 'heroicon-o-building-library';

    // Rótulo de navegação e labels do recurso
    protected static ?string $navigationLabel = 'Estado';
    protected static ?string $modelLabel = 'Estados';

    // Grupo de navegação e ordenação
    protected static ?string $navigationGroup = 'Gestão do Sistema';
    protected static ?int $navigationSort = 2;

    /**
     * Define o formulário de criação/edição do estado.
     */
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Detalhes do Estado')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nome do Estado')
                            ->required()
                            ->maxLength(255),
                    ])
            ]);
    }

    /**
     * Define a tabela de listagem dos estados.
     */
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nome do Estado')
                    ->sortable()
                    ->searchable(),
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
                // Adicione filtros adicionais, se necessário
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
                Tables\Actions\CreateAction::make()->label('Criar Novo Estado'),
            ]);
    }

    /**
     * Define a lista de informações do estado.
     */
    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Informações do Estado')
                    ->schema([
                        TextEntry::make('name')->label('Nome do Estado'),
                    ])->columns(2)
            ]);
    }

    /**
     * Define as relações do recurso, se houver.
     */
    public static function getRelations(): array
    {
        return [
            CitiesRelationManager::class,
            EmployeesRelationManager::class,
        ];
    }

    /**
     * Define as páginas disponíveis para o recurso Estado.
     */
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListStates::route('/'),
            'create' => Pages\CreateState::route('/create'),
            'edit' => Pages\EditState::route('/{record}/edit'),
        ];
    }
}
