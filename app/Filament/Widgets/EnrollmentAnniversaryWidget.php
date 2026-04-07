<?php

namespace App\Filament\Widgets;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Filament\Tables\Columns\TextColumn;
use App\Models\Student;

class EnrollmentAnniversaryWidget extends BaseWidget
{
    protected static ?int $sort = 3;

    public function table(Table $table): Table
{
    return $table
        ->query(
            Student::whereMonth('data_matricula', now()->month)
                   ->whereYear('data_matricula', '<', now()->year)
                   ->orderByRaw('DAY(data_matricula) ASC')
        )
        ->columns([
            TextColumn::make('nome')->label('Aluno'),
            TextColumn::make('dia_exato')
                ->label('Dia Exato')
                ->state(fn (Student $record) => $record->data_matricula->format('d/m')),
            TextColumn::make('data_matricula')
                ->label('Matriculado em')
                ->date('d/m/Y'),
            TextColumn::make('anos_de_box')
                ->label('Tempo')
                ->state(fn (Student $record) => now()->year - $record->data_matricula->year . ' anos'),
        ])
        ->heading('🏋️‍♂️ Aniversário de Box (Matrícula)');
}

}
