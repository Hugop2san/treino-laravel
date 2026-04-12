<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Redefinir senha</title>
</head>
<body>
    <h1>Redefinir senha</h1>

    @if ($errors->any())
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    @endif

    <form method="POST" action="{{ route('password.update') }}">
        @csrf
        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <label for="email">E-mail</label>
        <input id="email" name="email" type="email" value="{{ old('email', $request->email) }}" required autofocus>

        <label for="password">Nova senha</label>
        <input id="password" name="password" type="password" required>

        <label for="password_confirmation">Confirmar senha</label>
        <input id="password_confirmation" name="password_confirmation" type="password" required>

        <button type="submit">Atualizar senha</button>
    </form>
</body>
</html>
