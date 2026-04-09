<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Produtos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1>CRUD de Produtos</h1>
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

    @php $editing = isset($product); @endphp

    <div class="card mb-4">
        <div class="card-body">
            <h5 class="card-title">{{ $editing ? 'Editar Produto' : 'Novo Produto' }}</h5>
            <form method="POST" action="{{ $editing ? route('dashboard.product.update', $product) : route('dashboard.product.store') }}">
                @csrf
                @if($editing)
                    @method('PUT')
                @endif
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Nome</label>
                        <input type="text" name="name" class="form-control" value="{{ old('name', $editing ? $product->name : '') }}" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Quantidade</label>
                        <input type="number" name="quantidade" class="form-control" value="{{ old('quantidade', $editing ? $product->quantidade : '') }}" min="0" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Preco</label>
                        <input type="number" step="0.01" name="preco" class="form-control" value="{{ old('preco', $editing ? $product->preco : '') }}" min="0" required>
                    </div>
                </div>
                <div class="mt-3 d-flex gap-2">
                    <button class="btn btn-primary" type="submit">{{ $editing ? 'Atualizar' : 'Salvar' }}</button>
                    @if($editing)
                        <a class="btn btn-outline-secondary" href="{{ route('dashboard.product.index') }}">Cancelar</a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Lista de Produtos</h5>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nome</th>
                        <th>Quantidade</th>
                        <th>Preco</th>
                        <th>Acoes</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $item)
                        <tr>
                            <td>{{ $item->id }}</td>
                            <td>{{ $item->name }}</td>
                            <td>{{ $item->quantidade }}</td>
                            <td>R$ {{ number_format($item->preco, 2, ',', '.') }}</td>
                            <td class="d-flex gap-2">
                                <a class="btn btn-sm btn-warning" href="{{ route('dashboard.product.edit', $item) }}">Editar</a>
                                <form method="POST" action="{{ route('dashboard.product.destroy', $item) }}" onsubmit="return confirm('Excluir produto?');">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-danger" type="submit">Excluir</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5">Nenhum produto cadastrado.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
</body>
</html>
