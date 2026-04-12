<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login</title>
</head>
<body>
    <h1>Entrar no sistema </h1>

    @if (session('status'))
        <span>{{ session('status') }}</span>
    @endif

    @if ($errors->any())
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    @endif

    <form method="POST" action="{{ route('login.store') }}">
        @csrf
        <label for="email">E-mail</label>
        <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus>

        <label for="password">Senha</label>
        <input id="password" name="password" type="password" required>

        <label for="remember">
            <input id="remember" name="remember" type="checkbox" value="1">
            Permanecer conectado
        </label>

        <button type="submit">Entrar</button>
    </form>

    <p>
        <a href="{{ route('register') }}">Registrar-se</a>
    </p>

    <p>
        <a href="{{ route('password.request') }}">Esqueci minha senha</a>
    </p>
</body>
</html>
