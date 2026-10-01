<div class="space-y-6 animate-in fade-in duration-700">
    <!-- Filtros y Acciones -->
    <x-filament::section class="overflow-visible border-none shadow-sm bg-white dark:bg-gray-900 rounded-3xl">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="flex-1">
                <div class="flex items-center gap-3 mb-6">
                    <div class="p-2 bg-primary-50 dark:bg-primary-900/20 rounded-lg text-primary-600">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white">Panel de Reportería Consul</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Análisis detallado de participación y entregas</p>
                    </div>
                </div>
                
                <div class="flex flex-wrap items-center gap-6">
                    <div class="flex items-center gap-3 bg-transparent p-2 px-0 rounded-2xl">
                        <span class="text-[10px] font-black uppercase text-gray-400">Desde</span>
                        <input type="date" wire:model.live="dateStart" class="bg-transparent border-none p-0 text-sm font-bold focus:ring-0 text-gray-700 dark:text-gray-200" />
                    </div>
                    <div class="flex items-center gap-3 bg-transparent p-2 px-0 rounded-2xl">
                        <span class="text-[10px] font-black uppercase text-gray-400">Hasta</span>
                        <input type="date" wire:model.live="dateEnd" class="bg-transparent border-none p-0 text-sm font-bold focus:ring-0 text-gray-700 dark:text-gray-200" />
                    </div>
                </div>
            </div>

            <div class="flex items-center">
                <x-filament::button 
                    wire:click="exportReport" 
                    wire:loading.attr="disabled"
                    color="success"
                    size="lg"
                    class="shadow-xl shadow-success-500/10"
                >
                    <div class="flex items-center gap-2">
                        <svg wire:loading.remove wire:target="exportReport" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
                        </svg>
                        <svg wire:loading wire:target="exportReport" class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span wire:loading.remove wire:target="exportReport">Exportar Excel</span>
                        <span wire:loading wire:target="exportReport">Preparando...</span>
                    </div>
                </x-filament::button>
            </div>
        </div>
    </x-filament::section>

    <!-- Indicador de Carga Global -->
    <div wire:loading.delay wire:target="dateStart, dateEnd" class="fixed top-0 left-0 w-full h-1 bg-primary-500 animate-pulse z-50"></div>

    <!-- Barra de Agregados Compacta (Ribbon) -->
    <div class="bg-white dark:bg-gray-900 border-none rounded-2xl p-2 px-4 shadow-sm">
        <div class="flex flex-wrap items-center justify-between gap-4 divide-x divide-gray-100 dark:divide-gray-800">
            @php
                $stats = [
                    ['label' => 'Participaciones', 'value' => $totalForms, 'color' => 'text-primary-600', 'bg' => 'bg-primary-50', 'icon' => '<path d="M16.5 6v.75m0 3v.75m0 3v.75m0 3V18m-9-5.25h5.25M7.5 15h3m-3 3h6m-12-10.5h12c.621 0 1.125.504 1.125 1.125v12c0 .621-.504 1.125-1.125 1.125h-12a1.125 1.125 0 01-1.125-1.125v-12c0-.621.504-1.125 1.125-1.125z" />'],
                    ['label' => 'Entregados', 'value' => $totalEntregados, 'color' => 'text-success-600', 'bg' => 'bg-success-50', 'icon' => '<path d="M9 12.75L11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 01-1.043 3.296 3.745 3.745 0 01-3.296 1.043A3.745 3.745 0 0112 21c-1.268 0-2.39-.63-3.068-1.593a3.746 3.746 0 01-3.296-1.043 3.745 3.745 0 01-1.043-3.296A3.745 3.745 0 013 12c0-1.268.63-2.39 1.593-3.068a3.745 3.745 0 011.043-3.296 3.746 3.746 0 013.296-1.043A3.746 3.746 0 0112 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 013.296 1.043 3.746 3.746 0 011.043 3.296A3.745 3.745 0 0121 12z" />'],
                    ['label' => 'Pendientes', 'value' => $totalPendientes, 'color' => 'text-warning-600', 'bg' => 'bg-warning-50', 'icon' => '<path d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />'],
                    ['label' => 'Invalidados', 'value' => $totalInvalidos, 'color' => 'text-danger-600', 'bg' => 'bg-danger-50', 'icon' => '<path d="M9.75 9.75l4.5 4.5m0-4.5l-4.5 4.5M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />'],
                ];
            @endphp

            @foreach($stats as $stat)
                <div class="flex-1 flex items-center justify-center gap-3 py-1 first:pl-0 pl-4">
                    <div class="w-8 h-8 flex items-center justify-center rounded-lg {{ $stat['bg'] }} dark:bg-opacity-10 {{ $stat['color'] }}">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                            {!! $stat['icon'] !!}
                        </svg>
                    </div>
                    <div>
                        <p class="text-[9px] font-black uppercase text-gray-400 leading-none tracking-tighter">{{ $stat['label'] }}</p>
                        <p class="text-base font-black text-gray-900 dark:text-white leading-none mt-0.5">{{ number_format($stat['value']) }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Secciones de Análisis -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 mt-4">
        <!-- Top Premios -->
        <div class="lg:col-span-4">
            <x-filament::section class="rounded-2xl shadow-sm border-none bg-white dark:bg-gray-900">
                <x-slot name="heading">
                    <div class="flex items-center gap-2">
                        <x-filament::icon icon="heroicon-o-gift" class="w-4 h-4 text-success-600" />
                        <span class="text-sm font-bold">Premios Entregados</span>
                    </div>
                </x-slot>
                
                <div class="space-y-2 pt-2">
                    @forelse($porPremio as $row)
                        <div class="flex items-center justify-between p-2 px-3 rounded-xl bg-gray-50/50 dark:bg-gray-800/50 border-none hover:bg-success-50 dark:hover:bg-success-900/10 transition-colors">
                            <span class="text-xs font-bold text-gray-950 dark:text-white truncate mr-2">{{ $row->prize->name ?? 'Desconocido' }}</span>
                            <span class="text-xs font-black text-success-600">{{ number_format($row->total) }}</span>
                        </div>
                    @empty
                        <p class="text-center py-4 text-xs text-gray-400 italic">Sin datos</p>
                    @endforelse
                </div>
            </x-filament::section>
        </div>

        <!-- Top Sucursales -->
        <div class="lg:col-span-4">
            <x-filament::section class="rounded-2xl shadow-sm border-none bg-white dark:bg-gray-900">
                <x-slot name="heading">
                    <div class="flex items-center gap-2">
                        <x-filament::icon icon="heroicon-o-building-storefront" class="w-4 h-4 text-primary-600" />
                        <span class="text-sm font-bold">Entregas por Sucursal</span>
                    </div>
                </x-slot>
                
                <div class="space-y-2 pt-2 max-h-[300px] overflow-y-auto custom-scrollbar pr-1">
                    @forelse($porSucursal as $row)
                        <div class="flex items-center justify-between p-2 px-3 rounded-xl bg-gray-50/50 dark:bg-gray-800/50 border-none hover:bg-primary-50 dark:hover:bg-primary-900/10 transition-colors">
                            <div class="flex flex-col min-w-0">
                                <span class="text-xs font-bold text-gray-950 dark:text-white truncate">{{ $row->branch->name ?? 'Desconocido' }}</span>
                                <span class="text-[9px] uppercase font-black text-primary-400 tracking-tighter">{{ $row->branch->city ?? '-' }}</span>
                            </div>
                            <span class="text-xs font-black text-primary-600">{{ number_format($row->total) }}</span>
                        </div>
                    @empty
                        <p class="text-center py-4 text-xs text-gray-400 italic">Sin datos</p>
                    @endforelse
                </div>
            </x-filament::section>
        </div>

        <!-- Mapa de Calor por Ciudad -->
        <div class="lg:col-span-4">
            <x-filament::section class="rounded-2xl shadow-sm border-none bg-white dark:bg-gray-900">
                <x-slot name="heading">
                    <div class="flex items-center gap-2">
                        <x-filament::icon icon="heroicon-o-map" class="w-4 h-4 text-amber-600" />
                        <span class="text-sm font-bold">Distribución por Ciudad</span>
                    </div>
                </x-slot>
                
                <div class="space-y-3 pt-2">
                    @forelse($porCiudad as $row)
                        @php
                            $percentage = $totalForms > 0 ? ($row->total / $totalForms) * 100 : 0;
                        @endphp
                        <div class="space-y-1">
                            <div class="flex justify-between text-[10px] font-black uppercase text-gray-950 dark:text-gray-400">
                                <span>{{ $row->city ?: 'Sin Ciudad' }}</span>
                                <span class="text-gray-900 dark:text-white">{{ number_format($row->total) }}</span>
                            </div>
                            <div class="h-1.5 w-full bg-gray-100 dark:bg-gray-800 rounded-full overflow-hidden">
                                <div class="h-full bg-amber-500 rounded-full transition-all duration-1000" style="width: {{ $percentage }}%"></div>
                            </div>
                        </div>
                    @empty
                        <p class="text-center py-4 text-xs text-gray-400 italic">Sin datos</p>
                    @endforelse
                </div>
            </x-filament::section>
        </div>
    </div>

    <style>
        .custom-scrollbar::-webkit-scrollbar { width: 3px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
        .dark .custom-scrollbar::-webkit-scrollbar-thumb { background: #475569; }
    </style>
</div>
