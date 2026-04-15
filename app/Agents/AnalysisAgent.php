<?php

namespace App\Agents;

use App\Agents\Tools\GetCheapestProductTool;
use App\Agents\Tools\GetMostExpensiveProductTool;
use App\Agents\Tools\GetProductInventoryMetricsTool;
use App\Agents\Tools\GetSalesRankingTool;
use App\Agents\Tools\ListProductNamesTool;
use App\Agents\Tools\ReadReferencesTool;
use App\Agents\Tools\SaveAnalysisTool;
use App\Agents\Tools\CreateProductTool;
use App\Agents\Tools\ExtractProductCreationRequestTool;
use App\Models\Analysis;
use Illuminate\Support\Facades\Http;

class AnalysisAgent
{
    public function __construct(
        private readonly GetSalesRankingTool $getSalesRankingTool,
        private readonly GetMostExpensiveProductTool $getMostExpensiveProductTool,
        private readonly GetCheapestProductTool $getCheapestProductTool,
        private readonly GetProductInventoryMetricsTool $getProductInventoryMetricsTool,
        private readonly ReadReferencesTool $readReferencesTool,
        private readonly SaveAnalysisTool $saveAnalysisTool,
        private readonly ExtractProductCreationRequestTool $extractProductCreationRequestTool,
        private readonly CreateProductTool $createProductTool,
        private readonly ListProductNamesTool $listProductNamesTool
    ) {
    }

    public function run(string $prompt): AgentResult
    {
        $createdProduct = $this->tryCreateProduct($prompt);
        $analysis = $this->refreshAnalysis();

        if ($createdProduct !== null) {
            return new AgentResult(
                answer: 'produto criado com sucesso',
                errorMessage: null,
                analysis: $analysis
            );
        }

        if ($this->shouldListAllProducts($prompt)) {
            return new AgentResult(
                answer: $this->listProductNamesTool->execute()->implode("\n"),
                errorMessage: null,
                analysis: $analysis
            );
        }

        if (! config('services.openai.api_key')) {
            return new AgentResult(
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

            return new AgentResult(
                answer: $response['output_text'] ?? $this->extractOutputText($response),
                errorMessage: null,
                analysis: $analysis
            );
        } catch (\Throwable $exception) {
            return new AgentResult(
                answer: null,
                errorMessage: 'Falha ao consultar a OpenAI: ' . $exception->getMessage(),
                analysis: $analysis
            );
        }
    }

    private function refreshAnalysis(): Analysis
    {
        return $this->saveAnalysisTool->execute(
            produtoMaisVendido: $this->getSalesRankingTool->execute(),
            produtoMaisCaro: $this->getMostExpensiveProductTool->execute(),
            produtoMaisBarato: $this->getCheapestProductTool->execute(),
            productMetrics: $this->getProductInventoryMetricsTool->execute()
        );
    }

    private function tryCreateProduct(string $prompt): ?\App\Models\Product
    {
        $payload = $this->extractProductCreationRequestTool->execute($prompt);

        if ($payload === null) {
            return null;
        }

        return $this->createProductTool->execute(
            name: $payload['name'],
            quantidade: $payload['quantidade'],
            preco: $payload['preco']
        );
    }

    private function buildInstructions(Analysis $analysis): string
    {
        return implode("\n\n", [
            'Voce responde como um agente interno do sistema Laravel.',
            'Use as tools internas ja executadas como fonte de contexto: ranking de vendas, produtos por preco, persistencia da analise e referencias locais.',
            'Use apenas o contexto fornecido pelo banco e pelos arquivos de referencia locais.',
            'Se faltar dado, deixe isso explicito e nao invente.',
            'Contexto atual da analise:',
            'produto_mais_vendido: ' . ($analysis->produto_mais_vendido ?? 'nenhum'),
            'produto_mais_caro: ' . ($analysis->produto_mais_caro ?? 'nenhum'),
            'produto_mais_barato: ' . ($analysis->produto_mais_barato ?? 'nenhum'),
            'preco_produto_mais_caro: ' . ($analysis->preco_produto_mais_caro ?? 'nenhum'),
            'preco_produto_mais_barato: ' . ($analysis->preco_produto_mais_barato ?? 'nenhum'),
            'total_produtos_cadastrados: ' . ($analysis->total_produtos_cadastrados ?? 0),
            'quantidade_total_estoque: ' . ($analysis->quantidade_total_estoque ?? 0),
            'valor_total_estoque: ' . ($analysis->valor_total_estoque ?? 0),
            'produto_maior_estoque: ' . ($analysis->produto_maior_estoque ?? 'nenhum'),
            'produtos_sem_estoque: ' . ($analysis->produtos_sem_estoque ?? 0),
            'produtos_cadastrados:',
            $this->listProductNamesTool->execute()->implode("\n"),
            'Referencias locais:',
            $this->readReferencesTool->execute(),
        ]);
    }

    private function shouldListAllProducts(string $prompt): bool
    {
        return (bool) preg_match('/\b(todos|listar|liste|retornar|retorne|mostrar|mostre)\b.*\b(produtos|produto)\b/i', $prompt)
            || (bool) preg_match('/\b(nome|nomes)\b.*\b(produtos|produto)\b/i', $prompt);
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
}
