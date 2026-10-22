@extends('layouts.app')

@section('content')
<div class="row mb-4">
    <!-- Filtro Lateral de Famílias -->
    <div class="col-md-3">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-primary text-white">
                <h5 class="card-title m-0"><i class="fas fa-list mr-2"></i>Categorias</h5>
            </div>
            <div class="list-group list-group-flush">
                <a href="{{ route('loja.index', ['search' => request('search')]) }}" class="list-group-item list-group-item-action {{ !request('familia') ? 'active' : '' }}">
                    Todas as Famílias
                </a>
                @foreach($familias as $fam)
                    <a href="{{ route('loja.index', ['familia' => $fam['nFamilia'], 'search' => request('search')]) }}"
                       class="list-group-item list-group-item-action {{ request('familia') == $fam['nFamilia'] ? 'active' : '' }}">
                        {{ $fam['nome'] }}
                    </a>
                @endforeach
            </div>

            <!-- Paginação das Categorias -->
            @if(method_exists($familias, 'links'))
                <div class="card-footer bg-white border-0 py-2 d-flex justify-content-center">
                    {{ $familias->appends(request()->except('page_familias'))->links('pagination::bootstrap-4') }}
                </div>
            @endif
        </div>
    </div>

    <!-- Grelha de Produtos -->
    <div class="col-md-9">
        <!-- Barra de Pesquisa -->
        <div class="card card-body shadow-sm border-0 mb-4">
            <form action="{{ route('loja.index') }}" method="GET" class="row">
                @if(request('familia'))
                    <input type="hidden" name="familia" value="{{ request('familia') }}">
                @endif
                @if(request('page_familias'))
                    <input type="hidden" name="page_familias" value="{{ request('page_familias') }}">
                @endif
                <div class="col-md-10">
                    <input type="text" name="search" class="form-control" placeholder="Pesquisar produto pelo nome ou código..." value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary btn-block"><i class="fas fa-search"></i> Buscar</button>
                </div>
            </form>
        </div>

        <!-- Lista em Cards (3 colunas) -->
        <div class="row">
            @forelse($produtos as $prod)
                <div class="col-md-4 mb-4">
                    <div class="card h-100 shadow-sm border-0">
                        <!-- Imagem do Produto -->
                        <div class="text-center p-3 bg-light rounded-top d-flex align-items-center justify-content-center" style="height: 160px;">
                            <a href="{{ route('loja.show', $prod['nArtigo']) }}" class="w-100">
                                <img src="{{ $prod['imagem_url'] }}"
                                     alt="{{ $prod['nome'] ?? 'Produto' }}"
                                     class="img-fluid rounded"
                                     style="max-height: 140px; object-fit: contain;"
                                     onerror="this.onerror=null; this.src='https://via.placeholder.com/300x200?text={{ urlencode($prod['nome'] ?? 'Sem Imagem') }}';">
                            </a>
                        </div>

                        <div class="card-body d-flex flex-column justify-content-between text-center">
                            <div>
                                <small class="text-muted d-block mb-1">{{ $prod['nomeFamilia'] ?? 'Geral' }}</small>
                                <a href="{{ route('loja.show', $prod['nArtigo']) }}" class="text-decoration-none">
                                    <h6 class="font-weight-bold text-dark mb-1">{{ $prod['nome'] ?? $prod['descricao'] }}</h6>
                                </a>
                                <p class="text-muted small mb-0">Cód: {{ $prod['nArtigo'] }}</p>
                            </div>

                            <div class="mt-3">
                                <h5 class="text-success font-weight-bold mb-3">
                                    {{ number_format($prod['precoComIva'] ?? 0, 2, ',', '.') }} Kz
                                </h5>

                                <form action="{{ route('carrinho.add') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="codigo" value="{{ $prod['nArtigo'] }}">
                                    <input type="hidden" name="quantidade" value="1">
                                    <button type="submit" class="btn btn-primary btn-sm btn-block">
                                        <i class="fas fa-cart-plus"></i> Adicionar
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="alert alert-warning text-center py-4">
                        Nenhum produto encontrado nesta categoria.
                    </div>
                </div>
            @endforelse
        </div>

        <!-- Paginação dos Produtos -->
        <div class="d-flex justify-content-center mt-3">
            {{ $produtos->appends(request()->except('page'))->links('pagination::bootstrap-4') }}
        </div>
    </div>
</div>
@endsection