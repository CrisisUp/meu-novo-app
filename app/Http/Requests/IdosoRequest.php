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
            // Valida que deve ter pelo menos 60 anos (nascido antes de 60 anos atrás a partir de hoje)
            'data_nascimento' => 'required|date|before_or_equal:' . today()->subYears(60)->format('Y-m-d'),
            'sexo' => 'required|string|in:cis_m,cis_f,trans_m,trans_f,agenero,nao_declarado',
            'raca_cor' => 'required|string|in:branca,preta,parda,amarela,indigena,nao_informado',
            'grau_dependencia' => 'required|string|in:I,II,III',
            'remover_foto' => 'nullable|boolean',
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

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'data_nascimento.before_or_equal' => 'O idoso deve ter pelo menos 60 anos para ser cadastrado no CDI.',
        ];
    }
}
