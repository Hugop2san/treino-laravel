<?php

namespace App\Agents\Tools;

use App\Models\Analysis;
use App\Models\Product;
use App\Models\Sale;
use Illuminate\Support\Facades\Schema;

class SaveAnalysisTool
{
    private ?bool $hasPrecoProdutoMaisCaroColumn = null;
    private ?bool $hasProdutoMaisBaratoColumn = null;
    private ?bool $hasPrecoProdutoMaisBaratoColumn = null;
    private array $columnSupport = [];

    /**
     * @param array<string, int|float|string|null> $productMetrics
     */
    public function execute(
        ?Sale $produtoMaisVendido,
        ?Product $produtoMaisCaro,
        ?Product $produtoMaisBarato,
        array $productMetrics = []
    ): Analysis
    {
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

        foreach ($productMetrics as $column => $value) {
            if ($this->supportsColumn($column)) {
                $payload[$column] = $value;
            }
        }

        $analysis->fill($payload);
        $analysis->save();

        return $analysis;
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

    private function supportsColumn(string $column): bool
    {
        if (array_key_exists($column, $this->columnSupport)) {
            return $this->columnSupport[$column];
        }

        $this->columnSupport[$column] = Schema::hasColumn('analyses', $column);

        return $this->columnSupport[$column];
    }
}
