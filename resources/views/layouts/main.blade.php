<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>@yield('title', 'Teste Laravel')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark ">
    <div class="container">
        <a class="navbar-brand" href="{{ route('dashboard') }}">Teste Laravel</a>
        <div class="navbar-nav ms-5 fs-6">
            <a class="nav-link" href="{{ route('dashboard.user.index') }}">Usuarios</a>
            <a class="nav-link" href="{{ route('dashboard.product.index') }}">Produtos</a>
            <a class="nav-link" href="{{ route('dashboard.venda.index') }}">Vendas</a>
        </div>
    </div>
</nav>

<main class="container mt-4">
    @yield('content')
</main>
</body>
</html>
