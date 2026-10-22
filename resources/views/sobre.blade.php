@extends('layouts.app')

@section('title', 'Sobre Nós')

@section('content')
<div class="card card-outline card-primary shadow-sm border-0 p-4">
    <div class="card-body">
        <h2 class="text-primary font-weight-bold mb-4">Sobre a Nossa Empresa</h2>
        <p class="lead">Somos uma organização dedicada a fornecer soluções comerciais integradas de alta qualidade, ligando a experiência física ao ecossistema digital.</p>
        <hr class="my-4">
        <p>A nossa plataforma de e-commerce e gestão de encomendas encontra-se diretamente ligada a infraestruturas de ERP avançadas, garantindo rapidez, transparência e fiabilidade em todas as transações efetuadas.</p>
        <a href="{{ route('home') }}" class="btn btn-secondary mt-3">
            <i class="fas fa-arrow-left mr-1"></i> Voltar à Página Principal
        </a>
    </div>
</div>
@endsection