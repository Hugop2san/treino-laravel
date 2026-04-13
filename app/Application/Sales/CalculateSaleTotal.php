<?php

namespace App\Application\Sales;

use App\Models\Product;

class CalculateSaleTotal
{
    public function execute(int $productId, int $quantity): float
    {
        $product = Product::query()->findOrFail($productId);

        return (float) ($quantity * $product->preco);
    }
}
