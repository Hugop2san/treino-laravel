<?php

namespace App\Application\Sales;

use App\Models\Sale;

class CreateSaleUseCase
{
    public function __construct(
        private readonly CalculateSaleTotal $calculateSaleTotal
    ) {
    }

    /**
     * @param array{user_id:int,product_id:int,quantidade:int} $payload
     */
    public function execute(array $payload): Sale
    {
        $payload['total'] = $this->calculateSaleTotal->execute(
            (int) $payload['product_id'],
            (int) $payload['quantidade']
        );

        return Sale::query()->create($payload);
    }
}
