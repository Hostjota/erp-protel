<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\ErpService;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class SyncErpEncomendas extends Command
{
    protected $signature = 'erp:sync-encomendas {--range=300}';
    protected $description = 'Sincroniza apenas as novas encomendas a partir do último ID existente na cache.';

    protected $erpService;

    public function __construct(ErpService $erpService)
    {
        parent::__construct();
        $this->erpService = $erpService;
    }

    public function handle()
    {
        $this->info("A verificar novas encomendas no ERP...");

        // 1. Carrega a cache atual existente para não perder nada
        $encomendasMapeadas = [];
        $maiorIdExistente = 210000; // Ponto de partida base caso a cache esteja vazia

        if (Storage::exists('encomendas_cache.json')) {
            $conteudo = Storage::get('encomendas_cache.json');
            $encomendasMapeadas = json_decode($conteudo, true) ?? [];

            // Descobre qual é o maior ID já gravado na cache
            if (!empty($encomendasMapeadas)) {
                $ids = array_column($encomendasMapeadas, 'id');
                $maiorIdExistente = max($ids);
            }
        }

        $this->info("Maior ID atual na cache: {$maiorIdExistente}. A procurar novos documentos...");

        // 2. Define o intervalo de busca incremental (ex: procura os próximos 300 IDs a partir do último conhecido)
        $range = (int) $this->option('range');
        $inicio = $maiorIdExistente + 1;
        $fim = $maiorIdExistente + $range;

        $novosEncontrados = 0;

        for ($id = $inicio; $id <= $fim; $id++) {
            try {
                $detalhes = $this->erpService->getDocumentoComercialById($id);

                if ($detalhes && is_array($detalhes)) {
                    $cab = $detalhes['cab'] ?? $detalhes;
                    $dataDoc = $cab['dataDoc'] ?? $cab['data'] ?? '1970-01-01';

                    // Adiciona a nova encomenda à lista existente
                    $encomendasMapeadas[] = [ // (nota: manter $encomendasMapeadas)
                        'id' => (int) $id,
                        'detalhes' => $detalhes,
                        'data_timestamp' => Carbon::parse($dataDoc)->timestamp,
                        'nDoc' => $cab['nDoc'] ?? $cab['nNumer'] ?? '',
                        'nome' => $cab['nome'] ?? $cab['nomeConta'] ?? ''
                    ];

                    $novosEncontrados++;
                    $this->line("-> Nova encomenda encontrada e adicionada: ID {$id}");
                }
            } catch (\Exception $e) {
                // Ignora IDs que não existem (404/500) para avançar rapidamente
                continue;
            }
        }

        if ($novosEncontrados === 0) {
            $this->info("Nenhuma nova encomenda encontrada. A cache já está atualizada!");
            return;
        }

        // 3. Reordena tudo do mais recente para o mais antigo
        usort($encomendasMapeadas, function ($a, $b) {
            $comparacaoDatas = $b['data_timestamp'] <=> $a['data_timestamp'];
            if ($comparacaoDatas === 0) {
                return $b['id'] <=> $a['id'];
            }
            return $comparacaoDatas;
        });

        // 4. Guarda a cache atualizada com o histórico antigo + as novas
        Storage::put('encomendas_cache.json', json_encode($encomendasMapeadas));

        $this->info("Sincronização concluída! Foram adicionadas {$novosEncontrados} novas encomendas.");
    }
}