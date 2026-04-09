<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StudentResource\Pages;
use App\Filament\Resources\StudentResource\RelationManagers;
use App\Models\Student;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Actions\DeleteAction;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class StudentResource extends Resource
{
    protected static ?string $model = Student::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $modelLabel = 'Matrícula';

    protected static ?string $pluralModelLabel = 'Matrículas';

    protected static ?int $navigationSort = 1;

        public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nome')
                    ->label('Nome Completo')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('email')
                    ->label('E-mail')
                    ->email()
                    ->maxLength(255),
                Forms\Components\TextInput::make('telefone')
                    ->label('Telefone')
                    ->tel()
                    ->maxLength(20),
                Forms\Components\DatePicker::make('data_nascimento')
                    ->label('Data de Nascimento')
                    ->required(),
                Forms\Components\DatePicker::make('data_matricula')
                    ->label('Data da Matrícula')
                    ->default(now())
                    ->required(),
                Forms\Components\Select::make('plan_id')
                    ->relationship('plan', 'name')
                    ->label('Plano Escolhido')
                    ->required()
                    ->preload() // Carrega os planos automaticamente na lista
                    ->searchable(),
                Forms\Components\Select::make('schedule_id')
                    ->relationship('schedule', 'name')
                    ->label('Turma / Horário')
                    ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->name} ({$record->time_slot})")
                    ->preload()
                    ->searchable(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nome')
                    ->label('Nome')
                    ->searchable(),
                Tables\Columns\TextColumn::make('plan.name')
                    ->label('Plano')
                    ->sortable(),
                Tables\Columns\TextColumn::make('schedule.name')
                    ->label('Turma')
                    ->sortable(),
                Tables\Columns\TextColumn::make('telefone')
                    ->label('Telefone'),
                Tables\Columns\TextColumn::make('data_matricula')
                    ->label('Matriculado em')
                    ->date('d/m/Y')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('plan_id')
                    ->relationship('plan', 'name')
                    ->label('Plano')
                    ->searchable()
                    ->preload(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
            'index' => Pages\ListStudents::route('/'),
            'create' => Pages\CreateStudent::route('/create'),
            'edit' => Pages\EditStudent::route('/{record}/edit'),
        ];
    }
}
