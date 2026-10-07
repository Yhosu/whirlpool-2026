<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class InformalMarketFormRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        if ($this->has('document_number') && $this->document_number !== null) {
            $this->merge([
                'document_number' => (string) $this->document_number,
            ]);
        }

        if ($this->has('ci_number') && $this->ci_number !== null) {
            $this->merge([
                'ci_number' => (string) $this->ci_number,
            ]);
        }

        if ($this->has('cellphone') && $this->cellphone !== null) {
            $this->merge([
                'cellphone' => (string) $this->cellphone,
            ]);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'cellphone' => 'required|string|max:50',
            'file' => 'required|string',
            'document_number' => 'required_without:ci_number|nullable|string|max:50',
            'ci_number' => 'required_without:document_number|nullable|string|max:50',
            'city' => 'required|string|max:100',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'El nombre completo es obligatorio.',
            'cellphone.required' => 'El número de teléfono/celular es obligatorio.',
            'file.required' => 'El archivo de la factura/boleta en formato base64 es obligatorio.',
            'document_number.required' => 'El número de documento de identidad es obligatorio.',
            'document_number.required_without' => 'El número de documento de identidad es obligatorio.',
            'city.required' => 'La ciudad es obligatoria.',
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
