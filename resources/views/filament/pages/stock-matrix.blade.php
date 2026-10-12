<x-filament-panels::page>
    <div class="space-y-8">
        {{-- KPI Cards: Resumen de Stock Inicial vs Actual --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 lg:gap-8 my-4 mb-8">
            <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-white/10 rounded-2xl p-6 md:p-7 shadow-sm flex items-center justify-between transition-all hover:shadow-md">
                <div class="space-y-2.5">
                    <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                        Stock Inicial Total
                    </p>
                    <p class="text-3xl lg:text-4xl font-black text-gray-900 dark:text-gray-100 font-mono tracking-tight">
                        {{ $globalInitialTotal }}
                    </p>
                    <p class="text-xs text-gray-400 dark:text-gray-500 font-medium">
                        Carga inicial registrada en campaña
                    </p>
                </div>
                <div class="w-14 h-14 rounded-2xl bg-gray-100 dark:bg-gray-800 flex items-center justify-center text-gray-600 dark:text-gray-300 shrink-0 ml-4 shadow-inner">
                    <x-heroicon-o-archive-box class="w-7 h-7" />
                </div>
            </div>

            <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-white/10 rounded-2xl p-6 md:p-7 shadow-sm flex items-center justify-between transition-all hover:shadow-md">
                <div class="space-y-2.5">
                    <p class="text-xs font-semibold text-success-600 dark:text-success-400 uppercase tracking-wider">
                        Stock Disponible Actual
                    </p>
                    <p class="text-3xl lg:text-4xl font-black text-success-600 dark:text-success-400 font-mono tracking-tight">
                        {{ $globalActualTotal }}
                    </p>
                    <p class="text-xs text-gray-400 dark:text-gray-500 font-medium">
                        Unidades disponibles para entrega / giro
                    </p>
                </div>
                <div class="w-14 h-14 rounded-2xl bg-success-50 dark:bg-success-500/10 flex items-center justify-center text-success-600 dark:text-success-400 shrink-0 ml-4 shadow-inner">
                    <x-heroicon-o-check-circle class="w-7 h-7" />
                </div>
            </div>

            <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-white/10 rounded-2xl p-6 md:p-7 shadow-sm flex items-center justify-between transition-all hover:shadow-md">
                <div class="space-y-2.5">
                    <p class="text-xs font-semibold text-danger-600 dark:text-danger-400 uppercase tracking-wider">
                        Premios Entregados / Reducidos
                    </p>
                    <div class="flex items-baseline gap-2.5">
                        <p class="text-3xl lg:text-4xl font-black text-danger-600 dark:text-danger-400 font-mono tracking-tight">
                            {{ $globalConsumedTotal }}
                        </p>
                        @if($globalInitialTotal > 0)
                            <span class="text-xs font-bold text-danger-500 dark:text-danger-400 bg-danger-50 dark:bg-danger-500/10 px-2.5 py-0.5 rounded-full">
                                ({{ round(($globalConsumedTotal / $globalInitialTotal) * 100, 1) }}%)
                            </span>
                        @endif
                    </div>
                    <p class="text-xs text-gray-400 dark:text-gray-500 font-medium">
                        Unidades consumidas del inventario
                    </p>
                </div>
                <div class="w-14 h-14 rounded-2xl bg-danger-50 dark:bg-danger-500/10 flex items-center justify-center text-danger-600 dark:text-danger-400 shrink-0 ml-4 shadow-inner">
                    <x-heroicon-o-arrow-trending-down class="w-7 h-7" />
                </div>
            </div>
        </div>

        {{-- Tabla Matriz de Stock --}}
        <div class="overflow-auto rounded-xl border border-gray-200 dark:border-white/10 bg-white dark:bg-gray-900 shadow-sm relative mt-6" style="max-height: calc(100vh - 14rem);">
            <table class="w-full text-left border-collapse min-w-max">
                <thead>
                    <tr class="bg-gray-50 dark:bg-gray-800 border-b border-gray-200 dark:border-white/10">
                        <th class="px-6 py-4 font-bold text-gray-700 dark:text-gray-200 sticky top-0 left-0 z-30 bg-gray-50 dark:bg-gray-800 border-r border-b border-gray-200 dark:border-white/10 shadow-[inset_0_-1px_0_0_#e5e7eb] dark:shadow-none min-w-[220px]">
                            <div class="flex flex-col">
                                <span>Sucursal</span>
                                <span class="text-[10px] font-normal text-gray-400">Canal / Ubicación</span>
                            </div>
                        </th>
                        @foreach($prizes as $prize)
                            <th class="px-3 py-3 text-xs font-semibold uppercase tracking-wider text-center border-r border-b border-gray-200 dark:border-white/10 text-gray-600 dark:text-gray-400 min-w-[130px] sticky top-0 z-20 bg-gray-50 dark:bg-gray-800 shadow-[inset_0_-1px_0_0_#e5e7eb] dark:shadow-none">
                                <div class="font-bold text-gray-900 dark:text-gray-100">{{ $prize->name }}</div>
                                <div class="text-[10px] font-medium lowercase text-gray-400 mt-0.5">
                                    actual / inicial
                                </div>
                            </th>
                        @endforeach
                        <th class="px-4 py-3 text-xs font-black uppercase tracking-wider text-center border-b border-gray-200 dark:border-white/10 text-gray-900 dark:text-gray-100 min-w-[130px] sticky top-0 right-0 z-30 bg-gray-100 dark:bg-gray-800 shadow-[inset_1px_-1px_0_0_#e5e7eb] dark:shadow-none">
                            <div class="font-black">TOTAL</div>
                            <div class="text-[10px] font-normal lowercase text-gray-500 mt-0.5">
                                actual / inicial
                            </div>
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-white/10">
                    @foreach($cities as $cityName => $branches)
                        {{-- City Header --}}
                        @php
                            $cityActualTotal = 0;
                            $cityInitialTotal = 0;
                            foreach($branches as $b) {
                                foreach($prizes as $prize) {
                                    $cityActualTotal += $stockMap["{$prize->id}.{$b->id}"] ?? 0;
                                    $cityInitialTotal += $initialStockMap["{$prize->id}.{$b->id}"] ?? 0;
                                }
                            }
                        @endphp
                        <tr class="bg-primary-50/50 dark:bg-primary-500/10">
                            <td colspan="{{ count($prizes) + 2 }}" class="px-6 py-2.5 font-black text-primary-600 dark:text-primary-400 uppercase tracking-widest text-[11px] sticky left-0 z-10">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-2">
                                        <x-heroicon-o-map-pin class="w-4 h-4 text-primary-500" />
                                        <span>Ciudad: {{ $cityName }}</span>
                                    </div>
                                    <div class="flex items-center gap-2 text-[10px] lowercase tracking-normal">
                                        <span class="bg-primary-100 dark:bg-primary-500/20 text-primary-700 dark:text-primary-300 px-2.5 py-0.5 rounded-full font-bold">
                                            Actual: {{ $cityActualTotal }}
                                        </span>
                                        <span class="bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 px-2.5 py-0.5 rounded-full font-semibold">
                                            Inicial: {{ $cityInitialTotal }}
                                        </span>
                                        @if($cityInitialTotal > $cityActualTotal)
                                            <span class="bg-danger-100 dark:bg-danger-500/20 text-danger-700 dark:text-danger-300 px-2.5 py-0.5 rounded-full font-bold">
                                                Reducido: -{{ $cityInitialTotal - $cityActualTotal }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </td>
                        </tr>

                        @foreach($branches as $branch)
                            <tr class="hover:bg-gray-50 dark:hover:bg-white/5 transition-colors group">
                                <td class="px-6 py-3 font-medium text-gray-900 dark:text-gray-100 sticky left-0 z-10 bg-white dark:bg-gray-900 border-r border-gray-200 dark:border-white/10 group-hover:bg-gray-50 dark:group-hover:bg-gray-800">
                                    <div class="flex flex-col">
                                        <span class="font-semibold">{{ $branch->name }}</span>
                                        <div class="flex items-center gap-1.5 mt-0.5">
                                            <span class="inline-flex px-1.5 py-0.5 text-[9px] rounded font-bold uppercase tracking-wider bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400">
                                                {{ $branch->channel }}
                                            </span>
                                            <span class="text-[10px] text-gray-400 font-normal">({{ $branch->city }})</span>
                                        </div>
                                    </div>
                                </td>

                                @php
                                    $branchActualTotal = 0;
                                    $branchInitialTotal = 0;
                                @endphp

                                @foreach($prizes as $prize)
                                    @php
                                        $stockValue = $stockMap["{$prize->id}.{$branch->id}"] ?? 0;
                                        $initialValue = $initialStockMap["{$prize->id}.{$branch->id}"] ?? 0;
                                        $consumed = max(0, $initialValue - $stockValue);
                                        $branchActualTotal += $stockValue;
                                        $branchInitialTotal += $initialValue;
                                    @endphp
                                    <td class="px-3 py-3 text-center border-r border-gray-200 dark:border-white/10">
                                        <div class="flex flex-col items-center justify-center gap-1">
                                            {{-- Stock Actual --}}
                                            <div @class([
                                                'inline-flex items-center justify-center px-3 py-0.5 rounded-full text-xs font-bold font-mono shadow-sm',
                                                'bg-danger-500/10 text-danger-600 dark:text-danger-400 border border-danger-500/20' => $stockValue <= 0,
                                                'bg-warning-500/10 text-warning-600 dark:text-warning-400 border border-warning-500/20' => $stockValue > 0 && $stockValue <= 10,
                                                'bg-success-500/10 text-success-600 dark:text-success-400 border border-success-500/20' => $stockValue > 10,
                                            ]) title="Stock Disponible Actual">
                                                {{ $stockValue }}
                                            </div>

                                            {{-- Stock Inicial & Reducción --}}
                                            <div class="flex items-center gap-1 text-[10px] font-medium text-gray-500 dark:text-gray-400">
                                                <span title="Stock Inicial">Ini: <strong class="text-gray-700 dark:text-gray-300 font-semibold">{{ $initialValue }}</strong></span>
                                                @if($consumed > 0)
                                                    <span class="inline-flex items-center px-1 rounded text-[9px] font-bold bg-danger-100 dark:bg-danger-500/20 text-danger-700 dark:text-danger-400" title="Premios reducidos / entregados: {{ $consumed }}">
                                                        -{{ $consumed }}
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                @endforeach

                                {{-- Total Sucursal --}}
                                <td class="px-3 py-3 text-center border-l border-gray-200 dark:border-white/10 font-black text-gray-900 dark:text-gray-100 bg-gray-50/50 dark:bg-gray-800/50 sticky right-0 z-10 shadow-[inset_1px_0_0_0_#e5e7eb] dark:shadow-[inset_1px_0_0_0_rgba(255,255,255,0.1)] min-w-[130px]">
                                    <div class="flex flex-col items-center justify-center gap-0.5">
                                        <span class="text-sm font-black text-gray-900 dark:text-gray-100 font-mono">
                                            {{ $branchActualTotal }}
                                        </span>
                                        <div class="flex items-center gap-1 text-[10px] text-gray-500 dark:text-gray-400 font-medium">
                                            <span>Ini: <strong class="text-gray-700 dark:text-gray-300 font-semibold">{{ $branchInitialTotal }}</strong></span>
                                            @if($branchInitialTotal > $branchActualTotal)
                                                <span class="text-danger-600 dark:text-danger-400 font-bold text-[9px]">
                                                    (-{{ $branchInitialTotal - $branchActualTotal }})
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="bg-gray-100 dark:bg-gray-800 border-t-2 border-gray-300 dark:border-gray-700">
                        <td class="px-6 py-4 font-black text-gray-900 dark:text-gray-100 sticky bottom-0 left-0 z-30 bg-gray-100 dark:bg-gray-800 border-r border-gray-300 dark:border-gray-700 text-right uppercase text-xs shadow-[inset_0_1px_0_0_#d1d5db] dark:shadow-[inset_0_1px_0_0_rgba(255,255,255,0.1)]">
                            TOTAL GLOBAL
                        </td>
                        @foreach($prizes as $prize)
                            @php
                                $totalValue = $prizeTotals[$prize->id] ?? 0;
                                $totalInitial = $prizeInitialTotals[$prize->id] ?? 0;
                                $prizeDiff = max(0, $totalInitial - $totalValue);
                            @endphp
                            <td class="px-3 py-3 text-center border-r border-gray-300 dark:border-gray-700 sticky bottom-0 z-20 bg-gray-100 dark:bg-gray-800 shadow-[inset_0_1px_0_0_#d1d5db] dark:shadow-[inset_0_1px_0_0_rgba(255,255,255,0.1)]">
                                <div class="flex flex-col items-center justify-center gap-0.5">
                                    <div @class([
                                        'inline-flex items-center justify-center px-3 py-0.5 rounded-full text-xs font-black font-mono',
                                        'bg-danger-500/10 text-danger-600 dark:text-danger-400' => $totalValue <= 0,
                                        'bg-success-500/10 text-success-600 dark:text-success-400' => $totalValue > 0,
                                    ])>
                                        {{ $totalValue }}
                                    </div>
                                    <span class="text-[10px] text-gray-500 dark:text-gray-400 font-semibold">
                                        Ini: {{ $totalInitial }}
                                        @if($prizeDiff > 0)
                                            <span class="text-danger-600 dark:text-danger-400 font-bold">(-{{ $prizeDiff }})</span>
                                        @endif
                                    </span>
                                </div>
                            </td>
                        @endforeach
                        <td class="px-4 py-3 font-black text-gray-900 dark:text-gray-100 sticky bottom-0 right-0 z-30 bg-gray-200 dark:bg-gray-700 text-center shadow-[inset_1px_1px_0_0_#d1d5db] dark:shadow-none min-w-[130px]">
                            <div class="flex flex-col items-center justify-center gap-0.5">
                                <span class="text-sm font-black text-gray-900 dark:text-gray-100 font-mono">
                                    {{ $globalActualTotal }}
                                </span>
                                <div class="flex items-center gap-1 text-[10px] text-gray-600 dark:text-gray-300 font-bold">
                                    <span>Ini: {{ $globalInitialTotal }}</span>
                                    @if($globalConsumedTotal > 0)
                                        <span class="text-danger-600 dark:text-danger-400 text-[9px]">
                                            (-{{ $globalConsumedTotal }})
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>

        {{-- Legend --}}
        <div class="flex flex-wrap gap-6 text-xs text-gray-500 dark:text-gray-400 items-center justify-between px-2 pt-1">
            <div class="flex items-center gap-4">
                <span class="font-medium text-gray-700 dark:text-gray-300">Referencia:</span>
                <span class="inline-flex items-center gap-1.5"><span class="font-bold text-gray-900 dark:text-gray-100">Número Grande:</span> Stock Disponible Actual</span>
                <span class="inline-flex items-center gap-1.5"><span class="font-semibold text-gray-600 dark:text-gray-400">Ini: X</span> Stock Inicial Cargado</span>
                <span class="inline-flex items-center gap-1.5"><span class="font-bold text-danger-600 dark:text-danger-400">(-Y):</span> Premios Canjeados / Reducidos</span>
            </div>
            <div class="flex items-center gap-5">
                <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-success-500"></span> Stock Óptimo (&gt; 10)</span>
                <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-warning-500"></span> Stock Crítico (1-10)</span>
                <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-danger-500"></span> Agotado (0)</span>
            </div>
        </div>
    </div>
</x-filament-panels::page>
