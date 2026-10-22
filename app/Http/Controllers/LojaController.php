<?php

namespace App\Http\Controllers;

use App\Services\ErpService;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Storage;

class LojaController extends Controller
{
    protected $erpService;

    public function __construct(ErpService $erpService)
    {
        $this->erpService = $erpService;
    }

    /**
     * Catálogo / Vitrine da Loja
     */
    public function index(Request $request)
    {
        $familiaFiltro = $request->input('familia');
        $search = trim($request->input('search'));

        // 1. Lê diretamente da cache local sincronizada (evita chamadas à ERP-API)
        $todosArtigos = [];
        if (Storage::exists('artigos_cache.json')) {
            $conteudo = Storage::get('artigos_cache.json');
            $dados = json_decode($conteudo, true) ?? [];
            $todosArtigos = isset($dados['list']) ? $dados['list'] : $dados;
        }

        if (!is_array($todosArtigos)) {
            $todosArtigos = [];
        }

        // Mantém a obtenção de famílias do serviço
        $familias = $this->erpService->getFamilias();

        // 2. Filtra estritamente artigos ativos e com nSubFa == 1 provenientes da cache local
        $artigosAtivos = array_filter($todosArtigos, function ($item) {
            if (!is_array($item)) return false;

            $ativo = empty($item['inactivo']) && empty($item['artDesactivado']);
            $isSubFaPublica = isset($item['nSubFa']) && (int)$item['nSubFa'] === 1;

            return $ativo && $isSubFaPublica;
        });

        // 3. Recolhe os IDs únicos das famílias que possuem artigos ativos
        $familiasComProdutosIds = collect($artigosAtivos)->pluck('nFamilia')->unique()->filter()->toArray();

        // 4. Filtra as famílias para manter apenas as que têm produtos associados
        $familiasFiltradas = [];
        if (is_array($familias)) {
            $familiasFiltradas = array_filter($familias, function ($familia) use ($familiasComProdutosIds) {
                $familiaId = $familia['nFamilia'] ?? $familia['codigo'] ?? $familia['id'] ?? null;
                return in_array($familiaId, $familiasComProdutosIds);
            });
        }

        // 5. Paginação das Categorias/Famílias (20 por página, usando 'page_familias')
        $perPageFamilias = 20;
        $pageFamilias = $request->input('page_familias', 1);
        $familiasCollection = collect(array_values($familiasFiltradas));

        $familias = new LengthAwarePaginator(
            $familiasCollection->forPage($pageFamilias, $perPageFamilias),
            $familiasCollection->count(),
            $perPageFamilias,
            $pageFamilias,
            [
                'path' => $request->url(),
                'pageName' => 'page_familias',
                'query' => $request->query()
            ]
        );

        // 6. Aplica filtro de pesquisa ou família se selecionado nos artigos ativos
        if (!empty($search)) {
            $artigosAtivos = array_filter($artigosAtivos, function ($item) use ($search) {
                return stripos($item['nome'] ?? '', $search) !== false ||
                       stripos($item['nArtigo'] ?? '', $search) !== false;
            });
        }

        if (!empty($familiaFiltro)) {
            $artigosAtivos = array_filter($artigosAtivos, function ($item) use ($familiaFiltro) {
                return ($item['nFamilia'] ?? null) == $familiaFiltro;
            });
        }

        // 7. Injeta a URL da imagem apontando para a rota proxy interna do Laravel
        $artigosComImagem = array_map(function ($item) {
            $nArtigo = $item['nArtigo'] ?? null;

            if (!empty($nArtigo)) {
                $item['imagem_url'] = route('erp.imagem', $nArtigo);
            } else {
                $item['imagem_url'] = 'https://via.placeholder.com/300x200?text=' . urlencode($item['nome'] ?? 'Sem Imagem');
            }

            return $item;
        }, array_values($artigosAtivos));

        // 8. Paginação de 9 produtos por página
        $perPage = 9;
        $page = $request->input('page', 1);
        $collection = collect($artigosComImagem);

        $produtos = new LengthAwarePaginator(
            $collection->forPage($page, $perPage),
            $collection->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view('loja.index', compact('produtos', 'familias'));
    }

    /**
     * Detalhe do Produto
     */
public function show($nArtigo, \App\Services\ErpService $erpService)
{
    $response = Http::baseUrl('http://garfosgarfos.ddns.net:9000')
        ->withToken(env('ERP_API_TOKEN'))
        ->get("/api/v1/artigos/{$nArtigo}");

    if ($response->successful()) {
        $json = $response->json();

        // Extrai o array interno 'data' se ele existir, senão usa a resposta direta
        $produto = $json['data'] ?? $json;

        // --- BUSCA E CONVERSÃO DA IMAGEM DO ERP ---
        $produto['imagem_base64'] = null;
        $imagemData = $erpService->getImagemArtigo($nArtigo, 1);

        if ($imagemData) {
            $base64 = base64_encode($imagemData['body']);
            $contentType = $imagemData['contentType'];
            $produto['imagem_base64'] = "data:{$contentType};base64,{$base64}";
        }
        // ------------------------------------------

        return view('loja.show', compact('produto'));
    }

    abort(404, 'Artigo não encontrado.');
}

    /**
     * Proxy para descarregar a imagem do ERP de forma segura (com Bearer Token)
     */
    public function servirImagem(string $nArtigo)
    {
        $imagemData = $this->erpService->getImagemArtigo($nArtigo, 1);

        if (!$imagemData) {
            return redirect('https://via.placeholder.com/300x200?text=Sem+Imagem');
        }

        return response($imagemData['body'], 200)
            ->header('Content-Type', $imagemData['contentType']);
    }
}