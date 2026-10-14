<?php

namespace App\Console\Commands;

use App\Models\Code;
use App\Models\Prize;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Rap2hpoutre\FastExcel\FastExcel;

class ImportCodesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:import-codes {--path=codes : Carpeta donde se encuentran los archivos excel}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Importa los cupones de Dismac desde los archivos Excel en la carpeta codes a la tabla codes';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $folder = base_path($this->option('path'));

        if (! is_dir($folder)) {
            $this->error("La carpeta {$folder} no existe.");
            return Command::FAILURE;
        }

        $files = glob($folder . '/*.xlsx');
        if (empty($files)) {
            $this->warn("No se encontraron archivos .xlsx en {$folder}.");
            return Command::SUCCESS;
        }

        $prizes = Prize::all();
        $totalImported = 0;
        $totalSkipped = 0;

        foreach ($files as $filePath) {
            $fileName = basename($filePath);
            $this->info("Procesando archivo: {$fileName}...");

            $rows = (new FastExcel)->import($filePath);
            $fileCount = 0;

            foreach ($rows as $row) {
                $codeStr = trim((string) ($row['NroSerie'] ?? ''));
                if ($codeStr === '') {
                    continue;
                }

                $value = (float) ($row['Valor'] ?? 0);
                $prize = $prizes->first(function ($p) use ($value) {
                    return (float) $p->amount === $value;
                });

                $expirationDate = $this->parseSpanishDate($row['FechaVencimiento'] ?? null);

                $codeRecord = Code::firstOrCreate(
                    ['code' => $codeStr],
                    [
                        'id' => (string) Str::uuid(),
                        'prize_id' => $prize?->id,
                        'product_code' => trim((string) ($row['CodigoProducto'] ?? '')),
                        'value' => $value,
                        'expiration_date' => $expirationDate,
                        'days_valid' => (int) ($row['DiasVencimiento'] ?? 90),
                        'created_by' => trim((string) ($row['UsuarioCreacion'] ?? '')),
                        'status' => 'available',
                        'is_used' => false,
                    ]
                );

                if ($codeRecord->wasRecentlyCreated) {
                    $totalImported++;
                    $fileCount++;
                } else {
                    if ($prize && $codeRecord->prize_id !== $prize->id && ! $codeRecord->is_used) {
                        $codeRecord->update([
                            'prize_id' => $prize->id,
                            'value' => $value,
                        ]);
                    }
                    $totalSkipped++;
                }
            }

            $this->line("  -> {$fileCount} cupones nuevos importados de {$fileName}.");
        }

        $this->info("Proceso completado. Total nuevos: {$totalImported}, Ya existentes: {$totalSkipped}. Total en BD: " . Code::count());

        return Command::SUCCESS;
    }

    /**
     * Parsea fechas en español con formato ej: '04/01/2027 02:18:17 p. m.'
     */
    private function parseSpanishDate($dateStr): ?Carbon
    {
        if (empty($dateStr)) {
            return null;
        }

        if ($dateStr instanceof \DateTimeInterface) {
            return Carbon::instance($dateStr);
        }

        $cleaned = str_replace(
            ['p. m.', 'p.m.', 'a. m.', 'a.m.', 'p. m', 'a. m'],
            ['PM', 'PM', 'AM', 'AM', 'PM', 'AM'],
            trim((string) $dateStr)
        );

        try {
            return Carbon::createFromFormat('d/m/Y h:i:s A', $cleaned);
        } catch (\Exception $e) {
            try {
                return Carbon::parse($cleaned);
            } catch (\Exception $e2) {
                return null;
            }
        }
    }
}
