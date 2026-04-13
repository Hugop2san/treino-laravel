<?php

namespace App\Http\Controllers;

use App\Application\Products\CreateProductUseCase;
use App\Application\Products\UpdateProductUseCase;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::orderByDesc('id')->get();

        return view('createproduct.createproduct', compact('products'));
    }

    public function create()
    {
        return view('createproduct.createproduct', ['products' => Product::orderByDesc('id')->get()]);
    }

    public function store(Request $request, CreateProductUseCase $createProductUseCase)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'quantidade' => ['required', 'integer', 'min:0'],
            'preco' => ['required', 'numeric', 'min:0'],
        ]);

        $createProductUseCase->execute($validated);

        return redirect()->route('dashboard.product.index')->with('success', 'Produto criado com sucesso.');
    }

    public function edit(Product $product)
    {
        $products = Product::orderByDesc('id')->get();

        return view('createproduct.createproduct', compact('products', 'product'));
    }

    public function update(Request $request, Product $product, UpdateProductUseCase $updateProductUseCase)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'quantidade' => ['required', 'integer', 'min:0'],
            'preco' => ['required', 'numeric', 'min:0'],
        ]);

        $updateProductUseCase->execute($product, $validated);

        return redirect()->route('dashboard.product.index')->with('success', 'Produto atualizado com sucesso.');
    }

    public function destroy(Product $product)
    {
        $product->delete();

        return redirect()->route('dashboard.product.index')->with('success', 'Produto removido com sucesso.');
    }
}
