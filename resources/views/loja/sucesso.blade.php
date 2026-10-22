@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm border-0 text-center p-4">
                <div class="card-body">
                    <!-- Ícone de Sucesso -->
                    <div class="mb-4">
                        <i class="fas fa-check-circle text-success fa-4x"></i>
                    </div>

                    <h2 class="font-weight-bold text-dark mb-2">Encomenda Registada com Sucesso!</h2>
                    <p class="text-muted">Obrigado pela sua preferência. A sua encomenda foi integrada e processada com sucesso no ERP.</p>

                    <!-- Referência da Encomenda -->
                    <div class="bg-light p-3 rounded my-4 border">
                        <span class="d-block text-muted small text-uppercase">Referência da Encomenda</span>
                        <span class="h4 font-weight-bold text-primary">{{ $referencia }}</span>
                    </div>

                    <p class="text-left font-weight-bold mb-2"><i class="fas fa-university mr-2"></i> Coordenadas Bancárias para Pagamento:</p>
                    <p class="text-left text-muted small mb-3">Por favor, efectue a transferência bancária para uma das contas abaixo indicadas e envie o comprovativo para validação.</p>

                    <!-- Lista de Contas Bancárias -->
                    <div class="table-responsive text-left mb-4">
                        <table class="table table-bordered table-sm m-0">
                            <thead class="thead-light">
                                <tr>
                                    <th>Banco</th>
                                    <th>IBAN / Conta</th>
                                    <th>Titular</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($contasBancarias as $conta)
                                    <tr>
                                        <td><strong>{{ $conta['banco'] }}</strong></td>
                                        <td><code class="text-dark">{{ $conta['ibans'] }}</code></td>
                                        <td>{{ $conta['titular'] }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Botões de Ação -->
                    <div class="d-flex justify-content-center">
                        <a href="{{ route('loja.index') }}" class="btn btn-primary px-4">
                            <i class="fas fa-store mr-1"></i> Voltar à Loja
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection