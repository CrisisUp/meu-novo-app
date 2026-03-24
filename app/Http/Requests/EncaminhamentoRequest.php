<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EncaminhamentoRequest extends FormRequest
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
            'idoso_id' => 'required|exists:idosos,id,data_desligamento,NULL',
            'instituicao_destino' => 'required|string|max:255',
            'especialidade' => 'nullable|string|max:255',
            'motivo' => 'required|string',
            'prioridade' => 'required|in:urgente,programado,rotina',
            'data_encaminhamento' => 'required|date',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     */
    public function messages(): array
    {
        return [
            'idoso_id.required' => 'O idoso é obrigatório!',
            'idoso_id.exists' => 'O idoso selecionado é inválido, está desligado ou não existe.',
            'instituicao_destino.required' => 'A instituição de destino é obrigatória!',
            'motivo.required' => 'O motivo do encaminhamento é obrigatório!',
            'prioridade.required' => 'A prioridade é obrigatória!',
            'data_encaminhamento.required' => 'A data do encaminhamento é obrigatória!',
        ];
    }
}
