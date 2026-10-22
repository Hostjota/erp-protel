<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'ERP Panel') }} | @yield('title', 'Painel')</title>

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- AdminLTE 3 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">

    <!-- Navbar Topo -->
    <nav class="main-header navbar navbar-expand navbar-white navbar-light">
        <!-- Links do lado esquerdo -->
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
            </li>
            <li class="nav-item d-none d-sm-inline-block">
                <a href="{{ route('dashboard') }}" class="nav-link">Início</a>
            </li>
        </ul>

        <!-- Links do lado direito (Ícone de Perfil / Dropdown) -->
        <ul class="navbar-nav ml-auto">
            <li class="nav-item dropdown">
                @php
                    $words = explode(' ', trim(auth()->user()->name));
                    $initials = '';
                    if (count($words) >= 2) {
                        $initials = strtoupper(substr($words[0], 0, 1) . substr(end($words), 0, 1));
                    } else {
                        $initials = strtoupper(substr($words[0], 0, 2));
                    }
                @endphp

                <a class="nav-link d-flex align-items-center py-1" data-toggle="dropdown" href="#" role="button">
                    <!-- Substitui o ícone pelas iniciais em círculo -->
                    <span class="badge rounded-circle bg-secondary d-flex align-items-center justify-content-center text-white font-weight-bold shadow-sm"
                          style="width: 32px; height: 32px; font-size: 0.8rem;">
                        {{ $initials }}
                    </span>
                </a>

                <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                    <span class="dropdown-item dropdown-header font-weight-bold">{{ auth()->user()->name }}</span>
                    <div class="dropdown-divider"></div>

                    <!-- Opção Perfil -->
                    <a href="{{ route('profile.edit') }}" class="dropdown-item">
                    <i class="fas fa-user mr-2 text-muted"></i> Perfil
                </a>

                    <div class="dropdown-divider"></div>

                    <!-- Opção Logout -->
                    <form action="{{ route('logout') }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="dropdown-item text-danger">
                            <i class="fas fa-sign-out-alt mr-2"></i> Logout
                        </button>
                    </form>
                </div>
            </li>
        </ul>
    </nav>

    <!-- Sidebar Menu Lateral -->
    <aside class="main-sidebar sidebar-dark-primary elevation-4">
        <!-- Logótipo a estampar o topo -->
        <a href="{{ route('dashboard') }}" class="brand-link text-center py-3">
            <img src="{{ asset('assets/img/protel.JPG') }}"
                 alt="Protel Angola"
                 class="brand-image img-fluid mx-auto"
                 style="max-height: 70px; width: auto; float: none; margin-left: auto; margin-right: auto;">
        </a>

        <!-- Contentor principal da barra lateral estruturado para fixar o perfil no fundo -->
        <div class="sidebar d-flex flex-column justify-content-between" style="height: calc(100vh - 5rem);">

            <!-- Parte Superior: Menu de Navegação -->
            <nav class="mt-3">
                <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">

                    <!-- Dashboard (Menu Principal) -->
                    <li class="nav-item">
                        <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-tachometer-alt"></i>
                            <p>Dashboard</p>
                        </a>
                    </li>

                    <li class="nav-header">MÓDULO ERP</li>

                    <!-- Menu de Gestão do ERP -->
                    <li class="nav-item menu-open">
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="{{ route('artigos.index') }}" class="nav-link {{ request()->routeIs('artigos.*') ? 'active' : '' }}">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Gestão de Artigos</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('encomendas.index') }}" class="nav-link {{ request()->routeIs('encomendas.*') ? 'active' : '' }}">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Gestão de Encomendas</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="" class="nav-link">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Gestão de Clientes</p>
                                </a>
                            </li>
                        </ul>
                    </li>

                    <li class="nav-header">MÓDULO ADMINISTRATIVO</li>
                    <li class="nav-item">
                        <a href="{{ route('utilizadores.index') }}" class="nav-link">
                            <i class="far fa-circle nav-icon"></i>
                            <p>Gestão de Perfil</p>
                        </a>
                    </li>

                </ul>
            </nav>

            <!-- Parte Inferior da Sidebar: Iniciais, Nome e Nível de Acesso no Fundo -->
            <div class="user-panel mt-auto pb-3 pt-3 mb-2 d-flex align-items-center border-top border-secondary px-2">
                @php
                    $words = explode(' ', trim(auth()->user()->name));
                    $initials = '';
                    if (count($words) >= 2) {
                        $initials = strtoupper(substr($words[0], 0, 1) . substr(end($words), 0, 1));
                    } else {
                        $initials = strtoupper(substr($words[0], 0, 2));
                    }
                @endphp

                <!-- Círculo com as Iniciais -->
                <div class="image mr-2">
                    <span class="badge rounded-circle bg-secondary d-flex align-items-center justify-content-center text-white font-weight-bold shadow-sm"
                          style="width: 38px; height: 38px; font-size: 0.9rem;">
                        {{ $initials }}
                    </span>
                </div>

                <!-- Nome e Cargo -->
                <div class="info" style="line-height: 1.2;">
                    <a href="#" class="d-block text-white font-weight-bold" style="font-size: 0.85rem;">
                        {{ auth()->user()->name }}
                    </a>
                    <span class="text-muted" style="font-size: 0.7rem; text-transform: uppercase;">
                        Cargo: {{ auth()->user()->role }}
                    </span>
                </div>
            </div>

        </div>
    </aside>

    <!-- Conteúdo da Página -->
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">@yield('page_title', 'Dashboard')</h1>
                    </div>
                </div>
            </div>
        </div>

        <section class="content">
            <div class="container-fluid">
                @yield('content')
            </div>
        </section>
    </div>

    <!-- Rodapé -->
    <footer class="main-footer">
        <div class="float-right d-none d-sm-inline">
            Sincronizado com API ERP
        </div>
        <strong>&copy; {{ date('Y') }} Painel de Gestão ERP.</strong>
    </footer>
</div>

<!-- jQuery e Bootstrap 4 JS -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE JS -->
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>

@stack('scripts')
</body>
</html>