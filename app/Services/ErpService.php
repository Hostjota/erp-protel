<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ErpService
{
    /**
     * Cliente HTTP base com a URL e o Token do ERP.
     */
    protected function client()
    {
        return Http::baseUrl('http://garfosgarfos.ddns.net:9000')
            ->withToken(env('ERP_API_TOKEN'))
            ->timeout(10) // Timeout de 10 segundos para evitar travamentos
            ->acceptJson();
    }

    public function getArtigosComFiltro(array $filtros = [])
    {
        try {
            $response = $this->client()->post('/api/v1/artigos', $filtros);

            if ($response->successful()) {
                $json = $response->json();
                return $json['data'] ?? $json;
            }

            Log::error('Erro ao buscar artigos via POST no ERP: ' . $response->body());
            return [];
        } catch (\Exception $e) {
            Log::error('Exceção ao buscar artigos: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Pesquisa um único artigo pelo código exato na API do ERP.
     */
    public function getArtigoByCodigo(string $codigo): ?array
    {
        try {
            $codigoFormatado = urlencode(trim($codigo));
            $response = $this->client()
                ->timeout(10)
                ->get("/api/v1/artigos/{$codigoFormatado}");
            if ($response->successful()) {
                $json = $response->json();
                return $json['data'] ?? null;
            }
            Log::warning("Busca de artigo '{$codigo}' retornou status: " . $response->status());
            return null;

        } catch (\Exception $e) {
            Log::error("Erro ao buscar artigo por código ({$codigo}): " . $e->getMessage());
            return null;
        }
    }

    /**
     * Consulta a listagem geral de artigos da API do ERP (via cache local).
     */
    public function getArtigos(): array
    {
        try {
            // 1. Aumenta o tempo limite de execução do PHP para 120 segundos
            set_time_limit(120);

            // 2. Aumenta o timeout do cliente HTTP para 120 segundos (permite descarregar os ~23MB com folga)
            $response = $this->client()
                ->timeout(120)
                ->get('/api/v1/artigos');

            if ($response->successful()) {
                $json = $response->json();
                $artigos = $json['data'] ?? $json;

                if (is_array($artigos)) {
                    // Guarda em cache para as próximas leituras serem instantâneas
                    Storage::put('artigos_cache.json', json_encode($artigos));
                    return $artigos;
                }
            }

            Log::error("Erro ao buscar artigos na API do ERP: " . $response->body());

            // Fallback para a cache caso a API falhe
            if (Storage::exists('artigos_cache.json')) {
                $jsonContent = Storage::get('artigos_cache.json');
                return json_decode($jsonContent, true) ?? [];
            }

            return [];

        } catch (\Exception $e) {
            Log::error("Exceção ao comunicar com a API do ERP (getArtigos): " . $e->getMessage());

            // Fallback de segurança para a cache
            if (Storage::exists('artigos_cache.json')) {
                $jsonContent = Storage::get('artigos_cache.json');
                return json_decode($jsonContent, true) ?? [];
            }

            return [];
        }
    }

    public function getArtigosSycr()
    {
        try {
            set_time_limit(120);
            // Usamos GET para listar os artigos da API do ERP
            $response = $this->client()->get('/api/v1/artigos');

            if ($response->successful()) {
                $json = $response->json();
                return $json['data'] ?? $json;
            }

            Log::error('Erro ao buscar lista de artigos no ERP: ' . $response->body());
            return [];
        } catch (\Exception $e) {
            Log::error('Exceção ao buscar artigos: ' . $e->getMessage());
            return [];
        }
    }

    public function createArtigo(array $payload): ?array
    {
        try {
            $response = $this->client()
                ->timeout(15)
                ->post('/api/v1/artigos', $payload);

            if ($response->successful()) {
                $json = $response->json();
                return $json['data'] ?? $json['body'] ?? $json ?? [];
            }

            Log::error("Erro na API do ERP ao criar artigo. Status: " . $response->status(), [
                'body' => $response->body()
            ]);

            return null;

        } catch (\Exception $e) {
            Log::error("Exceção ao criar artigo no ERP: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Lê a lista de famílias a partir do ficheiro de cache local.
     */
    public function getFamilias(): array
    {
        try {
            if (Storage::exists('familias_cache.json')) {
                $jsonContent = Storage::get('familias_cache.json');
                return json_decode($jsonContent, true) ?? [];
            }

            return [];
        } catch (\Exception $e) {
            Log::error("Erro ao ler cache de famílias: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Consulta uma família específica pelo ID/Número na API do ERP.
     */
    public function getFamiliaById(int $id): ?array
    {
        try {
            $response = $this->client()
                ->timeout(10)
                ->get("/api/v1/familias/{$id}");

            if ($response->successful()) {
                $json = $response->json();
                return $json['data'] ?? null;
            }

            return null;
        } catch (\Exception $e) {
            Log::error("Erro ao buscar família {$id}: " . $e->getMessage());
            return null;
        }
    }

    public function getImagemArtigo($nArtigo, $numero = 1): ?array
    {
        try {
            $response = $this->client()
                ->timeout(10)
                ->get("/api/v1/Imagens/downloadImagens", [
                    'tabela' => 0, // 0 - Artigos
                    'chave'  => $nArtigo,
                    'numero' => $numero
                ]);

            if ($response->successful() && !empty($response->body())) {
                return [
                    'body' => $response->body(),
                    'contentType' => $response->header('Content-Type') ?? 'image/jpeg'
                ];
            }
        } catch (\Exception $e) {
            Log::error("Erro ao buscar imagem do artigo {$nArtigo}: " . $e->getMessage());
        }

        return null;
    }

    /**
     * Cria um documento comercial no ERP.
     */
    public function criarDocumentoComercial(array $dados)
    {
        $response = $this->client()
            ->timeout(15)
            ->post('/api/v1/docscomerciais', $dados);

        if ($response->failed()) {
            throw new \Exception('Erro ao criar documento comercial: ' . $response->body());
        }

        return $response->json();
    }

    /**
     * Lista as encomendas a partir da cache local.
     */
    public function getEncomendas(): array
    {
        try {
            if (Storage::exists('encomendas_cache.json')) {
                $jsonContent = Storage::get('encomendas_cache.json');
                return json_decode($jsonContent, true) ?? [];
            }

            return [];
        } catch (\Exception $e) {
            Log::error("Erro ao ler cache de encomendas: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Cria uma encomenda no ERP.
     */
    public function criarEncomenda(array $dados): ?array
    {
        try {
            $response = $this->client()
                ->timeout(30)
                ->post('/api/v1/docscomerciais', $dados);

            if ($response->successful()) {
                $json = $response->json();
                return $json['data'] ?? $json;
            }

            $status = $response->status();
            $body = $response->body();

            Log::error("Erro ao criar documento comercial no ERP (Status {$status}): " . $body);

            throw new \Exception("Erro ao criar documento no ERP (Status {$status}): " . ($body ?: 'Resposta vazia do servidor'));

        } catch (\Exception $e) {
            Log::error("Exceção ao criar documento comercial no ERP: " . $e->getMessage());
            throw $e;
        }
    }

    public function getUltimasEncomendas()
    {
        // Exemplo de pedido à rota de listagem do ERP (ordenado por ID decrescente ou data)
        $response = Http::withToken($this->token)
            ->get($this->baseUrl . '/api/v1/docscomerciais', [
                'per_page' => 50, // Pede os últimos 50 documentos de uma só vez
                'orderBy' => 'id',
                'sortedBy' => 'desc'
            ]);

        if ($response->successful()) {
            return $response->json()['data'] ?? $response->json();
        }

        return [];
    }

    /**
     * Obtém as configurações por defeito para iniciar um processamento de encomendas.
     */
    public function obterConfiguracoesPorDefeito($nDocfa, $nConta)
    {
        try {
            $response = $this->client()
                ->timeout(10)
                ->get("/api/v1/encomendas/configuracoespordefeito/{$nDocfa}/{$nConta}");

            if ($response->successful()) {
                return $response->json();
            }

            return [];
        } catch (\Exception $e) {
            Log::error("Erro ao obter configurações por defeito: " . $e->getMessage());
            return [];
        }
    }

    public function getClienteByConta(string $conta): ?array
    {
        try {
            // Ajusta a rota da API do ERP para buscar por conta/id, se existir
            $response = $this->client()->timeout(15)->get("/api/v1/clientes/{$conta}");

            if ($response->failed()) {
                return null;
            }

            $json = $response->json();
            return $json['data'] ?? $json ?? null;

        } catch (\Exception $e) {
            Log::error("Erro ao buscar cliente por conta: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Procura um cliente pelo NIF na API do ERP.
     */
    public function getClienteByNif(string $nif): ?array
    {
        try {
            $response = $this->client()
                ->timeout(10)
                ->get("/api/v1/clientes", ['nif' => $nif]);

            if ($response->successful()) {
                $json = $response->json();
                $clientes = $json['data'] ?? $json ?? [];
                return collect($clientes)->firstWhere('nContribuint', $nif) ?? null;
            }

            return null;
        } catch (\Exception $e) {
            Log::error("Erro ao buscar cliente por NIF ({$nif}): " . $e->getMessage());
            return null;
        }
    }

    /**
     * Cria ou actualiza um cliente no ERP e retorna os seus dados.
     */
    public function salvarCliente(array $dadosCliente): ?array
    {
        try {
            $response = $this->client()
                ->timeout(15)
                ->post('/api/v1/clientes', $dadosCliente);

            if ($response->failed()) {
                $body = $response->body();
                Log::warning("Cliente já existe ou erro ao criar no ERP: " . $body);

                $nif = $dadosCliente['nContribuint'] ?? null;
                if ($nif && $nif !== '999999999') {
                    $clienteExistente = $this->getClienteByNif($nif);
                    if ($clienteExistente) {
                        return $clienteExistente;
                    }
                }
                return null;
            }

            $json = $response->json();
            return $json['data'] ?? $json ?? null;

        } catch (\Exception $e) {
            Log::error("Exceção ao salvar cliente: " . $e->getMessage());
            return null;
        }
    }

   /**
     * Lista os documentos comerciais por lotes (paginação) para evitar timeout.
     */
    public function listarDocumentosComerciais(): array
    {
        $todosDocumentos = [];
        $page = 1;
        $limit = 50; // Podes ajustar o tamanho do lote se necessário (ex: 100, 200 ou 500)
        $maxPages = 50; // Segurança para evitar loops infinitos

        try {
            do {
                Log::info("A descarregar lote de documentos comerciais: Página {$page}...");

                $response = $this->client()
                    ->timeout(120)
                    ->get('/api/v1/docscomerciais', [
                        'page' => $page,
                        'limit' => $limit
                    ]);

                if (!$response->successful()) {
                    Log::error("Erro ao descarregar lote na página {$page}. Status: " . $response->status());
                    break;
                }

                $json = $response->json();
                $lote = $json['list'] ?? $json['data'] ?? $json ?? [];

                // Se o lote vier vazio, terminamos a paginação
                if (empty($lote) || !is_array($lote)) {
                    break;
                }

                $todosDocumentos = array_merge($todosDocumentos, $lote);

                // Se o número de itens recebidos for menor que o limite, significa que chegámos ao fim
                if (count($lote) < $limit) {
                    break;
                }

                $page++;

            } while ($page <= $maxPages);

            return $todosDocumentos;

        } catch (\Exception $e) {
            Log::error("Exceção ao listar documentos comerciais em lotes: " . $e->getMessage());
            return $todosDocumentos; // Retorna o que conseguiu acumular até ao erro
        }
    }
    /**
     * Obtém os detalhes de um documento comercial específico pelo ID (faccbId).
     */
    public function getDocumentoComercialById(int $id): ?array
    {
        try {
            $response = $this->client()
                ->timeout(15)
                ->get("/api/v1/docscomerciais/{$id}");

            if ($response->successful()) {
                $json = $response->json();
                return $json['data'] ?? $json ?? null;
            }

            Log::error("Erro ao buscar documento comercial ID {$id}. Status: " . $response->status());
            return null;
        } catch (\Exception $e) {
            Log::error("Exceção ao buscar documento comercial ID {$id}: " . $e->getMessage());
            return null;
        }
    }

    public function getDocumentosComerciaisList()
    {
        $response = $this->client()
            ->timeout(120) // Aumenta o timeout para 2 minutos
            ->get('/api/v1/docscomerciais', [
                'limit' => 100, // Se a API aceitar limite/paginação, evitas puxar a base de dados toda de uma vez
                'orderBy' => 'id',
                'sortedBy' => 'desc'
            ]);

        if ($response->successful()) {
            $dados = $response->json();
            return $dados['list'] ?? $dados['data'] ?? $dados;
        }

        return [];
    }
}