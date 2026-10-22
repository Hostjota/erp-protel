@extends('layouts.adminlte')

@section('title', 'Novo Artigo')
@section('page_title', 'Cadastrar Artigo no ERP')

@section('content')
<div class="row">
    <div class="col-md-10 offset-md-1">

        <!-- Alertas de Feedback -->
        @if (session('success'))
            <div class="alert alert-success alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                <h5><i class="icon fas fa-check"></i> Sucesso!</h5>
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                <h5><i class="icon fas fa-ban"></i> Erro!</h5>
                {{ session('error') }}
            </div>
        @endif

        <!-- Card de Formulário AdminLTE -->
        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title">Dados do Artigo</h3>
            </div>

            <form method="POST" action="{{ route('artigos.store') }}">
                @csrf
                <div class="card-body">

                        <div class="form-group">
                            <label for="nome">Nome / Descrição do Artigo</label>
                            <input type="text" name="nome" class="form-control" id="nome" placeholder="Ex: Monitor Dell 24" required>
                        </div>

                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label for="nFamilia">ID da Família / Categoria</label>
                            <input type="number" name="nFamilia" class="form-control" id="nFamilia" value="1" required>
                        </div>

                        <div class="col-md-6 form-group">
                            <label for="taxaIvaVenda">Taxa de IVA (%)</label>
                            <input type="number" step="0.01" name="taxaIvaVenda" class="form-control" id="taxaIvaVenda" value="14" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label for="precoSemIva">Preço Sem IVA (Kz)</label>
                            <input type="number" step="0.01" name="precoSemIva" class="form-control" id="precoSemIva" placeholder="10000" required>
                        </div>

                        <div class="col-md-6 form-group">
                            <label for="precoComIva">Preço Com IVA (Kz)</label>
                            <input type="number" step="0.01" name="precoComIva" class="form-control" id="precoComIva" placeholder="11400" required>
                        </div>
                    </div>

                    <div class="form-check">
                        <input type="checkbox" name="movimStock" class="form-check-input" id="movimStock" value="1" checked>
                        <label class="form-check-label" for="movimStock">Artigo Físico (Movimenta Stock)</label>
                    </div>
                </div>

                <div class="card-footer text-right">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save mr-1"></i> Guardar no ERP</button>
                </div>
            </form>
        </div>

    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        function calcularIva() {
            let semIva = parseFloat($('#precoSemIva').val()) || 0;
            let taxa = parseFloat($('#taxaIvaVenda').val()) || 0;
            let comIva = semIva + (semIva * (taxa / 100));
            $('#precoComIva').val(comIva.toFixed(2));
        }

        $('#precoSemIva, #taxaIvaVenda').on('input', calcularIva);
    });
</script>
@endpush