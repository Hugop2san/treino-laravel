<?php

namespace App\Http\Controllers;

use App\Models\Analysis;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $users = User::with(['sales.product'])->get();

        return view('dashboard.dashboard', [
            'users' => $users,
            'analysis' => Analysis::query()->first(),
            'prompt' => old('prompt', 'Explique o cenario atual das analises com base nos dados locais.'),
            'answer' => null,
            'errorMessage' => null,
        ]);
    }
}
