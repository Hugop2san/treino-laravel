<?php

namespace App\Http\Controllers;

use App\Application\Analysis\AskAnalysisUseCase;
use App\Models\User;
use Illuminate\Http\Request;

class AnalysisController extends Controller
{
    public function index()
    {
        return redirect()->route('dashboard');
    }

    public function ask(Request $request, AskAnalysisUseCase $askAnalysisUseCase)
    {
        $validated = $request->validate([
            'prompt' => ['required', 'string', 'max:4000'],
        ]);

        $result = $askAnalysisUseCase->execute($validated['prompt']);

        return view('dashboard.dashboard', [
            'users' => User::with(['sales.product'])->get(),
            'analysis' => $result->analysis,
            'prompt' => $validated['prompt'],
            'answer' => $result->answer,
            'errorMessage' => $result->errorMessage,
        ]);
    }

}
