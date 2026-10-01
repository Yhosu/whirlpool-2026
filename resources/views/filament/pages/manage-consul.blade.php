<x-filament-panels::page>
    <div x-data="{ activeTab: 'stock' }">
        <x-filament::tabs>
            <x-filament::tabs.item alpine-active="activeTab === 'stock'" x-on:click="activeTab = 'stock'">
                Stock por Sucursal
            </x-filament::tabs.item>

            <x-filament::tabs.item alpine-active="activeTab === 'prizes'" x-on:click="activeTab = 'prizes'">
                Premios
            </x-filament::tabs.item>

            <x-filament::tabs.item alpine-active="activeTab === 'branches'" x-on:click="activeTab = 'branches'">
                Sucursales
            </x-filament::tabs.item>

            <x-filament::tabs.item alpine-active="activeTab === 'reports'" x-on:click="activeTab = 'reports'">
                Reportería
            </x-filament::tabs.item>
        </x-filament::tabs>

        <div class="mt-4">
            <div x-show="activeTab === 'stock'">
                @livewire('consul.stock-table')
            </div>

            <div x-cloak x-show="activeTab === 'prizes'">
                @livewire('consul.prizes-table')
            </div>

            <div x-cloak x-show="activeTab === 'branches'">
                @livewire('consul.branches-table')
            </div>

            <div x-cloak x-show="activeTab === 'reports'">
                @livewire('consul.reports-dashboard')
            </div>
        </div>
    </div>
</x-filament-panels::page>