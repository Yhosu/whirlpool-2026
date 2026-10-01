<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\Prize;
use App\Models\PrizeBranch;
use App\Models\PrizeStockHistory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class PrizeAndBranchSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Crear Sucursales
        $centralDismac = Branch::where('channel', 'ECOMMERCE')->first();
        if (! $centralDismac) {
            $centralDismac = Branch::create([
                'id' => (string) Str::uuid(),
                'name' => 'Central Dismac',
                'channel' => 'ECOMMERCE',
                'city' => 'Santa Cruz',
                'address' => 'Central Dismac',
            ]);
        }

        $cashbackMayoristas = Branch::where('channel', 'MAYORISTA')->first();
        if (! $cashbackMayoristas) {
            $cashbackMayoristas = Branch::create([
                'id' => (string) Str::uuid(),
                'name' => 'CashBack Mayoristas',
                'channel' => 'MAYORISTA',
                'city' => 'Santa Cruz',
                'address' => 'CashBack Mayoristas',
            ]);
        }

        // 2. Definición de Premios y Stocks
        $prizesData = [
            [
                'name' => 'Premio 3000 Bs',
                'amount' => 3000.00,
                'description' => 'Premio en efectivo / voucher de 3000 Bs',
                'stock_ecommerce' => 7,
                'stock_mayorista' => 3,
            ],
            [
                'name' => 'Premio 2000 Bs',
                'amount' => 2000.00,
                'description' => 'Premio en efectivo / voucher de 2000 Bs',
                'stock_ecommerce' => 7,
                'stock_mayorista' => 3,
            ],
            [
                'name' => 'Premio 1000 Bs',
                'amount' => 1000.00,
                'description' => 'Premio en efectivo / voucher de 1000 Bs',
                'stock_ecommerce' => 15,
                'stock_mayorista' => 5,
            ],
            [
                'name' => 'Premio 500 Bs',
                'amount' => 500.00,
                'description' => 'Premio en efectivo / voucher de 500 Bs',
                'stock_ecommerce' => 14,
                'stock_mayorista' => 6,
            ],
            [
                'name' => 'Premio 250 Bs',
                'amount' => 250.00,
                'description' => 'Premio en efectivo / voucher de 250 Bs',
                'stock_ecommerce' => 75,
                'stock_mayorista' => 15,
            ],
            [
                'name' => 'Premio 100 Bs',
                'amount' => 100.00,
                'description' => 'Premio en efectivo / voucher de 100 Bs',
                'stock_ecommerce' => 170,
                'stock_mayorista' => 30,
            ],
        ];

        foreach ($prizesData as $data) {
            $prize = Prize::where('name', $data['name'])->first();
            if (! $prize) {
                $prize = Prize::create([
                    'id' => (string) Str::uuid(),
                    'name' => $data['name'],
                    'amount' => $data['amount'],
                    'description' => $data['description'],
                    'active' => true,
                ]);
            }

            // Stock Central Dismac (ECOMMERCE)
            $historyEco = PrizeStockHistory::where('prize_id', $prize->id)
                ->where('branch_id', $centralDismac->id)
                ->where('description', 'Carga inicial de stock - Central Dismac (ECOMMERCE)')
                ->first();

            if (! $historyEco) {
                PrizeStockHistory::create([
                    'id' => (string) Str::uuid(),
                    'prize_id' => $prize->id,
                    'branch_id' => $centralDismac->id,
                    'quantity' => $data['stock_ecommerce'],
                    'type' => 'addition',
                    'description' => 'Carga inicial de stock - Central Dismac (ECOMMERCE)',
                ]);

                $ecoStock = PrizeBranch::firstOrCreate([
                    'prize_id' => $prize->id,
                    'branch_id' => $centralDismac->id,
                ]);
                $ecoStock->stock = $data['stock_ecommerce'];
                $ecoStock->save();
            }

            // Stock CashBack Mayoristas (MAYORISTA)
            $historyMay = PrizeStockHistory::where('prize_id', $prize->id)
                ->where('branch_id', $cashbackMayoristas->id)
                ->where('description', 'Carga inicial de stock - CashBack Mayoristas (MAYORISTA)')
                ->first();

            if (! $historyMay) {
                PrizeStockHistory::create([
                    'id' => (string) Str::uuid(),
                    'prize_id' => $prize->id,
                    'branch_id' => $cashbackMayoristas->id,
                    'quantity' => $data['stock_mayorista'],
                    'type' => 'addition',
                    'description' => 'Carga inicial de stock - CashBack Mayoristas (MAYORISTA)',
                ]);

                $mayStock = PrizeBranch::firstOrCreate([
                    'prize_id' => $prize->id,
                    'branch_id' => $cashbackMayoristas->id,
                ]);
                $mayStock->stock = $data['stock_mayorista'];
                $mayStock->save();
            }
        }
    }
}
