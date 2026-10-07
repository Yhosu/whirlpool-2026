<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class MarkAsUsedRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'id' => 'required|uuid',
            'control_code' => 'nullable|string|max:100',
            'codigo_control' => 'nullable|string|max:100',
        ];
    }

    public function messages(): array
    {
        return [
            'id.required' => 'El identificador (uuid) del formulario es obligatorio.',
            'id.uuid' => 'El identificador debe tener un formato UUID válido.',
        ];
    }

    public function failedValidation(Validator $validator): void
    {
        $errors = $validator->errors()->all();
        $response = [
            'process' => false,
            'message' => 'Error de validación en los parámetros enviados.',
            'errors' => $errors,
            'data' => (object) [],
        ];

        throw new ValidationException($validator, response()->json($response, 400));
    }
}
