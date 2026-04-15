<?php

namespace App\Agents\Tools;

use Illuminate\Support\Facades\Http;

class ExtractProductCreationRequestTool
{
    /**
     * @return array{name:string,quantidade:int,preco:float}|null
     */
    public function execute(string $prompt): ?array
    {
        if (! preg_match('/\b(cadastre|cadastrar|crie|criar|adicione|adicionar|insira|inserir|insert|isert)\b/i', $prompt)) {
            return null;
        }

        $payload = $this->extractWithLocalParser($prompt)
            ?? $this->extractWithModel($prompt);

        if ($payload === null) {
            return null;
        }

        return [
            'name' => $payload['name'],
            'quantidade' => $payload['quantidade'],
            'preco' => $payload['preco'],
        ];
    }

    /**
     * @return array{name:string,quantidade:int,preco:float}|null
     */
    private function extractWithLocalParser(string $prompt): ?array
    {
        $name = $this->extractName($prompt);
        $quantidade = $this->extractQuantity($prompt);
        $preco = $this->extractPrice($prompt);

        if ($name === null || $quantidade === null || $preco === null) {
            return null;
        }

        return [
            'name' => $name,
            'quantidade' => $quantidade,
            'preco' => $preco,
        ];
    }

    /**
     * @return array{name:string,quantidade:int,preco:float}|null
     */
    private function extractWithModel(string $prompt): ?array
    {
        if (! config('services.openai.api_key')) {
            return null;
        }

        try {
            $response = Http::timeout(30)
                ->withOptions([
                    'verify' => config('services.openai.verify_ssl', true),
                ])
                ->withToken(config('services.openai.api_key'))
                ->post('https://api.openai.com/v1/responses', [
                    'model' => config('services.openai.model', 'gpt-5.4'),
                    'instructions' => implode("\n", [
                        'Extraia uma chamada de ferramenta para cadastro de produto.',
                        'Responda apenas JSON valido.',
                        'Formato: {"action":"create_product","name":"...","quantidade":10,"preco":6.9}',
                        'Se o texto nao pedir cadastro de produto ou faltar nome, quantidade ou preco, responda {"action":"none"}.',
                    ]),
                    'input' => $prompt,
                ])
                ->throw()
                ->json();

            return $this->normalizePayload($this->extractOutputText($response));
        } catch (\Throwable) {
            return null;
        }
    }

    /**
     * @return array{name:string,quantidade:int,preco:float}|null
     */
    private function normalizePayload(?string $rawOutput): ?array
    {
        if ($rawOutput === null) {
            return null;
        }

        $json = trim($rawOutput);

        if (preg_match('/```(?:json)?\s*(\{.*\})\s*```/is', $json, $matches)) {
            $json = $matches[1];
        } elseif (preg_match('/(\{.*\})/s', $json, $matches)) {
            $json = $matches[1];
        }

        $payload = json_decode($json, true);

        if (! is_array($payload) || ($payload['action'] ?? null) !== 'create_product') {
            return null;
        }

        if (! isset($payload['name'], $payload['quantidade'], $payload['preco'])) {
            return null;
        }

        return [
            'name' => trim((string) $payload['name']),
            'quantidade' => (int) $payload['quantidade'],
            'preco' => (float) str_replace(',', '.', (string) $payload['preco']),
        ];
    }

    private function extractOutputText(array $response): ?string
    {
        if (isset($response['output_text'])) {
            return $response['output_text'];
        }

        foreach ($response['output'] ?? [] as $outputItem) {
            foreach ($outputItem['content'] ?? [] as $contentItem) {
                if (($contentItem['type'] ?? null) === 'output_text') {
                    return $contentItem['text'] ?? null;
                }
            }
        }

        return null;
    }

    private function extractName(string $prompt): ?string
    {
        if (preg_match('/nome\s*(?:do\s+produto)?\s*(?:de|:|=)?\s*[\'"]([^\'"]+)[\'"]/i', $prompt, $matches)) {
            return trim($matches[1]);
        }

        if (preg_match('/nome\s*(?:do\s+produto)?\s*(?:de|:|=)?\s*(.+?)(?:\s+(?:com\s+)?(?:qntt|qtd|qtde|quantidade|estoque|preco|pre.o|valor)\b|$)/i', $prompt, $matches)) {
            return trim($matches[1], " \t\n\r\0\x0B,.;:-");
        }

        if (preg_match('/produto\s+[\'"]([^\'"]+)[\'"]/i', $prompt, $matches)) {
            return trim($matches[1]);
        }

        if (preg_match('/produto\s+(.+?)(?:\s+(?:com\s+)?(?:qntt|qtd|qtde|quantidade|estoque|preco|pre.o|valor)\b|$)/i', $prompt, $matches)) {
            return trim($matches[1], " \t\n\r\0\x0B,.;:-");
        }

        if (preg_match('/(?:cadastre|cadastrar|crie|criar|adicione|adicionar|insira|inserir|insert|isert)\s+(?:um|uma|o|a)?\s*(?:produto\s+)?(?:de\s+)?(.+?)(?:\s+(?:com\s+)?(?:qntt|qtd|qtde|quantidade|estoque|preco|pre.o|valor|\d+\s*un)\b|$)/i', $prompt, $matches)) {
            return trim($matches[1], " \t\n\r\0\x0B,.;:-");
        }

        return null;
    }

    private function extractQuantity(string $prompt): ?int
    {
        if (preg_match('/(?:qntt|qtd|qtde|quantidade|estoque)\s*(?:de|:|=)?\s*(\d+)/i', $prompt, $matches)) {
            return (int) $matches[1];
        }

        if (preg_match('/\b(\d+)\s*(?:un|unidades|unidade|unds?|itens)\b/i', $prompt, $matches)) {
            return (int) $matches[1];
        }

        return null;
    }

    private function extractPrice(string $prompt): ?float
    {
        if (preg_match('/(?:preco|pre.o|valor|preco_unitario)\s*(?:unitario|de|:|=)?\s*(?:R\$\s*)?(\d+(?:[,.]\d+)?)/i', $prompt, $matches)) {
            return (float) str_replace(',', '.', $matches[1]);
        }

        if (preg_match('/\bpor\s*(?:R\$\s*)?(\d+(?:[,.]\d+)?)/i', $prompt, $matches)) {
            return (float) str_replace(',', '.', $matches[1]);
        }

        return null;
    }
}
