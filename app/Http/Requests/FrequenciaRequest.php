<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FrequenciaRequest extends FormRequest
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
     */
    public function rules(): array
    {
        return [
            'data' => 'required|date',
            'presencas' => 'nullable|array',
            'observacoes' => 'nullable|array',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     */
    public function messages(): array
    {
        return [
            'data.required' => 'A data da frequência é obrigatória!',
            'data.date' => 'A data deve ser em um formato válido!',
        ];
    }
}
