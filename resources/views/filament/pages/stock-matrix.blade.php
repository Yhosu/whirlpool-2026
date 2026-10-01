<x-filament-panels::page>
    <div class="space-y-6">
        <div class="overflow-auto rounded-xl border border-gray-200 dark:border-white/10 bg-white dark:bg-gray-900 shadow-sm relative" style="max-height: calc(100vh - 14rem);">
            <table class="w-full text-left border-collapse min-w-max">
                <thead>
                    <tr class="bg-gray-50 dark:bg-gray-800 border-b border-gray-200 dark:border-white/10">
                        <th class="px-6 py-4 font-bold text-gray-700 dark:text-gray-200 sticky top-0 left-0 z-30 bg-gray-50 dark:bg-gray-800 border-r border-b border-gray-200 dark:border-white/10 shadow-[inset_0_-1px_0_0_#e5e7eb] dark:shadow-none">
                            Sucursal
                        </th>
                        @foreach($prizes as $prize)
                            <th class="px-3 py-4 text-xs font-semibold uppercase tracking-wider text-center border-r border-b border-gray-200 dark:border-white/10 text-gray-600 dark:text-gray-400 min-w-[120px] sticky top-0 z-20 bg-gray-50 dark:bg-gray-800 shadow-[inset_0_-1px_0_0_#e5e7eb] dark:shadow-none">
                                {{ $prize->name }}
                            </th>
                        @endforeach
                        <th class="px-4 py-4 text-xs font-black uppercase tracking-wider text-center border-b border-gray-200 dark:border-white/10 text-gray-900 dark:text-gray-100 min-w-[100px] sticky top-0 right-0 z-30 bg-gray-100 dark:bg-gray-800 shadow-[inset_1px_-1px_0_0_#e5e7eb] dark:shadow-none">
                            TOTAL
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-white/10">
                    @foreach($cities as $cityName => $branches)
                        {{-- City Header --}}
                        @php
                            $cityTotal = 0;
                            foreach($branches as $b) {
                                foreach($prizes as $prize) {
                                    $cityTotal += $stockMap["{$prize->id}.{$b->id}"] ?? 0;
                                }
                            }
                        @endphp
                        <tr class="bg-primary-50/50 dark:bg-primary-500/10">
                            <td colspan="{{ count($prizes) + 2 }}" class="px-6 py-2 font-black text-primary-600 dark:text-primary-400 uppercase tracking-widest text-[10px] sticky left-0 z-10">
                                <div class="flex items-center gap-3">
                                    <div class="flex items-center gap-2">
                                        <x-heroicon-o-map-pin class="w-4 h-4" />
                                        {{ $cityName }}
                                    </div>
                                    <span class="bg-primary-100 dark:bg-primary-500/20 text-primary-700 dark:text-primary-300 px-2 py-0.5 rounded-full text-[10px] lowercase group-hover:bg-primary-200 transition">
                                        (Total: {{ $cityTotal }})
                                    </span>
                                </div>
                            </td>
                        </tr>

                        @foreach($branches as $branch)
                            <tr class="hover:bg-gray-50 dark:hover:bg-white/5 transition-colors group">
                                <td class="px-6 py-3 font-medium text-gray-900 dark:text-gray-100 sticky left-0 z-10 bg-white dark:bg-gray-900 border-r border-gray-200 dark:border-white/10 group-hover:bg-gray-50 dark:group-hover:bg-gray-800">
                                    <div class="flex flex-col">
                                        <span>{{ $branch->name }}</span>
                                        <span class="text-[10px] text-gray-400 font-normal">{{ $branch->channel }}</span>
                                    </div>
                                </td>

                                @php $branchTotal = 0; @endphp
                                @foreach($prizes as $prize)
                                    @php
                                        $stockValue = $stockMap["{$prize->id}.{$branch->id}"] ?? 0;
                                        $branchTotal += $stockValue;
                                    @endphp
                                    <td class="px-3 py-3 text-center border-r border-gray-200 dark:border-white/10">
                                        <div @class([
                                            'inline-flex items-center justify-center px-3 py-1 rounded-full text-xs font-bold font-mono',
                                            'bg-danger-500/10 text-danger-600 dark:text-danger-400' => $stockValue <= 0,
                                            'bg-warning-500/10 text-warning-600 dark:text-warning-400' => $stockValue > 0 && $stockValue <= 10,
                                            'bg-success-500/10 text-success-600 dark:text-success-400' => $stockValue > 10,
                                        ])>
                                            {{ $stockValue }}
                                        </div>
                                    </td>
                                @endforeach
                                <td class="px-3 py-3 text-center border-l border-gray-200 dark:border-white/10 font-black text-gray-900 dark:text-gray-100 bg-gray-50/50 dark:bg-gray-800/50 sticky right-0 z-10 shadow-[inset_1px_0_0_0_#e5e7eb] dark:shadow-[inset_1px_0_0_0_rgba(255,255,255,0.1)] min-w-[100px]">
                                    {{ $branchTotal }}
                                </td>
                            </tr>
                        @endforeach
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="bg-gray-100 dark:bg-gray-800 border-t-2 border-gray-300 dark:border-gray-700">
                        <td class="px-6 py-4 font-black text-gray-900 dark:text-gray-100 sticky bottom-0 left-0 z-30 bg-gray-100 dark:bg-gray-800 border-r border-gray-300 dark:border-gray-700 text-right uppercase shadow-[inset_0_1px_0_0_#d1d5db] dark:shadow-[inset_0_1px_0_0_rgba(255,255,255,0.1)]">
                            TOTAL GLOBAL
                        </td>
                        @foreach($prizes as $prize)
                            @php
                                $totalValue = $prizeTotals[$prize->id] ?? 0;
                            @endphp
                            <td class="px-3 py-4 text-center border-r border-gray-300 dark:border-gray-700 sticky bottom-0 z-20 bg-gray-100 dark:bg-gray-800 shadow-[inset_0_1px_0_0_#d1d5db] dark:shadow-[inset_0_1px_0_0_rgba(255,255,255,0.1)]">
                                <div @class([
                                    'inline-flex items-center justify-center px-3 py-1 rounded-full text-xs font-black font-mono',
                                    'bg-danger-500/10 text-danger-600 dark:text-danger-400' => $totalValue <= 0,
                                    'bg-success-500/10 text-success-600 dark:text-success-400' => $totalValue > 0,
                                ])>
                                    {{ $totalValue }}
                                </div>
                            </td>
                        @endforeach
                        <td class="px-4 py-4 font-black text-gray-900 dark:text-gray-100 sticky bottom-0 right-0 z-30 bg-gray-200 dark:bg-gray-700 text-center shadow-[inset_1px_1px_0_0_#d1d5db] dark:shadow-none min-w-[100px]">
                            {{ collect($prizeTotals)->sum() }}
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>

        {{-- Legend --}}
        <div class="flex gap-6 text-xs text-gray-500 items-center justify-end px-2">
            <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-success-500"></span> Stock Óptimo</span>
            <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-warning-500"></span> Stock Crítico (1-10)</span>
            <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-danger-500"></span> Agotado</span>
        </div>
    </div>
</x-filament-panels::page>
