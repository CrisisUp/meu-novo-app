<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AtividadeRequest extends FormRequest
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
            'nome' => 'required|string|max:255',
            'facilitador' => 'nullable|string|max:255',
            'dia_semana' => 'required|in:segunda,terca,quarta,quinta,sexta,sabado,domingo',
            'horario' => 'required',
            'descricao' => 'nullable|string',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     */
    public function messages(): array
    {
        return [
            'nome.required' => 'O nome da atividade é obrigatório!',
            'dia_semana.required' => 'O dia da semana é obrigatório!',
            'dia_semana.in' => 'Selecione um dia da semana válido!',
            'horario.required' => 'O horário de início é obrigatório!',
        ];
    }
}
