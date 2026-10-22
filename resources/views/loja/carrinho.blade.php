@extends('layouts.app')

@section('content')
@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif
<div class="row">
    <div class="col-md-12 mb-3">
        <a href="{{ route('loja.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="fas fa-arrow-left"></i> Continuar a comprar
        </a>
    </div>

    <div class="col-md-8">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white font-weight-bold">
                <i class="fas fa-shopping-cart mr-1"></i> Os seus Artigos
            </div>
            <div class="card-body p-0">
                <table class="table table-hover align-middle m-0">
                    <thead class="thead-light">
                        <tr>
                            <th style="width: 70px;">Imagem</th>
                            <th>Produto</th>
                            <th class="text-center" style="width: 140px;">Qtd</th>
                            <th class="text-right">Preço Un.</th>
                            <th class="text-right">Total</th>
                            <th class="text-center" style="width: 50px;"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($carrinho as $codigo => $item)
                            <tr>
                                <!-- Imagem do Produto no Carrinho -->
                                <td class="align-middle text-center">
                                    <img src="{{ $item['imagem_url'] ?? route('erp.imagem', $item['codigo']) }}" alt="{{ $item['nome'] }}" class="img-fluid rounded" style="max-height: 80px; object-fit: contain;">
                                </td>
                                <td class="align-middle">
                                    <strong>{{ $item['nome'] }}</strong>
                                    <small class="d-block text-muted">Cód: {{ $codigo }}</small>
                                </td>
                                <td class="align-middle">
                                    <form action="{{ route('carrinho.update') }}" method="POST" class="d-flex flex-column align-items-start">
                                        @csrf
                                        <input type="hidden" name="codigo" value="{{ $codigo }}">

                                        <!-- Bloco do input e botão alinhados na mesma linha -->
                                        <div class="d-flex align-items-center">
                                            <input type="number"
                                                name="quantidade"
                                                value="{{ $item['quantidade'] }}"
                                                min="1"
                                                max="{{ $item['stock_max'] ?? 0 }}"
                                                class="form-control form-control-sm text-center"
                                                style="width: 65px;"
                                                required>

                                            <button type="submit" class="btn btn-sm btn-light border ms-1" title="Atualizar">
                                                <i class="fas fa-sync-alt"></i>
                                            </button>
                                        </div>

                                        <!-- Texto do stock alinhado logo abaixo de forma limpa -->
                                        <small class="text-muted mt-1" style="font-size: 11px;">
                                            Stock: {{ $item['stock_max'] ?? 0 }}
                                        </small>
                                    </form>
                                </td>
                                <td class="text-right align-middle">
                                    {{ number_format($item['preco'], 2, ',', '.') }} Kz
                                </td>
                                <td class="text-right align-middle font-weight-bold">
                                    {{ number_format($item['preco'] * $item['quantidade'], 2, ',', '.') }} Kz
                                </td>
                                <td class="text-center align-middle">
                                    <form action="{{ route('carrinho.remove') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="codigo" value="{{ $codigo }}">
                                        <button type="submit" class="btn btn-sm btn-outline-danger border-0">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <i class="fas fa-shopping-basket fa-2x mb-2 d-block"></i>
                                    O seu carrinho está vazio.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if(count($carrinho) > 0)
                <div class="card-footer bg-white text-right">
                    <form action="{{ route('carrinho.clear') }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-link text-danger">Esvaziar Carrinho</button>
                    </form>
                </div>
            @endif
        </div>
    </div>

    <!-- Resumo da Encomenda -->
    <div class="col-md-4">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white font-weight-bold">
                Resumo da Compra
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between mb-2">
                    <span>Subtotal (Sem IVA)</span>
                    <span>{{ number_format($subtotalSemIva ?? 0, 2, ',', '.') }} Kz</span>
                </div>
                <div class="d-flex justify-content-between mb-3">
                    <span>Impostos (IVA)</span>
                    <span>{{ number_format($valorIva ?? 0, 2, ',', '.') }} Kz</span>
                </div>
                <hr>
                <div class="d-flex justify-content-between font-weight-bold h5 text-primary">
                    <span>Total</span>
                    <span>{{ number_format($totalComIva ?? 0, 2, ',', '.') }} Kz</span>
                </div>

                <a href="{{ route('checkout.index') }}" class="btn btn-success btn-block mt-4 w-100 text-white text-decoration-none {{ count($carrinho) == 0 ? 'disabled' : '' }}">
                    <i class="fas fa-check-circle mr-1"></i> Finalizar Encomenda
                </a>
            </div>
        </div>
    </div>
</div>
@endsection