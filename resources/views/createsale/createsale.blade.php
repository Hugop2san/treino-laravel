<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Vendas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1>CRUD de Vendas</h1>
        <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">Voltar ao Dashboard</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if($users->isEmpty() || $products->isEmpty())
        <div class="alert alert-warning">Cadastre ao menos 1 usuario e 1 produto antes de criar vendas.</div>
    @endif

    @php $editing = isset($sale); @endphp

    <div class="card mb-4">
        <div class="card-body">
            <h5 class="card-title">{{ $editing ? 'Editar Venda' : 'Nova Venda' }}</h5>
            <form method="POST" action="{{ $editing ? route('dashboard.venda.update', $sale) : route('dashboard.venda.store') }}">
                @csrf
                @if($editing)
                    @method('PUT')
                @endif
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Usuario</label>
                        <select name="user_id" class="form-select" required>
                            <option value="">Selecione</option>
                            @foreach($users as $userItem)
                                <option value="{{ $userItem->id }}" @selected(old('user_id', $editing ? $sale->user_id : '') == $userItem->id)>{{ $userItem->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Produto</label>
                        <select name="product_id" class="form-select" required>
                            <option value="">Selecione</option>
                            @foreach($products as $productItem)
                                <option value="{{ $productItem->id }}" @selected(old('product_id', $editing ? $sale->product_id : '') == $productItem->id)>{{ $productItem->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Quantidade</label>
                        <input type="number" name="quantidade" class="form-control" min="1" value="{{ old('quantidade', $editing ? $sale->quantidade : 1) }}" required>
                    </div>
                </div>
                <div class="mt-3 d-flex gap-2">
                    <button class="btn btn-primary" type="submit" @disabled($users->isEmpty() || $products->isEmpty())>{{ $editing ? 'Atualizar' : 'Salvar' }}</button>
                    @if($editing)
                        <a class="btn btn-outline-secondary" href="{{ route('dashboard.venda.index') }}">Cancelar</a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Lista de Vendas</h5>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Usuario</th>
                        <th>Produto</th>
                        <th>Quantidade</th>
                        <th>Total</th>
                        <th>Acoes</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($sales as $item)
                        <tr>
                            <td>{{ $item->id }}</td>
                            <td>{{ $item->user?->name }}</td>
                            <td>{{ $item->product?->name }}</td>
                            <td>{{ $item->quantidade }}</td>
                            <td>R$ {{ number_format($item->total, 2, ',', '.') }}</td>
                            <td class="d-flex gap-2">
                                <a class="btn btn-sm btn-warning" href="{{ route('dashboard.venda.edit', $item) }}">Editar</a>
                                <form method="POST" action="{{ route('dashboard.venda.destroy', $item) }}" onsubmit="return confirm('Excluir venda?');">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-danger" type="submit">Excluir</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6">Nenhuma venda cadastrada.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
</body>
</html>
