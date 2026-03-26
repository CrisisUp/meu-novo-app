<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        $users = User::filtered($search)
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString(); 

        return view('users.index', compact('users', 'search'));
    }

    public function create()
    {
        return view('users.create');
    }

    public function store(UserRequest $request)
    {
        $data = $request->validated();
        
        $user = new User();
        $user->name = $data['name'];
        $user->email = $data['email'];
        $user->password = Hash::make($data['password']);
        
        // Atribuição manual para segurança e contorno do fillable
        $user->role = $data['role'];
        $user->save();

        return redirect()->route('user.index')->with('success', 'Usuário cadastrado com sucesso!');
    }

    public function edit(User $user)
    {
        return view('users.edit', compact('user'));
    }

    public function update(UserRequest $request, User $user)
    {
        $data = $request->validated();

        // Evitar que o próprio admin altere seu perfil e perca acesso acidentalmente
        if (auth()->id() === $user->id && $data['role'] !== 'admin') {
            return back()->with('error', 'Você não pode alterar seu próprio nível de acesso administrativo.');
        }

        $user->name = $data['name'];
        $user->email = $data['email'];
        
        if ($request->filled('password')) {
            $user->password = Hash::make($data['password']);
        }

        $user->role = $data['role'];
        $user->save();

        return redirect()->route('user.index')->with('success', 'Usuário atualizado com sucesso!');
    }

    public function destroy(User $user)
    {
        if (auth()->id() === $user->id) {
            return back()->with('error', 'Você não pode excluir sua própria conta administrativa.');
        }

        $user->delete();
        return redirect()->route('user.index')->with('success', 'Usuário excluído com sucesso!');
    }
}
