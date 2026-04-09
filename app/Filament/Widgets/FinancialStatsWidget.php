<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Payment;
use App\Models\StockMovement;

class FinancialStatsWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
{
    $hoje = now()->startOfDay();
    $hojeStr = $hoje->toDateString();
    $seteDiasStr = $hoje->copy()->addDays(7)->toDateString();
    
    // Vencidas (data de vencimento passou e não tem data de pagamento)
    $vencidasQuery = Payment::with('student')->whereNull('data_pagamento')->where('data_vencimento', '<', $hojeStr);
    $vencidasCount = $vencidasQuery->count();
    $alunosVencidos = $vencidasQuery->get()->pluck('student.nome')->filter()->unique()->implode(', ');
    $tooltipVencidos = htmlspecialchars($alunosVencidos ?: 'Nenhuma mensalidade vencida', ENT_QUOTES);
    
    // A Vencer (vence nos próximos 7 dias)
    $aVencerQuery = Payment::with('student')
        ->whereNull('data_pagamento')
        ->whereDate('data_vencimento', '>=', $hojeStr)
        ->whereDate('data_vencimento', '<=', $seteDiasStr);
    $aVencerCount = $aVencerQuery->count();
    $alunosAVencer = $aVencerQuery->get()->pluck('student.nome')->filter()->unique()->implode(', ');
    $tooltipAVencer = htmlspecialchars($alunosAVencer ?: 'Nenhuma mensalidade a vencer', ENT_QUOTES);
    
    // Em dia (pagas este mês ou vencimento futuro seguro)
    // Uma forma simples é contar quem pagou a última mensalidade
    $emDia = Payment::whereNotNull('data_pagamento')->whereMonth('data_vencimento', $hoje->month)->count();
    
    // Lucro de Estoque
    $lucroEstoque = StockMovement::with('product')->where('type', 'out')->get()->sum(function($movement) {
        return ($movement->product->price - $movement->product->cost_price) * $movement->quantity;
    });
    $lucroFormatado = 'R$ ' . number_format($lucroEstoque, 2, ',', '.');

    return [
        Stat::make('Mensalidades Vencidas', new \Illuminate\Support\HtmlString("<span x-data x-tooltip.raw=\"{$tooltipVencidos}\" style=\"cursor: help; border-bottom: 2px dotted gray;\">{$vencidasCount}</span>"))
            ->description('Atrasadas')
            ->descriptionIcon('heroicon-m-x-circle')
            ->color('danger'),
            
        Stat::make('A Vencer (7 dias)', new \Illuminate\Support\HtmlString("<span x-data x-tooltip.raw=\"{$tooltipAVencer}\" style=\"cursor: help; border-bottom: 2px dotted gray;\">{$aVencerCount}</span>"))
            ->description('Vencendo em breve')
            ->descriptionIcon('heroicon-m-exclamation-circle')
            ->color('warning'),
            
        Stat::make('Pagamentos em Dia', $emDia)
            ->description('Tudo certo')
            ->descriptionIcon('heroicon-m-check-circle')
            ->color('info'),
            
        Stat::make('Lucro da Geladeira', $lucroFormatado)
            ->description('Lucro total de vendas')
            ->descriptionIcon('heroicon-m-currency-dollar')
            ->color('success'),
    ];
}

}
