<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
            'data_nascimento' => 'required|date|before:today',
            'sexo' => 'required|string|in:cis_m,cis_f,trans_m,trans_f,agenero,nao_declarado',
            'raca_cor' => 'required|string|in:branca,preta,parda,amarela,indigena,nao_informado',
            'grau_dependencia' => 'required|string|in:I,II,III',
            'remover_foto' => 'nullable|boolean',
            'data_admissao' => 'required|date',
            'data_desligamento' => 'nullable|date|after_or_equal:data_admissao',
            'motivo_desligamento' => 'nullable|string|max:255',
            'cpf' => [
                'nullable',
                'string',
                'max:14',
                Rule::unique('idosos', 'cpf')->ignore($id)->whereNull('deleted_at')
            ],
            'nis' => [
                'nullable',
                'string',
                'max:20',
                Rule::unique('idosos', 'nis')->ignore($id)->whereNull('deleted_at')
            ],
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
            'data_nascimento.before' => 'A data de nascimento deve ser anterior a hoje.',
            'cpf.unique' => 'Este CPF já está cadastrado no sistema.',
            'nis.unique' => 'Este NIS já está cadastrado no sistema.',
        ];
    }
}
