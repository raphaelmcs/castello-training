<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LeadResource\Pages;
use App\Models\Lead;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class LeadResource extends Resource
{
    protected static ?string $model = Lead::class;
    protected static ?string $navigationIcon = 'heroicon-o-user-plus';
    protected static ?string $modelLabel = 'Lead / Visitante';
    protected static ?string $pluralModelLabel = 'Leads & Visitantes';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')->label('Nome Completo')->required(),
                Forms\Components\TextInput::make('phone')->label('WhatsApp')->tel(),
                Forms\Components\Radio::make('type')
                    ->label('Tipo de Visita')
                    ->options([
                        'experimental' => 'Aula Experimental (Gratuita)',
                        'diaria' => 'Diária Avulsa (R$ 20,00)',
                    ])
                    ->required()
                    ->live()
                    ->afterStateUpdated(function (\Filament\Forms\Set $set, $state) {
                        $set('amount_paid', $state === 'diaria' ? 20.00 : 0.00);
                    }),
                Forms\Components\TextInput::make('amount_paid')
                    ->label('Valor Pago')
                    ->numeric()
                    ->prefix('R$')
                    ->default(0),
                Forms\Components\DatePicker::make('date')->label('Data da Visita')->default(now())->required(),
                Forms\Components\Select::make('schedule_id')
                    ->relationship('schedule', 'name')
                    ->label('Turma / Horário da Visita')
                    ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->name} ({$record->time_slot})")
                    ->preload(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->label('Nome')->searchable(),
                Tables\Columns\TextColumn::make('type')
                    ->label('Tipo')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'experimental' => 'info',
                        'diaria' => 'success',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'experimental' => 'Experimental',
                        'diaria' => 'Diária',
                        default => $state,
                    }),
                Tables\Columns\TextColumn::make('amount_paid')->label('Recebido')->money('BRL'),
                Tables\Columns\TextColumn::make('date')->label('Data')->date('d/m/Y'),
            ])
            ->actions([ Tables\Actions\EditAction::make(), Tables\Actions\DeleteAction::make() ]);
    }

    public static function getPages(): array {
        return [ 'index' => Pages\ManageLeads::route('/') ];
    }
}