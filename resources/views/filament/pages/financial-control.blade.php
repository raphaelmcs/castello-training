<x-filament-panels::page>
    
    <!-- Filtro de Período -->
    <div class="flex justify-end mb-2">
        <div class="w-full md:w-64">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Filtrar Período Analisado:</label>
            <select wire:model.live="period" class="w-full p-2 border border-gray-300 rounded-lg shadow-sm text-gray-900 bg-white dark:bg-gray-800 dark:border-gray-600 dark:text-white focus:ring-primary-500 focus:border-primary-500">
                <option value="current_month">Mês Atual</option>
                <option value="last_month">Mês Passado</option>
                <option value="current_quarter">Trimestre Atual</option>
                <option value="current_semester">Semestre Atual</option>
                <option value="current_year">Ano Atual</option>
                <option value="all_time">Balanço Geral (Tudo)</option>
            </select>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Card Entradas -->
        <x-filament::section class="bg-emerald-50 dark:bg-emerald-900/20 ring-1 ring-emerald-200 dark:ring-emerald-800">
            <x-slot name="heading">
                <span class="text-emerald-700 dark:text-emerald-400 text-lg flex items-center gap-2">📈 Entradas (Receitas)</span>
            </x-slot>
            <div class="text-3xl font-bold text-emerald-600 dark:text-emerald-400 mb-4 border-b border-emerald-200 dark:border-emerald-800 pb-4">
                R$ {{ number_format($totalReceitas, 2, ',', '.') }}
            </div>
            <div class="text-sm space-y-2 text-gray-700 dark:text-gray-300">
                <div class="flex justify-between"><span>+ Mensalidades:</span> <strong>R$ {{ number_format($receitaMensalidades, 2, ',', '.') }}</strong></div>
                <div class="flex justify-between"><span>+ Diárias Avulsas:</span> <strong>R$ {{ number_format($receitaDiarias, 2, ',', '.') }}</strong></div>
                <div class="flex justify-between"><span>+ Vendas Geladeira:</span> <strong>R$ {{ number_format($receitaGeladeira, 2, ',', '.') }}</strong></div>
            </div>
        </x-filament::section>

        <!-- Card Saídas -->
        <x-filament::section class="bg-rose-50 dark:bg-rose-900/20 ring-1 ring-rose-200 dark:ring-rose-800">
            <x-slot name="heading">
                <span class="text-rose-700 dark:text-rose-400 text-lg flex items-center gap-2">📉 Saídas (Despesas)</span>
            </x-slot>
            <div class="text-3xl font-bold text-rose-600 dark:text-rose-400 mb-4 border-b border-rose-200 dark:border-rose-800 pb-4">
                R$ {{ number_format($totalDespesas, 2, ',', '.') }}
            </div>
            <div class="text-sm space-y-2 text-gray-700 dark:text-gray-300">
                <div class="flex justify-between"><span>- Despesas da Box:</span> <strong>R$ {{ number_format($despesasGerais, 2, ',', '.') }}</strong></div>
                <div class="flex justify-between"><span>- Compras Geladeira:</span> <strong>R$ {{ number_format($despesasGeladeira, 2, ',', '.') }}</strong></div>
            </div>
        </x-filament::section>

        <!-- Card Saldo -->
        @php $isPositive = $saldo >= 0; @endphp
        <x-filament::section class="{{ $isPositive ? 'bg-sky-50 dark:bg-sky-900/20 ring-1 ring-sky-200 dark:ring-sky-800' : 'bg-orange-50 dark:bg-orange-900/20 ring-1 ring-orange-200 dark:ring-orange-800' }}">
            <x-slot name="heading">
                <span class="{{ $isPositive ? 'text-sky-700 dark:text-sky-400' : 'text-orange-700 dark:text-orange-400' }} text-lg flex items-center gap-2">💰 Saldo Líquido</span>
            </x-slot>
            <div class="text-4xl font-black {{ $isPositive ? 'text-sky-600 dark:text-sky-400' : 'text-orange-600 dark:text-orange-400' }}">
                R$ {{ number_format($saldo, 2, ',', '.') }}
            </div>
        </x-filament::section>
    </div>
</x-filament-panels::page>