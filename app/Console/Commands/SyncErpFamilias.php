<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class SyncErpFamilias extends Command
{
    protected $signature = 'erp:sync-familias';
    protected $description = 'Sincroniza a lista de famílias do ERP e armazena localmente.';

    public function handle()
    {
        $this->info('A iniciar a sincronização das famílias do ERP...');

        set_time_limit(0);

        try {
            $response = Http::baseUrl('http://garfosgarfos.ddns.net:9000')
                ->withToken(env('ERP_API_TOKEN'))
                ->timeout(120)
                ->get('/api/v1/familias');

            if ($response->successful()) {
                $json = $response->json();

                // Extrai o array de famílias (suporta 'list' ou 'data')
                $familias = $json['list'] ?? $json['data'] ?? [];

                Storage::put('familias_cache.json', json_encode($familias));

                $this->info('Famílias sincronizadas com sucesso! Total: ' . count($familias));
            } else {
                $this->error('Erro ao conectar com o ERP. Status: ' . $response->status());
            }
        } catch (\Exception $e) {
            $this->error('Exceção ao sincronizar famílias: ' . $e->getMessage());
        }
    }
}