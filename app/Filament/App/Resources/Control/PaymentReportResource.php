<?php

namespace App\Filament\App\Resources\Control;

use App\Filament\App\Resources\Control\PaymentReportResource\Pages\ListPaymentReports;
use App\Filament\App\Resources\Control\PaymentReportResource\Pages\CreatePaymentReport;
use App\Filament\App\Resources\Control\PaymentReportResource\Pages\EditPaymentReport;
use App\Models\PaymentReport;
use App\Models\Employee;
use Filament\Forms;
use Filament\Forms\Components\{Section, Select, TextInput, Textarea, DatePicker};
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\{Section as InfolistSection, TextEntry};
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

    // Defina a relação usada para tenancy
    protected static ?string $tenantRelationshipName = 'paymentReports';

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
        // Substituído 'full_name' por 'first_name' e 'last_name'
        return "Relatório de {$record->employee->first_name} {$record->employee->last_name} - {$record->month}/{$record->year}";
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['employee.first_name', 'employee.last_name', 'month', 'year', 'status'];
    }

    /**
     * Função auxiliar para formatar valores monetários
     */
    private static function formatCurrency(?string $value): string
    {
        if (is_null($value) || $value === '') {
            return 'R$ 0,00';
        }
        return 'R$ ' . number_format((float)$value, 2, ',', '.');
    }

    /**
     * Função auxiliar para sanitizar valores monetários
     */
    private static function parseCurrency(?string $value): string
    {
        if (is_null($value) || $value === '') {
            return '0.00';
        }
        // Remove 'R$', pontos de milhar e espaços
        $sanitized = str_replace(['R$', '.', ' '], '', $value);
        // Substitui vírgula por ponto para conversão
        $sanitized = str_replace(',', '.', $sanitized);
        // Garante que o valor tenha duas casas decimais
        return number_format((float)$sanitized, 2, '.', '');
    }

    /**
     * Formulário de criação/edição
     */
    public static function form(Forms\Form $form): Forms\Form
    {
        return $form
            ->schema([
                Section::make('Informações do Relatório')
                    ->schema([
                        Select::make('employee_id')
                            ->label('Funcionário')
                            ->options(function () {
                                return Employee::all()->mapWithKeys(function ($employee) {
                                    // Substituído 'full_name' por 'first_name' e 'last_name'
                                    return [$employee->id => $employee->first_name . ' ' . $employee->last_name];
                                })->toArray();
                            })
                            ->searchable()
                            ->preload()
                            ->required()
                            ->placeholder('Selecione um Funcionário')
                            ->columnSpan(2),

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
                            ->placeholder('Selecione o Mês')
                            ->columnSpan(1),

                        TextInput::make('year')
                            ->label('Ano')
                            ->required()
                            ->numeric()
                            ->minValue(2000)
                            ->maxValue(2100)
                            ->placeholder('Digite o Ano')
                            ->columnSpan(1),

                        TextInput::make('amount')
                            ->label('Valor')
                            ->required()
                            ->prefix('R$ ')
                            ->placeholder('Digite o Valor')
                            ->rules([
                                'required',
                                'regex:/^R\$ \d{1,3}(?:\.\d{3})*,\d{2}$/', // Permite múltiplos grupos de milhar
                            ])
                            ->formatStateUsing(function ($state) {
                                // Formata o valor para exibição
                                return self::formatCurrency($state);
                            })
                            ->dehydrateStateUsing(function ($state) {
                                // Sanitiza para conversão antes de salvar
                                return self::parseCurrency($state);
                            })
                            ->extraAttributes([
                                'class' => 'form-control campoTexto __tabCampo __foco money __valor',
                                'maxlength' => '15',
                                'autocomplete' => 'off',
                                'tabindex' => '0',
                            ])
                            ->columnSpan(2)
                            ->helperText('Formato esperado: R$ 1.234,56'),

                        Select::make('payment_method')
                            ->label('Forma de Pagamento')
                            ->options([
                                'transfer' => 'Transferência Bancária',
                                'pix'      => 'Pix',
                                'boleto'   => 'Boleto',
                                'cheque'   => 'Cheque',
                                'cash'     => 'Dinheiro',
                            ])
                            ->required()
                            ->placeholder('Selecione a Forma de Pagamento')
                            ->columnSpan(2),

                        Select::make('status')
                            ->label('Status')
                            ->options([
                                'pendente'   => 'Pendente',
                                'pago'       => 'Pago',
                                'cancelado'  => 'Cancelado',
                            ])
                            ->default('pendente')
                            ->required()
                            ->placeholder('Selecione o Status')
                            ->columnSpan(2),
                    ])
                    ->columns(2)
                    ->columnSpan('full'),

                Section::make('Datas Importantes')
                    ->schema([
                        DatePicker::make('payment_date')
                            ->label('Data de Pagamento')
                            ->required()
                            ->placeholder('Selecione a Data de Pagamento')
                            ->columnSpan(1),

                        DatePicker::make('created_at')
                            ->label('Criado em')
                            ->disabled()
                            ->default(now())
                            ->visible(false)
                            ->columnSpan(1),

                        DatePicker::make('updated_at')
                            ->label('Atualizado em')
                            ->disabled()
                            ->default(now())
                            ->visible(false)
                            ->columnSpan(1),

                        Textarea::make('notes')
                            ->label('Notas ou Observações')
                            ->placeholder('Adicione notas ou observações (opcional)')
                            ->maxLength(500)
                            ->columnSpan(2),
                    ])
                    ->columns(2)
                    ->columnSpan('full'),
            ]);
    }

    /**
     * Método para atualizar os cálculos utilizando bcmath (se necessário)
     */
    protected static function updateCalculations(callable $get, callable $set)
    {
        // Implemente os cálculos necessários aqui, se aplicável
        // Este método pode ser removido se não houver cálculos automáticos
    }

    /**
     * Tabela de listagem
     */
    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                TextColumn::make('employee.first_name')
                    ->label('Funcionário')
                    ->sortable()
                    ->searchable()
                    ->formatStateUsing(function ($state, $record) {
                        return $record->employee->first_name . ' ' . $record->employee->last_name;
                    }),

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

                TextColumn::make('payment_date')
                    ->label('Data de Pagamento')
                    ->date('d/m/Y')
                    ->sortable(),

                TextColumn::make('amount')
                    ->label('Valor')
                    ->money('BRL', true)
                    ->sortable(),

                TextColumn::make('payment_method')
                    ->label('Forma de Pagamento')
                    ->sortable()
                    ->formatStateUsing(function ($state) {
                        $methods = [
                            'transfer' => 'Transferência Bancária',
                            'pix'      => 'Pix',
                            'boleto'   => 'Boleto',
                            'cheque'   => 'Cheque',
                            'cash'     => 'Dinheiro',
                        ];
                        return $methods[$state] ?? ucfirst($state);
                    }),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge(function ($state) {
                        return match ($state) {
                            'pendente'  => 'warning',
                            'pago'      => 'success',
                            'cancelado' => 'danger',
                            default      => 'secondary',
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
                        'pendente'   => 'Pendente',
                        'pago'       => 'Pago',
                        'cancelado'  => 'Cancelado',
                    ])
                    ->label('Filtrar por Status'),

                SelectFilter::make('month')
                    ->options([
                        1  => 'Janeiro',
                        2  => 'Fevereiro',
                        3  => 'Março',
                        4  => 'Abril',
                        5  => 'Maio',
                        6  => 'Junho',
                        7  => 'Julho',
                        8  => 'Agosto',
                        9  => 'Setembro',
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

    /**
     * Detalhes da folha de pagamento
     */
    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                InfolistSection::make('Informações do Funcionário')
                    ->schema([
                        TextEntry::make('employee.first_name')->label('Primeiro Nome'),
                        TextEntry::make('employee.last_name')->label('Último Nome'),
                        TextEntry::make('employee.email')->label('E-mail'),
                    ])
                    ->columns(3),
                InfolistSection::make('Detalhes do Relatório')
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
                                return $months[$state] ?? 'Mês Inválido';
                            }),

                        TextEntry::make('year')->label('Ano'),

                        TextEntry::make('payment_date')->label('Data de Pagamento')
                            ->formatStateUsing(fn ($state) => \Carbon\Carbon::parse($state)->format('d/m/Y')),

                        TextEntry::make('amount')->label('Valor')
                            ->formatStateUsing(fn ($state) => self::formatCurrency($state)),

                        TextEntry::make('payment_method')->label('Forma de Pagamento')
                            ->formatStateUsing(function ($state) {
                                $methods = [
                                    'transfer' => 'Transferência Bancária',
                                    'pix'      => 'Pix',
                                    'boleto'   => 'Boleto',
                                    'cheque'   => 'Cheque',
                                    'cash'     => 'Dinheiro',
                                ];
                                return $methods[$state] ?? ucfirst($state);
                            }),

                        TextEntry::make('status')->label('Status')
                            ->formatStateUsing(function ($state) {
                                $statuses = [
                                    'pendente'  => 'Pendente',
                                    'pago'      => 'Pago',
                                    'cancelado' => 'Cancelado',
                                ];
                                return $statuses[$state] ?? ucfirst($state);
                            }),

                        TextEntry::make('notes')->label('Notas ou Observações')
                            ->formatStateUsing(function ($state) {
                                return $state ?? 'Nenhuma observação.';
                            }),
                    ])
                    ->columns(2),
                InfolistSection::make('Datas Importantes')
                    ->schema([
                        TextEntry::make('created_at')->label('Criado em')
                            ->formatStateUsing(fn ($state) => \Carbon\Carbon::parse($state)->format('d/m/Y H:i')),
                        TextEntry::make('updated_at')->label('Atualizado em')
                            ->formatStateUsing(fn ($state) => \Carbon\Carbon::parse($state)->format('d/m/Y H:i')),
                    ])
                    ->columns(2),
            ]);
    }

    /**
     * Relacionamentos (se houver)
     */
    public static function getRelations(): array
    {
        return [
            // Defina relações aqui se necessário
        ];
    }

    /**
     * Páginas do recurso
     */
    public static function getPages(): array
    {
        return [
            'index' => ListPaymentReports::route('/'),
            'create' => CreatePaymentReport::route('/create'),
            'edit' => EditPaymentReport::route('/{record}/edit'),
        ];
    }
}
