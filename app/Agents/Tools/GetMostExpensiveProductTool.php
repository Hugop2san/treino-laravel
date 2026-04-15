<?php

namespace App\Agents\Tools;

use App\Models\Product;

class GetMostExpensiveProductTool
{
    public function execute(): ?Product
    {
        return Product::query()
            ->orderByDesc('preco')
            ->first();
    }
}
