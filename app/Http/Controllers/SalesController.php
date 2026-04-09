<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Sale;
use App\Models\User;
use Illuminate\Http\Request;

class SalesController extends Controller
{
    public function index()
    {
        $sales = Sale::with(['user', 'product'])->orderByDesc('id')->get();
        $users = User::orderBy('name')->get();
        $products = Product::orderBy('name')->get();

        return view('createsale.createsale', compact('sales', 'users', 'products'));
    }

    public function create()
    {
        return $this->index();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'product_id' => ['required', 'exists:products,id'],
            'quantidade' => ['required', 'integer', 'min:1'],
        ]);

        $product = Product::findOrFail($validated['product_id']);
        $validated['total'] = $validated['quantidade'] * $product->preco;

        Sale::create($validated);

        return redirect()->route('dashboard.venda.index')->with('success', 'Venda criada com sucesso.');
    }

    public function edit(Sale $venda)
    {
        $sales = Sale::with(['user', 'product'])->orderByDesc('id')->get();
        $users = User::orderBy('name')->get();
        $products = Product::orderBy('name')->get();

        return view('createsale.createsale', [
            'sales' => $sales,
            'users' => $users,
            'products' => $products,
            'sale' => $venda,
        ]);
    }

    public function update(Request $request, Sale $venda)
    {
        $validated = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'product_id' => ['required', 'exists:products,id'],
            'quantidade' => ['required', 'integer', 'min:1'],
        ]);

        $product = Product::findOrFail($validated['product_id']);
        $validated['total'] = $validated['quantidade'] * $product->preco;

        $venda->update($validated);

        return redirect()->route('dashboard.venda.index')->with('success', 'Venda atualizada com sucesso.');
    }

    public function destroy(Sale $venda)
    {
        $venda->delete();

        return redirect()->route('dashboard.venda.index')->with('success', 'Venda removida com sucesso.');
    }
}
