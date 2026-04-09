<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand" href="{{ route('dashboard') }}">Teste Laravel</a>
        <div class="navbar-nav ms-auto">
            <a class="nav-link" href="{{ route('dashboard.user.index') }}">Usuarios</a>
            <a class="nav-link" href="{{ route('dashboard.product.index') }}">Produtos</a>
            <a class="nav-link" href="{{ route('dashboard.venda.index') }}">Vendas</a>
        </div>
    </div>
</nav>

<div class="container mt-4">
    <h1 class="mb-3">Dashboard</h1>
    <p>Escolha um modulo para gerenciar.</p>
    <div class="d-flex gap-2">
        <a class="btn btn-primary" href="{{ route('dashboard.user.index') }}">Ir para Usuarios</a>
        <a class="btn btn-success" href="{{ route('dashboard.product.index') }}">Ir para Produtos</a>
        <a class="btn btn-warning" href="{{ route('dashboard.venda.index') }}">Ir para Vendas</a>
    </div>
</div>
</body>
</html>
