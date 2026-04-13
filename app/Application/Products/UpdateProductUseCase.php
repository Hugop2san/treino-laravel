<?php

namespace App\Application\Products;

use App\Models\Product;

class UpdateProductUseCase
{
    /**
     * @param array{name:string,quantidade:int,preco:numeric-string|int|float} $payload
     */
    public function execute(Product $product, array $payload): Product
    {
        $product->update($payload);

        return $product->refresh();
    }
}
