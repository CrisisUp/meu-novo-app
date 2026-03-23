<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class IdosoRequest extends FormRequest
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
        $id = $this->route('idoso') ? $this->route('idoso')->id : null;

        return [
            'nome' => 'required|string|max:255',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:10240',
            'data_nascimento' => 'required|date',
            'sexo' => 'required|string|in:cis_m,cis_f,trans_m,trans_f,agenero,nao_declarado',
            'grau_dependencia' => 'required|string|in:I,II,III',
            'data_admissao' => 'required|date',
            'data_desligamento' => 'nullable|date|after_or_equal:data_admissao',
            'motivo_desligamento' => 'nullable|string|max:255',
            'cpf' => 'nullable|string|max:14|unique:idosos,cpf,' . $id,
            'nis' => 'nullable|string|max:14|unique:idosos,nis,' . $id,
            'contato_emergencia_nome' => 'required|string|max:255',
            'contato_emergencia_telefone' => 'required|string|max:20',
            'alergias' => 'nullable|string',
            'medicamentos' => 'nullable|string',
            'observacoes' => 'nullable|string',
        ];
    }
}
