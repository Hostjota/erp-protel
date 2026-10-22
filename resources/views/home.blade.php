@extends('layouts.app')

@section('title', 'Início - Loja Online')

@section('content')
<!-- Slider / Carrossel de Destaques Comerciais -->
<div id="ecommerceCarousel" class="carousel slide shadow-sm rounded-lg mb-5 overflow-hidden" data-ride="carousel">
    <ol class="carousel-indicators">
        <li data-target="#ecommerceCarousel" data-slide-to="0" class="active"></li>
        <li data-target="#ecommerceCarousel" data-slide-to="1"></li>
        <li data-target="#ecommerceCarousel" data-slide-to="2"></li>
    </ol>
    <div class="carousel-inner">
        <!-- Slide 1 -->
        <div class="carousel-item active bg-primary text-white p-5" style="background: linear-gradient(135deg, #007bff 0%, #004085 100%); min-height: 380px;">
            <div class="container h-100 d-flex align-items-center py-4">
                <div class="row align-items-center w-100">
                    <div class="col-lg-7">
                        <span class="badge badge-light text-primary font-weight-bold px-3 py-2 mb-3 rounded-pill text-uppercase">
                            <i class="fas fa-bolt mr-1"></i> Destaque da Semana
                        </span>
                        <h1 class="display-4 font-weight-bold mb-3">Inovação e Tecnologia</h1>
                        <p class="lead mb-4 text-white-50">Encontre os melhores equipamentos e artigos sincronizados em tempo real com o nosso sistema ERP.</p>
                        <a href="{{ route('loja.index') }}" class="btn btn-light btn-lg font-weight-bold px-4 shadow-sm">
                            <i class="fas fa-shopping-bag text-primary mr-2"></i> Comprar Agora
                        </a>
                    </div>
                    <div class="col-lg-5 d-none d-lg-block text-center">
                        <i class="fas fa-laptop-code text-white-50" style="font-size: 130px; opacity: 0.8;"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Slide 2 -->
        <div class="carousel-item bg-dark text-white p-5" style="background: linear-gradient(135deg, #343a40 0%, #1d2124 100%); min-height: 380px;">
            <div class="container h-100 d-flex align-items-center py-4">
                <div class="row align-items-center w-100">
                    <div class="col-lg-7">
                        <span class="badge badge-warning text-dark font-weight-bold px-3 py-2 mb-3 rounded-pill text-uppercase">
                            <i class="fas fa-star mr-1"></i> Qualidade Garantida
                        </span>
                        <h1 class="display-4 font-weight-bold mb-3">Gestão e Encomendas Rápidas</h1>
                        <p class="lead mb-4 text-white-50">Processamento de encomendas automatizado e faturas emitidas de forma rápida e segura.</p>
                        <a href="{{ route('loja.index') }}" class="btn btn-warning btn-lg font-weight-bold px-4 shadow-sm text-dark">
                            <i class="fas fa-box-open mr-2"></i> Ver Catálogo
                        </a>
                    </div>
                    <div class="col-lg-5 d-none d-lg-block text-center">
                        <i class="fas fa-boxes text-white-50" style="font-size: 130px; opacity: 0.8;"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Slide 3 -->
        <div class="carousel-item bg-success text-white p-5" style="background: linear-gradient(135deg, #28a745 0%, #1e7e34 100%); min-height: 380px;">
            <div class="container h-100 d-flex align-items-center py-4">
                <div class="row align-items-center w-100">
                    <div class="col-lg-7">
                        <span class="badge badge-light text-success font-weight-bold px-3 py-2 mb-3 rounded-pill text-uppercase">
                            <i class="fas fa-handshake mr-1"></i> Parceria de Confiança
                        </span>
                        <h1 class="display-4 font-weight-bold mb-3">Atendimento Exclusivo</h1>
                        <p class="lead mb-4 text-white-50">Estamos sempre disponíveis para ajudar a sua empresa a crescer com as melhores ferramentas.</p>
                        <a href="{{ route('sobre') }}" class="btn btn-light btn-lg font-weight-bold px-4 shadow-sm text-success">
                            <i class="fas fa-info-circle mr-2"></i> Saber Mais
                        </a>
                    </div>
                    <div class="col-lg-5 d-none d-lg-block text-center">
                        <i class="fas fa-store text-white-50" style="font-size: 130px; opacity: 0.8;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <a class="carousel-control-prev" href="#ecommerceCarousel" role="button" data-slide="prev">
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        <span class="sr-only">Anterior</span>
    </a>
    <a class="carousel-control-next" href="#ecommerceCarousel" role="button" data-slide="next">
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
        <span class="sr-only">Seguinte</span>
    </a>
</div>

