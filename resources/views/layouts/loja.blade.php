<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nossa Loja Online</title>
    <!-- AdminLTE / Bootstrap 4 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body class="hold-transition layout-top-nav">
<div class="wrapper">

    <!-- Navbar Pública -->
    <nav class="main-header navbar navbar-expand-md navbar-light navbar-white border-bottom-0 shadow-sm">
        <div class="container">
            <a href="{{ route('loja.index') }}" class="navbar-brand">
                <span class="brand-text font-weight-bold text-primary"><i class="fas fa-shopping-bag mr-2"></i>E-Commerce</span>
            </a>

            <!-- Contador do Carrinho no Topo -->
            <div class="navbar-nav ml-auto">
                <a href="{{ route('carrinho.index') }}" class="btn btn-outline-primary position-relative">
                    <i class="fas fa-shopping-cart"></i>
                    @php $qtdTotal = array_sum(array_column(session('carrinho', []), 'quantidade')); @endphp
                    @if($qtdTotal > 0)
                        <span class="badge badge-danger badge-pill ml-1">{{ $qtdTotal }}</span>
                    @endif
                </a>
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

            @yield('content')
        </div>
    </div>

    <footer class="main-footer text-center py-3">
        <strong>&copy; {{ date('Y') }} Loja Protel ERP.</strong> Todos os direitos reservados.
    </footer>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>