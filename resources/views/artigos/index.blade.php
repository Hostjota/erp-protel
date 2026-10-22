@extends('layouts.adminlte')

@section('title', 'Lista de Artigos')
@section('page_title', 'Artigos / Produtos no ERP')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card card-outline card-primary">
            <div class="card-header">
                <h3 class="card-title">Produtos Sincronizados</h3>
                <div class="card-tools">
                    @php
                        // Verifica se o utilizador tem permissão de admin (ou superior)
                        $isAdmin = auth()->user()->hasRole('admin');
                    @endphp

                    <a href="{{ $isAdmin ? route('artigos.resync') : '#' }}"
                    class="btn btn-primary mb-3 {{ !$isAdmin ? 'disabled' : '' }}"
                    @if(!$isAdmin) onclick="event.preventDefault(); alert('Apenas administradores podem resincronizar os dados.');" @endif>
                        <i class="fas fa-sync-alt"></i> Resincronizar
                    </a>
                </div>
            </div>

            {{-- Formulário de Pesquisa --}}
            <div class="card">
                <div class="card-tools">
                    <form action="{{ route('artigos.index') }}" method="GET" class="form-inline">
                        <div class="input-group input-group-sm">
                            <input type="text" name="search" class="form-control" placeholder="Pesquisar por Código..." value="{{ request('search') }}">
                            <div class="input-group-append">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-search"></i> Pesquisar
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="card-body p-0">
                    {{-- Tabela sem rolagem inferior (fixed layout + colunas truncadas) --}}
                    <div class="table-responsive" style="overflow-x: hidden;">
                        <table class="table table-striped table-hover m-0" style="table-layout: fixed; width: 100%;">
                            <thead>
                                <tr>
                                    <th style="width: 12%;">Código</th>
                                    <th style="width: 30%;">Nome / Descrição</th>
                                    <th style="width: 16%;">Família</th>
                                    <th style="width: 12%;" class="text-right">Preço s/ IVA</th>
                                    <th style="width: 14%;" class="text-right">Preço c/ IVA</th>
                                    <!--th style="width: 8%;" class="text-center">Stock</th-->
                                    <th style="width: 8%;" class="text-center">Estado</th>
                                </tr>
                            </thead>
                            <tbody class="text-xs">
                                @forelse ($artigos as $artigo)
                                    <tr>
                                        {{-- 1. Código --}}
                                        <td class="text-truncate" title="{{ $artigo['nArtigo'] ?? '' }}">
                                            <strong>{{ $artigo['nArtigo'] ?? 'N/A' }}</strong>
                                        </td>

                                        {{-- 2. Nome / Descrição --}}
                                        <td class="text-truncate" title="{{ $artigo['nome'] ?? '' }}">
                                            {{ $artigo['nome'] ?? 'N/A' }}
                                        </td>

                                        {{-- 3. Família --}}
                                        <td class="text-truncate" title="{{ $artigo['nomeFamilia'] ?? '' }}">
                                            {{ $artigo['nomeFamilia'] ?? 'N/A' }}
                                        </td>

                                        {{-- 4. Preço s/ IVA --}}
                                        <td class="text-right">
                                            {{ number_format($artigo['precoSemIva'] ?? 0, 2, ',', '.') }} Kz
                                        </td>

                                        {{-- 5. Preço c/ IVA --}}
                                        <td class="text-right font-weight-bold">
                                            {{ number_format($artigo['precoComIva'] ?? 0, 2, ',', '.') }} Kz
                                        </td>

                                        {{-- 6. Stock --}}
                                        <!--td class="text-center">
                                            @php
                                                $stock = $artigo['qtdTotal'] ?? 0;
                                            @endphp

                                            @if($stock > 0)
                                                <span class="badge badge-success">{{ $stock }}</span>
                                            @else
                                                <span class="badge badge-secondary">0</span>
                                            @endif
                                        </td-->

                                        {{-- 7. Estado --}}
                                        <td class="text-center">
                                            @if(isset($artigo['nSubFa']) == 1)
                                                <span class="badge badge-success">Publica</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-4 text-muted">
                                            Nenhum artigo encontrado -.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                {{-- Rodapé com Paginação de 10 em 10 --}}
                <div class="card-footer clearfix d-flex justify-content-between align-items-center">
                    <span class="text-muted small">
                        A mostrar {{ $artigos->firstItem() ?? 0 }} a {{ $artigos->lastItem() ?? 0 }} de {{ $artigos->total() }} artigos
                    </span>
                    <div>
                        {{ $artigos->links('pagination::bootstrap-4') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection