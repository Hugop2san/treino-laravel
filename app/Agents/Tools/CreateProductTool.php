<?php

namespace App\Agents\Tools;

use App\Application\Products\CreateProductUseCase;
use App\Models\Product;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class CreateProductTool
{
    public function __construct(
        private readonly CreateProductUseCase $createProductUseCase
    ) {
    }

    /**
     * @throws ValidationException
     */
    public function execute(string $name, int $quantidade, int|float|string $preco): Product
    {
        $payload = Validator::make([
            'name' => $name,
            'quantidade' => $quantidade,
            'preco' => $preco,
        ], [
            'name' => ['required', 'string', 'max:255'],
            'quantidade' => ['required', 'integer', 'min:0'],
            'preco' => ['required', 'numeric', 'min:0'],
        ])->validate();

        return $this->createProductUseCase->execute($payload);
    }
}
