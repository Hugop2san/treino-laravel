<?php

namespace App\Application\Analysis;

use App\Models\Analysis;
use App\Models\Product;
use App\Models\Sale;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Schema;

class AskAnalysisUseCase
{
    private ?bool $hasPrecoProdutoMaisCaroColumn = null;
    private ?bool $hasProdutoMaisBaratoColumn = null;
    private ?bool $hasPrecoProdutoMaisBaratoColumn = null;

    public function execute(string $prompt): AskAnalysisResult
    {
        $analysis = $this->refreshAnalysis();

        if (! config('services.openai.api_key')) {
            return new AskAnalysisResult(
                answer: null,
                errorMessage: 'Configure OPENAI_API_KEY no arquivo .env para usar o prompt.',
                analysis: $analysis
            );
        }

        try {
            $response = Http::timeout(60)
                ->withOptions([
                    'verify' => config('services.openai.verify_ssl', true),
                ])
                ->withToken(config('services.openai.api_key'))
                ->post('https://api.openai.com/v1/responses', [
                    'model' => config('services.openai.model', 'gpt-5.4'),
                    'instructions' => $this->buildInstructions($analysis),
                    'input' => $prompt,
                ])
                ->throw()
                ->json();

            return new AskAnalysisResult(
                answer: $response['output_text'] ?? $this->extractOutputText($response),
                errorMessage: null,
                analysis: $analysis
            );
        } catch (\Throwable $exception) {
            return new AskAnalysisResult(
                answer: null,
                errorMessage: 'Falha ao consultar a OpenAI: ' . $exception->getMessage(),
                analysis: $analysis
            );
        }
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
        $produtoMaisBarato = Product::query()
            ->orderBy('preco')
            ->first();

        $analysis = Analysis::query()->first() ?? new Analysis();

        $payload = [
            'produto_mais_vendido' => $produtoMaisVendido?->product?->name,
            'produto_mais_caro' => $produtoMaisCaro?->name,
        ];

        if ($this->supportsPrecoProdutoMaisCaroColumn()) {
            $payload['preco_produto_mais_caro'] = $produtoMaisCaro?->preco;
        }

        if ($this->supportsProdutoMaisBaratoColumn()) {
            $payload['produto_mais_barato'] = $produtoMaisBarato?->name;
        }

        if ($this->supportsPrecoProdutoMaisBaratoColumn()) {
            $payload['preco_produto_mais_barato'] = $produtoMaisBarato?->preco;
        }

        $analysis->fill($payload);

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
            'produto_mais_barato: ' . ($analysis->produto_mais_barato ?? 'nenhum'),
            'preco_produto_mais_caro: ' . ($analysis->preco_produto_mais_caro ?? 'nenhum'),
            'preco_produto_mais_barato: ' . ($analysis->preco_produto_mais_barato ?? 'nenhum'),
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

    private function supportsPrecoProdutoMaisCaroColumn(): bool
    {
        if ($this->hasPrecoProdutoMaisCaroColumn !== null) {
            return $this->hasPrecoProdutoMaisCaroColumn;
        }

        $this->hasPrecoProdutoMaisCaroColumn = Schema::hasColumn('analyses', 'preco_produto_mais_caro');

        return $this->hasPrecoProdutoMaisCaroColumn;
    }

    private function supportsPrecoProdutoMaisBaratoColumn(): bool
    {
        if ($this->hasPrecoProdutoMaisBaratoColumn !== null) {
            return $this->hasPrecoProdutoMaisBaratoColumn;
        }

        $this->hasPrecoProdutoMaisBaratoColumn = Schema::hasColumn('analyses', 'preco_produto_mais_barato');

        return $this->hasPrecoProdutoMaisBaratoColumn;
    }

    private function supportsProdutoMaisBaratoColumn(): bool
    {
        if ($this->hasProdutoMaisBaratoColumn !== null) {
            return $this->hasProdutoMaisBaratoColumn;
        }

        $this->hasProdutoMaisBaratoColumn = Schema::hasColumn('analyses', 'produto_mais_barato');

        return $this->hasProdutoMaisBaratoColumn;
    }
}
