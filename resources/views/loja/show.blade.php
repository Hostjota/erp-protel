@extends('layouts.app')

@section('content')
<div class="container my-5">
    <div class="row">
        <!-- Imagem do Produto -->
        <div class="col-md-6 mb-4">
            <div class="card shadow-sm border-0 mb-3 text-center bg-light" style="min-height: 250px; display: flex; align-items: center; justify-content: center;">
                @if(!empty($produto['imagem_base64']))
                    <img src="{{ $produto['imagem_base64'] }}"
                        class="card-img-top img-fluid rounded"
                        alt="{{ $produto['nome'] ?? 'Produto' }}"
                        style="max-height: 300px; object-fit: contain;">
                @else
                    <div class="p-5 text-muted">
                        <i class="bi bi-image fs-1 d-block mb-2"></i>
                        <span>Sem imagem disponível</span>
                    </div>
                @endif
            </div>
        </div>

        <!-- Informações e Descrição -->
        <div class="col-md-6">
            <h5 class="font-weight-bold">{{ $produto['nome'] ?? $produto['designacao'] ?? 'Nome do Artigo' }}</h5>

            <!-- Tratamento seguro para o Código -->
            <p class="text-muted mb-0">
                <strong>Cód:</strong> {{ data_get($produto, 'nArtigo') ?? data_get($produto, 'data.nArtigo') ?? 'N/D' }}
            </p>

            <p class="text-muted mb-0">
                <strong>Stock:</strong>
                {{
                    $produto['qtdTotal']
                    ?? ($produto['data']['qtdTotal']
                    ?? collect($produto['stockArmazens'] ?? ($produto['data']['stockArmazens'] ?? []))->sum('qtd'))
                    ?? 0
                }}
            </p>

            <!-- Tratamento seguro para o Preço -->
            <h5 class="text-primary font-weight-bold mb-4">
                {{ number_format(data_get($produto, 'precoComIva', data_get($produto, 'data.precoComIva', 0)), 2, ',', '.') }} Kz
            </h5>

            <hr>

            <h5 class="font-weight-bold mb-3">Descrição do Produto</h5>
            <p class="text-muted">
                {{ data_get($produto, 'descricaoClasse') ?? data_get($produto, 'data.descricaoClasse') ?? 'Nenhuma descrição detalhada disponível para este artigo neste momento.' }}
            </p>

            <!-- Ações (Adicionar ao Carrinho usando a rota correta: carrinho.add) -->
            <form action="{{ route('carrinho.add') }}" method="POST" class="mt-4">
                @csrf
                <!-- Enviamos o código correto do artigo -->
                <input type="hidden" name="codigo" value="{{ $produto['codigo'] ?? $produto['nArtigo'] ?? '' }}">

                <div class="form-row align-items-center">
                    <div class="col-auto mb-3">
                        <label for="quantidade" class="sr-only">Quantidade</label>
                        <input type="number"
                                name="quantidade"
                                id="qtd"
                                class="form-control"
                                value="1"
                                min="1"
                                max="{{ $produto['qtdTotal'] ?? 0 }}"
                                required>
                    </div>
                    <div class="col-auto mb-3">
                        <button type="submit" class="btn btn-success ">
                            <i class="fas fa-cart-plus mr-2"></i>Adicionar
                        </button>
                    </div>
                </div>
            </form>

            <div class="mt-3">
                <a href="{{ route('loja.index') }}" class="text-muted">
                    <i class="fas fa-arrow-left mr-1"></i> Ir para loja
                </a>
            </div>
        </div>
    </div>
</div>
@endsection