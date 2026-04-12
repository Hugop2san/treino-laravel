<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard</title>
</head>
<body>
    <h1>Parabens, usuario logado</h1>

    @php
        $dados = auth()->user()->only([
            'name',
            'email',
            'created_at',
            'updated_at',
        ]);
    @endphp

    <ul>
        @foreach ($dados as $campo => $valor)
            <ul><strong>{{ $campo }}:</strong> {{ $valor }}</ul>
        @endforeach
    </ul>

    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit">Sair</button>
    </form>
</body>
</html>
