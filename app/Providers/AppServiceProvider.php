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

            $fieldDescriptions = [
                'generate_participant_code' => 'Identificador único (UUID) asignado al participante registrado.',
                'participant_link' => 'Enlace web directo para que el participante ingrese a la ruleta/lavadora con su carnet.',
                'participant_name' => 'Nombre completo del participante registrado.',
                'participant_email' => 'Correo electrónico del participante (puede ser null).',
                'participant_ci' => 'Carnet o documento de identidad del participante.',
                'total_attempts' => '🟢 [NUEVO] Cantidad de giros/intentos disponibles en la promoción Whirlpool (formularios en estado "registered"). Excluye los estados "used", "invalidated" y "pending".',
                'message_promotion' => '🟢 [NUEVO] Mensaje promocional dinámico informativo indicando la cantidad de intentos generados o disponibles.',
            ];

            foreach ($openApi->paths as $path) {
                foreach ($path->operations as $operation) {
                    foreach ($operation->responses as $response) {
                        if (($response->code ?? null) != 200) {
                            continue;
                        }
                        $schema = $response->content['application/json'] ?? null;
                        if (! $schema) {
                            continue;
                        }
                        $type = $schema->type ?? null;
                        if ($type instanceof \Dedoc\Scramble\Support\Generator\Types\ObjectType) {
                            $dataType = $type->properties['data'] ?? null;
                            if ($dataType instanceof \Dedoc\Scramble\Support\Generator\Types\ObjectType) {
                                foreach ($fieldDescriptions as $field => $desc) {
                                    if (isset($dataType->properties[$field])) {
                                        $dataType->properties[$field]->setDescription($desc);
                                    }
                                }
                            }
                        }
                    }
                }
            }
        });
    }
}
