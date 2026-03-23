<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    // carregar o formulario "cadastrar novo usuário"
    public function create()
    {
        return view('users.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6|confirmed',
            'role' => 'required|in:admin,funcionario',
        ]);

        // Criamos o usuário sem o role primeiro (fillable)
        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        
        // Atribuímos o role manualmente (seguro, pois este método é protegido pelo middleware admin-access)
        $user->role = $request->role;
        $user->save();

        return redirect()->route('user.index')->with('success', 'Usuário cadastrado com sucesso!');
    }

    public function index(Request $request)
    {
        $search = $request->input('search');

        $users = User::when($search, function ($query, $search) {
            return $query->where('name', 'like', "%{$search}%")
                         ->orWhere('email', 'like', "%{$search}%");
        })
        ->orderByDesc('id')
        ->paginate(10)
        ->withQueryString(); 

        return view('users.index', compact('users', 'search'));
    }

    public function edit(User $user)
    {
        return view('users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|min:6|confirmed',
            'role' => 'required|in:admin,funcionario',
        ]);

        $user->name = $request->name;
        $user->email = $request->email;
        
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        // Atribuição manual para contornar o bloqueio do fillable
        $user->role = $request->role;
        $user->save();

        return redirect()->route('user.index')->with('success', 'Usuário atualizado com sucesso!');
    }

    public function destroy(User $user)
    {
        // Impedir que o administrador exclua a si próprio
        if (auth()->id() === $user->id) {
            return back()->with('error', 'Você não pode excluir sua própria conta administrativa.');
        }

        $user->delete();
        return redirect()->route('user.index')->with('success', 'Usuário excluído com sucesso!');
    }
}
