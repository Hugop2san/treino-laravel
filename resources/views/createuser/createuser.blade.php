<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Usuarios</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1>CRUD de Usuarios</h1>
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

    @php $editing = isset($user); @endphp

    <div class="card mb-4">
        <div class="card-body">
            <h5 class="card-title">{{ $editing ? 'Editar Usuario' : 'Novo Usuario' }}</h5>
            <form method="POST" action="{{ $editing ? route('dashboard.user.update', $user) : route('dashboard.user.store') }}">
                @csrf
                @if($editing)
                    @method('PUT')
                @endif
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Nome</label>
                        <input type="text" name="name" class="form-control" value="{{ old('name', $editing ? $user->name : '') }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" value="{{ old('email', $editing ? $user->email : '') }}" required>
                    </div>
                </div>
                <div class="mt-3 d-flex gap-2">
                    <button class="btn btn-primary" type="submit">{{ $editing ? 'Atualizar' : 'Salvar' }}</button>
                    @if($editing)
                        <a class="btn btn-outline-secondary" href="{{ route('dashboard.user.index') }}">Cancelar</a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Lista de Usuarios</h5>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nome</th>
                        <th>Email</th>
                        <th>Acoes</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $item)
                        <tr>
                            <td>{{ $item->id }}</td>
                            <td>{{ $item->name }}</td>
                            <td>{{ $item->email }}</td>
                            <td class="d-flex gap-2">
                                <a class="btn btn-sm btn-warning" href="{{ route('dashboard.user.edit', $item) }}">Editar</a>
                                <form method="POST" action="{{ route('dashboard.user.destroy', $item) }}" onsubmit="return confirm('Excluir usuario?');">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-danger" type="submit">Excluir</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4">Nenhum usuario cadastrado.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
</body>
</html>
