<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Painel ERP | Login</title>

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- AdminLTE 3 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
</head>
<body class="hold-transition login-page d-flex flex-column justify-content-between" style="min-height: 100vh;">

<!-- Espaçador superior para centrar o login -->
<div></div>

<div class="login-box">
    <div class="login-logo">
        <a href="/">
            <img src="{{ asset('assets/img/protel.jpg') }}" alt="Logo ERP" style="max-height: 60px; width: auto;" class="mb-2">
            <br>
            <b>Painel de Gestão</b> ERP</a><br><br>
    </div>
    <!-- /.login-logo -->
    <div class="card">
        <div class="card-body login-card-body">
            <p class="login-box-msg">Inicie sessão para aceder ao painel</p>

            <!-- Mensagem de Erro do Laravel -->
            @if($errors->any())
                <div class="alert alert-danger p-2 mb-3">
                    <small>{{ $errors->first() }}</small>
                </div>
            @endif

            <!-- O action aponta para a rota de login padrão -->
            <form action="{{ route('login') }}" method="POST">
                @csrf
                <!-- Campo Email -->
                <div class="input-group mb-3">
                    <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" placeholder="E-mail" value="{{ old('email') }}" required autofocus autocomplete="username">
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fas fa-envelope"></span>
                        </div>
                    </div>
                </div>

                <!-- Campo Password -->
                <div class="input-group mb-3">
                    <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" placeholder="Palavra-passe" required autocomplete="current-password">
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fas fa-lock"></span>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <!-- Lembrar-me -->
                    <div class="col-8">
                        <div class="icheck-primary">
                            <input type="checkbox" id="remember_me" name="remember">
                            <label for="remember_me">
                                Lembrar-me
                            </label>
                        </div>
                    </div>
                    <!-- Botão Submeter -->
                    <div class="col-4">
                        <button type="submit" class="btn btn-primary btn-block">Entrar</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Rodapé Integrado -->
<footer class="w-100 text-center py-3 bg-white border-top">
    <strong>&copy; {{ date('Y') }} Loja ERP.</strong> Todos os direitos reservados.
</footer>

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- Bootstrap 4 -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE App -->
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
</body>
</html>