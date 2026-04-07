<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PlanResource\Pages;
use App\Filament\Resources\PlanResource\RelationManagers;
use App\Models\Plan;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PlanResource extends Resource
{
    protected static ?string $model = Plan::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static ?string $modelLabel = 'Plano';

    protected static ?string $pluralModelLabel = 'Planos';

    protected static ?int $navigationSort = 2;

        public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Nome do Plano')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Select::make('days_per_week')
                    ->label('Dias por Semana')
                    ->options([
                        2 => '2 Dias',
                        3 => '3 Dias',
                        5 => '5 Dias',
                    ])
                    ->required(),
                Forms\Components\TextInput::make('price')
                    ->label('Valor da Mensalidade')
                    ->numeric()
                    ->required()
                    ->prefix('R$'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Plano')
                    ->searchable(),
                Tables\Columns\TextColumn::make('days_per_week')
                    ->label('Dias/Semana'),
                Tables\Columns\TextColumn::make('price')
                    ->label('Valor')
                    ->money('BRL'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }


    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPlans::route('/'),
            'create' => Pages\CreatePlan::route('/create'),
            'edit' => Pages\EditPlan::route('/{record}/edit'),
        ];
    }
}
