<?php

namespace App\Application\Products;

use App\Models\Product;

class CreateProductUseCase
{
    /**
     * @param array{name:string,quantidade:int,preco:numeric-string|int|float} $payload
     */
    public function execute(array $payload): Product
    {
        return Product::query()->create($payload);
    }
}
