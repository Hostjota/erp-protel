<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class SyncErpArtigos extends Command
{
    protected $signature = 'erp:sync-artigos';
    protected $description = 'Baixa a lista de artigos do ERP e guarda localmente.';

    public function handle()
    {
        $this->info('A iniciar a sincronização inteligente de artigos do ERP...');

        set_time_limit(0);

        try {
            $response = Http::baseUrl('http://garfosgarfos.ddns.net:9000')
                ->withToken(env('ERP_API_TOKEN'))
                ->timeout(300)
                ->get('/api/v1/artigos');

            if ($response->successful()) {
                $json = $response->json();
                $novosArtigos = $json['list'] ?? [];

                if (empty($novosArtigos)) {
                    $this->warn('A API não retornou nenhum artigo.');
                    return;
                }

                $cachePath = 'artigos_cache.json';
                $artigosIndexados = [];
                $totalAtualizados = 0;
                $totalNovos = 0;

                // Carrega cache antiga apenas para estatísticas (opcional) ou validação
                $artigosAntigos = [];
                if (Storage::exists($cachePath)) {
                    $conteudo = Storage::get($cachePath);
                    $dadosAntigos = json_decode($conteudo, true) ?? [];
                    $listaAntiga = isset($dadosAntigos['list']) ? $dadosAntigos['list'] : $dadosAntigos;

                    foreach ($listaAntiga as $artigo) {
                        if (isset($artigo['nArtigo'])) {
                            $artigosAntigos[$artigo['nArtigo']] = true;
                        }
                    }
                }

                // Processa os artigos vindos da API
                foreach ($novosArtigos as $novoArtigo) {
                    $nArtigo = $novoArtigo['nArtigo'] ?? null;
                    if (!$nArtigo) continue;

                    if (isset($artigosAntigos[$nArtigo])) {
                        $totalAtualizados++;
                    } else {
                        $totalNovos++;
                    }

                    // Indexa diretamente o artigo fresco da API
                    $artigosIndexados[$nArtigo] = $novoArtigo;
                }

                // Converte para array indexado numericamente para manter a estrutura original
                $listaFinal = array_values($artigosIndexados);

                // Guarda a nova lista limpa e atualizada
                Storage::put($cachePath, json_encode($listaFinal, JSON_UNESCAPED_UNICODE));

                $this->info("Sincronização concluída com sucesso!");
                $this->info("- Artigos novos adicionados: {$totalNovos}");
                $this->info("- Artigos atualizados: {$totalAtualizados}");
                $this->info("- Total final na cache: " . count($listaFinal));

            } else {
                $this->error('Erro na resposta da API. Status: ' . $response->status());
            }

        } catch (\Exception $e) {
            $this->error('Exceção ao sincronizar: ' . $e->getMessage());
        }
    }
}