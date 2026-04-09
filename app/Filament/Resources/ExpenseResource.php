<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ExpenseResource\Pages;
use App\Models\Expense;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ExpenseResource extends Resource
{
    protected static ?string $model = Expense::class;
    protected static ?string $navigationIcon = 'heroicon-o-credit-card';
    protected static ?string $navigationGroup = 'Financeiro';
    protected static ?string $modelLabel = 'Despesa';
    protected static ?string $pluralModelLabel = 'Despesas da Box';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('description')
                    ->label('Descrição da Despesa')
                    ->placeholder('Ex: Conta de Luz, Aluguel, Compra de Anilhas')
                    ->required(),
                Forms\Components\Select::make('category')
                    ->label('Categoria')
                    ->options([
                        'Contas Fixas (Água, Luz, Internet)' => 'Contas Fixas (Água, Luz, Internet)',
                        'Aluguel' => 'Aluguel',
                        'Salários / Professores' => 'Salários / Professores',
                        'Equipamentos / Manutenção' => 'Equipamentos / Manutenção',
                        'Impostos / Taxas' => 'Impostos / Taxas',
                        'Outros' => 'Outros',
                    ])
                    ->required(),
                Forms\Components\TextInput::make('amount')
                    ->label('Valor Pago')
                    ->numeric()
                    ->prefix('R$')
                    ->required(),
                Forms\Components\DatePicker::make('date')
                    ->label('Data de Pagamento')
                    ->default(now())
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('date')
                    ->label('Data')
                    ->date('d/m/Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('description')
                    ->label('Descrição')
                    ->searchable(),
                Tables\Columns\TextColumn::make('category')
                    ->label('Categoria')
                    ->badge(),
                Tables\Columns\TextColumn::make('amount')
                    ->label('Valor')
                    ->money('BRL')
                    ->sortable()
                    ->color('danger'),
            ])
            ->defaultSort('date', 'desc')
            ->actions([ Tables\Actions\EditAction::make(), Tables\Actions\DeleteAction::make() ]);
    }

    public static function getPages(): array {
        return [ 'index' => Pages\ManageExpenses::route('/') ];
    }
}