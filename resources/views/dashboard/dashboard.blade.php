@extends('layouts.main')

@section('title', 'Dashboard')

@section('content')
    <h1 class="mb-3">Dashboard</h1>
    <p>Escolha um modulo para gerenciar.</p>

    @if($errorMessage)
        <div class="alert alert-danger">{{ $errorMessage }}</div>
    @endif

    <div class="card mb-4">
        <div class="card-body">
            <h5 class="card-title">Pergunta para o agent</h5>
            <form method="POST" action="{{ route('dashboard.prompt.ask') }}">
                @csrf
                <div class="mb-3">
                    <label for="prompt" class="form-label">Prompt</label>
                    <textarea id="prompt" name="prompt" class="form-control" rows="5" required>{{ $prompt }}</textarea>
                </div>
                <button type="submit" class="btn btn-dark">Enviar prompt</button>
            </form>
        </div>
    </div>

    @if($answer)
        <div class="card mb-4">
            <div class="card-body">
                <h5 class="card-title">Resposta do agent</h5>
                <div style="white-space: pre-wrap;">{{ $answer }}</div>
            </div>
        </div>
    @endif

    <div class="card">
        <div class="card-body">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Usuarios</th>
                        <th>Produtos Vendidos</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                        @foreach($user->sales as $index => $sale)
                            <tr>
                                @if($index === 0)
                                    <td rowspan="{{ $user->sales->count() }}">
                                        {{ $user->name }}
                                    </td>
                                @endif

                                <td>
                                    {{ $sale->product->name }}
                                </td>
                            </tr>
                        @endforeach
                    @empty
                        <tr>
                            <td colspan="2">Nenhum usuario cadastrado.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