<!-- Secção de Vantagens / Trust Badges -->
<div class="row text-center mb-5">
    <div class="col-md-4 mb-3 mb-md-0">
        <div class="card border-0 shadow-sm h-100 py-3">
            <div class="card-body">
                <div class="text-primary mb-3" style="font-size: 2rem;"><i class="fas fa-sync-alt"></i></div>
                <h5 class="font-weight-bold text-dark">Stock em Tempo Real</h5>
                <p class="text-muted small mb-0">Artigos e preços sempre atualizados diretamente a partir do nosso sistema de ERP.</p>
            </div>
        </div>
    </div>
    <div class="col-md-4 mb-3 mb-md-0">
        <div class="card border-0 shadow-sm h-100 py-3">
            <div class="card-body">
                <div class="text-primary mb-3" style="font-size: 2rem;"><i class="fas fa-shield-alt"></i></div>
                <h5 class="font-weight-bold text-dark">Compra Segura</h5>
                <p class="text-muted small mb-0">Processamento de encomendas fiável e emissão automática de documentos certificados.</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100 py-3">
            <div class="card-body">
                <div class="text-primary mb-3" style="font-size: 2rem;"><i class="fas fa-headset"></i></div>
                <h5 class="font-weight-bold text-dark">Suporte Dedicado</h5>
                <p class="text-muted small mb-0">Estamos sempre disponíveis para ajudar na escolha dos melhores produtos para si.</p>
            </div>
        </div>
    </div>
</div>

<!-- Secção de Produtos em Destaque -->
<div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-2">
    <h3 class="font-weight-bold text-dark m-0">Artigos em Destaque</h3>
    <a href="{{ route('loja.index') }}" class="btn btn-outline-primary btn-sm font-weight-bold">
        Ver Todos <i class="fas fa-arrow-right ml-1"></i>
    </a>
</div>

<div class="row">
    @php
        // Garante que `$destaques` é tratado em segurança e pega os primeiros 4 artigos
        $listaDestaques = is_array($destaques ?? []) ? $destaques : [];
        $destaquesLimitados = array_slice($listaDestaques, 0, 4);
    @endphp

    @forelse($destaquesLimitados as $prod)
        @php
            // Define o link da imagem de forma segura, evitando erros de chave
            $imgUrl =$prod['imagem_url'] ?? $prod['imagem'] ?? $prod['foto'] ?? null;
            if (empty($imgUrl) || !filter_var($imgUrl, FILTER_VALIDATE_URL)) {$imgUrl = 'https://via.placeholder.com/300x200?text=' . urlencode($prod['nome'] ?? $prod['descricao'] ?? 'Produto');
            }
        @endphp

        <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
            <div class="card h-100 shadow-sm border-0 product-card transition-hover" style="transition: transform 0.2s ease, box-shadow 0.2s ease;">

                <!-- Imagem do Produto -->
                <div class="text-center p-3 bg-light rounded-top d-flex align-items-center justify-content-center position-relative" style="height: 160px;">
                    <a href="{{ route('loja.show', $prod['nArtigo'] ?? 1) }}" class="w-100">
                        <img src="{{ $imgUrl }}"
                             alt="{{ $prod['nome'] ?? $prod['descricao'] ?? 'Produto' }}"
                             class="img-fluid rounded"
                             style="max-height: 140px; object-fit: contain;"
                             onerror="this.onerror=null; this.src='https://via.placeholder.com/300x200?text=Sem+Imagem';">
                    </a>
                </div>

                <div class="card-body d-flex flex-column justify-content-between text-center">
                    <div>
                        <small class="text-muted d-block mb-1">{{ $prod['nomeFamilia'] ?? 'Geral' }}</small>
                        <a href="{{ route('loja.show', $prod['nArtigo'] ?? 1) }}" class="text-decoration-none">
                            <h6 class="font-weight-bold text-dark mb-1 text-truncate" title="{{ $prod['nome'] ?? $prod['descricao'] ?? '' }}">
                                {{ $prod['nome'] ?? $prod['descricao'] ?? 'Produto' }}
                            </h6>
                        </a>
                    </div>

                    <div class="mt-3">
                        <h5 class="text-success font-weight-bold mb-3">
                            {{ number_format($prod['precoComIva'] ?? 0, 2, ',', '.') }} Kz
                        </h5>

                        <form action="{{ route('carrinho.add') }}" method="POST">
                            @csrf
                            <input type="hidden" name="codigo" value="{{ $prod['nArtigo'] ?? '' }}">
                            <input type="hidden" name="quantidade" value="1">
                            <button type="submit" class="btn btn-primary btn-sm btn-block">
                                <i class="fas fa-cart-plus mr-1"></i> Adicionar
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="col-12">
            <div class="alert alert-light border text-center py-5 shadow-sm">
                <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">Nenhum artigo disponível em destaque.</h5>
            </div>
        </div>
    @endforelse
</div>

{{-- Estilo extra para hover suave nos cards --}}
<style>
    .product-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 .5rem 1rem rgba(0,0,0,.15)!important;
    }
</style>
@endsection