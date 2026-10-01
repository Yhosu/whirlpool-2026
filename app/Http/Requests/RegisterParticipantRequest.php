<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class RegisterParticipantRequest extends FormRequest
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
     * @bodyParam sale_items.*.code_brand integer required El código de la marca debe ser un número entero. Example: 15
     * @bodyParam sale_items.*.quantity integer Opcional. La cantidad de ítems. Por defecto es 1. Example: 2
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required',
            'cellphone' => 'nullable',
            'birth_date' => 'nullable',
            'ci_number' => 'required',
            'business_unit' => 'required|in:ECO,RET,ACE',
            'control_code' => 'required|unique:sales,control_code',
            'ci_number_complement' => 'nullable',
            'sale_type' => 'required|in:CONTADO,MINICUOTAS',
            'city' => 'required',
            'city_agency' => 'required',
            'email' => 'nullable',
            'total_amount' => 'required|min:1',
            'invoice_number' => 'nullable',
            'invoice_url' => 'nullable',
            'sale_date' => 'required',
            'document_type' => 'required|in:CI,NIT,PE',
            'sale_items' => 'required|array|min:1',
            'sale_items.*.code_brand' => 'required',
            'sale_items.*.amount' => 'required|min:1',
            'sale_items.*.name' => 'nullable',
            'sale_items.*.quantity' => 'nullable|integer|min:1',
        ];
    }

    public function messages(): array
    {
        return [
            'name' => 'El nombre es obligatorio.',
            'ci_number' => 'El carnet de identidad es obligatorio.',
            'city' => 'La ciudad es obligatoria',
            'city_agency' => 'La agencia es obligatoria.',
            'total_amount' => 'El monto total es obligatorio',
            'sale_type' => 'sale_type es obligatorio y debe tener los valores CONTADO o MINICUOTAS',
            'sale_date' => 'La fecha de compra es obligatoria.',
            'business_unit' => 'business_unit es obligatorio y debe tener los valores RET o ECO',
            'control_code' => 'El código de control es obligatorio y debe ser único',
        ];
    }

    public function failedValidation(Validator $validator): void
    {
        $errors = $validator->errors()->all();
        $response = [
            'process' => false,
            'message' => 'Error en el envío de parámetros',
            'errors' => $errors,
            'data' => [],
        ];
        throw new ValidationException($validator, response()->json($response, 400));
    }
}
