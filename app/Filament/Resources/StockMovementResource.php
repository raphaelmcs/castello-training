<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StockMovementResource\Pages;
use App\Models\StockMovement;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class StockMovementResource extends Resource
{
    protected static ?string $model = StockMovement::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrows-right-left';
    
    protected static ?string $navigationGroup = 'Geladeira / Estoque';

    protected static ?string $modelLabel = 'Movimentação';

    protected static ?string $pluralModelLabel = 'Movimentações de Estoque';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('product_id')
                    ->relationship('product', 'name')
                    ->label('Produto')
                    ->required()
                    ->searchable()
                    ->preload(),
                Forms\Components\Select::make('type')
                    ->label('Tipo')
                    ->options([
                        'in' => 'Entrada (+)',
                        'out' => 'Saída (-)',
                    ])
                    ->required(),
                Forms\Components\TextInput::make('quantity')
                    ->label('Quantidade')
                    ->numeric()
                    ->required()
                    ->minValue(1)
                    ->default(1),
                Forms\Components\TextInput::make('description')
                    ->label('Observação')
                    ->maxLength(255)
                    ->placeholder('Ex: Compra mercado / Venda aluno X'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('product.name')
                    ->label('Produto')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('type')
                    ->label('Tipo')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'in' => 'success',
                        'out' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'in' => 'Entrada',
                        'out' => 'Saída',
                        default => $state,
                    }),
                Tables\Columns\TextColumn::make('quantity')
                    ->label('Qtd')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('lucro')
                    ->label('Lucro')
                    ->money('BRL')
                    ->state(function (StockMovement $record): float {
                        if ($record->type === 'out' && $record->product) {
                            return ($record->product->price - $record->product->cost_price) * $record->quantity;
                        }
                        return 0;
                    }),
                Tables\Columns\TextColumn::make('description')
                    ->label('Observação')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Data')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->actions([
                Tables\Actions\DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageStockMovements::route('/'),
        ];
    }
}