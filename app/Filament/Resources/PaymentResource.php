<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PaymentResource\Pages;
use App\Filament\Resources\PaymentResource\RelationManagers;
use App\Models\Payment;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PaymentResource extends Resource
{
    protected static ?string $model = Payment::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';

    protected static ?string $modelLabel = 'Pagamento';

    protected static ?string $pluralModelLabel = 'Pagamentos';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('student_id')
                    ->relationship('student', 'nome')
                    ->label('Aluno')
                    ->required()
                    ->searchable()
                    ->preload()
                    ->live()
                    ->afterStateUpdated(function (Forms\Set $set, $state) {
                        $student = \App\Models\Student::with('plan')->find($state);
                        if ($student) {
                            if ($student->plan) {
                                $set('valor', $student->plan->price);
                            }
                            if ($student->data_matricula) {
                                $diaMatricula = $student->data_matricula->day;
                                // min() garante que dias como 31 não quebrem em meses como fevereiro (que vai até 28/29)
                                $set('data_vencimento', now()->day(min($diaMatricula, now()->daysInMonth))->toDateString());
                            }
                        }
                    }),
                Forms\Components\DatePicker::make('data_vencimento')
                    ->label('Data de Vencimento')
                    ->required(),
                Forms\Components\DatePicker::make('data_pagamento')
                    ->label('Data de Pagamento'),
                Forms\Components\TextInput::make('valor')
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
                Tables\Columns\TextColumn::make('student.nome')
                    ->label('Aluno')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('data_vencimento')
                    ->label('Vencimento')
                    ->date('d/m/Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('data_pagamento')
                    ->label('Pagamento')
                    ->date('d/m/Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('valor')
                    ->label('Valor')
                    ->money('BRL')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->state(function (Payment $record) {
                        if ($record->data_pagamento) {
                            return 'Pago';
                        }
                        if ($record->data_vencimento < now()->startOfDay()) {
                            return 'Vencido';
                        }
                        return 'Pendente';
                    })
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Pago' => 'success',
                        'Vencido' => 'danger',
                        'Pendente' => 'warning',
                        default => 'gray',
                    }),
            ])
            ->filters([
                //
            ])
            ->actions([
            Tables\Actions\Action::make('marcar_pago')
                ->label('Marcar Pago')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->action(fn (Payment $record) => $record->update(['data_pagamento' => now()]))
                ->visible(fn (Payment $record) => $record->data_pagamento === null),
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
            'index' => Pages\ListPayments::route('/'),
            'create' => Pages\CreatePayment::route('/create'),
            'edit' => Pages\EditPayment::route('/{record}/edit'),
        ];
    }
}
