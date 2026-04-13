<?php

namespace App\Http\Controllers;

use App\Application\Sales\CreateSaleUseCase;
use App\Application\Sales\UpdateSaleUseCase;
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

    public function store(Request $request, CreateSaleUseCase $createSaleUseCase)
    {
        $validated = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'product_id' => ['required', 'exists:products,id'],
            'quantidade' => ['required', 'integer', 'min:1'],
        ]);

        $createSaleUseCase->execute($validated);

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

    public function update(Request $request, Sale $venda, UpdateSaleUseCase $updateSaleUseCase)
    {
        $validated = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'product_id' => ['required', 'exists:products,id'],
            'quantidade' => ['required', 'integer', 'min:1'],
        ]);

        $updateSaleUseCase->execute($venda, $validated);

        return redirect()->route('dashboard.venda.index')->with('success', 'Venda atualizada com sucesso.');
    }

    public function destroy(Sale $venda)
    {
        $venda->delete();

        return redirect()->route('dashboard.venda.index')->with('success', 'Venda removida com sucesso.');
    }
}
