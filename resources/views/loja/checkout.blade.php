@extends('layouts.app')

@section('content')
<div class="row mb-4">
    <div class="col-md-12">
        <h2 class="mb-4"><i class="fas fa-check-circle mr-2"></i>Finalizar Encomenda</h2>
    </div>

    <form action="{{ route('checkout.store') }}" method="POST" class="row w-100 m-0">
        @csrf

        <!-- Formulário de Dados do Cliente -->
        <div class="col-md-8">

            <!-- Bloco de Seleção: Tipo de Cliente -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-info text-white">
                    <h5 class="card-title m-0"><i class="fas fa-id-badge mr-2"></i>Identificação do Cliente</h5>
                </div>
                <div class="card-body">
                    <div class="form-group mb-0">
                        <label class="font-weight-bold mb-2">Já possui conta ou NIF registado no sistema?</label>
                        <div class="d-flex">
                            <div class="custom-control custom-radio mr-4" style="margin-right: 20px;">
                                <input type="radio" id="tipo_novo" name="tipo_cliente" value="novo" onclick="mudarTipoCliente('novo')" {{ old('tipo_cliente', 'novo') == 'novo' ? 'checked' : '' }}>
                                <label for="tipo_novo" style="cursor: pointer;">Não, sou um novo cliente</label>
                            </div>
                            <div class="custom-control custom-radio">
                                <input type="radio" id="tipo_existente" name="tipo_cliente" value="existente" onclick="mudarTipoCliente('existente')" {{ old('tipo_cliente') == 'existente' ? 'checked' : '' }}>
                                <label for="tipo_existente" style="cursor: pointer;">Sim, já tenho conta / NIF</label>
                            </div>
                        </div>
                    </div>

                    <!-- Input visível apenas se for cliente existente -->
                    <div class="form-group mt-3" id="bloco_nif_busca" style="display: none;">
                        <label for="nif_busca">Introduza o seu NIF ou Número de Conta *</label>
                        <input type="text" name="nif_busca" id="nif_busca" class="form-control @error('nif_busca') is-invalid @enderror" value="{{ old('nif_busca') }}" placeholder="Ex: 501352882 ou 31010024">
                        @error('nif_busca') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        <small class="form-text text-muted">Consulte no seu email o numero de conta</small>
                        <small class="form-text text-muted">Vamos procurar os seus dados automaticamente no ERP.</small>
                    </div>
                </div>
            </div>

            <!-- Formulário de Novo Cliente (Só aparece se NÃO tiver conta) -->
            <div class="card shadow-sm border-0 mb-4" id="bloco_dados_morada">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title m-0"><i class="fas fa-user mr-2"></i>Dados de Faturação e Registo</h5>
                </div>
                <div class="card-body">
                    <div class="form-group mb-3">
                        <label for="nome">Nome Completo / Empresa *</label>
                        <input type="text" name="nome" id="nome" class="form-control @error('nome') is-invalid @enderror" value="{{ old('nome') }}">
                        @error('nome') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="row">
                        <div class="form-group col-md-6 mb-3">
                            <label for="email">E-mail *</label>
                            <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}">
                            @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="form-group col-md-6 mb-3">
                            <label for="telefone">Telefone / Telemóvel *</label>
                            <input type="text" name="telefone" id="telefone" class="form-control @error('telefone') is-invalid @enderror" value="{{ old('telefone') }}">
                            @error('telefone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="form-group mb-3">
                        <label for="morada">Morada Completa *</label>
                        <textarea name="morada" id="morada" rows="2" class="form-control @error('morada') is-invalid @enderror">{{ old('morada') }}</textarea>
                        @error('morada') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="row">
                        <div class="form-group col-md-6 mb-3">
                            <label for="codigo_postal">Código Postal *</label>
                            <input type="text" name="codigo_postal" id="codigo_postal" class="form-control @error('codigo_postal') is-invalid @enderror" value="{{ old('codigo_postal', '1000') }}">
                            @error('codigo_postal') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="form-group col-md-6 mb-3">
                            <label for="localidade">Localidade / Cidade *</label>
                            <input type="text" name="localidade" id="localidade" class="form-control @error('localidade') is-invalid @enderror" value="{{ old('localidade', 'LUANDA') }}">
                            @error('localidade') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="form-group mb-3">
                        <label for="nif">NIF (Número de Identificação Fiscal)</label>
                        <input type="text" name="nif" id="nif" class="form-control @error('nif') is-invalid @enderror" value="{{ old('nif') }}" placeholder="Opcional para novos clientes">
                        @error('nif') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- Resumo da Encomenda Lateral -->
        <div class="col-md-4">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-secondary text-white">
                    <h5 class="card-title m-0">Resumo do Pedido</h5>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush mb-3">
                        @foreach($carrinho as $item)
                            <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                <div>
                                    <h6 class="my-0 text-dark" style="font-size: 14px;">{{ $item['nome'] }}</h6>
                                    <small class="text-muted">Qtd: {{ $item['quantidade'] }}</small>
                                </div>
                                <span class="text-muted">{{ number_format(($item['preco'] ?? 0) * ($item['quantidade'] ?? 0), 2, ',', '.') }} Kz</span>
                            </li>
                        @endforeach
                    </ul>

                    <div class="d-flex justify-content-between mb-2">
                        <span>Subtotal</span>
                        <strong>{{ number_format($subtotal, 2, ',', '.') }} Kz</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-3">
                        <span>Impostos (IVA)</span>
                        <span class="text-muted">Incluído</span>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between mb-4">
                        <span class="h5 font-weight-bold">Total</span>
                        <span class="h5 font-weight-bold text-success">{{ number_format($subtotal, 2, ',', '.') }} Kz</span>
                    </div>

                    <button type="submit" class="btn btn-success btn-block btn-lg w-100">
                        <i class="fas fa-paper-plane mr-1"></i> Confirmar Encomenda
                    </button>
                    <a href="{{ route('carrinho.index') }}" class="btn btn-outline-secondary btn-block mt-2 w-100">
                        Voltar ao Carrinho
                    </a>
                </div>
            </div>
        </div>
    </form>
</div>

<!-- Script direto e infalível inserido na própria view -->
<script>
    function mudarTipoCliente(tipo) {
        const blocoNifBusca = document.getElementById('bloco_nif_busca');
        const blocoMorada = document.getElementById('bloco_dados_morada');

        const inputNifBusca = document.getElementById('nif_busca');
        const inputsNovo = [
            document.getElementById('nome'),
            document.getElementById('email'),
            document.getElementById('telefone'),
            document.getElementById('morada'),
            document.getElementById('codigo_postal'),
            document.getElementById('localidade')
        ];

        if (tipo === 'existente') {
            // Esconde o formulário de cadastro e mostra a busca por NIF
            blocoNifBusca.style.display = 'block';
            blocoMorada.style.display = 'none';

            // Torna o NIF de busca obrigatório
            inputNifBusca.setAttribute('required', 'required');

            // Retira o required dos campos que ficaram ocultos
            inputsNovo.forEach(input => {
                if(input) input.removeAttribute('required');
            });
        } else {
            // Mostra o formulário de cadastro e esconde a busca por NIF
            blocoNifBusca.style.display = 'none';
            blocoMorada.style.display = 'block';

            // Retira o required da busca
            inputNifBusca.removeAttribute('required');

            // Devolve o required aos campos do cadastro
            inputsNovo.forEach(input => {
                if(input) input.setAttribute('required', 'required');
            });
        }
    }

    // Executar ao carregar a página para respeitar o estado inicial (ou erros de validação anteriores)
    document.addEventListener("DOMContentLoaded", function() {
        if (document.getElementById('tipo_existente').checked) {
            mudarTipoCliente('existente');
        } else {
            mudarTipoCliente('novo');
        }
    });
</script>
@endsection