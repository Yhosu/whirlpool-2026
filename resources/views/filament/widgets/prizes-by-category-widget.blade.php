<x-filament-widgets::widget>
    <style>
        .th-dismac-header {
            background-color: rgba(226, 35, 25, 0.12) !important;
            color: #f87171 !important;
        }
        .th-informal-header {
            background-color: rgba(59, 130, 246, 0.12) !important;
            color: #60a5fa !important;
        }
        .td-badge-anulado {
            display: inline-flex;
            align-items: center;
            padding: 1px 6px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: 600;
            background-color: rgba(255, 255, 255, 0.08);
            color: #94a3b8;
            border: 1px solid rgba(255, 255, 255, 0.14);
        }
        .td-badge-valido {
            display: inline-flex;
            align-items: center;
            padding: 1px 8px;
            border-radius: 9999px;
            font-size: 11px;
            font-weight: 700;
            background-color: rgba(16, 185, 129, 0.15);
            color: #34d399;
        }
    </style>

    <x-filament::section>
        <x-slot name="heading">
            <div class="flex items-center gap-2">
                <x-heroicon-o-gift class="w-5 h-5 text-primary-500" />
                <span class="font-bold text-gray-900 dark:text-gray-100">Premios Entregados e Invalidados por Canal</span>
            </div>
        </x-slot>
        <x-slot name="description">
            <span class="text-xs text-gray-500 dark:text-gray-400">
                Detalle comparativo de premios válidos entregados vs. formularios anulados entre el canal Dismac (Ecommerce) y Mercado Informal.
            </span>
        </x-slot>

        <div class="overflow-x-auto mt-4 rounded-xl border border-gray-200 dark:border-white/10">
            <table class="w-full text-left text-sm divide-y divide-gray-200 dark:divide-white/10">
                <thead class="bg-gray-50 dark:bg-gray-800 text-xs uppercase font-bold text-gray-600 dark:text-gray-300">
                    <tr>
                        <th class="px-4 py-3 min-w-[150px]">Premio</th>
                        <th class="px-4 py-3 text-center border-l border-gray-200 dark:border-white/10 th-dismac-header" colspan="2">
                            <span>Dismac (Ecommerce)</span>
                        </th>
                        <th class="px-4 py-3 text-center border-l border-gray-200 dark:border-white/10 th-informal-header" colspan="2">
                            <span>Mercado Informal</span>
                        </th>
                        <th class="px-4 py-3 text-center border-l border-gray-200 dark:border-white/10" colspan="2">
                            <span>Total Unidades</span>
                        </th>
                        <th class="px-4 py-3 text-right border-l border-gray-200 dark:border-white/10 min-w-[120px]">
                            Monto Canjeado
                        </th>
                    </tr>
                    <tr class="text-xs font-semibold text-gray-500 dark:text-gray-400 border-t border-gray-200 dark:border-white/10">
                        <th class="px-4 py-2">Denominación</th>
                        <th class="px-3 py-2 text-center border-l border-gray-200 dark:border-white/10 text-success-500">Válidos</th>
                        <th class="px-3 py-2 text-center text-gray-400">Anulados</th>
                        <th class="px-3 py-2 text-center border-l border-gray-200 dark:border-white/10 text-success-500">Válidos</th>
                        <th class="px-3 py-2 text-center text-gray-400">Anulados</th>
                        <th class="px-3 py-2 text-center border-l border-gray-200 dark:border-white/10 font-bold text-success-500">Total Válidos</th>
                        <th class="px-3 py-2 text-center text-gray-400">Total Anulados</th>
                        <th class="px-4 py-2 text-right border-l border-gray-200 dark:border-white/10">Subtotal (Bs)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-white/10 bg-white dark:bg-gray-900">
                    @forelse($matrix as $row)
                        <tr class="hover:bg-gray-50/50 dark:hover:bg-white/5 transition-colors">
                            <td class="px-4 py-3 font-semibold text-gray-900 dark:text-gray-100 flex items-center gap-2">
                                <span class="w-2.5 h-2.5 rounded-full bg-primary-500"></span>
                                <span>{{ $row['name'] }}</span>
                            </td>
                            {{-- Dismac --}}
                            <td class="px-3 py-3 text-center border-l border-gray-200 dark:border-white/10 font-mono font-bold text-success-500">
                                {{ $row['dismac_valid'] }}
                            </td>
                            <td class="px-3 py-3 text-center font-mono text-gray-400 text-xs">
                                @if($row['dismac_invalid'] > 0)
                                    <span class="td-badge-anulado">
                                        {{ $row['dismac_invalid'] }}
                                    </span>
                                @else
                                    <span class="text-gray-600">0</span>
                                @endif
                            </td>
                            {{-- Mercado Informal --}}
                            <td class="px-3 py-3 text-center border-l border-gray-200 dark:border-white/10 font-mono font-bold text-success-500">
                                {{ $row['informal_valid'] }}
                            </td>
                            <td class="px-3 py-3 text-center font-mono text-gray-400 text-xs">
                                @if($row['informal_invalid'] > 0)
                                    <span class="td-badge-anulado">
                                        {{ $row['informal_invalid'] }}
                                    </span>
                                @else
                                    <span class="text-gray-600">0</span>
                                @endif
                            </td>
                            {{-- Totales Unidades --}}
                            <td class="px-3 py-3 text-center border-l border-gray-200 dark:border-white/10 font-mono font-black text-gray-900 dark:text-gray-100">
                                @if($row['total_valid'] > 0)
                                    <span class="td-badge-valido">
                                        {{ $row['total_valid'] }}
                                    </span>
                                @else
                                    <span class="text-gray-500">0</span>
                                @endif
                            </td>
                            <td class="px-3 py-3 text-center font-mono text-xs text-gray-400">
                                @if($row['total_invalid'] > 0)
                                    <span class="td-badge-anulado">
                                        {{ $row['total_invalid'] }}
                                    </span>
                                @else
                                    <span class="text-gray-600">0</span>
                                @endif
                            </td>
                            {{-- Monto --}}
                            <td class="px-4 py-3 text-right border-l border-gray-200 dark:border-white/10 font-mono font-bold text-gray-900 dark:text-gray-100">
                                {{ number_format($row['total_amount'], 2) }} Bs
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-4 py-6 text-center text-gray-400">
                                No se registraron entregas de premios en el periodo seleccionado.
                            </td>
                        </tr>
                    @endforelse

                    @if(($noPrizeInvalid['total'] ?? 0) > 0)
                        <tr class="bg-gray-50/40 dark:bg-white/[0.02] border-t border-dashed border-gray-300 dark:border-white/10">
                            <td class="px-4 py-2.5 text-xs text-gray-400 italic flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-gray-500"></span>
                                <span>Sin premio (Rechazados en validación previa)</span>
                            </td>
                            <td class="px-3 py-2.5 text-center border-l border-gray-200 dark:border-white/10 font-mono text-gray-500 text-xs">-</td>
                            <td class="px-3 py-2.5 text-center font-mono text-xs text-gray-400">
                                @if($noPrizeInvalid['dismac'] > 0)
                                    <span class="td-badge-anulado">{{ $noPrizeInvalid['dismac'] }}</span>
                                @else
                                    <span class="text-gray-600">0</span>
                                @endif
                            </td>
                            <td class="px-3 py-2.5 text-center border-l border-gray-200 dark:border-white/10 font-mono text-gray-500 text-xs">-</td>
                            <td class="px-3 py-2.5 text-center font-mono text-xs text-gray-400">
                                @if($noPrizeInvalid['informal'] > 0)
                                    <span class="td-badge-anulado">{{ $noPrizeInvalid['informal'] }}</span>
                                @else
                                    <span class="text-gray-600">0</span>
                                @endif
                            </td>
                            <td class="px-3 py-2.5 text-center border-l border-gray-200 dark:border-white/10 font-mono text-gray-500 text-xs">-</td>
                            <td class="px-3 py-2.5 text-center font-mono text-xs text-gray-400">
                                <span class="td-badge-anulado">{{ $noPrizeInvalid['total'] }}</span>
                            </td>
                            <td class="px-4 py-2.5 text-right border-l border-gray-200 dark:border-white/10 font-mono text-gray-500 text-xs">
                                0.00 Bs
                            </td>
                        </tr>
                    @endif
                </tbody>
                <tfoot class="bg-gray-50 dark:bg-gray-800 font-bold text-xs uppercase border-t-2 border-gray-300 dark:border-gray-700 text-gray-900 dark:text-gray-100">
                    <tr>
                        <td class="px-4 py-3">TOTALES</td>
                        <td class="px-3 py-3 text-center border-l border-gray-200 dark:border-white/10 font-mono text-success-500 font-black">
                            {{ $totals['dismac_valid'] }}
                        </td>
                        <td class="px-3 py-3 text-center font-mono text-gray-400" title="{{ $totals['dismac_invalid'] }} con premio + {{ $noPrizeInvalid['dismac'] ?? 0 }} sin premio">
                            {{ $totals['dismac_invalid_all'] ?? $totals['dismac_invalid'] }}
                        </td>
                        <td class="px-3 py-3 text-center border-l border-gray-200 dark:border-white/10 font-mono text-success-500 font-black">
                            {{ $totals['informal_valid'] }}
                        </td>
                        <td class="px-3 py-3 text-center font-mono text-gray-400" title="{{ $totals['informal_invalid'] }} con premio + {{ $noPrizeInvalid['informal'] ?? 0 }} sin premio">
                            {{ $totals['informal_invalid_all'] ?? $totals['informal_invalid'] }}
                        </td>
                        <td class="px-3 py-3 text-center border-l border-gray-200 dark:border-white/10 font-mono font-black text-sm text-success-500">
                            {{ $totals['total_valid'] }}
                        </td>
                        <td class="px-3 py-3 text-center font-mono text-gray-400" title="{{ $totals['total_invalid'] }} con premio + {{ $noPrizeInvalid['total'] ?? 0 }} sin premio">
                            {{ $totals['total_invalid_all'] ?? $totals['total_invalid'] }}
                        </td>
                        <td class="px-4 py-3 text-right border-l border-gray-200 dark:border-white/10 font-mono font-black text-sm text-primary-500">
                            {{ number_format($totals['total_amount_delivered'], 2) }} Bs
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
