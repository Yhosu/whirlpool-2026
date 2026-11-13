<x-filament-panels::page>
    <style>
        .sm-kpi-grid {
            display: grid;
            grid-template-columns: repeat(1, minmax(0, 1fr));
            gap: 1rem;
            margin-top: 1rem;
            margin-bottom: 2rem;
        }
        @media (min-width: 640px) {
            .sm-kpi-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }
        @media (min-width: 1024px) {
            .sm-kpi-grid {
                grid-template-columns: repeat(4, minmax(0, 1fr));
                gap: 1.5rem;
            }
        }
        .badge-anulado {
            display: inline-flex;
            align-items: center;
            padding: 1px 6px;
            border-radius: 4px;
            font-size: 10px;
            font-weight: 600;
            line-height: 1.2;
            background-color: rgba(255, 255, 255, 0.08);
            color: #94a3b8;
            border: 1px solid rgba(255, 255, 255, 0.14);
        }
        .badge-anulado-city {
            display: inline-flex;
            align-items: center;
            padding: 2px 8px;
            border-radius: 9999px;
            font-size: 10px;
            font-weight: 600;
            line-height: 1.2;
            background-color: rgba(255, 255, 255, 0.1);
            color: #cbd5e1;
            border: 1px solid rgba(255, 255, 255, 0.15);
        }
        .badge-valid-city {
            display: inline-flex;
            align-items: center;
            padding: 2px 8px;
            border-radius: 9999px;
            font-size: 10px;
            font-weight: 700;
            line-height: 1.2;
            background-color: rgba(239, 68, 68, 0.18);
            color: #f87171;
            border: 1px solid rgba(239, 68, 68, 0.25);
        }
        .badge-valid-tag {
            display: inline-flex;
            align-items: center;
            padding: 1px 5px;
            border-radius: 4px;
            font-size: 10px;
            font-weight: 700;
            line-height: 1.2;
            background-color: rgba(239, 68, 68, 0.18);
            color: #fca5a5;
        }
        .badge-city-init {
            display: inline-flex;
            align-items: center;
            padding: 2px 8px;
            border-radius: 9999px;
            font-size: 10px;
            font-weight: 600;
            line-height: 1.2;
            background-color: rgba(255, 255, 255, 0.1);
            color: #cbd5e1;
        }
        .card-icon-box {
            width: 3rem;
            height: 3rem;
            border-radius: 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            margin-left: 0.75rem;
            background-color: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.08);
        }
    </style>

    <div class="space-y-8">
        {{-- KPI Cards: Resumen de Stock Inicial vs Actual --}}
        <div class="sm-kpi-grid">
            {{-- Card 1: Stock Inicial Total --}}
            <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-white/10 rounded-2xl p-5 md:p-6 shadow-sm flex items-center justify-between transition-all hover:shadow-md">
                <div class="space-y-2">
                    <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                        Stock Inicial Total
                    </p>
                    <p class="text-3xl font-black text-gray-900 dark:text-gray-100 font-mono tracking-tight">
                        {{ $globalInitialTotal }}
                    </p>
                    <p class="text-xs text-gray-400 dark:text-gray-500 font-medium">
                        Carga inicial de campaña
                    </p>
                </div>
                <div class="card-icon-box text-gray-400">
                    <x-heroicon-o-archive-box class="w-6 h-6" />
                </div>
            </div>

            {{-- Card 2: Stock Disponible Actual --}}
            <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-white/10 rounded-2xl p-5 md:p-6 shadow-sm flex items-center justify-between transition-all hover:shadow-md">
                <div class="space-y-2">
                    <p class="text-xs font-semibold text-success-600 dark:text-success-400 uppercase tracking-wider">
                        Stock Disponible Actual
                    </p>
                    <p class="text-3xl font-black text-success-600 dark:text-success-400 font-mono tracking-tight">
                        {{ $globalActualTotal }}
                    </p>
                    <p class="text-xs text-gray-400 dark:text-gray-500 font-medium">
                        Disponibles para entrega / giro
                    </p>
                </div>
                <div class="card-icon-box text-success-500 bg-success-500/10 border-success-500/20">
                    <x-heroicon-o-check-circle class="w-6 h-6" />
                </div>
            </div>

            {{-- Card 3: Premios Entregados / Reducidos --}}
            <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-white/10 rounded-2xl p-5 md:p-6 shadow-sm flex items-center justify-between transition-all hover:shadow-md">
                <div class="space-y-2">
                    <p class="text-xs font-semibold text-danger-600 dark:text-danger-400 uppercase tracking-wider">
                        Premios Entregados / Reducidos
                    </p>
                    <div class="flex items-baseline gap-2">
                        <p class="text-3xl font-black text-danger-600 dark:text-danger-400 font-mono tracking-tight">
                            {{ $globalConsumedTotal }}
                        </p>
                        @if($globalInitialTotal > 0)
                            <span class="text-xs font-bold text-danger-500 dark:text-danger-400 bg-danger-50 dark:bg-danger-500/10 px-2 py-0.5 rounded-full">
                                ({{ round(($globalConsumedTotal / $globalInitialTotal) * 100, 1) }}%)
                            </span>
                        @endif
                    </div>
                    <p class="text-xs text-gray-400 dark:text-gray-500 font-medium">
                        Premios válidos entregados (sin anulados)
                    </p>
                </div>
                <div class="card-icon-box text-danger-500 bg-danger-500/10 border-danger-500/20">
                    <x-heroicon-o-arrow-trending-down class="w-6 h-6" />
                </div>
            </div>

            {{-- Card 4: Premios Invalidados --}}
            <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-white/10 rounded-2xl p-5 md:p-6 shadow-sm flex items-center justify-between transition-all hover:shadow-md">
                <div class="space-y-2">
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">
                        Premios Invalidados
                    </p>
                    <div class="flex items-baseline gap-2">
                        <p class="text-3xl font-black text-gray-300 font-mono tracking-tight" title="Premios anulados que repusieron stock en sucursales">
                            {{ $globalInvalidatedTotal }}
                        </p>
                        @if(($invalidatedWithoutPrize ?? 0) > 0)
                            <span class="badge-anulado text-xs" title="Formularios adicionales anulados en validación antes de asignar premio">
                                +{{ $invalidatedWithoutPrize }} sin premio
                            </span>
                        @endif
                    </div>
                    <p class="text-xs text-gray-500 font-medium">
                        {{ $globalInvalidatedTotal }} con reposición de stock ({{ $totalAllInvalidatedForms ?? $globalInvalidatedTotal }} en total)
                    </p>
                </div>
                <div class="card-icon-box text-gray-400">
                    <x-heroicon-o-x-circle class="w-6 h-6" />
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
                                <span class="text-xs font-normal text-gray-400">Canal / Ubicación</span>
                            </div>
                        </th>
                        @foreach($prizes as $prize)
                            <th class="px-3 py-3 text-xs font-semibold uppercase tracking-wider text-center border-r border-b border-gray-200 dark:border-white/10 text-gray-600 dark:text-gray-400 min-w-[130px] sticky top-0 z-20 bg-gray-50 dark:bg-gray-800 shadow-[inset_0_-1px_0_0_#e5e7eb] dark:shadow-none">
                                <div class="font-bold text-gray-900 dark:text-gray-100">{{ $prize->name }}</div>
                                <div class="text-xs font-medium lowercase text-gray-400 mt-0.5">
                                    actual / inicial
                                </div>
                            </th>
                        @endforeach
                        <th class="px-4 py-3 text-xs font-black uppercase tracking-wider text-center border-b border-gray-200 dark:border-white/10 text-gray-900 dark:text-gray-100 min-w-[130px] sticky top-0 right-0 z-30 bg-gray-100 dark:bg-gray-800 shadow-[inset_1px_-1px_0_0_#e5e7eb] dark:shadow-none">
                            <div class="font-black">TOTAL</div>
                            <div class="text-xs font-normal lowercase text-gray-500 mt-0.5">
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
                            $cityValidTotal = 0;
                            $cityInvalidatedTotal = 0;
                            foreach($branches as $b) {
                                foreach($prizes as $prize) {
                                    $cityActualTotal += $stockMap["{$prize->id}.{$b->id}"] ?? 0;
                                    $cityInitialTotal += $initialStockMap["{$prize->id}.{$b->id}"] ?? 0;
                                    $cityValidTotal += $validDeliveredMap["{$prize->id}.{$b->id}"] ?? 0;
                                    $cityInvalidatedTotal += $invalidatedMap["{$prize->id}.{$b->id}"] ?? 0;
                                }
                            }
                        @endphp
                        <tr class="bg-primary-50/50 dark:bg-primary-500/10">
                            <td colspan="{{ count($prizes) + 2 }}" class="px-6 py-2.5 font-black text-primary-600 dark:text-primary-400 uppercase tracking-widest text-xs sticky left-0 z-10">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-2">
                                        <x-heroicon-o-map-pin class="w-4 h-4 text-primary-500" />
                                        <span>Ciudad: {{ $cityName }}</span>
                                    </div>
                                    <div class="flex items-center gap-2 text-xs lowercase tracking-normal">
                                        <span class="bg-primary-100 dark:bg-primary-500/20 text-primary-700 dark:text-primary-300 px-2.5 py-0.5 rounded-full font-bold">
                                            Actual: {{ $cityActualTotal }}
                                        </span>
                                        <span class="badge-city-init">
                                            Inicial: {{ $cityInitialTotal }}
                                        </span>
                                        @if($cityValidTotal > 0)
                                            <span class="badge-valid-city">
                                                Válidos: -{{ $cityValidTotal }}
                                            </span>
                                        @endif
                                        @if($cityInvalidatedTotal > 0)
                                            <span class="badge-anulado-city">
                                                Anulados: {{ $cityInvalidatedTotal }}
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
                                            <span class="inline-flex px-1.5 py-0.5 text-xs rounded font-bold uppercase tracking-wider bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400">
                                                {{ $branch->channel }}
                                            </span>
                                            <span class="text-xs text-gray-400 font-normal">({{ $branch->city }})</span>
                                        </div>
                                    </div>
                                </td>

                                @php
                                    $branchActualTotal = 0;
                                    $branchInitialTotal = 0;
                                    $branchValidTotal = 0;
                                    $branchInvalidTotal = 0;
                                @endphp

                                @foreach($prizes as $prize)
                                    @php
                                        $stockValue = $stockMap["{$prize->id}.{$branch->id}"] ?? 0;
                                        $initialValue = $initialStockMap["{$prize->id}.{$branch->id}"] ?? 0;
                                        $validDelivered = $validDeliveredMap["{$prize->id}.{$branch->id}"] ?? 0;
                                        $invalidated = $invalidatedMap["{$prize->id}.{$branch->id}"] ?? 0;

                                        $branchActualTotal += $stockValue;
                                        $branchInitialTotal += $initialValue;
                                        $branchValidTotal += $validDelivered;
                                        $branchInvalidTotal += $invalidated;
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

                                            {{-- Stock Inicial & Premios Válidos Entregados --}}
                                            <div class="flex items-center gap-1 text-xs font-medium text-gray-500 dark:text-gray-400">
                                                <span title="Stock Inicial">Ini: <strong class="text-gray-700 dark:text-gray-300 font-semibold">{{ $initialValue }}</strong></span>
                                                @if($validDelivered > 0)
                                                    <span class="badge-valid-tag" title="Premios válidos entregados: {{ $validDelivered }}">
                                                        -{{ $validDelivered }}
                                                    </span>
                                                @endif
                                            </div>

                                            {{-- Premios Invalidados Separados Abajo --}}
                                            @if($invalidated > 0)
                                                <div class="flex items-center gap-1 font-medium" title="Formularios anulados / invalidados: {{ $invalidated }}">
                                                    <span class="badge-anulado">
                                                        Anul: {{ $invalidated }}
                                                    </span>
                                                </div>
                                            @endif
                                        </div>
                                    </td>
                                @endforeach

                                {{-- Total Sucursal --}}
                                <td class="px-3 py-3 text-center border-l border-gray-200 dark:border-white/10 font-black text-gray-900 dark:text-gray-100 bg-gray-50/50 dark:bg-gray-800/50 sticky right-0 z-10 shadow-[inset_1px_0_0_0_#e5e7eb] dark:shadow-[inset_1px_0_0_0_rgba(255,255,255,0.1)] min-w-[130px]">
                                    <div class="flex flex-col items-center justify-center gap-0.5">
                                        <span class="text-sm font-black text-gray-900 dark:text-gray-100 font-mono">
                                            {{ $branchActualTotal }}
                                        </span>
                                        <div class="flex items-center gap-1 text-xs text-gray-500 dark:text-gray-400 font-medium">
                                            <span>Ini: <strong class="text-gray-700 dark:text-gray-300 font-semibold">{{ $branchInitialTotal }}</strong></span>
                                            @if($branchValidTotal > 0)
                                                <span class="text-danger-600 dark:text-danger-400 font-bold text-xs" title="Total premios válidos entregados">
                                                    (-{{ $branchValidTotal }})
                                                </span>
                                            @endif
                                        </div>
                                        @if($branchInvalidTotal > 0)
                                            <div class="flex items-center gap-1 font-medium mt-0.5" title="Total anulados">
                                                <span class="badge-anulado">
                                                    Anul: {{ $branchInvalidTotal }}
                                                </span>
                                            </div>
                                        @endif
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
                                $totalValid = $prizeValidDeliveredTotals[$prize->id] ?? 0;
                                $totalInv = $prizeInvalidatedTotals[$prize->id] ?? 0;
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
                                    <span class="text-xs text-gray-500 dark:text-gray-400 font-semibold">
                                        Ini: {{ $totalInitial }}
                                        @if($totalValid > 0)
                                            <span class="text-danger-600 dark:text-danger-400 font-bold" title="Premios válidos entregados">(-{{ $totalValid }})</span>
                                        @endif
                                    </span>
                                    @if($totalInv > 0)
                                        <span class="badge-anulado mt-0.5" title="Premios anulados / invalidados">
                                            Anul: {{ $totalInv }}
                                        </span>
                                    @endif
                                </div>
                            </td>
                        @endforeach
                        <td class="px-4 py-3 font-black text-gray-900 dark:text-gray-100 sticky bottom-0 right-0 z-30 bg-gray-200 dark:bg-gray-700 text-center shadow-[inset_1px_1px_0_0_#d1d5db] dark:shadow-none min-w-[130px]">
                            <div class="flex flex-col items-center justify-center gap-0.5">
                                <span class="text-sm font-black text-gray-900 dark:text-gray-100 font-mono">
                                    {{ $globalActualTotal }}
                                </span>
                                <div class="flex items-center gap-1 text-xs text-gray-600 dark:text-gray-300 font-bold">
                                    <span>Ini: {{ $globalInitialTotal }}</span>
                                    @if($globalConsumedTotal > 0)
                                        <span class="text-danger-600 dark:text-danger-400 text-xs" title="Total premios válidos entregados">
                                            (-{{ $globalConsumedTotal }})
                                        </span>
                                    @endif
                                </div>
                                @if($globalInvalidatedTotal > 0)
                                    <span class="badge-anulado mt-0.5" title="Total anulados">
                                        Anul: {{ $globalInvalidatedTotal }}
                                    </span>
                                @endif
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
                <span class="inline-flex items-center gap-1.5"><span class="badge-valid-tag">(-Y)</span> Premios Válidos Entregados</span>
                <span class="inline-flex items-center gap-1.5"><span class="badge-anulado">Anul: Z</span> Formularios Invalidados (Separados)</span>
            </div>
            <div class="flex items-center gap-5">
                <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-success-500"></span> Stock Óptimo (&gt; 10)</span>
                <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-warning-500"></span> Stock Crítico (1-10)</span>
                <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-danger-500"></span> Agotado (0)</span>
            </div>
        </div>
    </div>
</x-filament-panels::page>