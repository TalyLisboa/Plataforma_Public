<?php

namespace App\Filament\App\Resources\Control;

use App\Filament\App\Resources\Control\PayrollResource\Pages\ListPayrolls;
use App\Filament\App\Resources\Control\PayrollResource\Pages\CreatePayroll;
use App\Filament\App\Resources\Control\PayrollResource\Pages\EditPayroll;
use App\Filament\App\Resources\Control\PayrollResource\Pages\ViewPayroll;
use App\Models\Payroll;
use App\Models\Employee;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\{Section, Select, TextInput, Textarea, DatePicker};
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Notifications\Notification;
use Filament\Infolists\Components\{Section as InfolistSection, TextEntry};
use Filament\Infolists\Infolist;
use Illuminate\Database\Eloquent\Model;

class PayrollResource extends Resource
{
    protected static ?string $model = Payroll::class;
    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';
    protected static ?string $navigationGroup = 'Gestão de Funcionários';
    protected static ?string $recordTitleAttribute = 'id';

    // Especifica a relação de propriedade do inquilino
    protected static ?string $tenantOwnershipRelationshipName = 'team';

    public static function getModelLabel(): string
    {
        return 'Folha de Pagamento';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Folhas de Pagamento';
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
        // Remove 'R$', pontos e espaços
        $sanitized = str_replace(['R$', '.', ' '], '', $value);
        // Substitui vírgula por ponto
        $sanitized = str_replace(',', '.', $sanitized);
        // Garante que o valor tenha duas casas decimais
        return number_format((float)$sanitized, 2, '.', '');
    }

