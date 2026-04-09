<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Models\Payment;
use App\Models\Lead;
use App\Models\StockMovement;
use App\Models\Expense;

class FinancialControl extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-presentation-chart-line';
    protected static string $view = 'filament.pages.financial-control';
    protected static ?string $title = 'Balanço Financeiro';
    protected static ?string $navigationGroup = 'Financeiro';
    protected static ?int $navigationSort = 1;

    public $period = 'current_month'; // Variável conectada ao filtro na tela

    public function getViewData(): array
    {
        // 1. Definindo as datas baseadas no filtro escolhido
        $start = match($this->period) {
            'current_month' => now()->startOfMonth(),
            'last_month' => now()->subMonth()->startOfMonth(),
            'current_quarter' => now()->startOfQuarter(),
            'current_semester' => now()->month >= 7 ? now()->month(7)->startOfMonth() : now()->startOfYear(),
            'current_year' => now()->startOfYear(),
            'all_time' => now()->subYears(10),
            default => now()->startOfMonth(),
        };

        $end = match($this->period) {
            'current_month' => now()->endOfMonth(),
            'last_month' => now()->subMonth()->endOfMonth(),
            'current_quarter' => now()->endOfQuarter(),
            'current_semester' => now()->month >= 7 ? now()->endOfYear() : now()->month(6)->endOfMonth(),
            'current_year' => now()->endOfYear(),
            'all_time' => now()->addYears(10),
            default => now()->endOfMonth(),
        };

        // 2. Somando Receitas (Entradas)
        // Apenas mensalidades com status 'Pago' (data_pagamento preenchida)
        $receitaMensalidades = Payment::whereNotNull('data_pagamento')->whereBetween('data_pagamento', [$start, $end])->sum('valor');
        $receitaDiarias = Lead::whereBetween('date', [$start, $end])->sum('amount_paid');
        $receitaGeladeira = StockMovement::with('product')->where('type', 'out')->whereBetween('created_at', [$start, $end])->get()->sum(fn($m) => $m->product->price * $m->quantity);
        
        $totalReceitas = $receitaMensalidades + $receitaDiarias + $receitaGeladeira;

        // 3. Somando Despesas (Saídas)
        $despesasGerais = Expense::whereBetween('date', [$start, $end])->sum('amount');
        // O custo da mercadoria comprada para a geladeira
        $despesasGeladeira = StockMovement::with('product')->where('type', 'in')->whereBetween('created_at', [$start, $end])->get()->sum(fn($m) => $m->product->cost_price * $m->quantity);
        
        $totalDespesas = $despesasGerais + $despesasGeladeira;

        // 4. Calculando Saldo Final
        $saldo = $totalReceitas - $totalDespesas;

        return [
            'start' => $start,
            'end' => $end,
            'receitaMensalidades' => $receitaMensalidades,
            'receitaDiarias' => $receitaDiarias,
            'receitaGeladeira' => $receitaGeladeira,
            'totalReceitas' => $totalReceitas,
            'despesasGerais' => $despesasGerais,
            'despesasGeladeira' => $despesasGeladeira,
            'totalDespesas' => $totalDespesas,
            'saldo' => $saldo,
        ];
    }
}