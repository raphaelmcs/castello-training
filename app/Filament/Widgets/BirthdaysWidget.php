<?php

namespace App\Filament\Widgets;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Filament\Tables\Columns\TextColumn;
use App\Models\Student;

class BirthdaysWidget extends BaseWidget
{
    protected static ?int $sort = 2;

    public function table(Table $table): Table
{
    return $table
        ->query(
            Student::whereMonth('data_nascimento', now()->month)
                   ->orderByRaw('DAY(data_nascimento) ASC')
        )
        ->columns([
            TextColumn::make('nome')->label('Aluno'),
            TextColumn::make('data_nascimento')
                ->label('Data')
                ->date('d/m'),
        ])
        ->heading('🎉 Aniversariantes do Mês');
}

}
