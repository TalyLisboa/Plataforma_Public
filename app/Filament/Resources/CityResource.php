<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CityResource\Pages;
use App\Filament\Resources\CityResource\RelationManagers;
use App\Filament\Resources\CityResource\RelationManagers\EmployeesRelationManager;
use App\Models\City;
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

class CityResource extends Resource
{
    // Modelo associado ao recurso de cidades
    protected static ?string $model = City::class;

    // Ícone de navegação
    protected static ?string $navigationIcon = 'heroicon-o-building-office';

    // Rótulo de navegação e labels do recurso
    protected static ?string $navigationLabel = 'Cidade';
    protected static ?string $modelLabel = 'Cidade';

    // Grupo de navegação e ordenação
    protected static ?string $navigationGroup = 'Gestão do Sistema';
    protected static ?int $navigationSort = 3;

    /**
     * Define o formulário de criação/edição da cidade.
     */
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Detalhes da Cidade')
                    ->schema([
                        Forms\Components\Select::make('state_id')
                            ->label('Estado')
                            ->relationship(name: 'state', titleAttribute: 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        Forms\Components\TextInput::make('name')
                            ->label('Nome da Cidade')
                            ->required()
                            ->maxLength(255),
                    ])
            ]);
    }

    /**
     * Define a tabela de listagem das cidades.
     */
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('state.name')
                    ->label('Estado')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->label('Nome da Cidade')
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
                Tables\Actions\CreateAction::make()->label('Criar Nova Cidade'),
            ]);
    }

    /**
     * Define a lista de informações da cidade.
     */
    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Informações da Cidade')
                    ->schema([
                        TextEntry::make('state.name')->label('Nome do Estado'),
                        TextEntry::make('name')->label('Nome da Cidade'),
                    ])->columns(2)
            ]);
    }

    /**
     * Define as relações do recurso, se houver.
     */
    public static function getRelations(): array
    {
        return [
            EmployeesRelationManager::class
        ];
    }

    /**
     * Define as páginas disponíveis para o recurso Cidade.
     */
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCities::route('/'),
            'create' => Pages\CreateCity::route('/create'),
            'view' => Pages\ViewCity::route('/{record}'),
            'edit' => Pages\EditCity::route('/{record}/edit'),
        ];
    }

        public static function canViewAny(): bool
    {
        return false;
    }
}
