<?php

namespace App\Agents\Tools;

use App\Models\Product;

class GetProductInventoryMetricsTool
{
    /**
     * @return array{
     *     total_produtos_cadastrados:int,
     *     quantidade_total_estoque:int,
     *     valor_total_estoque:float,
     *     produto_maior_estoque:?string,
     *     produtos_sem_estoque:int
     * }
     */
    public function execute(): array
    {
        $produtoMaiorEstoque = Product::query()
            ->orderByDesc('quantidade')
            ->orderBy('name')
            ->first();

        return [
            'total_produtos_cadastrados' => Product::query()->count(),
            'quantidade_total_estoque' => Product::query()->sum('quantidade'),
            'valor_total_estoque' => (float) Product::query()
                ->selectRaw('COALESCE(SUM(quantidade * preco), 0) as total')
                ->value('total'),
            'produto_maior_estoque' => $produtoMaiorEstoque?->name,
            'produtos_sem_estoque' => Product::query()
                ->where('quantidade', 0)
                ->count(),
        ];
    }
}
