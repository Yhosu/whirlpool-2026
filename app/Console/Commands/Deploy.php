<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class Deploy extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'deploy';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reset migration, migrate, seed and run other initial tasks.';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        if (\App::environment('local')) {
            // $this->callSilent('empty:storage');
            $this->callSilent('down');
            $this->info('0%: Deploy iniciado. Modo Mantenimiento iniciado');
            if (config('jagq.before_migrate')) {
                \CustomFunc::before_migrate_actions();
                $this->info('10%: Before migrate');
            }
            $this->callSilent('migrate:fresh');
            $this->info('20%: Reset migrate ejecutado correctamente.');
            $this->info('60%: Migrate ejecutado correctamente.');
            if (config('jagq.after_migrate')) {
                \CustomFunc::after_migrate_actions();
                $this->info('70%: After migrate');
            }
            $this->info('75%: Database seed ejecutado correctamente con nodos.');
            $this->info('100%: Deploy finalizado.');
            $this->callSilent('db:seed');
            $this->callSilent('up');
            \Log::info('Deploy realizado con exito al sistema.');
        } else {
            $this->info('No autorizado.');
        }
    }
}
