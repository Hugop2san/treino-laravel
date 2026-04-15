<?php

namespace App\Agents\Tools;

use App\Models\Product;
use Illuminate\Support\Collection;

class ListProductNamesTool
{
    /**
     * @return Collection<int, string>
     */
    public function execute(): Collection
    {
        return Product::query()
            ->orderBy('name')
            ->pluck('name');
    }
}
