<?php

namespace App\Agents\Tools;

use App\Models\Sale;

class GetSalesRankingTool
{
    public function execute(): ?Sale
    {
        return Sale::query()
            ->select('product_id')
            ->selectRaw('COUNT(*) as total_vendas')
            ->groupBy('product_id')
            ->orderByDesc('total_vendas')
            ->with('product:id,name')
            ->first();
    }
}
