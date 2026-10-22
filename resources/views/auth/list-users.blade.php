@extends('layouts.adminlte')

@section('title', 'Gestão de Utilizadores')
@section('page_title', 'Lista de Utilizadores')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card card-primary card-outline shadow-sm">
            <div class="card-header">
                <div class="card-tools">
                    @php
                        $isAdmin = auth()->user()->hasRole('admin');
                    @endphp

                    <a href="{{ $isAdmin ? route('register') : '#' }}"
                    class="btn btn-primary mb-3 {{ !$isAdmin ? 'disabled' : '' }}"
                    @if(!$isAdmin) onclick="event.preventDefault(); alert('Apenas administradores podem gerir registos.');" @endif>
                        <i class="fa fa-registered"></i> Registar
                    </a>
                </div>
            </div>

            <div class="table-responsive" style="overflow-x: hidden;">
                <table class="table table-striped table-hover m-0" style="table-layout: fixed; width: 100%;">
                    <thead>
                        <tr>
                            <th style="width: 12%;">ID</th>
                            <th style="width: 20%;">Nome</th>
                            <th style="width: 16%;">E-mail</th>
                            <th style="width: 12%;">Telefone</th>
                            <th style="width: 18%;">Morada</th>
                            <th style="width: 8%;">Cargo</th>
                            <th style="width: 14%;" class="text-center">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="text-xs">
                    @forelse($users as $user)
                        <tr>
                            <td>{{ $user->id }}</td>
                            <td class="font-weight-bold">{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>{{ $user->telefone ?? 'N/A' }}</td>
                            <td>{{ $user->morada ?? 'N/A' }}</td>
                            <td>
                                <span class="badge badge-info text-uppercase px-2 py-1">
                                    {{ $user->role ?? 'user' }}
                                </span>
                            </td>
                            <td class="text-center">
                            <!-- Botão Visualizar (Todos os admins/superadmins podem ver) -->
                            <button type="button" class="btn btn-sm btn-info" data-toggle="modal" data-target="#viewUserModal-{{ $user->id }}" title="Ver Detalhes">
                                <i class="fas fa-eye"></i>
                            </button>

                            @php
                                $currentUser = auth()->user();
                                $PodeEliminar = false;

                                // Lógica de permissão visual
                                if ($currentUser->id !== $user->id) { // Ninguém apaga a si mesmo
                                    if ($currentUser->role === 'superadmin') {
                                        $PodeEliminar = true; // Superadmin pode apagar admins e users
                                    } elseif ($currentUser->role === 'admin' && $user->role === 'user') {
                                        $PodeEliminar = true; // Admin só pode apagar users comuns (nunca outros admins ou superadmins)
                                    }
                                }
                            @endphp

                            @if($PodeEliminar)
                                <!-- Botão Eliminar -->
                                <button type="button" class="btn btn-sm btn-danger" data-toggle="modal" data-target="#deleteUserModal-{{ $user->id }}" title="Eliminar">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            @endif
                        </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">Nenhum utilizador encontrado.</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            <div class="card-footer bg-white clearfix">
                <div class="float-right">
                    {{ $users->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ================= MODAIS (Fora da tabela, iterados em segurança) ================= -->
@foreach($users as $user)
    <!-- MODAL DE VISUALIZAR -->
    <div class="modal fade" id="viewUserModal-{{ $user->id }}" tabindex="-1" role="dialog" aria-labelledby="viewModalLabel-{{ $user->id }}" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content shadow">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title" id="viewModalLabel-{{ $user->id }}">
                        <i class="fas fa-user-circle mr-2"></i> Detalhes do Utilizador
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body text-left">
                    <p><strong>ID:</strong> <span class="text-muted">{{ $user->id }}</span></p>
                    <p><strong>Nome Completo:</strong> {{ $user->name }}</p>
                    <p><strong>E-mail:</strong> {{ $user->email }}</p>
                    <p><strong>Telefone:</strong> {{ $user->telefone ?? 'Não registado' }}</p>
                    <p><strong>Morada:</strong> {{ $user->morada ?? 'Não registada' }}</p>
                    <p><strong>Cargo:</strong> <span class="badge badge-info text-uppercase">{{ $user->role ?? 'user' }}</span></p>
                    <p><strong>Data de Registo:</strong> {{ $user->created_at->format('d/m/Y H:i') }}</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Fechar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL DE ELIMINAR -->
    <div class="modal fade" id="deleteUserModal-{{ $user->id }}" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel-{{ $user->id }}" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content shadow">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="deleteModalLabel-{{ $user->id }}">
                        <i class="fas fa-exclamation-triangle mr-2"></i> Confirmar Eliminação
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body text-left">
                    <p>Tens a certeza de que pretendes eliminar o utilizador <strong>{{ $user->name }}</strong>?</p>
                    <p class="text-danger small mb-0"><i class="fas fa-info-circle"></i> Esta ação é irreversível.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Cancelar</button>

                    <form action="{{ route('utilizadores.destroy', $user->id) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm">Sim, Eliminar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endforeach

@endsection