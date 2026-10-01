<?php

/**
 * Promoción Whirlpool Special Days
 *
 * PHP version 8.2
 *
 * @abstract  Controlador Principal de API REST PARA PARTICIPANTES - Promoción Whirlpool
 *
 * @category  Production
 *
 * @copyright 2026
 */

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterParticipantRequest;
use App\Interfaces\ParticipantServiceInteface;
use App\Services\ApiResponseService;
use Dedoc\Scramble\Attributes\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AppController extends Controller
{
    public function __construct(
        private readonly ParticipantServiceInteface $participantService
    ) {}

    /**
     * Registrar Participante
     *
     * Registra un nuevo participante y sus ventas en la promoción WHIRLPOOL SPECIAL DAYS, generando sus intentos disponibles para la ruleta/lavadora.
     *
     * @return array{process: bool, message: string, data: array{generate_participant_code: string, participant_link: string, participant_name: string, participant_email: string|null, participant_ci: string, total_attempts: int, message_promotion: string}, errors: array<int, string>}
     */
    #[Response(
        status: 200,
        description: 'Participante registrado con éxito',
        examples: [
            'Registro Exitoso' => [
                'process' => true,
                'message' => 'Participante registrado con éxito.',
                'data' => [
                    'generate_participant_code' => 'b7e28938-1a52-4f1e-9276-32454a867b32',
                    'participant_link' => 'https://promociones.dismac.com.bo/participate/12345678',
                    'participant_name' => 'Juan Pérez',
                    'participant_email' => 'juan.perez@example.com',
                    'participant_ci' => '12345678',
                    'total_attempts' => 1,
                    'message_promotion' => '¡Felicidades! Usted generó 1 intento(s) en la promoción WHIRLPOOL SPECIAL DAYS por la compra de su(s) producto(s) de la marca WHIRLPOOL.',
                ],
                'errors' => [],
            ],
        ]
    )]
    #[Response(
        status: 400,
        description: 'Error en los parámetros o participante no permitido',
        examples: [
            'Error de Participación' => [
                'process' => false,
                'message' => 'Error al crear el participante',
                'errors' => ['Este participante no puede participar de esta promoción.'],
                'data' => [],
            ],
        ]
    )]
    #[Response(
        status: 401,
        description: 'API Key inválida',
        examples: [
            'No Autorizado' => [
                'message' => 'Unauthorized: Invalid or missing API Key',
            ],
        ]
    )]
    #[Response(
        status: 422,
        description: 'Error de validación de campos',
        examples: [
            'Campos Requeridos' => [
                'process' => false,
                'message' => 'Error en el envío de parámetros',
                'errors' => [
                    'El carnet de identidad es obligatorio.',
                    'La ciudad es obligatoria',
                ],
                'data' => [],
            ],
        ]
    )]
    public function registerParticipant(RegisterParticipantRequest $request)
    {
        try {
            $participant = $this->participantService->registerParticipant($request->all());

            return ApiResponseService::success(
                message: 'Participante registrado con éxito.',
                data: $participant
            );
        } catch (\Exception $e) {
            Log::error('Error register participant', [
                'error' => $e->getMessage(),
            ]);
            $method = method_exists($e, 'getStatusCode') ? 'getStatusCode' : 'getCode';
            $responseMethod = $e->$method() == 0 ? 500 : $e->$method();

            return ApiResponseService::error('Error al crear el participante', [$e->getMessage()], (object) [], (object) [], 400);
        }
    }

    /**
     * Obtener Datos del Participante
     *
     * Busca un participante por su CI y retorna sus datos e intentos disponibles en la promoción WHIRLPOOL SPECIAL DAYS.
     *
     * @return array{process: bool, message: string, data: array{generate_participant_code: string, participant_link: string, participant_name: string, participant_email: string|null, participant_ci: string, total_attempts: int, message_promotion: string}, errors: array<int, string>}
     */
    #[Response(
        status: 200,
        description: 'Participante obtenido con éxito',
        examples: [
            'Consulta Exitosa' => [
                'process' => true,
                'message' => 'Participante obtenido con éxito.',
                'data' => [
                    'generate_participant_code' => 'b7e28938-1a52-4f1e-9276-32454a867b32',
                    'participant_link' => 'https://promociones.dismac.com.bo/participate/12345678',
                    'participant_name' => 'Juan Pérez',
                    'participant_email' => 'juan.perez@example.com',
                    'participant_ci' => '12345678',
                    'total_attempts' => 1,
                    'message_promotion' => '¡Felicidades! Usted cuenta con 1 intento(s) disponible(s) en la promoción WHIRLPOOL SPECIAL DAYS.',
                ],
                'errors' => [],
            ],
        ]
    )]
    #[Response(
        status: 400,
        description: 'Participante no encontrado o error en la consulta',
        examples: [
            'No Encontrado' => [
                'process' => false,
                'message' => 'Error al obtener el participante',
                'errors' => ['El participante no se encuentra registrado.'],
                'data' => [],
            ],
        ]
    )]
    #[Response(
        status: 401,
        description: 'API Key inválida',
        examples: [
            'No Autorizado' => [
                'message' => 'Unauthorized: Invalid or missing API Key',
            ],
        ]
    )]
    public function getDataParticipant($ciNumber)
    {
        try {
            $participant = $this->participantService->findByCiNumber($ciNumber);

            return ApiResponseService::success(
                message: 'Participante obtenido con éxito.',
                data: $participant
            );
        } catch (\Exception $e) {
            Log::error('Error register participant', ['error' => $e->getMessage()]);
            $method = method_exists($e, 'getStatusCode') ? 'getStatusCode' : 'getCode';
            $responseMethod = $e->$method() == 0 ? 500 : $e->$method();

            return ApiResponseService::error('Error al obtener el participante', [$e->getMessage()], (object) [], (object) [], $responseMethod);
        }
    }

    public function getPrices()
    {
        try {
            $prices = $this->participantService->getPrices();

            return ApiResponseService::success(
                message: 'Premios obtenidos exitósamente.',
                data: $prices
            );
        } catch (\Exception $e) {
            Log::error('Error register participant', ['error' => $e->getMessage()]);
            $method = method_exists($e, 'getStatusCode') ? 'getStatusCode' : 'getCode';
            $responseMethod = $e->$method() == 0 ? 500 : $e->$method();

            return ApiResponseService::error('Error al obtener el participante', [$e->getMessage()], [], [], $responseMethod);
        }
    }

    public function getJsonParticipants()
    {
        try {
            $result = $this->participantService->getJsonParticipants();

            return ApiResponseService::success(
                message: 'Premios obtenidos exitósamente.',
                data: $result
            );
        } catch (\Exception $e) {
            Log::error('Error register participant', ['error' => $e->getMessage()]);
            $method = method_exists($e, 'getStatusCode') ? 'getStatusCode' : 'getCode';
            $responseMethod = $e->$method() == 0 ? 500 : $e->$method();

            return ApiResponseService::error('Error al obtener el participante', [$e->getMessage()], [], [], $responseMethod);
        }
    }

    public function postUpdateWinner(Request $request)
    {
        try {
            $result = $this->participantService->updateWinner($request->all());

            return ApiResponseService::success(
                message: 'Cupón ganador actualizado correctamente.',
                data: []
            );
        } catch (\Exception $e) {
            Log::error('Error register participant', ['error' => $e->getMessage()]);
            $method = method_exists($e, 'getStatusCode') ? 'getStatusCode' : 'getCode';
            $responseMethod = $e->$method() == 0 ? 500 : $e->$method();

            return ApiResponseService::error('Error al obtener el participante', [$e->getMessage()], [], [], $responseMethod);
        }
    }

    public function getWinnersByPrice($priceId)
    {
        try {
            $result = $this->participantService->getWinnersByPrice($priceId);

            return ApiResponseService::success(
                message: 'Premios obtenidos exitósamente.',
                data: $result
            );
        } catch (\Exception $e) {
            Log::error('Error register participant', ['error' => $e->getMessage()]);
            $method = method_exists($e, 'getStatusCode') ? 'getStatusCode' : 'getCode';
            $responseMethod = $e->$method() == 0 ? 500 : $e->$method();

            return ApiResponseService::error('Error al obtener el participante', [$e->getMessage()], [], [], $responseMethod);
        }
    }
}
