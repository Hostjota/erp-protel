<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class DashboardController
{
    public function index()
    {
        $encomendas = [];
        if (Storage::exists('encomendas_cache.json')) {
            $encomendas = json_decode(Storage::get('encomendas_cache.json'), true) ?? [];
        }

        $hoje = Carbon::today()->format('Y-m-d');

        // 1. Métricas do Dia
        $encomendasHoje = 0;
        $totalKwanzaHoje = 0;

        // Nota: se também tiveres clientes guardados em cache ou base de dados, podes puxar aqui.
        // Vamos focar nas encomendas e faturamento diário primeiro:
        $vendasMensais = array_fill(1, 12, 0); // Array de Janeiro a Dezembro

        foreach ($encomendas as $enc) {
            $cab = $enc['detalhes']['cab'] ?? $enc['detalhes'] ?? [];
            $dataDocStr = $cab['dataDoc'] ?? $cab['data'] ?? null;

            if ($dataDocStr) {
                $dataDoc = Carbon::parse($dataDocStr);

                // Verifica se é do dia de hoje
                if ($dataDoc->format('Y-m-d') === $hoje) {
                    $encomendasHoje++;
                    $totalKwanzaHoje += (float) ($cab['totalDocumento'] ?? 0);
                }

                // Agrupa para o gráfico mensal (ano corrente)
                if ($dataDoc->year == Carbon::now()->year) {
                    $mes = $dataDoc->month;
                    $vendasMensais[$mes] += (float) ($cab['totalDocumento'] ?? 0);
                }
            }
        }

        // Exemplo para clientes hoje (podes ajustar conforme guardas os teus clientes)
        $clientesHoje = 0;

        return view('dashboard', compact(
            'encomendasHoje',
            'totalKwanzaHoje',
            'clientesHoje',
            'vendasMensais'
        ));
    }
}