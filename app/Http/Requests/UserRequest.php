<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserRequest extends FormRequest
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
        $user = $this->route('user');
        
        $rules = [
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                'max:255',
                $user ? Rule::unique('users')->ignore($user->id) : Rule::unique('users'),
            ],
            'role' => 'required|in:admin,funcionario',
        ];

        if ($this->isMethod('POST')) {
            $rules['password'] = 'required|string|min:6|confirmed';
        } else {
            $rules['password'] = 'nullable|string|min:6|confirmed';
        }

        return $rules;
    }

    /**
     * Get the error messages for the defined validation rules.
     */
    public function messages(): array
    {
        return [
            'name.required' => "O nome é obrigatório!",
            'email.required' => "O e-mail é obrigatório!",
            'email.email' => "O e-mail deve ser válido!",
            'email.unique' => "Este e-mail já está em uso!",
            'role.required' => "O cargo/perfil é obrigatório!",
            'role.in' => "Selecione um perfil válido!",
            'password.required' => "A senha é obrigatória para novos usuários!",
            'password.min' => "A senha deve ter no mínimo :min caracteres!",
            'password.confirmed' => "A confirmação da senha não confere!",
        ];
    }
}
