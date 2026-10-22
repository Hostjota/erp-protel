<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">


    <title>@yield('title', 'Loja Online')</title>
    <!-- AdminLTE / Bootstrap 4 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    <!-- Favicon clássico em formato .ico -->
    <link rel="icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">

    <!-- Favicon moderno corrigindo a pasta e a extensão exata -->
    <link rel="icon" href="{{ asset('assets/img/logo-protel.JPG') }}" type="image/jpeg">

    <!-- Para dispositivos Apple -->
    <link rel="apple-touch-icon" href="{{ asset('assets/img/logo-protel.JPG') }}">

</head>
<body class="hold-transition layout-top-nav">
<div class="wrapper">

    <!-- Navbar Pública -->
    <nav class="main-header navbar navbar-expand-md navbar-light navbar-white border-bottom-0 shadow-sm">
        <div class="container">
            <a href="{{ route('home') }}" class="navbar-brand">
                <img src="{{ asset('assets/img/protel.JPG') }}"
                    alt="Protel Angola"
                    class="brand-logo">
            </a>

            <button class="navbar-toggler order-1" type="button" data-toggle="collapse" data-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse order-3" id="navbarCollapse">
                <ul class="navbar-nav mx-auto">
                    <li class="nav-item">
                        <a href="{{ route('home') }}" class="nav-link {{ request()->routeIs('home') ? 'active font-weight-bold text-primary' : '' }}">Início</a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('loja.index') }}" class="nav-link {{ request()->routeIs('loja.*') ? 'active font-weight-bold text-primary' : '' }}">Loja Online</a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('sobre') }}" class="nav-link {{ request()->routeIs('sobre') ? 'active font-weight-bold text-primary' : '' }}">Sobre Nós</a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('contacto.index') }}" class="nav-link {{ request()->routeIs('contacto.index') ? 'active font-weight-bold text-primary' : '' }}">Contactos</a>
                    </li>
                </ul>
                <!-- Contador do Carrinho no Topo -->
                <div class="navbar-nav ml-auto d-flex flex-row align-items-center">
                    <a href="{{ route('login') }}" class="btn btn-outline-primary">
                        <i class="fas fa-sign-in-alt"></i>
                    </a>

                    <a href="{{ route('carrinho.index') }}" class="btn btn-outline-primary ml-2 position-relative">
                        <i class="fas fa-shopping-cart"></i>
                        @php $qtdTotal = array_sum(array_column(session('carrinho', []), 'quantidade')); @endphp
                        @if($qtdTotal > 0)
                            <span class="badge badge-danger position-absolute">{{ $qtdTotal }}</span>
                        @endif
                    </a>

                </div>
            </div>
        </div>
    </nav>

    <!-- Conteúdo Principal -->
    <div class="content-wrapper bg-light py-4">
        <div class="container">
            {{-- Mensagens de Alerta --}}
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show">
                    {{ session('success') }}
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show">
                    {{ session('error') }}
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                </div>
            @endif

            @yield('content')
        </div>
    </div>

    <footer class="main-footer text-center py-3 bg-white border-top">
        <strong>&copy; {{ date('Y') }} Loja ERP.</strong> Todos os direitos reservados.
    </footer>
</div>

<style>
.brand-logo {
    height: 55px;
    width: auto;
    object-fit: contain;
    display: block;
}
</style>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>