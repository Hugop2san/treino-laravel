<?php

namespace App\Agents\Tools;

use App\Models\Product;

class GetCheapestProductTool
{
    public function execute(): ?Product
    {
        return Product::query()
            ->orderBy('preco')
            ->first();
    }
}
