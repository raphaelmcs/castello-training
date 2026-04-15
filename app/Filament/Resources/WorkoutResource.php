<?php

namespace App\Filament\Resources;

use App\Filament\Resources\WorkoutResource\Pages;
use App\Models\Workout;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class WorkoutResource extends Resource
{
    protected static ?string $model = Workout::class;

    protected static ?string $navigationIcon = 'heroicon-o-bolt';

    protected static ?string $modelLabel = 'Treino';

    protected static ?string $pluralModelLabel = 'Treinos';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Configurações Principais')
                    ->description('Defina a data e o nível alvo deste treino.')
                    ->schema([
                        Forms\Components\DatePicker::make('date')
                            ->label('Data do Treino')
                            ->default(now())
                            ->required(),
                        Forms\Components\Select::make('level')
                            ->label('Nível da Turma')
                            ->options([
                                'Iniciante' => 'Iniciante',
                                'Intermediário' => 'Intermediário',
                                'Avançado' => 'Avançado',
                                'RX' => 'RX',
                                'Todos' => 'Todos os Níveis',
                            ])
                            ->required(),
                    ])->columns(2),

                Forms\Components\Section::make('Skill / Força / Técnica')
                    ->schema([
                        Forms\Components\RichEditor::make('skill')
                            ->label('Descrição do Aquecimento e Técnica')
                            ->toolbarButtons(['bold', 'italic', 'bulletList', 'orderedList', 'h2', 'h3', 'undo', 'redo'])
                            ->required(),
                    ]),

                Forms\Components\Section::make('WOD (Workout of the Day)')
                    ->schema([
                        Forms\Components\Select::make('wod_type')
                            ->label('Modalidade do WOD')
                            ->options([
                                'AMRAP' => 'AMRAP (As Many Rounds As Possible)',
                                'EMOM' => 'EMOM (Every Minute on the Minute)',
                                'For Time' => 'For Time',
                                'Tabata' => 'Tabata',
                                'Chipper' => 'Chipper',
                                'Cindy' => 'Cindy',
                                'Relay' => 'Relay (Revezamento)',
                                'Outro' => 'Outro',
                            ])
                            ->required(),
                        Forms\Components\RichEditor::make('wod_description')
                            ->label('Descrição do WOD')
                            ->toolbarButtons(['bold', 'italic', 'bulletList', 'orderedList', 'h2', 'h3', 'undo', 'redo'])
                            ->required(),
                    ]),

                Forms\Components\Section::make('Observações Extras')
                    ->schema([
                        Forms\Components\Textarea::make('notes')
                            ->label('Avisos ou Adaptações (Opcional)')
                            ->columnSpanFull(),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('date')->label('Data')->date('d/m/Y')->sortable(),
                Tables\Columns\TextColumn::make('level')->label('Nível')->badge(),
                Tables\Columns\TextColumn::make('wod_type')->label('Tipo de WOD')->searchable(),
            ])
            ->defaultSort('date', 'desc')
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListWorkouts::route('/'),
            'create' => Pages\CreateWorkout::route('/create'),
            'edit' => Pages\EditWorkout::route('/{record}/edit'),
        ];
    }
}