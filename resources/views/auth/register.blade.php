<!doctype html>
  <html lang="pt-BR">
  <head>
      <meta charset="utf-8">
      <title>Criar conta</title>
  </head>
  <body>
      <h1>Criar conta</h1>

      @if ($errors->any())
          <ul>
              @foreach ($errors->all() as $error)
                  <li>{{ $error }}</li>
              @endforeach
          </ul>
      @endif

      <form method="POST" action="{{ route('register.store') }}">
          @csrf

          <label>Nome</label>
          <input type="text" name="name" value="{{ old('name') }}" required>

          <label>E-mail</label>
          <input type="email" name="email" value="{{ old('email') }}" required>

          <label>Senha</label>
          <input type="password" name="password" required>

          <label>Confirmar senha</label>
          <input type="password" name="password_confirmation" required>

          <button type="submit">Criar conta</button>
      </form>
      <a href="{{ route('login') }}" > voltar </a>
  </body>
  </html>