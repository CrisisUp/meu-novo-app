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
            'cpf' => 'nullable|string|max:14|unique:idosos,cpf,' . $id,
            'nis' => 'required|string|max:14|unique:idosos,nis,' . $id,
            'contato_emergencia_nome' => 'required|string|max:255',
            'contato_emergencia_telefone' => 'required|string|max:20',
            'alergias' => 'nullable|string',
            'medicamentos' => 'nullable|string',
            'observacoes' => 'nullable|string',
        ];
    }
}
