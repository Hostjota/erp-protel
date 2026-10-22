<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\ErpService;
use Illuminate\Support\Facades\Http;

class TestErpConnection extends Command
{
    protected $signature = 'erp:test-connection';
    protected $description = 'Testa a ligação com a API Swagger do ERP';

    public function handle(ErpService $erpService)
    {
        $baseUrl = config('services.erp.base_url');

        $this->info("A tentar ligar à API em: {$baseUrl}...");

        if (empty($baseUrl)) {
            $this->error('A URL base da API (ERP_API_BASE_URL) não está definida no ficheiro .env!');
            return Command::FAILURE;
        }

        try {
            // Tenta fazer um pedido simples de teste
            $artigos = $erpService->getArtigos();

            if (!empty($artigos)) {
                $this->info(' Conexão estabelecida com sucesso!');
                $this->line('Dados recebidos da API:');
                $this->line(json_encode(array_slice($artigos, 0, 2), JSON_PRETTY_PRINT)); // Mostra até 2 registos
                return Command::SUCCESS;
            } else {
                $this->warn(' A API respondeu, mas não retornou dados ou ocorreu um erro de autenticação/resposta.');
                $this->comment('Verifica o log em storage/logs/laravel.log para mais detalhes.');
                return Command::FAILURE;
            }
        } catch (\Exception $e) {
            $this->error(' Falha ao ligar ao servidor da API: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}