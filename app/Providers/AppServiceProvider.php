<?php

namespace App\Providers;

use App\Interfaces\ParticipantInteface;
use App\Interfaces\ParticipantServiceInteface;
use App\Repositories\ParticipantRepository;
use App\Repositories\ParticipantService;
use Dedoc\Scramble\Scramble;
use Dedoc\Scramble\Support\Generator\OpenApi;
use Dedoc\Scramble\Support\Generator\SecuritySchemes\ApiKeySecurityScheme;
use Illuminate\Routing\Route;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(ParticipantInteface::class, ParticipantService::class);

        $this->app->bind(
            ParticipantInteface::class,
            ParticipantRepository::class
        );

        $this->app->bind(
            ParticipantServiceInteface::class,
            ParticipantService::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::define('viewApiDocs', function ($user = null) {
            return true;
        });

        Scramble::routes(function (Route $route) {
            return in_array($route->uri, [
                'api/register-participant',
                'api/participant/{document}',
            ]);
        });

        Scramble::afterOpenApiGenerated(function (OpenApi $openApi) {
            $openApi->info->title = 'Promo Whirlpool API Docs';
            $openApi->info->description = 'Documentación oficial de la API REST para la promoción WHIRLPOOL SPECIAL DAYS.';

            $scheme = new class('header', 'X-API-KEY') extends ApiKeySecurityScheme
            {
                public function toArray()
                {
                    return array_merge(parent::toArray(), [
                        'x-default' => 'XXXXX', // Stoplight Elements placeholder
                    ]);
                }
            };
            $scheme->setDescription('Clave secreta requerida para autenticar todas las solicitudes a la API. Solicítala al administrador.');
            $scheme->as('apiKey');

            $openApi->secure($scheme);
        });

        /*
        // Limpieza final del spec generado para evitar filtraciones por inferencia
        Scramble::configure()->withDocumentTransformers(function (array $openApi) {
            $sensitiveKey = config('services.dismac.secret_key');
            if (!$sensitiveKey) return $openApi;

            // Buscamos y reemplazamos cualquier rastro de la key real en el JSON final
            $json = json_encode($openApi);
            $json = str_replace($sensitiveKey, 'XXXXX', $json);
            return json_decode($json, true);
        });
        */
    }
}
