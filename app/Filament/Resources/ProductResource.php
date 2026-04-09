<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-archive-box';
    
    protected static ?string $navigationGroup = 'Geladeira / Estoque';

    protected static ?string $modelLabel = 'Produto';

    protected static ?string $pluralModelLabel = 'Produtos';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Nome do Produto')
                    ->placeholder('Ex: Monster, Gatorade')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('cost_price')
                    ->label('Preço de Custo')
                    ->numeric()
                    ->prefix('R$')
                    ->required(),
                Forms\Components\TextInput::make('price')
                    ->label('Preço de Venda')
                    ->numeric()
                    ->prefix('R$')
                    ->required(),
                Forms\Components\TextInput::make('stock_quantity')
                    ->label('Quantidade em Estoque')
                    ->numeric()
                    ->default(0)
                    ->readOnly()
                    ->helperText('O estoque é atualizado automaticamente ao registrar entradas ou saídas.'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Produto')
                    ->searchable(),
                Tables\Columns\TextColumn::make('cost_price')
                    ->label('Custo')
                    ->money('BRL')
                    ->sortable(),
                Tables\Columns\TextColumn::make('price')
                    ->label('Venda')
                    ->money('BRL')
                    ->sortable(),
                Tables\Columns\TextColumn::make('stock_quantity')
                    ->label('Estoque')
                    ->sortable()
                    ->badge()
                    ->color(fn (int $state): string => match (true) {
                        $state <= 5 => 'danger',
                        $state <= 10 => 'warning',
                        default => 'success',
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageProducts::route('/'),
        ];
    }
}