    /**
     * Formulário de criação/edição
     */
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Detalhes da Folha de Pagamento')
                    ->schema([
                        // Selecionar Funcionário
                        Select::make('employee_id')
                            ->label('Funcionário')
                            ->relationship('employee', 'first_name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->placeholder('Selecione um Funcionário')
                            ->columnSpan(2),

                        // Selecionar Equipe
                        Select::make('team_id')
                            ->label('Equipe')
                            ->relationship('team', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->placeholder('Selecione uma Equipe')
                            ->columnSpan(2),

                        // Tipo de Contrato
                        Select::make('contract_type')
                            ->label('Tipo de Contrato')
                            ->options([
                                'clt' => 'CLT',
                                'pj'  => 'PJ',
                            ])
                            ->required()
                            ->reactive()
                            ->placeholder('Selecione o Tipo de Contrato')
                            ->columnSpan(2),

                        // Data de Pagamento
                        DatePicker::make('payment_date')
                            ->label('Data de Pagamento')
                            ->native(false)
                            ->displayFormat('d/m/Y')
                            ->required()
                            ->placeholder('Selecione a data de pagamento'),

                        // Salário Bruto
                        TextInput::make('salary_amount')
                            ->label('Salário Bruto')
                            ->required()
                            ->prefix('R$ ')
                            ->placeholder('')
                            ->step(0.01)
                            ->rules([
                                'required',
                                'regex:/^R\$ \d{1,3}(?:\.\d{3})*,\d{2}$/',
                            ]) // Corrigido regex para incluir 'R$ ' no início e tornar os pontos opcionais
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $get, callable $set) {
                                // Sanitiza o valor para cálculo
                                $sanitized = self::parseCurrency($state);
                                // Reaplica o valor formatado para manter o estado
                                $formatted = self::formatCurrency($sanitized);
                                $set('salary_amount', $formatted);
                                // Atualiza os cálculos
                                self::updateCalculations($get, $set);
                            })
                            ->formatStateUsing(function ($state) {
                                // Verifica se o estado é numérico antes de formatar
                                return self::formatCurrency($state);
                            })
                            ->dehydrateStateUsing(function ($state) {
                                // Sanitiza para conversão antes de salvar
                                return self::parseCurrency($state);
                            })
                            ->extraAttributes([
                                'class' => 'form-control campoTexto __tabCampo __foco money __salarioBruto',
                                'maxlength' => '12',
                                'data-bind' => "value:SalarioBrutoTexto, valueUpdate: 'afterkeydown', event: { blur: function (data, event) { formatarDecimal('SalarioBrutoTexto', '__salarioBruto')} }",
                                'autocomplete' => 'off',
                                'tabindex' => '0',
                            ])
                            ->columnSpan(2)
                            ->helperText('Formato esperado: R$ 1.234,56'),

                        // Comissão
                        TextInput::make('commission')
                            ->label('Comissão')
                            ->prefix('R$ ')
                            ->placeholder('')
                            ->step(0.01)
                            ->rules([
                                'nullable',
                                'regex:/^R\$ \d{1,3}(?:\.\d{3})*,\d{2}$/',
                            ]) // Corrigido regex para incluir 'R$ ' no início e tornar os pontos opcionais
                            ->reactive()
                            ->default('0.00') // Removido o prefixo para evitar duplicação
                            ->afterStateUpdated(function ($state, callable $get, callable $set) {
                                $sanitized = self::parseCurrency($state);
                                $formatted = self::formatCurrency($sanitized);
                                $set('commission', $formatted);
                                self::updateCalculations($get, $set);
                            })
                            ->formatStateUsing(function ($state) {
                                return self::formatCurrency($state);
                            })
                            ->dehydrateStateUsing(function ($state) {
                                if (is_null($state) || $state === '') {
                                    return '0.00';
                                }
                                return self::parseCurrency($state);
                            })
                            ->extraAttributes([
                                'class' => 'form-control campoTexto __tabCampo __foco money __comissao',
                                'maxlength' => '12',
                                'data-bind' => "value:ComissaoTexto, valueUpdate: 'afterkeydown', event: { blur: function (data, event) { formatarDecimal('ComissaoTexto', '__comissao')} }",
                                'autocomplete' => 'off',
                                'tabindex' => '0',
                            ])
                            ->columnSpan(1)
                            ->helperText('Formato esperado: R$ 1.234,56'),

                        // Bonificações
                        TextInput::make('bonuses')
                            ->label('Bonificações')
                            ->prefix('R$ ')
                            ->placeholder('')
                            ->step(0.01)
                            ->rules([
                                'nullable',
                                'regex:/^R\$ \d{1,3}(?:\.\d{3})*,\d{2}$/',
                            ]) // Corrigido regex para incluir 'R$ ' no início e tornar os pontos opcionais
                            ->reactive()
                            ->default('0.00') // Removido o prefixo para evitar duplicação
                            ->afterStateUpdated(function ($state, callable $get, callable $set) {
                                $sanitized = self::parseCurrency($state);
                                $formatted = self::formatCurrency($sanitized);
                                $set('bonuses', $formatted);
                                self::updateCalculations($get, $set);
                            })
                            ->formatStateUsing(function ($state) {
                                return self::formatCurrency($state);
                            })
                            ->dehydrateStateUsing(function ($state) {
                                if (is_null($state) || $state === '') {
                                    return '0.00';
                                }
                                return self::parseCurrency($state);
                            })
                            ->extraAttributes([
                                'class' => 'form-control campoTexto __tabCampo __foco money __bonificacoes',
                                'maxlength' => '12',
                                'data-bind' => "value:BonificacoesTexto, valueUpdate: 'afterkeydown', event: { blur: function (data, event) { formatarDecimal('BonificacoesTexto', '__bonificacoes')} }",
                                'autocomplete' => 'off',
                                'tabindex' => '0',
                            ])
                            ->columnSpan(1)
                            ->helperText('Formato esperado: R$ 1.234,56'),

                        // INSS
                        TextInput::make('inss')
                            ->label('INSS')
                            ->disabled()
                            ->dehydrated(true)
                            ->default('0.00') // Valor sem o prefixo
                            ->prefix('R$ ')
                            ->placeholder('')
                            ->formatStateUsing(function ($state) {
                                return self::formatCurrency($state);
                            })
                            ->dehydrateStateUsing(function ($state) {
                                return self::parseCurrency($state);
                            })
                            ->extraAttributes([
                                'class' => 'form-control campoTexto __tabCampo __foco money __inss',
                                'maxlength' => '12',
                                'data-bind' => "value:InssTexto, valueUpdate: 'afterkeydown', event: { blur: function (data, event) { formatarDecimal('InssTexto', '__inss')} }",
                                'autocomplete' => 'off',
                                'tabindex' => '0',
                            ])
                            ->columnSpan(1)
                            ->hidden(fn (callable $get) => $get('contract_type') !== 'clt')
                            ->helperText('Formato esperado: R$ 1.234,56'),

                        // FGTS
                        TextInput::make('fgts')
                            ->label('FGTS')
                            ->disabled()
                            ->dehydrated(true)
                            ->default('0.00') // Valor sem o prefixo
                            ->prefix('R$ ')
                            ->placeholder('')
                            ->formatStateUsing(function ($state) {
                                return self::formatCurrency($state);
                            })
                            ->dehydrateStateUsing(function ($state) {
                                return self::parseCurrency($state);
                            })
                            ->extraAttributes([
                                'class' => 'form-control campoTexto __tabCampo __foco money __fgts',
                                'maxlength' => '12',
                                'data-bind' => "value:FgtsTexto, valueUpdate: 'afterkeydown', event: { blur: function (data, event) { formatarDecimal('FgtsTexto', '__fgts')} }",
                                'autocomplete' => 'off',
                                'tabindex' => '0',
                            ])
                            ->columnSpan(1)
                            ->hidden(fn (callable $get) => $get('contract_type') !== 'clt')
                            ->helperText('Formato esperado: R$ 1.234,56'),

                        // Vale Transporte (VT)
                        TextInput::make('vt')
                            ->label('Vale Transporte')
                            ->disabled()
                            ->dehydrated(true)
                            ->default('0.00') // Valor sem o prefixo
                            ->prefix('R$ ')
                            ->placeholder('')
                            ->formatStateUsing(function ($state) {
                                return self::formatCurrency($state);
                            })
                            ->dehydrateStateUsing(function ($state) {
                                return self::parseCurrency($state);
                            })
                            ->extraAttributes([
                                'class' => 'form-control campoTexto __tabCampo __foco money __vt',
                                'maxlength' => '12',
                                'data-bind' => "value:VtTexto, valueUpdate: 'afterkeydown', event: { blur: function (data, event) { formatarDecimal('VtTexto', '__vt')} }",
                                'autocomplete' => 'off',
                                'tabindex' => '0',
                            ])
                            ->columnSpan(1)
                            ->hidden(fn (callable $get) => $get('contract_type') !== 'clt')
                            ->helperText('Formato esperado: R$ 1.234,56'),

                        // IRRF (para CLT) ou Imposto de Renda (para PJ)
                        TextInput::make('irrf')
                            ->label(fn (callable $get) => $get('contract_type') === 'pj' ? 'Imposto de Renda' : 'IRRF')
                            ->disabled()
                            ->dehydrated(true)
                            ->default('0.00') // Valor sem o prefixo
                            ->prefix('R$ ')
                            ->placeholder('')
                            ->formatStateUsing(function ($state) {
                                return self::formatCurrency($state);
                            })
                            ->dehydrateStateUsing(function ($state) {
                                return self::parseCurrency($state);
                            })
                            ->extraAttributes([
                                'class' => 'form-control campoTexto __tabCampo __foco money __irrf',
                                'maxlength' => '12',
                                'data-bind' => "value:IrrfTexto, valueUpdate: 'afterkeydown', event: { blur: function (data, event) { formatarDecimal('IrrfTexto', '__irrf')} }",
                                'autocomplete' => 'off',
                                'tabindex' => '0',
                            ])
                            ->columnSpan(1)
                            ->hidden(fn (callable $get) => $get('contract_type') === null)
                            ->helperText('Formato esperado: R$ 1.234,56'),

                        // Outros Descontos
                        TextInput::make('other_deductions')
                            ->label('Outros Descontos')
                            ->prefix('R$ ')
                            ->placeholder('')
                            ->step(0.01)
                            ->rules([
                                'nullable',
                                'regex:/^R\$ \d{1,3}(?:\.\d{3})*,\d{2}$/',
                            ]) // Corrigido regex para incluir 'R$ ' no início e tornar os pontos opcionais
                            ->reactive()
                            ->default('0.00') // Removido o prefixo para evitar duplicação
                            ->afterStateUpdated(function ($state, callable $get, callable $set) {
                                $sanitized = self::parseCurrency($state);
                                $formatted = self::formatCurrency($sanitized);
                                $set('other_deductions', $formatted);
                                self::updateCalculations($get, $set);
                            })
                            ->formatStateUsing(function ($state) {
                                return self::formatCurrency($state);
                            })
                            ->dehydrateStateUsing(function ($state) {
                                if (is_null($state) || $state === '') {
                                    return '0.00';
                                }
                                return self::parseCurrency($state);
                            })
                            ->extraAttributes([
                                'class' => 'form-control campoTexto __tabCampo __foco money __outrosDescontos',
                                'maxlength' => '12',
                                'data-bind' => "value:OutrosDescontosTexto, valueUpdate: 'afterkeydown', event: { blur: function (data, event) { formatarDecimal('OutrosDescontosTexto', '__outrosDescontos')} }",
                                'autocomplete' => 'off',
                                'tabindex' => '0',
                            ])
                            ->columnSpan(1)
                            ->helperText('Formato esperado: R$ 1.234,56'),

                        // Descontos Totais
                        TextInput::make('deductions')
                            ->label('Descontos Totais')
                            ->disabled()
                            ->dehydrated(true)
                            ->default('0.00') // Valor sem o prefixo
                            ->prefix('R$ ')
                            ->placeholder('')
                            ->formatStateUsing(function ($state) {
                                return self::formatCurrency($state);
                            })
                            ->dehydrateStateUsing(function ($state) {
                                return self::parseCurrency($state);
                            })
                            ->extraAttributes([
                                'class' => 'form-control campoTexto __tabCampo __foco money __descontosTotais',
                                'maxlength' => '12',
                                'data-bind' => "value:DescontosTotaisTexto, valueUpdate: 'afterkeydown', event: { blur: function (data, event) { formatarDecimal('DescontosTotaisTexto', '__descontosTotais')} }",
                                'autocomplete' => 'off',
                                'tabindex' => '0',
                            ])
                            ->columnSpan(1)
                            ->helperText('Formato esperado: R$ 1.234,56'),

                        // Salário Líquido
                        TextInput::make('net_pay')
                            ->label('Salário Líquido')
                            ->disabled()
                            ->dehydrated(true)
                            ->default('0.00') // Valor sem o prefixo
                            ->prefix('R$ ')
                            ->placeholder('')
                            ->formatStateUsing(function ($state) {
                                return self::formatCurrency($state);
                            })
                            ->dehydrateStateUsing(function ($state) {
                                return self::parseCurrency($state);
                            })
                            ->extraAttributes([
                                'class' => 'form-control campoTexto __tabCampo __foco money __salarioLiquido',
                                'maxlength' => '12',
                                'data-bind' => "value:SalarioLiquidoTexto, valueUpdate: 'afterkeydown', event: { blur: function (data, event) { formatarDecimal('SalarioLiquidoTexto', '__salarioLiquido')} }",
                                'autocomplete' => 'off',
                                'tabindex' => '0',
                            ])
                            ->columnSpan(2)
                            ->helperText('Formato esperado: R$ 1.234,56'),

                        // Método de Pagamento
                        Select::make('payment_method')
                            ->label('Método de Pagamento')
                            ->options([
                                'transfer' => 'Transferência Bancária',
                                'check'    => 'Cheque',
                                'cash'     => 'Dinheiro',
                                'pix'      => 'Pix',
                            ])
                            ->required()
                            ->placeholder('Selecione o método de pagamento')
                            ->columnSpan(2),

                        // Comentários
                        Textarea::make('comments')
                            ->label('Comentários')
                            ->maxLength(500)
                            ->placeholder('Adicione comentários ou observações (opcional)')
                            ->columnSpan(2),
                    ])
                    ->columns(2)
                    ->columnSpan('full'),
            ])
            ->columns([
                'sm' => 2,
                'lg' => null,
            ]);
    }

    /**
     * Método para atualizar os cálculos utilizando bcmath
     */
    protected static function updateCalculations(callable $get, callable $set)
    {
        $contractType = $get('contract_type');

        // Função auxiliar para converter valor brasileiro para string com ponto decimal
        $convertToString = function ($value) {
            if (is_null($value) || $value === '') {
                return '0.00';
            }
            // Remove o prefixo 'R$' e espaços, pontos de milhar e substitui vírgula por ponto
            return self::parseCurrency($value);
        };

        // Obtém os valores e converte para strings utilizáveis pelo bcmath
        $salary = $convertToString($get('salary_amount'));
        $commission = $convertToString($get('commission'));
        $bonuses = $convertToString($get('bonuses'));
        $otherDeductions = $convertToString($get('other_deductions'));

        // Total de Ganhos: salário + comissão + bonificações
        $totalEarnings = bcadd(bcadd($salary, $commission, 2), $bonuses, 2);

        $inss = '0.00';
        $fgts = '0.00';
        $irrf = '0.00';
        $vt = '0.00';
        $deductions = '0.00';

        if ($contractType === 'clt') {
            // Cálculo do INSS
            $inss = self::calculateINSS($salary);
            $set('inss', self::formatCurrency($inss));

            // Cálculo do FGTS
            $fgts = self::calculateFGTS($salary);
            $set('fgts', self::formatCurrency($fgts));

            // Cálculo do IRRF
            $irrf = self::calculateIRRF($salary, $inss);
            $set('irrf', self::formatCurrency($irrf));

            // Cálculo do Vale Transporte (VT) - até 6% do salário
            $vt = bcmul($salary, '0.06', 2);
            // Garante que o VT não exceda o salário
            if (bccomp($vt, $salary, 2) === 1) {
                $vt = $salary;
            }
            $set('vt', self::formatCurrency($vt));

            // Total de Deduções: INSS + IRRF + VT + Outros Descontos
            $deductions = bcadd(bcadd(bcadd($inss, $irrf, 2), $vt, 2), $otherDeductions, 2);
        } elseif ($contractType === 'pj') {
            // Cálculo do Imposto de Renda para PJ
            $irrf = self::calculateIRPJ($salary);
            $set('irrf', self::formatCurrency($irrf));

            // Total de Deduções: IRPJ + Outros Descontos
            $deductions = bcadd($irrf, $otherDeductions, 2);
        } else {
            // Se nenhum tipo de contrato for selecionado, consideramos zero
            $deductions = $otherDeductions;
        }

        $set('deductions', self::formatCurrency($deductions));

        // Salário Líquido: Total de Ganhos - Total de Deduções
        $netPay = bcsub($totalEarnings, $deductions, 2);
        $set('net_pay', self::formatCurrency($netPay));
    }

    /**
     * Cálculo do INSS utilizando bcmath
     */
    protected static function calculateINSS(string $salary): string
    {
        $inss = '0.00';
        $salaryTiers = [
            '1302.00' => '0.075', // Até R$1.302,00: 7,5%
            '2571.29' => '0.09',  // De R$1.302,01 até R$2.571,29: 9%
            '3856.94' => '0.12',  // De R$2.571,30 até R$3.856,94: 12%
            '7507.49' => '0.14',  // De R$3.856,95 até R$7.507,49: 14%
        ];

        $previousTier = '0.00';
        foreach ($salaryTiers as $limit => $rate) {
            if (bccomp($salary, $limit, 2) === 1) {
                $range = bcsub($limit, $previousTier, 2);
                $inss = bcadd($inss, bcmul($range, $rate, 2), 2);
                $previousTier = $limit;
            } else {
                $range = bcsub($salary, $previousTier, 2);
                if (bccomp($range, '0.00', 2) === 1) {
                    $inss = bcadd($inss, bcmul($range, $rate, 2), 2);
                }
                break;
            }
        }

        // Teto do INSS (valor atualizado conforme legislação vigente)
        $inssMax = '877.24'; // Exemplo: R$877,24
        if (bccomp($inss, $inssMax, 2) === 1) {
            $inss = $inssMax;
        }

        return $inss;
    }

    /**
     * Cálculo do FGTS utilizando bcmath
     */
    protected static function calculateFGTS(string $salary): string
    {
        return bcmul($salary, '0.08', 2);
    }

    /**
     * Cálculo do IRRF para CLT utilizando bcmath
     */
    protected static function calculateIRRF(string $salary, string $inss): string
    {
        // Base de cálculo: salário - INSS
        $baseCalculo = bcsub($salary, $inss, 2);

        $irrf = '0.00';
        $irrfTiers = [
            ['limit' => '1903.98', 'rate' => '0.00', 'deduction' => '0.00'],
            ['limit' => '2826.65', 'rate' => '0.075', 'deduction' => '142.80'],
            ['limit' => '3751.05', 'rate' => '0.15', 'deduction' => '354.80'],
            ['limit' => '4664.68', 'rate' => '0.225', 'deduction' => '636.13'],
            ['limit' => '9999999.99', 'rate' => '0.275', 'deduction' => '869.36'],
        ];

        foreach ($irrfTiers as $tier) {
            if (bccomp($baseCalculo, $tier['limit'], 2) !== 1) {
                $rate = $tier['rate'];
                $deduction = $tier['deduction'];
                if ($rate === '0.00') {
                    $irrf = '0.00';
                } else {
                    $irrfCalc = bcmul($baseCalculo, $rate, 2);
                    $irrf = bcsub($irrfCalc, $deduction, 2);
                }
                break;
            }
        }

        // Garantir que o IRRF não seja negativo
        if (bccomp($irrf, '0.00', 2) === -1) {
            $irrf = '0.00';
        }

        return $irrf;
    }

    /**
     * Cálculo do Imposto de Renda para PJ utilizando bcmath
     */
    protected static function calculateIRPJ(string $salary): string
    {
        // Exemplo simplificado: taxa fixa de 15%
        return bcmul($salary, '0.15', 2);
    }

    /**
     * Tabela de listagem
     */
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                // Coluna do Funcionário
                TextColumn::make('employee.first_name')
                    ->label('Funcionário')
                    ->searchable()
                    ->sortable(),

                // Data de Pagamento
                TextColumn::make('payment_date')
                    ->label('Data de Pagamento')
                    ->date('d/m/Y')
                    ->sortable(),

                // Salário Bruto
                TextColumn::make('salary_amount')
                    ->label('Salário Bruto')
                    ->formatStateUsing(fn ($state) => self::formatCurrency($state))
                    ->sortable(),

                // Salário Líquido
                TextColumn::make('net_pay')
                    ->label('Salário Líquido')
                    ->formatStateUsing(fn ($state) => self::formatCurrency($state))
                    ->sortable(),
            ])
            ->filters([
                // Adicione filtros se necessário
            ])
            ->actions([
                // Ações de Visualizar
                Tables\Actions\ViewAction::make()
                    ->label('Visualizar')
                    ->icon('heroicon-o-eye')
                    ->color('primary'),

                // Ações de Editar
                Tables\Actions\EditAction::make()
                    ->label('Editar')
                    ->icon('heroicon-o-pencil')
                    ->color('warning'),

                // Ações de Excluir
                Tables\Actions\DeleteAction::make()
                    ->label('Excluir')
                    ->icon('heroicon-o-trash')
                    ->color('danger')
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title('Folha de pagamento excluída.')
                            ->body('A folha de pagamento foi excluída com sucesso.')
                    ),
            ])
            ->bulkActions([
                // Ações em massa
                Tables\Actions\DeleteBulkAction::make()
                    ->label('Excluir Selecionados')
                    ->icon('heroicon-o-trash')
                    ->color('danger')
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title('Folhas de pagamento excluídas.')
                            ->body('As folhas de pagamento selecionadas foram excluídas com sucesso.')
                    ),
            ])
            ->emptyStateActions([
                // Ação quando a tabela está vazia
                Tables\Actions\CreateAction::make()
                    ->label('Criar Nova Folha de Pagamento')
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
                InfolistSection::make('Detalhes da Folha de Pagamento')
                    ->schema([
                        // Informações do Funcionário
                        TextEntry::make('employee.first_name')
                            ->label('Funcionário'),

                        TextEntry::make('team.name')
                            ->label('Equipe'),

                        TextEntry::make('contract_type')
                            ->label('Tipo de Contrato')
                            ->formatStateUsing(function ($state) {
                                $types = [
                                    'clt' => 'CLT',
                                    'pj'  => 'PJ',
                                ];
                                return $types[$state] ?? $state;
                            }),

                        TextEntry::make('payment_date')
                            ->label('Data de Pagamento')
                            ->date('d/m/Y'),

                        TextEntry::make('salary_amount')
                            ->label('Salário Bruto')
                            ->formatStateUsing(fn ($state) => self::formatCurrency($state)),

                        TextEntry::make('commission')
                            ->label('Comissão')
                            ->formatStateUsing(fn ($state) => self::formatCurrency($state)),

                        TextEntry::make('bonuses')
                            ->label('Bonificações')
                            ->formatStateUsing(fn ($state) => self::formatCurrency($state)),

                        TextEntry::make('inss')
                            ->label('INSS')
                            ->formatStateUsing(fn ($state) => self::formatCurrency($state))
                            ->hidden(fn (Model $record) => $record->contract_type !== 'clt'),

                        TextEntry::make('fgts')
                            ->label('FGTS')
                            ->formatStateUsing(fn ($state) => self::formatCurrency($state))
                            ->hidden(fn (Model $record) => $record->contract_type !== 'clt'),

                        TextEntry::make('vt')
                            ->label('Vale Transporte')
                            ->formatStateUsing(fn ($state) => self::formatCurrency($state))
                            ->hidden(fn (Model $record) => $record->contract_type !== 'clt'),

                        TextEntry::make('irrf')
                            ->label(fn (Model $record) => $record->contract_type === 'pj' ? 'Imposto de Renda' : 'IRRF')
                            ->formatStateUsing(fn ($state) => self::formatCurrency($state)),

                        TextEntry::make('other_deductions')
                            ->label('Outros Descontos')
                            ->formatStateUsing(fn ($state) => self::formatCurrency($state)),

                        TextEntry::make('deductions')
                            ->label('Descontos Totais')
                            ->formatStateUsing(fn ($state) => self::formatCurrency($state)),

                        TextEntry::make('net_pay')
                            ->label('Salário Líquido')
                            ->formatStateUsing(fn ($state) => self::formatCurrency($state)),

                        TextEntry::make('payment_method')
                            ->label('Método de Pagamento')
                            ->formatStateUsing(function ($state) {
                                $methods = [
                                    'transfer' => 'Transferência Bancária',
                                    'check'    => 'Cheque',
                                    'cash'     => 'Dinheiro',
                                    'pix'      => 'Pix',
                                ];
                                return $methods[$state] ?? ucfirst($state);
                            }),

                        TextEntry::make('comments')
                            ->label('Comentários'),
                    ])
                    ->columns(2),
            ]);
    }

    /**
     * Relacionamentos (se houver)
     */
    public static function getRelations(): array
    {
        return [];
    }

    /**
     * Páginas do recurso
     */
    public static function getPages(): array
    {
        return [
            'index'  => ListPayrolls::route('/'),
            'create' => CreatePayroll::route('/create'),
            'view'   => ViewPayroll::route('/{record}'),
            'edit'   => EditPayroll::route('/{record}/edit'),
        ];
    }
}
