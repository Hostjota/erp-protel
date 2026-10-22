@extends('layouts.adminlte')

@section('title', 'Lista de Encomendas')
@section('page_title', 'Encomendas / Documentos Comerciais')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card card-outline card-primary">
            <div class="card-header">
                <h3 class="card-title">Encomendas Sincronizados</h3>
                <div class="card-tools">
                    @php
                        // Verifica se o utilizador tem permissão de admin (ou superior)
                        $isAdmin = auth()->user()->hasRole('admin');
                    @endphp

                    <a href="{{ $isAdmin ? route('encomendas.sincronizar') : '#' }}"
                        class="btn btn-primary mb-3 {{ !$isAdmin ? 'disabled' : '' }}"
                        @if(!$isAdmin) onclick="event.preventDefault(); alert('Apenas administradores podem resincronizar os dados.');" @endif>
                        <i class="fas fa-sync-alt"></i> Resincronizar
                    </a>
                </div>
            </div>

            {{-- Formulário de Pesquisa Corrigido --}}
            <div class="card-body border-bottom py-2">
                <form action="{{ route('encomendas.index') }}" method="GET" class="form-inline">
                    <div class="input-group input-group-sm">
                        <input type="text" name="search" class="form-control" placeholder="Pesquisar por Nº do Doc, Conta ou Nome..." value="{{ request('search') }}">
                        <div class="input-group-append">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search"></i> Pesquisar
                            </button>
                            @if(request('search'))
                                <a href="{{ route('encomendas.index') }}" class="btn btn-secondary ml-1">
                                    <i class="fas fa-times"></i> Limpar
                                </a>
                            @endif
                        </div>
                    </div>
                </form>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive" style="overflow-x: hidden;">
                    <table class="table table-striped table-hover m-0" style="table-layout: fixed; width: 100%;">
                        <thead>
                            <tr>
                                <th style="width: 18%;">Nº Documento</th>
                                <th style="width: 15%;">Data</th>
                                <th style="width: 15%;">Conta Cliente</th>
                                <th style="width: 24%;">Nome do Cliente</th>
                                <th style="width: 14%;" class="text-right">Total</th>
                                <th style="width: 14%;" class="text-center">Ações</th>
                            </tr>
                        </thead>
                        <tbody class="text-xs">
                            @forelse ($encomendas as $doc)
                                @php
                                    $cab = $doc['cab'] ?? $doc;
                                    $faccbId = $cab['faccbId'] ?? 0;
                                    $nDoc = $cab['nDoc'] ?? $cab['nNumer'] ?? 'N/A';
                                    $dataDoc = isset($cab['dataDoc']) ? \Carbon\Carbon::parse($cab['dataDoc'])->format('d/m/Y') : 'N/A';
                                    $nConta = $cab['nConta'] ?? 'N/A';
                                    $nomeCliente = $cab['nome'] ?? $cab['nomeConta'] ?? 'Desconhecido';
                                    $totalDoc = $cab['totalDocumento'] ?? 0;
                                    $moeda = $cab['abreviaturaMoeda'] ?? 'Kz';
                                @endphp
                                <tr>
                                    <td class="text-truncate" title="{{ $nDoc }}">
                                        <strong>{{ $nDoc }}</strong>
                                    </td>
                                    <td class="text-truncate">
                                        {{ $dataDoc }}
                                    </td>
                                    <td class="text-truncate" title="{{ $nConta }}">
                                        {{ $nConta }}
                                    </td>
                                    <td class="text-truncate" title="{{ $nomeCliente }}">
                                        {{ $nomeCliente }}
                                    </td>
                                    <td class="text-right font-weight-bold">
                                        {{ number_format($totalDoc, 2, ',', '.') }} {{ $moeda }}
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ route('encomendas.pdf', $faccbId) }}" class="btn btn-sm btn-info" title="Ver Detalhes / PDF" target="_blank">
                                            <i class="fas fa-file-pdf"></i> Detalhes
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">
                                        Nenhuma encomenda encontrada.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
 <div class="card-footer clearfix d-flex justify-content-between align-items-center">
    <span class="text-muted small">
        A mostrar {{ $encomendas->firstItem() ?? 0 }} a {{ $encomendas->lastItem() ?? 0 }} de {{ $encomendas->total() }} encomendas
    </span>
    <div>
        {{ $encomendas->links('pagination::bootstrap-4') }}
    </div>
</div>
    </div>
        </div>


</div>
@endsection