@extends('layouts.adminlte')

@section('title', 'Gestão de Utilizadores')
@section('page_title', 'Adicionar Utilizador')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card card-primary card-outline shadow-sm">
            <div class="card-header bg-white">
                <h3 class="card-title text-primary font-weight-bold">
                    <i class="fas fa-user-plus mr-2"></i> Formulário de Registo
                </h3>
            </div>

            <form method="POST" action="{{ route('register') }}">
                @csrf

                <div class="card-body">
                    <div class="row">
                        <!-- Nome Completo -->
                        <div class="col-md-6 form-group">
                            <label for="name">Nome Completo</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-user"></i></span>
                                </div>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required autofocus autocomplete="name" placeholder="Insira o nome completo">
                            </div>
                            @error('name')
                                <span class="invalid-feedback d-block" role="alert"><strong>{{ $message }}</strong></span>
                            @enderror
                        </div>

                        <!-- E-mail -->
                        <div class="col-md-6 form-group">
                            <label for="email">Endereço de E-mail</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                </div>
                                <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" required autocomplete="username" placeholder="exemplo@dominio.com">
                            </div>
                            @error('email')
                                <span class="invalid-feedback d-block" role="alert"><strong>{{ $message }}</strong></span>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <!-- Telefone -->
                        <div class="col-md-6 form-group">
                            <label for="telefone">Telefone</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-phone-alt"></i></span>
                                </div>
                                <input type="text" class="form-control @error('telefone') is-invalid @enderror" id="telefone" name="telefone" value="{{ old('telefone') }}" placeholder="Ex: +244 923 000 000">
                            </div>
                            @error('telefone')
                                <span class="invalid-feedback d-block" role="alert"><strong>{{ $message }}</strong></span>
                            @enderror
                        </div>

                        <!-- Cargo / Role -->
                        <div class="col-md-6 form-group">
                            <label for="role">Cargo</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-user-shield"></i></span>
                                </div>
                                <select class="form-control @error('role') is-invalid @enderror" id="role" name="role" required>
                                    <option value="" >Selecionar o Nível de Acesso</option>
                                    <option value="user" {{ old('role') == 'user' ? 'selected' : '' }}>User (Padrão)</option>
                                    <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                                </select>
                            </div>
                            @error('role')
                                <span class="invalid-feedback d-block" role="alert"><strong>{{ $message }}</strong></span>
                            @enderror
                        </div>
                    </div>

                    <!-- Morada -->
                    <div class="form-group">
                        <label for="morada">Morada</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fas fa-map-marker-alt"></i></span>
                            </div>
                            <input type="text" class="form-control @error('morada') is-invalid @enderror" id="morada" name="morada" value="{{ old('morada') }}" placeholder="Insira o endereço completo">
                        </div>
                        @error('morada')
                            <span class="invalid-feedback d-block" role="alert"><strong>{{ $message }}</strong></span>
                        @enderror
                    </div>

                    <hr class="my-4">

                    <div class="row">
                        <!-- Password -->
                        <div class="col-md-6 form-group">
                            <label for="password">Palavra-passe</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                </div>
                                <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" required autocomplete="new-password" placeholder="Mínimo de caracteres">
                            </div>
                            @error('password')
                                <span class="invalid-feedback d-block" role="alert"><strong>{{ $message }}</strong></span>
                            @enderror
                        </div>

                        <!-- Confirm Password -->
                        <div class="col-md-6 form-group">
                            <label for="password_confirmation">Confirmar Palavra-passe</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                </div>
                                <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required autocomplete="new-password" placeholder="Repita a palavra-passe">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-footer bg-white d-flex justify-content-between align-items-center">
                    <a href="{{ route('utilizadores.index') }}" class="btn btn-default px-4">
                        <i class="fas fa-arrow-left mr-1"></i> Voltar
                    </a>
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="fas fa-save mr-1"></i> Registar Utilizador
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection