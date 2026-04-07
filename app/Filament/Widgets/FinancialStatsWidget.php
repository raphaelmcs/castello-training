<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Payment;

class FinancialStatsWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
{
    $hoje = now()->startOfDay();
    
    // Vencidas (data de vencimento passou e não tem data de pagamento)
    $vencidasQuery = Payment::with('student')->whereNull('data_pagamento')->where('data_vencimento', '<', $hoje);
    $vencidasCount = $vencidasQuery->count();
    $alunosVencidos = $vencidasQuery->get()->pluck('student.nome')->filter()->unique()->implode(', ');
    $tooltipVencidos = htmlspecialchars($alunosVencidos ?: 'Nenhuma mensalidade vencida', ENT_QUOTES);
    
    // A Vencer (vence nos próximos 7 dias)
    $aVencerQuery = Payment::with('student')->whereNull('data_pagamento')->whereBetween('data_vencimento', [$hoje, $hoje->copy()->addDays(7)]);
    $aVencerCount = $aVencerQuery->count();
    $alunosAVencer = $aVencerQuery->get()->pluck('student.nome')->filter()->unique()->implode(', ');
    $tooltipAVencer = htmlspecialchars($alunosAVencer ?: 'Nenhuma mensalidade a vencer', ENT_QUOTES);
    
    // Em dia (pagas este mês ou vencimento futuro seguro)
    // Uma forma simples é contar quem pagou a última mensalidade
    $emDia = Payment::whereNotNull('data_pagamento')->whereMonth('data_vencimento', $hoje->month)->count();

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
    ];
}

}
