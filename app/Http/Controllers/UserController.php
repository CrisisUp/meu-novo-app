<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Models\User;
use Exception;

class UserController extends Controller
{
    // carregar o formulario "cadastrar novo usuário"
    public function create()
    {
        // Carregar a VIEW
        // dd("Formulário");
        return view('users.create');
    }

    public function store(UserRequest $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6|confirmed',
            'role' => 'required|in:admin,funcionario',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password,
            'role' => $request->role,
        ]);

        return redirect()->route('user.index')->with('success', 'Usuário cadastrado com sucesso!');
    }

    public function index(\Illuminate\Http\Request $request)
    {
        $search = $request->input('search');

        $users = User::when($search, function ($query, $search) {
            return $query->where('name', 'like', "%{$search}%")
                         ->orWhere('email', 'like', "%{$search}%");
        })
        ->orderByDesc('id')
        ->paginate(10)
        ->withQueryString(); // Mantém o termo de busca ao trocar de página

        return view('users.index', compact('users', 'search'));
    }


    public function edit(User $user)
    {
        return view('users.edit', compact('user'));
    }

    public function update(UserRequest $request, User $user)
    {
        // 1. Validação personalizada para edição
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|min:6|confirmed', // Senha opcional na edição
            'role' => 'required|in:admin,funcionario',
        ]);

        // 2. Preparar os dados para atualizar
        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
        ];

        // Se a senha foi preenchida, adiciona aos dados
        if ($request->filled('password')) {
            $data['password'] = $request->password;
        }

        // 3. Atualizar no banco
        $user->update($data);

        return redirect()->route('user.index')->with('success', 'Usuário atualizado com sucesso!');
    }

    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('user.index')->with('success', 'Usuário excluído com sucesso!');
    }
}
