<?php 

namespace App\Filament\Resources\Admin;

use App\Filament\Resources\Admin\EmployeeResource\Pages\ListEmployees;
use App\Filament\Resources\Admin\EmployeeResource\Pages\CreateEmployee;
use App\Filament\Resources\Admin\EmployeeResource\Pages\EditEmployee;
use App\Models\City;
use App\Models\Employee;
use App\Models\State;
use App\Models\Department;
use App\Models\Team;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class EmployeeResource extends Resource
{           
    public static function getModelLabel(): string
    {
        return 'Funcionário';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Funcionários';
    }

    protected static ?string $model = Employee::class;
    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?string $navigationGroup = 'Gestão de Funcionários';
    protected static ?string $recordTitleAttribute = 'first_name';

    public static function getGlobalSearchResultTitle(Model $record): string
    {
        return "{$record->first_name} {$record->last_name}";
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['first_name', 'last_name', 'email'];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        return static::getModel()::count() > 10 ? 'warning' : 'success';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Relacionamentos')
                    ->schema([
                        Select::make('state_id')
                            ->label('Estado')
                            ->options(fn () => State::pluck('name', 'id'))
                            ->searchable()
                            ->preload()
                            ->required()
                            ->reactive()
                            ->placeholder('Selecione um Estado')
                            ->afterStateUpdated(fn (callable $set) => $set('city_id', null)),
                        Select::make('city_id')
                            ->label('Cidade')
                            ->options(fn (callable $get) => City::where('state_id', $get('state_id'))->pluck('name', 'id'))
                            ->searchable()
                            ->preload()
                            ->required()
                            ->placeholder('Selecione uma Cidade'),
                        Select::make('department_id')
                            ->label('Departamento')
                            ->relationship('department', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->placeholder('Selecione um Departamento'),
                        Select::make('team_id')
                            ->label('Equipe')
                            ->relationship('team', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->placeholder('Selecione uma Equipe'),
                    ])
                    ->columns(2),
                Forms\Components\Section::make('Informações do Funcionário')
                    ->description('Informe os detalhes pessoais e de contato do funcionário.')
                    ->schema([
                        TextInput::make('first_name')
                            ->label('Primeiro Nome')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Digite o primeiro nome'),
                        TextInput::make('last_name')
                            ->label('Último Nome')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Digite o último nome'),
                        TextInput::make('middle_name')
                            ->label('Nome do Meio')
                            ->maxLength(255)
                            ->placeholder('Digite o nome do meio (opcional)'),
                        TextInput::make('email')
                            ->label('E-mail')
                            ->required()
                            ->email()
                            ->unique(Employee::class, 'email', ignoreRecord: true)
                            ->placeholder('Digite o e-mail'),
                        Select::make('contract_type')
                            ->label('Tipo de Contrato')
                            ->options([
                                'CLT' => 'CLT',
                                'CNPJ' => 'CNPJ',
                            ])
                            ->required()
                            ->placeholder('Selecione o tipo de contrato'),
                        TextInput::make('phone')
                            ->label('Telefone')
                            ->tel()
                            ->maxLength(15)
                            ->placeholder('(XX) XXXXX-XXXX'),
                        Select::make('marital_status')
                            ->label('Estado Civil')
                            ->options([
                                'single' => 'Solteiro(a)',
                                'married' => 'Casado(a)',
                                'divorced' => 'Divorciado(a)',
                                'widowed' => 'Viúvo(a)',
                            ])
                            ->placeholder('Selecione o estado civil'),
                        TextInput::make('nationality')
                            ->label('Nacionalidade')
                            ->maxLength(100)
                            ->placeholder('Digite a nacionalidade'),
                    ])
                    ->columns(2),
                Forms\Components\Section::make('Endereço do Funcionário')
                    ->schema([
                        TextInput::make('address')
                            ->label('Endereço')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Digite o endereço completo'),
                        TextInput::make('zip_code')
                            ->label('CEP')
                            ->required()
                            ->maxLength(10)
                            ->placeholder('Digite o CEP'),
                    ])
                    ->columns(2),
                Forms\Components\Section::make('Datas')
                    ->schema([
                        DatePicker::make('date_of_birth')
                            ->label('Data de Nascimento')
                            ->native(false)
                            ->displayFormat('d/m/Y')
                            ->required()
                            ->placeholder('Selecione a data de nascimento'),
                        DatePicker::make('date_hired')
                            ->label('Data de Contratação')
                            ->native(false)
                            ->displayFormat('d/m/Y')
                            ->required()
                            ->placeholder('Selecione a data de contratação'),
                    ])
                    ->columns(2)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('first_name')
                    ->label('Primeiro Nome')
                    ->searchable()
                    ->sortable()
                    ->color('primary'),
                TextColumn::make('last_name')
                    ->label('Último Nome')
                    ->searchable()
                    ->sortable()
                    ->color('primary'),
                TextColumn::make('email')
                    ->label('E-mail')
                    ->searchable()
                    ->sortable()
                    ->url(fn (Employee $record) => "mailto:{$record->email}")
                    ->openUrlInNewTab(),
                // Substituição de BadgeColumn por TextColumn com badge()
                TextColumn::make('contract_type')
                    ->label('Tipo de Contrato')
                    ->badge(function ($state) {
                        return match ($state) {
                            'CLT' => 'success',
                            'CNPJ' => 'warning',
                            default => 'secondary',
                        };
                    })
                    ->formatStateUsing(function ($state) {
                        return ucfirst($state);
                    })
                    ->sortable(),
                TextColumn::make('phone')
                    ->label('Telefone')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('marital_status')
                    ->label('Estado Civil')
                    ->formatStateUsing(function ($state) {
                        $labels = [
                            'single' => 'Solteiro(a)',
                            'married' => 'Casado(a)',
                            'divorced' => 'Divorciado(a)',
                            'widowed' => 'Viúvo(a)',
                        ];

                        return $labels[$state] ?? ucfirst($state);
                    })
                    ->sortable(),
                TextColumn::make('date_of_birth')
                    ->label('Data de Nascimento')
                    ->date('d/m/Y')
                    ->sortable(),
                TextColumn::make('date_hired')
                    ->label('Data de Contratação')
                    ->date('d/m/Y')
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
                SelectFilter::make('department_id')
                    ->relationship('department', 'name')
                    ->searchable()
                    ->preload()
                    ->label('Filtrar por Departamento')
                    ->indicator('Departamento'),
                Filter::make('created_at')
                    ->form([
                        DatePicker::make('created_from')->label('Criado a partir de'),
                        DatePicker::make('created_until')->label('Criado até'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'] ?? null,
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'] ?? null,
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if (!empty($data['created_from'])) {
                            $indicators['created_from'] = 'Criado a partir de ' . Carbon::parse($data['created_from'])->format('d/m/Y');
                        }
                        if (!empty($data['created_until'])) {
                            $indicators['created_until'] = 'Criado até ' . Carbon::parse($data['created_until'])->format('d/m/Y');
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
                            ->title('Funcionário excluído.')
                            ->body('O funcionário foi excluído com sucesso.')
                    )
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make()
                    ->label('Excluir Selecionados')
                    ->icon('heroicon-o-trash')
                    ->color('danger')
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title('Funcionários excluídos.')
                            ->body('Os funcionários selecionados foram excluídos com sucesso.')
                    ),
            ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()
                    ->label('Criar Novo Funcionário')
                    ->icon('heroicon-o-plus')
                    ->color('success'),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Relacionamentos')
                    ->schema([
                        TextEntry::make('state.name')->label('Estado'),
                        TextEntry::make('city.name')->label('Cidade'),
                        TextEntry::make('department.name')->label('Departamento'),
                        TextEntry::make('team.name')->label('Equipe'),
                    ])
                    ->columns(2),
                Section::make('Nome')
                    ->schema([
                        TextEntry::make('first_name')->label('Primeiro Nome'),
                        TextEntry::make('last_name')->label('Último Nome'),
                        TextEntry::make('email')->label('E-mail'),
                    ])
                    ->columns(3),
                Section::make('Endereço')
                    ->schema([
                        TextEntry::make('address')->label('Endereço'),
                        TextEntry::make('zip_code')->label('CEP'),
                    ])
                    ->columns(2),
                Section::make('Informações Adicionais')
                    ->schema([
                        TextEntry::make('phone')->label('Telefone'),
                        TextEntry::make('marital_status')->label('Estado Civil'),
                        TextEntry::make('nationality')->label('Nacionalidade'),
                        TextEntry::make('contract_type')->label('Tipo de Contrato'),
                    ])
                    ->columns(2),
                Section::make('Datas Importantes')
                    ->schema([
                        TextEntry::make('date_of_birth')->label('Data de Nascimento'),
                        TextEntry::make('date_hired')->label('Data de Contratação'),
                    ])
                    ->columns(2),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListEmployees::route('/'),
            'create' => CreateEmployee::route('/create'),
            'edit' => EditEmployee::route('/{record}/edit'),
        ];
    }
}
