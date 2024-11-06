<?php

namespace App\Filament\App\Resources\Control;

use App\Filament\App\Resources\Control\PaymentReportResource\Pages\ListPaymentReports;
use App\Filament\App\Resources\Control\PaymentReportResource\Pages\CreatePaymentReport;
use App\Filament\App\Resources\Control\PaymentReportResource\Pages\EditPaymentReport;
use App\Models\PaymentReport;
use App\Models\Employee;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class PaymentReportResource extends Resource
{
    protected static ?string $model = PaymentReport::class;
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationGroup = 'Gestão Financeira';

    // Defina a relação de tenancy
    protected static ?string $tenantOwnershipRelationshipName = 'team';

    public static function getModelLabel(): string
    {
        return 'Relatório de Pagamento';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Relatórios de Pagamento';
    }

    public static function getGlobalSearchResultTitle(Model $record): string
    {
        return "Relatório de {$record->employee->full_name} - {$record->month}/{$record->year}";
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['employee.first_name', 'employee.last_name', 'month', 'year', 'status'];
    }

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informações do Relatório')
                    ->schema([
                        Select::make('employee_id')
                            ->label('Funcionário')
                            ->options(Employee::all()->pluck('full_name', 'id'))
                            ->searchable()
                            ->preload()
                            ->required()
                            ->placeholder('Selecione um Funcionário'),
                        Select::make('month')
                            ->label('Mês')
                            ->options([
                                1 => 'Janeiro',
                                2 => 'Fevereiro',
                                3 => 'Março',
                                4 => 'Abril',
                                5 => 'Maio',
                                6 => 'Junho',
                                7 => 'Julho',
                                8 => 'Agosto',
                                9 => 'Setembro',
                                10 => 'Outubro',
                                11 => 'Novembro',
                                12 => 'Dezembro',
                            ])
                            ->required()
                            ->placeholder('Selecione o Mês'),
                        TextInput::make('year')
                            ->label('Ano')
                            ->required()
                            ->numeric()
                            ->minValue(2000)
                            ->maxValue(2100)
                            ->placeholder('Digite o Ano'),
                        TextInput::make('amount')
                            ->label('Valor')
                            ->required()
                            ->numeric()
                            ->prefix('R$ ')
                            ->step('0.01')
                            ->placeholder('Digite o Valor'),
                        Select::make('status')
                            ->label('Status')
                            ->options([
                                'pendente' => 'Pendente',
                                'pago' => 'Pago',
                                'cancelado' => 'Cancelado',
                            ])
                            ->default('pendente')
                            ->required()
                            ->placeholder('Selecione o Status'),
                    ])
                    ->columns(2),
                Forms\Components\Section::make('Datas')
                    ->schema([
                        DatePicker::make('created_at')
                            ->label('Criado em')
                            ->disabled()
                            ->default(now())
                            ->visible(false),
                        DatePicker::make('updated_at')
                            ->label('Atualizado em')
                            ->disabled()
                            ->default(now())
                            ->visible(false),
                    ])
                    ->columns(2)
            ]);
    }

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                TextColumn::make('employee.first_name')
                    ->label('Primeiro Nome')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('employee.last_name')
                    ->label('Último Nome')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('month')
                    ->label('Mês')
                    ->sortable()
                    ->formatStateUsing(function ($state) {
                        $months = [
                            1 => 'Janeiro',
                            2 => 'Fevereiro',
                            3 => 'Março',
                            4 => 'Abril',
                            5 => 'Maio',
                            6 => 'Junho',
                            7 => 'Julho',
                            8 => 'Agosto',
                            9 => 'Setembro',
                            10 => 'Outubro',
                            11 => 'Novembro',
                            12 => 'Dezembro',
                        ];
                        return $months[$state] ?? $state;
                    }),
                TextColumn::make('year')
                    ->label('Ano')
                    ->sortable(),
                TextColumn::make('amount')
                    ->label('Valor')
                    ->money('BRL', true)
                    ->sortable(),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge(function ($state) {
                        return match ($state) {
                            'pendente' => 'warning',
                            'pago' => 'success',
                            'cancelado' => 'danger',
                            default => 'secondary',
                        };
                    })
                    ->formatStateUsing(function ($state) {
                        return ucfirst($state);
                    })
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Criado em')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
                TextColumn::make('updated_at')
                    ->label('Atualizado em')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'pendente' => 'Pendente',
                        'pago' => 'Pago',
                        'cancelado' => 'Cancelado',
                    ])
                    ->label('Filtrar por Status'),
                SelectFilter::make('month')
                    ->options([
                        1 => 'Janeiro',
                        2 => 'Fevereiro',
                        3 => 'Março',
                        4 => 'Abril',
                        5 => 'Maio',
                        6 => 'Junho',
                        7 => 'Julho',
                        8 => 'Agosto',
                        9 => 'Setembro',
                        10 => 'Outubro',
                        11 => 'Novembro',
                        12 => 'Dezembro',
                    ])
                    ->label('Filtrar por Mês'),
                Tables\Filters\Filter::make('year')
                    ->form([
                        Forms\Components\TextInput::make('year_from')
                            ->label('Ano a partir de')
                            ->numeric()
                            ->minValue(2000)
                            ->maxValue(2100),
                        Forms\Components\TextInput::make('year_until')
                            ->label('Ano até')
                            ->numeric()
                            ->minValue(2000)
                            ->maxValue(2100),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['year_from'] ?? null, fn (Builder $query, $year) => $query->where('year', '>=', $year))
                            ->when($data['year_until'] ?? null, fn (Builder $query, $year) => $query->where('year', '<=', $year));
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if (!empty($data['year_from'])) {
                            $indicators['year_from'] = 'Ano a partir de ' . $data['year_from'];
                        }
                        if (!empty($data['year_until'])) {
                            $indicators['year_until'] = 'Ano até ' . $data['year_until'];
                        }

                        return $indicators;
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()->label('Visualizar')
                    ->icon('heroicon-o-eye')
                    ->color('primary'),
                Tables\Actions\EditAction::make()->label('Editar')
                    ->icon('heroicon-o-pencil')
                    ->color('warning'),
                Tables\Actions\DeleteAction::make()
                    ->label('Excluir')
                    ->icon('heroicon-o-trash')
                    ->color('danger')
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title('Relatório de Pagamento excluído.')
                            ->body('O relatório de pagamento foi excluído com sucesso.')
                    ),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make()
                    ->label('Excluir Selecionados')
                    ->icon('heroicon-o-trash')
                    ->color('danger')
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title('Relatórios de Pagamento excluídos.')
                            ->body('Os relatórios de pagamento selecionados foram excluídos com sucesso.')
                    ),
            ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()
                    ->label('Criar Novo Relatório de Pagamento')
                    ->icon('heroicon-o-plus')
                    ->color('success'),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Informações do Funcionário')
                    ->schema([
                        TextEntry::make('employee.first_name')->label('Primeiro Nome'),
                        TextEntry::make('employee.last_name')->label('Último Nome'),
                        TextEntry::make('employee.email')->label('E-mail'),
                    ])
                    ->columns(3),
                Section::make('Detalhes do Relatório')
                    ->schema([
                        TextEntry::make('month')->label('Mês')
                            ->formatStateUsing(function ($state) {
                                $months = [
                                    1 => 'Janeiro',
                                    2 => 'Fevereiro',
                                    3 => 'Março',
                                    4 => 'Abril',
                                    5 => 'Maio',
                                    6 => 'Junho',
                                    7 => 'Julho',
                                    8 => 'Agosto',
                                    9 => 'Setembro',
                                    10 => 'Outubro',
                                    11 => 'Novembro',
                                    12 => 'Dezembro',
                                ];
                                return $months[$state] ?? $state;
                            }),
                        TextEntry::make('year')->label('Ano'),
                        TextEntry::make('amount')->label('Valor')
                            ->formatStateUsing(fn ($state) => 'R$ ' . number_format($state, 2, ',', '.')),
                        TextEntry::make('status')->label('Status')
                            ->formatStateUsing(function ($state) {
                                $statuses = [
                                    'pendente' => 'Pendente',
                                    'pago' => 'Pago',
                                    'cancelado' => 'Cancelado',
                                ];
                                return $statuses[$state] ?? ucfirst($state);
                            }),
                    ])
                    ->columns(2),
                Section::make('Datas Importantes')
                    ->schema([
                        TextEntry::make('created_at')->label('Criado em')
                            ->formatStateUsing(fn ($state) => \Carbon\Carbon::parse($state)->format('d/m/Y H:i')),
                        TextEntry::make('updated_at')->label('Atualizado em')
                            ->formatStateUsing(fn ($state) => \Carbon\Carbon::parse($state)->format('d/m/Y H:i')),
                    ])
                    ->columns(2),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            // Defina relações aqui se necessário
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPaymentReports::route('/'),
            'create' => CreatePaymentReport::route('/create'),
            'edit' => EditPaymentReport::route('/{record}/edit'),
        ];
    }
}
