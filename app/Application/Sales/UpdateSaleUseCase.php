<?php

namespace App\Application\Sales;

use App\Models\Sale;

class UpdateSaleUseCase
{
    public function __construct(
        private readonly CalculateSaleTotal $calculateSaleTotal
    ) {
    }

    /**
     * @param array{user_id:int,product_id:int,quantidade:int} $payload
     */
    public function execute(Sale $sale, array $payload): Sale
    {
        $payload['total'] = $this->calculateSaleTotal->execute(
            (int) $payload['product_id'],
            (int) $payload['quantidade']
        );

        $sale->update($payload);

        return $sale->refresh();
    }
}
