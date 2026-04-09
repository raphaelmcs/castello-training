<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Models\Student;
use App\Models\Product;
use App\Models\StockMovement;
use App\Models\Payment;

class Reports extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-chart-pie';

    protected static string $view = 'filament.pages.reports';

    protected static ?string $title = 'Relatórios';
    
    protected static ?string $navigationGroup = 'Administração';

    protected function getViewData(): array
    {
        // Alunos Matriculados (Ativos)
        $totalAlunos = Student::count();

        // Alunos Inadimplentes / Não renovaram (Mensalidade Vencida)
        $alunosInativos = Payment::whereNull('data_pagamento')
            ->where('data_vencimento', '<', now()->startOfDay())
            ->pluck('student_id')
            ->unique()
            ->count();

        // Relatório de Estoque (Total de itens e valor investido)
        $produtos = Product::all();
        $totalItensEstoque = $produtos->sum('stock_quantity');
        $valorEmEstoque = $produtos->sum(fn($p) => $p->stock_quantity * $p->cost_price);

        // Lucros Gerados (Vendas)
        $lucroTotal = StockMovement::with('product')
            ->where('type', 'out')
            ->get()
            ->sum(fn($m) => ($m->product->price - $m->product->cost_price) * $m->quantity);

        return [
            'totalAlunos' => $totalAlunos,
            'alunosInativos' => $alunosInativos,
            'totalItensEstoque' => $totalItensEstoque,
            'valorEmEstoque' => $valorEmEstoque,
            'lucroTotal' => $lucroTotal,
        ];
    }
}