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

    <div class="mb-5 bg-dark"> 
        .
    </div>

    <h1>Sugestao 2 (botoes ou balões de sugestoes de analises ou tarefas)</h1>

    <div class="card mb-4" >
        <div class="card-body py-5">
            <h5 class="card-title">Sugestões de analises do Agent</h5>
            <p>Selecione uma sugestão para visualizar a análise:</p>
            <div class="row g-3">
                <div class="col-12 col-md-6 col-lg-3">
                    <button type="button" class="btn btn-primary w-100 py-3 analysis-filter-btn produto-mais-vendido-btn" data-target="card-produto-mais-vendido">Produto mais vendido</button>
                </div>

                <div class="col-12 col-md-6 col-lg-3">
                    <button type="button" class="btn btn-success w-100 py-3 analysis-filter-btn produto-mais-caro-btn" data-target="card-produto-mais-caro">Produto mais caro</button>
                </div>

                <div class="col-12 col-md-6 col-lg-3">
                    <button type="button" class="btn btn-warning w-100 py-3 analysis-filter-btn vendas-atuais-btn" data-target="card-vendas-atuais">Vendas atuais</button>
                </div>

                <div class="col-12 col-md-6 col-lg-3">
                    <button type="button" class="btn btn-dark w-100 py-3 analysis-filter-btn analises-btn" data-target="card-resumo-analise">Analises</button>
                </div>
            </div>
        </div>
    </div>
    <br>
    @if($analysis)
        <div id="card-vendas-atuais" class="card mb-4 analysis-card" style="display: none;">
            <div class="card-body">
                <h5 class="card-title">Resposta do agent</h5>
                <p class="mb-1"><strong>Total de produtos cadastrados:</strong> {{ $analysis->total_produtos_cadastrados ?? 0 }}</p>
                <p class="mb-1"><strong>Quantidade total em estoque:</strong> {{ $analysis->quantidade_total_estoque ?? 0 }}</p>
                <p class="mb-1"><strong>Valor total em estoque:</strong> {{ $analysis->valor_total_estoque ?? 0 }}</p>
            </div>
        </div>

        <div id="card-produto-mais-caro" class="card mb-4 analysis-card" style="display: none;">
            <div class="card-body">
                <h5 class="card-title">Resposta do agent</h5>
                <p class="mb-1"><strong>Produto mais caro:</strong> {{ $analysis->produto_mais_caro ?? 'nenhum' }}</p>
                <p class="mb-1"><strong>Preco mais caro:</strong> {{ $analysis->preco_produto_mais_caro ?? 'nenhum' }}</p>
            </div>
        </div>

        <div id="card-produto-mais-vendido" class="card mb-4 analysis-card" style="display: none;">
            <div class="card-body">
                <h5 class="card-title">Resposta do agent</h5>
                <p class="mb-1"><strong>Produto mais vendido:</strong> {{ $analysis->produto_mais_vendido ?? 'nenhum' }}</p>
            </div>
        </div>

        <div id="card-resumo-analise" class="card mb-4 analysis-card" style="display: none;">
            <div class="card-body">
                <h5 class="card-title">Resumo da analise</h5>
                <p class="mb-1"><strong>Produto mais vendido:</strong> {{ $analysis->produto_mais_vendido ?? 'nenhum' }}</p>
                <p class="mb-1"><strong>Produto mais caro:</strong> {{ $analysis->produto_mais_caro ?? 'nenhum' }}</p>
                <p class="mb-1"><strong>Preco mais caro:</strong> {{ $analysis->preco_produto_mais_caro ?? 'nenhum' }}</p>
                <p class="mb-1"><strong>Produto mais barato:</strong> {{ $analysis->produto_mais_barato ?? 'nenhum' }}</p>
                <p class="mb-1"><strong>Preco mais barato:</strong> {{ $analysis->preco_produto_mais_barato ?? 'nenhum' }}</p>
                <p class="mb-1"><strong>Total de produtos cadastrados:</strong> {{ $analysis->total_produtos_cadastrados ?? 0 }}</p>
                <p class="mb-1"><strong>Quantidade total em estoque:</strong> {{ $analysis->quantidade_total_estoque ?? 0 }}</p>
                <p class="mb-1"><strong>Valor total em estoque:</strong> {{ $analysis->valor_total_estoque ?? 0 }}</p>
                <p class="mb-1"><strong>Produto com maior estoque:</strong> {{ $analysis->produto_maior_estoque ?? 'nenhum' }}</p>
                <p class="mb-0"><strong>Produtos sem estoque:</strong> {{ $analysis->produtos_sem_estoque ?? 0 }}</p>
            </div>
        </div>
    @else
        <div class="alert alert-warning">
            Nenhuma analise disponivel ainda. Envie um prompt para gerar a primeira analise.
        </div>
    @endif

    <div class="mb-5 bg-dark"> 
        .
    </div>


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

    <script>
        (() => {
            const cards = Array.from(document.querySelectorAll('.analysis-card'));
            const buttons = Array.from(document.querySelectorAll('.analysis-filter-btn'));


            function showCard(target) {
                cards.forEach((card) => {
                    card.style.display = 'none';
                });

                const selectedCard = document.getElementById(target);
                if (selectedCard) {
                    selectedCard.style.display = 'block';
                }
            }

            buttons.forEach((button) => {
                button.addEventListener('click', () => {
                    showCard(button.dataset.target);
                });
            });

            // Mantem tudo oculto no carregamento inicial.
        })();
    </script>
@endsection
