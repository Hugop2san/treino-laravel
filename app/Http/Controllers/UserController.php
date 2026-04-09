<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

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
                            
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
        ]);

        $validated['password'] = Hash::make(Str::random(16));

        User::create($validated);

        return redirect()->route('dashboard.user.index')->with('success', 'Usuario criado com sucesso.');
    }

    public function edit(User $user)
    {
        $users = User::orderByDesc('id')->get();

        return view('createuser.createuser', compact('users', 'user'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,' . $user->id],
        ]);

        $user->update($validated);

        return redirect()->route('dashboard.user.index')->with('success', 'Usuario atualizado com sucesso.');
    }

    public function destroy(User $user)
    {
        $user->delete();

        return redirect()->route('dashboard.user.index')->with('success', 'Usuario removido com sucesso.');
    }
}
