@extends('layouts.adminlte')

@section('title', 'Lista de familias')
@section('page_title', 'Familias / Categorias no ERP')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card card-outline card-primary">
            <div class="card-header">
                <h3 class="card-title">Produtos Sincronizados</h3>
                <div class="card-tools">
                    <a href="" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus mr-1"></i> Nova Familia
                    </a>
                </div>
            </div>

            {{-- Formulário de Pesquisa --}}
            <div class="card">
                <div class="card-header">
                    <form action="{{ route('familias.index') }}" method="GET" class="form-inline">
                        <div class="input-group input-group-sm">
                            <input type="text" name="search" class="form-control" placeholder="ID ou Nome da Família..." value="{{ request('search') }}">
                            <div class="input-group-append">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-search"></i> Pesquisar
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="card-body p-0">
                    <div class="table-responsive" style="overflow-x: hidden;">
                        <table class="table table-striped table-hover m-0" style="table-layout: fixed; width: 100%;">
                            <thead>
                                <tr>
                                    <th style="width: 20%;">Nº Família</th>
                                    <th style="width: 50%;">Nome da Família</th>
                                    <th style="width: 30%;" class="text-center">ID Externo</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($familias as $familia)
                                    <tr>
                                        <td>
                                            <strong>{{ $familia['nFamilia'] ?? 'N/A' }}</strong>
                                        </td>
                                        <td class="text-truncate" title="{{ $familia['nome'] ?? '' }}">
                                            {{ $familia['nome'] ?? 'N/A' }}
                                        </td>
                                        <td class="text-center text-muted">
                                            {{ !empty($familia['idExterno']) ? $familia['idExterno'] : '-' }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center py-4 text-muted">
                                            Nenhuma família encontrada.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="card-footer clearfix d-flex justify-content-between align-items-center">
                    <span class="text-muted small">
                        A mostrar {{ $familias->firstItem() ?? 0 }} a {{ $familias->lastItem() ?? 0 }} de {{ $familias->total() }} famílias
                    </span>
                    <div>
                        {{ $familias->links('pagination::bootstrap-4') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection