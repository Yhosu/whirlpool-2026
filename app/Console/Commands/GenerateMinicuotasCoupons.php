<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Rap2hpoutre\FastExcel\FastExcel;

class GenerateMinicuotasCoupons extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:generate-minicuotas-coupons';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate SQL insert script for participants and manual mundial_coupons from public/minicuotas2.xlsx';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $excelPath = public_path('minicuotas2.xlsx');
        $outputPath = public_path('insert_minicuotas2_manual_coupons.sql');

        if (! file_exists($excelPath)) {
            $this->error("Excel file not found at: {$excelPath}");

            return 1;
        }

        $this->info('Loading existing participants from database...');
        $existingParticipants = DB::table('participants')->select('id', 'ci_number')->get();
        $participantMap = [];
        foreach ($existingParticipants as $p) {
            if ($p->ci_number !== null && $p->ci_number !== '') {
                $ciNormal = trim(strtoupper($p->ci_number));
                if (! isset($participantMap[$ciNormal])) {
                    $participantMap[$ciNormal] = $p->id;
                }
            }
        }
        $this->info('Loaded '.count($participantMap).' unique existing participants.');

        $this->info('Loading existing coupon codes from database...');
        $existingCodes = DB::table('mundial_coupons')->pluck('code')->toArray();
        $existingCodesMap = array_flip($existingCodes);
        $this->info('Loaded '.count($existingCodesMap).' existing coupon codes.');

        $this->info('Reading Excel file and generating SQL inserts...');
        $rows = (new FastExcel)->import($excelPath);

        $newParticipantsToInsert = [];
        $couponsToInsert = [];

        $totalRows = count($rows);
        $processed = 0;

        foreach ($rows as $row) {
            $processed++;
            if ($processed % 10000 === 0) {
                $this->info("Processed {$processed} / {$totalRows} rows...");
            }

            $name = trim(Str::upper((string) ($row['Nombre Completo'] ?? '')));
            $cellphone = trim((string) ($row['Telefono'] ?? ''));
            $ci = trim((string) ($row['Cedula de Identidad'] ?? ''));
            $dateStr = trim((string) ($row['Fecha'] ?? ''));

            if ($ci === '') {
                continue;
            }

            // Normalise date: e.g. 20260630 -> 2026-06-30
            $formattedDate = '2026-05-31';
            if (preg_match('/^\d{8}$/', $dateStr)) {
                $year = substr($dateStr, 0, 4);
                $month = substr($dateStr, 4, 2);
                $day = substr($dateStr, 6, 2);
                $formattedDate = "{$year}-{$month}-{$day}";
            }

            $ciNormal = strtoupper($ci);
            if (isset($participantMap[$ciNormal])) {
                $participantId = $participantMap[$ciNormal];
            } else {
                $participantId = (string) Str::uuid();
                $participantMap[$ciNormal] = $participantId;

                $newParticipantsToInsert[] = [
                    'id' => $participantId,
                    'name' => $name,
                    'cellphone' => $cellphone,
                    'ci_number' => $ci,
                    'document_type' => 'CI',
                    'status' => 'registered',
                    'date' => $formattedDate,
                    'time' => '00:00:00',
                ];
            }

            // Generate unique 7-digit alphanumeric code
            do {
                $code = Str::upper(Str::random(7));
            } while (isset($existingCodesMap[$code]));
            $existingCodesMap[$code] = true;

            $couponsToInsert[] = [
                'id' => (string) Str::uuid(),
                'sale_id' => null,
                'prize_id' => null,
                'participant_id' => $participantId,
                'business_unit' => 'ECO',
                'code' => $code,
                'status' => 'created',
                'active' => 1,
                'category' => 'manual',
            ];
        }

        $this->info('Generating SQL file...');

        $fileHandle = fopen($outputPath, 'w');
        if (! $fileHandle) {
            $this->error("Cannot open file for writing: {$outputPath}");

            return 1;
        }

        fwrite($fileHandle, "-- Database Schema Update\n");
        fwrite($fileHandle, "START TRANSACTION;\n\n");

        // Write participants chunked in 1000s
        $participantChunks = array_chunk($newParticipantsToInsert, 1000);
        foreach ($participantChunks as $chunkIndex => $chunk) {
            fwrite($fileHandle, "INSERT INTO participants (id, name, cellphone, ci_number, document_type, status, date, time, created_at, updated_at) VALUES\n");
            $valueLines = [];
            foreach ($chunk as $p) {
                $idEsc = $this->escape($p['id']);
                $nameEsc = $this->escape($p['name']);
                $phoneEsc = $this->escape($p['cellphone']);
                $ciEsc = $this->escape($p['ci_number']);
                $docTypeEsc = $this->escape($p['document_type']);
                $statusEsc = $this->escape($p['status']);
                $dateEsc = $this->escape($p['date']);
                $timeEsc = $this->escape($p['time']);

                $valueLines[] = "({$idEsc}, {$nameEsc}, {$phoneEsc}, {$ciEsc}, {$docTypeEsc}, {$statusEsc}, {$dateEsc}, {$timeEsc}, NOW(), NOW())";
            }
            fwrite($fileHandle, implode(",\n", $valueLines)."\n");
            fwrite($fileHandle, "ON DUPLICATE KEY UPDATE id=id;\n\n");
        }

        // Write coupons chunked in 1000s
        $couponChunks = array_chunk($couponsToInsert, 1000);
        foreach ($couponChunks as $chunkIndex => $chunk) {
            fwrite($fileHandle, "INSERT INTO mundial_coupons (id, sale_id, prize_id, participant_id, business_unit, code, status, active, category, created_at, updated_at) VALUES\n");
            $valueLines = [];
            foreach ($chunk as $c) {
                $idEsc = $this->escape($c['id']);
                $saleEsc = $this->escape($c['sale_id']);
                $prizeEsc = $this->escape($c['prize_id']);
                $partEsc = $this->escape($c['participant_id']);
                $buEsc = $this->escape($c['business_unit']);
                $codeEsc = $this->escape($c['code']);
                $statusEsc = $this->escape($c['status']);
                $activeEsc = (int) $c['active'];
                $categoryEsc = $this->escape($c['category']);

                $valueLines[] = "({$idEsc}, {$saleEsc}, {$prizeEsc}, {$partEsc}, {$buEsc}, {$codeEsc}, {$statusEsc}, {$activeEsc}, {$categoryEsc}, NOW(), NOW())";
            }
            fwrite($fileHandle, implode(",\n", $valueLines).";\n\n");
        }

        fwrite($fileHandle, "COMMIT;\n");
        fclose($fileHandle);

        $this->info("Successfully generated SQL script at: {$outputPath}");
        $this->info('Total participants to insert: '.count($newParticipantsToInsert));
        $this->info('Total coupons to insert: '.count($couponsToInsert));

        return 0;
    }

    /**
     * Escape string values for SQL
     */
    private function escape(?string $value): string
    {
        if ($value === null) {
            return 'NULL';
        }

        return "'".str_replace("'", "''", $value)."'";
    }
}
