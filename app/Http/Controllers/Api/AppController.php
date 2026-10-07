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
use App\Http\Requests\InformalMarketFormRequest;
use App\Http\Requests\MarkAsUsedRequest;
use App\Http\Requests\RegisterParticipantRequest;
use App\Interfaces\ParticipantServiceInteface;
use App\Models\Form;
use App\Models\Participant;
use App\Services\ApiResponseService;
use App\Services\FileUploadService;
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
     * Registra un nuevo participante y sus compras para la promoción **WHIRLPOOL SPECIAL DAYS**, generando los intentos disponibles para participar en la ruleta / lavadora.
     *
     * ### Explicación de los campos devueltos en `data`:
     * - `total_attempts` *(int)* 🟢 **`NUEVO`**: **Cantidad total de intentos/giros disponibles** para el participante en la promoción Whirlpool.
     *   - Representa el número de formularios registrados válidos listos para girar.
     *   - Solo contabiliza formularios con estado `registered`.
     *   - **No contabiliza** formularios en estado `used` (ya utilizados en un giro previo), `invalidated` (invalidados/anulados) ni `pending` (pendientes de aprobación administrativa).
     * - `message_promotion` *(string)* 🟢 **`NUEVO`**: **Mensaje dinámico informativo** personalizado para el usuario final según sus intentos disponibles:
     *   - Si tiene intentos disponibles (> 0): *"¡Felicidades! Usted generó X intento(s) en la promoción WHIRLPOOL SPECIAL DAYS por la compra de su(s) producto(s) de la marca WHIRLPOOL."*
     *   - Si no generó intentos: *"¡Gracias por su participación!"*
     * - `generate_participant_code` *(string)*: Identificador único (UUID) asignado al participante registrado.
     * - `participant_link` *(string)*: URL pública directa a la interfaz de juego (`/participate/{ci}`) para que el usuario gire la lavadora.
     * - `participant_name` *(string)*: Nombre completo del cliente registrado.
     * - `participant_email` *(string|null)*: Correo electrónico registrado.
     * - `participant_ci` *(string)*: Carnet de identidad o documento de identidad del participante.
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
     * Consulta el estado actual de un participante mediante su CI y devuelve sus datos junto con los giros/intentos disponibles en la promoción **WHIRLPOOL SPECIAL DAYS**.
     *
     * ### Explicación de los campos devueltos en `data`:
     * - `total_attempts` *(int)* 🟢 **`NUEVO`**: **Cantidad de intentos/giros disponibles y vigentes** que el cliente tiene en la promoción para participar en la ruleta/lavadora.
     *   - Se calcula contando los formularios asociados a la CI que se encuentren estrictamente en estado `registered`.
     *   - Formularios en estado `used` (ya girados mediante el WS), `invalidated` (anulados) o `pending` (aún en revisión manual) **no son contabilizados**.
     *   - Cuando el usuario realiza su giro mediante el Web Service, el formulario se actualiza a `used` y se descuenta automáticamente de este contador.
     * - `message_promotion` *(string)* 🟢 **`NUEVO`**: **Mensaje de estado para el cliente**:
     *   - Con intentos disponibles (> 0): *"¡Felicidades! Usted cuenta con X intento(s) disponible(s) en la promoción WHIRLPOOL SPECIAL DAYS."*
     *   - Sin intentos disponibles (= 0): *"No cuenta con intentos disponibles en este momento."*
     * - `generate_participant_code` *(string)*: Identificador único (UUID) del participante.
     * - `participant_link` *(string)*: URL directa al juego de la ruleta/lavadora (`/participate/{ci}`).
     * - `participant_name` *(string)*: Nombre del cliente.
     * - `participant_email` *(string|null)*: Correo electrónico del cliente.
     * - `participant_ci` *(string)*: Número de carnet de identidad consultado.
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

    /**
     * @exclude
     */
    public function checkCode(string $code)
    {
        try {
            $code = trim($code);
            if ($code === '') {
                return ApiResponseService::error(
                    message: 'El código proporcionado no es válido.',
                    errors: ['Debe enviar un código válido.'],
                    data: (object) [],
                    others: [],
                    code: 400
                );
            }

            // Buscar coincidencias directas por control_code o code
            $matchedForms = Form::where(function ($query) use ($code) {
                $query->where('control_code', $code)
                      ->orWhere('code', $code);
            })->get();

            if ($matchedForms->isEmpty()) {
                return ApiResponseService::error(
                    message: 'El código ingresado no existe.',
                    errors: ['No se encontró ningún formulario asociado al código proporcionado.'],
                    data: (object) [],
                    others: [],
                    code: 404
                );
            }

            // Si alguna coincidencia posee control_code (compra Dismac con múltiples productos),
            // agrupamos por ese control_code para obtener todos los intentos de dicha compra.
            $controlCode = $matchedForms->first(fn ($f) => ! empty($f->control_code))?->control_code;

            if (! empty($controlCode)) {
                $allForms = Form::with(['prize', 'branch', 'sale'])
                    ->where('control_code', $controlCode)
                    ->orderBy('created_at', 'asc')
                    ->get();
            } else {
                $allForms = Form::with(['prize', 'branch', 'sale'])
                    ->where('code', $code)
                    ->orderBy('created_at', 'asc')
                    ->get();
            }

            // Filtrar formularios según estado
            $registeredForms = $allForms->where('status', 'registered')->values();
            $usedForms = $allForms->where('status', 'used')->values();
            $lastForm = $registeredForms->first();

            // Si no existe ningún formulario en estado registered
            if (! $lastForm) {
                return ApiResponseService::error(
                    message: 'El código ya no cuenta con intentos disponibles o está en estado de validación.',
                    errors: ['Todos los intentos han sido utilizados o no se encuentran activos.'],
                    data: [
                        'last_form' => null,
                        'registered_count' => 0,
                        'registered_forms' => [],
                        'used_count' => $usedForms->count(),
                        'used_forms' => $usedForms,
                    ],
                    others: [],
                    code: 400
                );
            }

            return ApiResponseService::success(
                message: 'El código se validó con éxito.',
                data: [
                    'last_form' => $lastForm,
                    'registered_count' => $registeredForms->count(),
                    'registered_forms' => $registeredForms,
                    'used_count' => $usedForms->count(),
                    'used_forms' => $usedForms,
                ]
            );
        } catch (\Exception $e) {
            Log::error('Error in checkCode', [
                'code' => $code,
                'error' => $e->getMessage(),
            ]);
            $method = method_exists($e, 'getStatusCode') ? 'getStatusCode' : 'getCode';
            $responseMethod = $e->$method() == 0 ? 500 : $e->$method();

            return ApiResponseService::error(
                message: 'Error al validar el código.',
                errors: [$e->getMessage()],
                data: (object) [],
                others: [],
                code: $responseMethod
            );
        }
    }

    /**
     * @exclude
     */
    public function markAsUsed(MarkAsUsedRequest $request)
    {
        try {
            $id = $request->input('id');
            $controlCode = $request->input('control_code') ?: $request->input('codigo_control');

            $form = Form::where('id', $id)->first();

            if (! $form) {
                return ApiResponseService::error(
                    message: 'Formulario no encontrado.',
                    errors: ['No se encontró ningún registro con el identificador UUID proporcionado.'],
                    data: (object) [],
                    others: [],
                    code: 404
                );
            }

            if ($form->status === 'used') {
                return ApiResponseService::error(
                    message: 'El formulario ya ha sido utilizado.',
                    errors: ['Este intento ya fue utilizado previamente.'],
                    data: [
                        'id' => $form->id,
                        'status' => $form->status,
                    ],
                    others: [],
                    code: 400
                );
            }

            if ($form->status !== 'registered') {
                return ApiResponseService::error(
                    message: 'Estado de formulario no válido.',
                    errors: ["No se puede marcar como utilizado un formulario con estado '{$form->status}'."],
                    data: [
                        'id' => $form->id,
                        'status' => $form->status,
                    ],
                    others: [],
                    code: 400
                );
            }

            if (! empty($controlCode) && ! empty($form->control_code) && $form->control_code !== $controlCode) {
                return ApiResponseService::error(
                    message: 'El código de control no coincide.',
                    errors: ['El código de control enviado no coincide con el formulario.'],
                    data: (object) [],
                    others: [],
                    code: 400
                );
            }

            $form->update([
                'status' => 'used',
                'is_scratched' => 1,
            ]);

            return ApiResponseService::success(
                message: 'Formulario marcado como utilizado con éxito.',
                data: $form->fresh(['prize', 'branch', 'sale'])
            );
        } catch (\Exception $e) {
            Log::error('Error in markAsUsed', [
                'id' => $request->input('id'),
                'error' => $e->getMessage(),
            ]);
            $method = method_exists($e, 'getStatusCode') ? 'getStatusCode' : 'getCode';
            $responseMethod = $e->$method() == 0 ? 500 : $e->$method();

            return ApiResponseService::error(
                message: 'Error al marcar el formulario como utilizado.',
                errors: [$e->getMessage()],
                data: (object) [],
                others: [],
                code: $responseMethod
            );
        }
    }

    /**
     * @exclude
     */
    public function informalMarketForm(InformalMarketFormRequest $request)
    {
        try {
            $name = trim((string) $request->input('name'));
            $cellphone = trim((string) $request->input('cellphone'));
            // Recibir documento de identidad (priorizando ci_number o document_number)
            $ciNumber = trim((string) ($request->input('ci_number') ?: $request->input('document_number')));
            $city = trim((string) $request->input('city'));
            $base64File = (string) $request->input('file');

            // Optimizar imagen/documento y subir a S3 (o disco public local de respaldo)
            $uploadResult = FileUploadService::uploadOptimizedBase64($base64File, 'invoices/informal');

            // Generar código único de 6 caracteres alfanumérico
            $code = \Func::generateCode('App\Models\Form', 'code', 6, 'alphanumeric');

            // Filtrar por ci_number: si existe asociar participant_id, si no existe crear nuevo participante
            $participant = Participant::where('ci_number', $ciNumber)->first();
            if (! $participant) {
                $participant = Participant::create([
                    'name' => $name,
                    'ci_number' => $ciNumber,
                    'cellphone' => $cellphone,
                    'city' => $city,
                    'document_type' => 'CI',
                    'status' => 'registered',
                    'date' => date('Y-m-d'),
                    'time' => date('H:i:s'),
                ]);
            }

            // Crear registro en Form en estado 'pending' y tipo 'informal-market' (sin premio asignado)
            $form = Form::create([
                'name' => $name,
                'ci_number' => $ciNumber,
                'cellphone' => $cellphone,
                'city' => $city,
                'code' => $code,
                'status' => 'pending',
                'type' => 'informal-market',
                'business_unit' => 'MERCADO INFORMAL',
                'invoice_image' => $uploadResult['path'],
                'invoice_url' => $uploadResult['url'],
                'participant_id' => $participant->id,
                'branch_id' => null,
                'prize_id' => null,
                'user_id' => null,
            ]);

            return ApiResponseService::create(
                message: 'Su formulario se ha llenado con éxito.',
                data: [
                    'id' => $form->id,
                    'code' => $form->code,
                    'name' => $form->name,
                    'ci_number' => $form->ci_number,
                    'cellphone' => $form->cellphone,
                    'city' => $form->city,
                    'status' => $form->status,
                    'type' => $form->type,
                    'participant_id' => $form->participant_id,
                    'invoice_url' => $form->invoice_url,
                ]
            );
        } catch (\InvalidArgumentException $e) {
            return ApiResponseService::error(
                message: 'Error en el archivo enviado.',
                errors: [$e->getMessage()],
                data: (object) [],
                others: [],
                code: 422
            );
        } catch (\Exception $e) {
            Log::error('Error in informalMarketForm', [
                'error' => $e->getMessage(),
            ]);
            $method = method_exists($e, 'getStatusCode') ? 'getStatusCode' : 'getCode';
            $responseMethod = $e->$method() == 0 ? 500 : $e->$method();

            return ApiResponseService::error(
                message: 'Error al procesar el formulario.',
                errors: [$e->getMessage()],
                data: (object) [],
                others: [],
                code: $responseMethod
            );
        }
    }
}

