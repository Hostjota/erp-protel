@extends('layouts.adminlte')

@section('title', 'Dashboard')
@section('page_title', 'Painel de Controlo')

@section('content')
<div class="container-fluid">

    <!-- SECÇÃO 1: CARTÕES DE MÉTRICAS DO DIA -->
    <div class="row g-4 mb-4">
        <!-- Total de Clientes -->
        <div class="col-md-4">
            <div class="card shadow-sm border-0 border-start border-primary border-4">
                <div class="card-body">
                    <div class="text-muted small text-uppercase fw-bold">Clientes Registados (Hoje)</div>
                    <div class="h3 fw-bold mt-2 text-primary">{{ $clientesHoje }}</div>
                </div>
            </div>
        </div>

        <!-- Total de Encomendas -->
        <div class="col-md-4">
            <div class="card shadow-sm border-0 border-start border-success border-4">
                <div class="card-body">
                    <div class="text-muted small text-uppercase fw-bold">Encomendas Realizadas (Hoje)</div>
                    <div class="h3 fw-bold mt-2 text-success">{{ $encomendasHoje }}</div>
                </div>
            </div>
        </div>

        <!-- Total Faturado em Kwanza -->
        <div class="col-md-4">
            <div class="card shadow-sm border-0 border-start border-warning border-4">
                <div class="card-body">
                    <div class="text-muted small text-uppercase fw-bold">Faturamento (Hoje)</div>
                    <div class="h3 fw-bold mt-2 text-dark">{{ number_format($totalKwanzaHoje, 2, ',', '.') }} Kz</div>
                </div>
            </div>
        </div>
    </div>

    <!-- SECÇÃO 2: GRÁFICO DE CRESCIMENTO MENSAL -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h5 class="card-title mb-4"><i class="fas fa-chart-line text-primary mr-2"></i> Crescimento de Vendas Mensal ({{ date('Y') }})</h5>
                    <canvas id="graficoVendas" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const ctx = document.getElementById('graficoVendas').getContext('2d');
        const vendasMensais = @json(array_values($vendasMensais));

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez'],
                datasets: [{
                    label: 'Faturamento (Kz)',
                    data: vendasMensais,
                    backgroundColor: 'rgba(0, 123, 255, 0.1)',
                    borderColor: 'rgba(0, 123, 255, 1)',
                    borderWidth: 2,
                    tension: 0.3,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return value.toLocaleString() + ' Kz';
                            }
                        }
                    }
                }
            }
        });
    });
</script>
@endpush