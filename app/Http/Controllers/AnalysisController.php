<?php

namespace App\Http\Controllers;

use App\Models\Analysis;
use App\Models\Product;
use App\Models\Sale;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\File;

class AnalysisController extends Controller
{
    public function index()
    {
        return redirect()->route('dashboard');
    }

    public function ask(Request $request)
    {
        $validated = $request->validate([
            'prompt' => ['required', 'string', 'max:4000'],
        ]);

        $analysis = $this->refreshAnalysis();
        $errorMessage = null;
        $answer = null;

        if (! config('services.openai.api_key')) {
            $errorMessage = 'Configure OPENAI_API_KEY no arquivo .env para usar o prompt.';
        } else {
            try {
                $response = Http::timeout(60)
                    ->withToken(config('services.openai.api_key'))
                    ->post('https://api.openai.com/v1/responses', [
                        'model' => config('services.openai.model', 'gpt-4.1-mini'),
                        'instructions' => $this->buildInstructions($analysis),
                        'input' => $validated['prompt'],
                    ])
                    ->throw()
                    ->json();

                $answer = $response['output_text'] ?? $this->extractOutputText($response);
            } catch (\Throwable $exception) {
                $errorMessage = 'Falha ao consultar a OpenAI: ' . $exception->getMessage();
            }
        }

        return view('dashboard.dashboard', [
            'users' => User::with(['sales.product'])->get(),
            'prompt' => $validated['prompt'],
            'answer' => $answer,
            'errorMessage' => $errorMessage,
        ]);
    }

    private function refreshAnalysis(): Analysis
    {
        $produtoMaisVendido = Sale::query()
            ->select('product_id')
            ->selectRaw('COUNT(*) as total_vendas')
            ->groupBy('product_id')
            ->orderByDesc('total_vendas')
            ->with('product:id,name')
            ->first();

        $produtoMaisCaro = Product::query()
            ->orderByDesc('preco')
            ->first();

        $analysis = Analysis::query()->first() ?? new Analysis();

        $analysis->fill([
            'produto_mais_vendido' => $produtoMaisVendido?->product?->name,
            'produto_mais_caro' => $produtoMaisCaro?->name,
        ]);

        $analysis->save();

        return $analysis;
    }

    private function buildInstructions(Analysis $analysis): string
    {
        $referenceText = $this->loadReferences();

        return implode("\n\n", [
            'Voce responde como um assistente interno do sistema Laravel.',
            'Use apenas o contexto fornecido pelo banco e pelos arquivos de referencia locais.',
            'Se faltar dado, deixe isso explicito e nao invente.',
            'Contexto atual da analise:',
            'produto_mais_vendido: ' . ($analysis->produto_mais_vendido ?? 'nenhum'),
            'produto_mais_caro: ' . ($analysis->produto_mais_caro ?? 'nenhum'),
            'Referencias locais:',
            $referenceText,
        ]);
    }

    private function loadReferences(): string
    {
        $referencePath = base_path('references');

        if (! File::isDirectory($referencePath)) {
            return 'Nenhum arquivo de referencia encontrado.';
        }

        $files = collect(File::files($referencePath))
            ->filter(fn ($file) => in_array($file->getExtension(), ['md', 'txt'], true))
            ->sortBy(fn ($file) => $file->getFilename())
            ->map(function ($file) {
                return 'Arquivo: ' . $file->getFilename() . "\n" . trim(File::get($file->getPathname()));
            });

        return $files->isNotEmpty()
            ? $files->implode("\n\n")
            : 'Nenhum arquivo de referencia encontrado.';
    }

    private function extractOutputText(array $response): ?string
    {
        foreach ($response['output'] ?? [] as $outputItem) {
            foreach ($outputItem['content'] ?? [] as $contentItem) {
                if (($contentItem['type'] ?? null) === 'output_text') {
                    return $contentItem['text'] ?? null;
                }
            }
        }

        return null;
    }

    private function defaultPrompt(): string
    {
        return 'Explique o cenario atual das analises com base nos dados locais.';
    }
}
