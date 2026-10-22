@extends('layouts.adminlte')

@section('title', 'Gestão de Perfil')
@section('page_title', 'Gestão de Perfil')

@section('content')
<div class="row">
    <!-- Coluna Esquerda: Resumo do Perfil e Cargo -->
    <div class="col-md-4">
        <div class="card card-primary card-outline shadow-sm">
            <div class="card-body box-profile text-center">
                @php
                    $words = explode(' ', trim(auth()->user()->name));
                    $initials = '';
                    if (count($words) >= 2) {
                        $initials = strtoupper(substr($words[0], 0, 1) . substr(end($words), 0, 1));
                    } else {
                        $initials = strtoupper(substr($words[0], 0, 2));
                    }
                @endphp

                <!-- Círculo com as Iniciais em Grande -->
                <div class="my-3">
                    <span class="badge rounded-circle bg-primary d-inline-flex align-items-center justify-content-center text-white font-weight-bold shadow"
                          style="width: 80px; height: 80px; font-size: 2rem;">
                        {{ $initials }}
                    </span>
                </div>

                <h3 class="profile-username text-dark font-weight-bold">{{ auth()->user()->name }}</h3>
                <p class="text-muted mb-1">{{ auth()->user()->email }}</p>

                <div class="badge badge-info text-uppercase px-3 py-2 mt-2" style="font-size: 0.85rem;">
                    Cargo: {{ auth()->user()->role }}
                </div>
            </div>
        </div>
    </div>

    <!-- Coluna Direita: Formulários de Atualização -->
    <div class="col-md-8">

        <!-- Cartão 1: Atualizar Informações Pessoais -->
        <div class="card card-primary card-outline shadow-sm mb-4">
            <div class="card-header bg-white">
                <h3 class="card-title text-primary font-weight-bold">
                    <i class="fas fa-user-edit mr-2"></i> Atualizar Informações de Perfil
                </h3>
            </div>

            <form method="post" action="{{ route('profile.update') }}">
                @csrf
                @method('patch')

                <div class="card-body">
                    <p class="text-muted small mb-4">Atualize as informações de perfil e o endereço de e-mail da sua conta.</p>

                    <div class="row">
                        <div class="form-group col-md-6">
                            <label for="name">Nome Completo</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-user"></i></span>
                                </div>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $user->name) }}" required autofocus autocomplete="name">
                            </div>
                            @error('name')
                                <span class="invalid-feedback d-block" role="alert"><strong>{{ $message }}</strong></span>
                            @enderror
                        </div>

                        <div class="form-group col-md-6">
                            <label for="morada">Morada</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-home"></i></span>
                                </div>
                                <input type="text" class="form-control @error('morada') is-invalid @enderror" id="morada" name="morada" value="{{ old('morada', $user->morada) }}" required autocomplete="username">
                            </div>
                            @error('morada')
                                <span class="invalid-feedback d-block" role="alert"><strong>{{ $message }}</strong></span>
                            @enderror
                        </div>
                    </div>

                    <div class="row">

                        <div class="form-group col-md-6">
                            <label for="email">Endereço de E-mail</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                </div>
                                <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $user->email) }}" required autocomplete="username">
                            </div>
                            @error('email')
                                <span class="invalid-feedback d-block" role="alert"><strong>{{ $message }}</strong></span>
                            @enderror
                        </div>

                        <div class="form-group col-md-6">
                            <label for="telefone">Telefone</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-phone"></i></span>
                                </div>
                                <input type="number" class="form-control @error('telefone') is-invalid @enderror" id="telefone" name="telefone" value="{{ old('telefone', $user->telefone) }}" required autofocus autocomplete="name">
                            </div>
                            @error('telefone')
                                <span class="invalid-feedback d-block" role="alert"><strong>{{ $message }}</strong></span>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="card-footer bg-white d-flex align-items-center">
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="fas fa-save mr-1"></i> Guardar Alterações
                    </button>

                    @if (session('status') === 'profile-updated')
                        <p class="ml-3 mb-0 text-success small font-weight-bold animate__animated animate__fadeIn">
                            <i class="fas fa-check-circle mr-1"></i> Salvo com sucesso.
                        </p>
                    @endif
                </div>
            </form>
        </div>

        <!-- Cartão 2: Atualizar Palavra-passe -->
        <div class="card card-danger card-outline shadow-sm mb-4">
            <div class="card-header bg-white">
                <h3 class="card-title text-danger font-weight-bold">
                    <i class="fas fa-lock mr-2"></i> Atualizar Palavra-passe
                </h3>
            </div>

            <form method="post" action="{{ route('password.update') }}">
                @csrf
                @method('put')

                <div class="card-body">
                    <p class="text-muted small mb-4">Certifique-se de que a sua conta está a utilizar uma palavra-passe longa e aleatória para se manter segura.</p>

                    <div class="form-group">
                        <label for="current_password">Palavra-passe Atual</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fas fa-key"></i></span>
                            </div>
                            <input type="password" class="form-control @error('current_password', 'updatePassword') is-invalid @enderror" id="current_password" name="current_password" autocomplete="current-password">
                        </div>
                        @error('current_password', 'updatePassword')
                            <span class="invalid-feedback d-block" role="alert"><strong>{{ $message }}</strong></span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="password">Nova Palavra-passe</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fas fa-lock"></i></span>
                            </div>
                            <input type="password" class="form-control @error('password', 'updatePassword') is-invalid @enderror" id="password" name="password" autocomplete="new-password">
                        </div>
                        @error('password', 'updatePassword')
                            <span class="invalid-feedback d-block" role="alert"><strong>{{ $message }}</strong></span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="password_confirmation">Confirmar Nova Palavra-passe</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fas fa-lock"></i></span>
                            </div>
                            <input type="password" class="form-control @error('password_confirmation', 'updatePassword') is-invalid @enderror" id="password_confirmation" name="password_confirmation" autocomplete="new-password">
                        </div>
                        @error('password_confirmation', 'updatePassword')
                            <span class="invalid-feedback d-block" role="alert"><strong>{{ $message }}</strong></span>
                        @enderror
                    </div>
                </div>

                <div class="card-footer bg-white d-flex align-items-center">
                    <button type="submit" class="btn btn-danger px-4">
                        <i class="fas fa-shield-alt mr-1"></i> Atualizar Password
                    </button>

                    @if (session('status') === 'password-updated')
                        <p class="ml-3 mb-0 text-success small font-weight-bold animate__animated animate__fadeIn">
                            <i class="fas fa-check-circle mr-1"></i> Palavra-passe alterada com sucesso.
                        </p>
                    @endif
                </div>
            </form>
        </div>

    </div>
</div>
@endsection