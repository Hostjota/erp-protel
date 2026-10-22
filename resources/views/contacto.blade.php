@extends('layouts.app')

@section('content')
<div class="container my-5">
    <!-- Cabeçalho da Página -->
    <div class="row mb-5 text-center">
        <div class="col-md-12">
            <h1 class="font-weight-bold">Fale Connosco</h1>
            <p class="text-muted">Estamos sempre disponíveis para esclarecer as suas dúvidas e ouvir as suas sugestões.</p>
        </div>
    </div>

    <!-- 1ª SECÇÃO: 3 Caixas de Informação Centralizadas -->
    <div class="row mb-5">
        <!-- Caixa 1: Endereço -->
        <div class="col-md-4 mb-4">
            <div class="card h-100 shadow-sm border-0 py-4">
                <div class="card-body text-center">
                    <div class="text-primary mb-3">
                        <i class="fas fa-map-marker-alt fa-2x"></i>
                    </div>
                    <h5 class="">Endereço</h5>
                    <p class="card-text text-muted mb-1">Luanda, Angola</p>
                    <small class="text-muted">(Insira a sua morada exacta aqui)</small>
                </div>
            </div>
        </div>

        <!-- Caixa 2: Contactos -->
        <div class="col-md-4 mb-4">
            <div class="card h-100 shadow-sm border-0 py-4">
                <div class="card-body text-center">
                    <div class="text-primary mb-3">
                        <i class="fas fa-phone-alt fa-2x"></i>
                    </div>
                    <h5 class="">Contactos</h5>
                    <p class="card-text text-muted mb-1">Telemóvel: +244 900 000 000</p>
                    <p class="card-text text-muted mb-0">Email: suporte@tudodigital.com</p>
                </div>
            </div>
        </div>

        <!-- Caixa 3: Horário de Abertura -->
        <div class="col-md-4 mb-4">
            <div class="card h-100 shadow-sm border-0 py-4">
                <div class="card-body text-center">
                    <div class="text-primary mb-3">
                        <i class="fas fa-clock fa-2x"></i>
                    </div>
                    <h5 class="">Horário de Abertura</h5>
                    <p class="card-text text-muted mb-1">Segunda a Sexta: 08:00 - 18:00</p>
                    <p class="card-text text-muted mb-0">Sábado: 09:00 - 13:00</p>
                </div>
            </div>
        </div>
    </div>

    <!-- 2ª SECÇÃO: Formulário de Contactos -->
    <div class="row justify-content-center mb-5">
        <div class="col-lg-12">
            <div class="card shadow-sm border-0 p-4">
                <div class="card-body">
                <!-- Título Principal do Formulário -->
                <div class="text-center mb-4">
                    <h3 class="font-weight-bold">Envie-nos uma Mensagem</h3>
                    <p class="text-muted">Preencha o formulário abaixo para entrar em contacto connosco.</p>
                </div>

                <!-- Formulário -->
                <form action="{{ route('contacto.enviar') }}" method="POST">
                    @csrf

                    <div class="row">
                        <!-- Nome Completo -->
                        <div class="col-md-6 mb-3">
                            <label for="nome" class="form-label font-weight-bold">Nome Completo</label>
                            <input type="text" class="form-control" id="nome" name="nome" placeholder="O seu nome" required>
                        </div>

                        <!-- E-mail -->
                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label font-weight-bold">E-mail</label>
                            <input type="email" class="form-control" id="email" name="email" placeholder="exemplo@dominio.com" required>
                        </div>
                    </div>

                    <div class="row">
                        <!-- Endereço -->
                        <div class="col-md-6 mb-3">
                            <label for="endereco" class="form-label font-weight-bold">Endereço</label>
                            <input type="text" class="form-control" id="endereco" name="endereco" placeholder="Qual é o seu endereço?" required>
                        </div>


                        <!-- Assunto -->
                        <div class="col-md-6 mb-3">
                            <label for="assunto" class="form-label font-weight-bold">Assunto</label>
                            <input type="text" class="form-control" id="assunto" name="assunto" placeholder="Qual o motivo do contacto?" required>
                        </div>
                    </div>

                    <!-- Mensagem -->
                    <div class="mb-3">
                        <label for="mensagem" class="form-label font-weight-bold">Mensagem</label>
                        <textarea class="form-control" id="mensagem" name="mensagem" rows="5" placeholder="Escreva aqui a sua mensagem..." required></textarea>
                    </div>

                    <!-- Botão Enviar -->
                    <div class="text-center mt-4">
                        <button type="submit" class="btn btn-success btn-lg px-5">
                            <i class="fas fa-paper-plane mr-2"></i> Enviar Mensagem
                        </button>
                    </div>
                </form>
            </div>
            </div>
        </div>
    </div>

    <!-- 3ª SECÇÃO: MapGoogle -->
    <div class="row">
        <div class="col-md-12">
            <div class="card shadow-sm border-0 overflow-hidden">
                <div class="card-body p-0">
                    <div style="width: 100%; height: 450px;">
                        <iframe
                            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d126131.07722955506!2d13.1678248!3d-8.8383333!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x1a5f1ca7b827e879%3A0xc540cfc2e176da0!2sLuanda%2C%20Angola!5e0!3m2!1spt-PT!2sao!4v1650000000000!5m2!1spt-PT!2sao"
                            width="100%"
                            height="450"
                            style="border:0;"
                            allowfullscreen=""
                            loading="lazy"
                            referrerpolicy="no-referrer-when-downgrade">
                        </iframe>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection