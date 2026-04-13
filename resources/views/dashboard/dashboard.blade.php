@extends('layouts.main')

@section('title', 'Dashboard')

@section('content')
    <h1 class="mb-3">Dashboard</h1>
    <p>Escolha um modulo para gerenciar.</p>

    -----------------------------------------------------------------------------------------

    <h1>Sugestao 1 (prompt)</h1>

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

    ----------------------------------------------------------------------------------------

    <h1>Sugestao 2 (botoes)</h1>

    <div class="container py-4">
        <div class="row g-3">
            <div class="col-12 col-md-6 col-lg-3">
                <a href="{{ url('/dashboard/user') }}" class="btn btn-primary w-100 py-3">Analises x</a>
            </div>

            <div class="col-12 col-md-6 col-lg-3">
                <a href="{{ url('/dashboard/product') }}" class="btn btn-success w-100 py-3">Analises y</a>
            </div>

            <div class="col-12 col-md-6 col-lg-3">
                <a href="{{ url('/dashboard/venda') }}" class="btn btn-warning w-100 py-3">Analises j</a>
            </div>

            <div class="col-12 col-md-6 col-lg-3">
                <a href="{{ url('/dashboard/analises') }}" class="btn btn-dark w-100 py-3">Analises w</a>
            </div>
        </div>
    </div>

    ----------------------------------------------------------------------------------------


    @if(! $analysis)
        <div class="card mb-4">
            <div class="card-body">
                <h5 class="card-title">Resumo da analise</h5>
                <p class="mb-1"><strong>Produto mais vendido:</strong> {{ $analysis->produto_mais_vendido ?? 'nenhum' }}</p>
                <p class="mb-1"><strong>Produto mais caro:</strong> {{ $analysis->produto_mais_caro ?? 'nenhum' }}</p>
                <p class="mb-0"><strong>Preco:</strong> {{ $analysis->preco_produto_mais_caro ?? 'nenhum' }}</p>
            </div>
        </div>
    @endif

    <div class="card d-none">
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
