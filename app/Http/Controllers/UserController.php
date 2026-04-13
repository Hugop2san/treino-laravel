<?php

namespace App\Http\Controllers;

use App\Application\Users\CreateUserUseCase;
use App\Application\Users\UpdateUserUseCase;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        $users = User::orderByDesc('id')->get();

        return view('createuser.createuser', compact('users'));
    }
    
    public function create()
    {
        return view('createuser.createuser', ['users' => User::orderByDesc('id')->get()]);
    }
                            
    public function store(Request $request, CreateUserUseCase $createUserUseCase)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
        ]);

        $createUserUseCase->execute($validated);

        return redirect()->route('dashboard.user.index')->with('success', 'Usuario criado com sucesso.');
    }

    public function edit(User $user)
    {
        $users = User::orderByDesc('id')->get();

        return view('createuser.createuser', compact('users', 'user'));
    }

    public function update(Request $request, User $user, UpdateUserUseCase $updateUserUseCase)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,' . $user->id],
        ]);

        $updateUserUseCase->execute($user, $validated);

        return redirect()->route('dashboard.user.index')->with('success', 'Usuario atualizado com sucesso.');
    }

    public function destroy(User $user)
    {
        $user->delete();

        return redirect()->route('dashboard.user.index')->with('success', 'Usuario removido com sucesso.');
    }
}
