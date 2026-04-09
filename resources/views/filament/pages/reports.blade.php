<x-filament-panels::page>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        
        <x-filament::section>
            <x-slot name="heading">
                👥 Alunos
            </x-slot>
            <div class="space-y-2">
                <p><strong>Total Matriculados:</strong> {{ $totalAlunos }}</p>
                <p><strong>Não Renovaram / Vencidos:</strong> {{ $alunosInativos }}</p>
            </div>
        </x-filament::section>

        <x-filament::section>
            <x-slot name="heading">
                📦 Estoque e Geladeira
            </x-slot>
            <div class="space-y-2">
                <p><strong>Total de Itens na Geladeira:</strong> {{ $totalItensEstoque }} unidades</p>
                <p><strong>Valor Investido (Parado):</strong> R$ {{ number_format($valorEmEstoque, 2, ',', '.') }}</p>
            </div>
        </x-filament::section>

        <x-filament::section class="md:col-span-2">
            <x-slot name="heading">
                💰 Financeiro (Bebidas)
            </x-slot>
            <div class="space-y-2 text-xl">
                <p><strong>Lucro Total Gerado:</strong> <span class="text-green-600">R$ {{ number_format($lucroTotal, 2, ',', '.') }}</span></p>
            </div>
        </x-filament::section>

    </div>
</x-filament-panels::page>