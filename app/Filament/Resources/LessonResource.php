<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LessonResource\Pages;
use App\Models\Lesson;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class LessonResource extends Resource
{
    protected static ?string $model = Lesson::class;
    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';
    protected static ?string $modelLabel = 'Lista de Chamada';
    protected static ?string $pluralModelLabel = 'Listas de Chamada';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\DatePicker::make('date')->label('Data da Aula')->default(now())->required(),
                Forms\Components\Select::make('schedule_id')
                    ->relationship('schedule', 'name')
                    ->label('Turma')
                    ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->name} ({$record->time_slot})")
                    ->required()
                    ->live()
                    ->afterStateUpdated(function (\Filament\Forms\Set $set, $state) {
                        if ($state) {
                            $students = \App\Models\Student::where('schedule_id', $state)->pluck('id')->toArray();
                            $set('students', $students);
                        }
                    }),
                Forms\Components\Select::make('students')
                    ->relationship('students', 'nome')
                    ->label('Alunos Presentes')
                    ->multiple()
                    ->searchable()
                    ->preload()
                    ->helperText('Os alunos matriculados nesta turma já vêm marcados. Você pode adicionar ou remover alunos de outras turmas livremente.')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('date')->label('Data')->date('d/m/Y')->sortable(),
                Tables\Columns\TextColumn::make('schedule.name')->label('Turma')->sortable(),
                Tables\Columns\TextColumn::make('students_count')->counts('students')->label('Total Presente'),
            ])
            ->defaultSort('date', 'desc')
            ->actions([ Tables\Actions\EditAction::make(), Tables\Actions\DeleteAction::make() ]);
    }

    public static function getPages(): array
    {
        return [ 'index' => Pages\ManageLessons::route('/') ];
    }
}