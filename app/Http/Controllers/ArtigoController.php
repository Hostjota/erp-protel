<?php

namespace App\Http\Controllers;

use App\Services\ErpService;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class ArtigoController extends Controller
{
    protected $erpService;

    public function __construct(ErpService $erpService)
    {
        $this->erpService = $erpService;
    }

    public function index(Request $request)
{
    $search = trim($request->input('search', '') ?? '');
    $todosArtigos = [];

    if (\Illuminate\Support\Facades\Storage::exists('artigos_cache.json')) {
        $conteudo = \Illuminate\Support\Facades\Storage::get('artigos_cache.json');
        $dados = json_decode($conteudo, true) ?? [];

        // Garante que obtemos a lista real de artigos, independentemente de como foi guardada
        $todosArtigos = isset($dados['list']) ? $dados['list'] : $dados;

        // Se por acaso os dados vierem em formato numérico estranho, filtra apenas os arrays válidos
        if (isset($todosArtigos[0]) && is_array($todosArtigos[0]) === false) {
            $todosArtigos = []; // fallback de segurança
        }
    }

    // FILTRO: Mantém apenas os artigos onde nSubFa == 1
    $todosArtigos = array_filter($todosArtigos, function($artigo) {
        if (!is_array($artigo)) return false;
        return isset($artigo['nSubFa']) && (int)$artigo['nSubFa'] === 1;
    });

    // Se houver pesquisa por código ou nome
    if (!empty($search)) {
        $todosArtigos = array_filter($todosArtigos, function($artigo) use ($search) {
            if (!is_array($artigo)) return false;
            return stripos((string)($artigo['nArtigo'] ?? ''), $search) !== false ||
                   stripos((string)($artigo['nome'] ?? ''), $search) !== false;
        });
    }

    // Paginação manual eficiente para a collection
    $perPage = 10;
    $page = $request->input('page', 1);
    $collection = collect($todosArtigos)->values();

    $artigos = new \Illuminate\Pagination\LengthAwarePaginator(
        $collection->forPage($page, $perPage)->values(),
        $collection->count(),
        $perPage,
        $page,
        ['path' => $request->url(), 'query' => $request->query()]
    );

    return view('artigos.index', compact('artigos'));
}

    /**
     * Limpa a cache local e força a atualização com os dados do ERP
     */
    public function resync()
    {
        try {
            // Executa o comando Artisan programaticamente
            \Illuminate\Support\Facades\Artisan::call('erp:sync-artigos');

            return redirect()->route('artigos.index')->with('success', 'Artigos resincronizados e atualizados com sucesso!');
        } catch (\Exception $e) {
            return redirect()->route('artigos.index')->with('error', 'Erro ao resincronizar: ' . $e->getMessage());
        }
    }

    public function show($codigo)
    {
        // Obter detalhes de APENAS UM artigo
        $artigo = $this->erpService->getArtigoByCodigo($codigo);
        if (!$artigo) {
            return redirect()->route('artigos.index')->with('error', 'Artigo não encontrado.');
        }
        return view('artigos.show', compact('artigo'));
    }

    /**
     * Exibe o formulário de criação de novo artigo (AdminLTE)
     */
    public function create()
    {
        return view('artigos.create');
    }

    /**
     * Recebe os dados do formulário e envia para o ERP
     */
    public function store(Request $request)
    {
        // 1. Validação dos campos vindos do formulário
        $validated = $request->validate([
            'nome'         => 'required|string|max:100',
            'nFamilia'     => 'required|integer',
            'precoSemIva'  => 'required|numeric|min:0',
            'taxaIvaVenda' => 'required|numeric|min:0',
            'precoComIva'  => 'required|numeric|min:0',
            'movimStock'   => 'nullable|boolean',
        ]);

        // 2. Prepara o payload ajustado com os IDs padrão válidos da tabela do ERP
        $payload = [
            "nome"               => $validated['nome'],
            "nFamilia"           => 40303,
            "nTpArt"             => 4,
            "nGrFamilia"         => 403,
            "nSubFa"             => 1,
            "codCalcPrecoVenda"  => 1,
            "codIvaVenda"        => 3,
            "taxaIvaVenda"       => (float) $validated['taxaIvaVenda'],
            "codIvaCompra"       => 3,
            "taxaIvaCompra"      => 14.0,
            "unidadeMov"         => 1,
            "qtdPorUnid1"        => 1.0,
            "movimStock"         => $request->has('movimStock'),
            "temLote"            => false,
            "nIec"               => 0,
            "codvaloriz"         => 1,
            "precoComIva"        => (float) $validated['precoComIva'],
            "precoSemIva"        => (float) $validated['precoSemIva'],
            "stockArmazens"      => [],
            "qtdTotal"           => 0,
            "temRetencaoNaFonte" => false,
            "tipoTaxa"           => 3,
            "artDesactivado"     => false,
            "artBloqueado"       => false,
            "nDecimaisPrecos"    => 2,
            "tags"               => [],
            "artigoPrecos"       => []
        ];

        $resultado = $this->erpService->createArtigo($payload);

        if ($resultado) {
            // Se a API não devolver a estrutura completa do item, cria uma representação básica para a lista
            $artigoParaCache = is_array($resultado) ? $resultado : array_merge($payload, [
                'nArtigo' => 'NOVO',
                'inactivo' => false,
                'movimentaStock' => $payload['movimStock']
            ]);

            // 3. Atualização síncrona do Storage local
            $artigosAtuais = $this->erpService->getArtigos();

            // Coloca o artigo criado no início do array para aparecer no topo da página 1
            array_unshift($artigosAtuais, $artigoParaCache);

            // Regrava a lista atualizada no ficheiro de cache do storage
            \Illuminate\Support\Facades\Storage::put('artigos_cache.json', json_encode($artigosAtuais));

            $codigo = is_array($resultado) ? ($resultado['nArtigo'] ?? $resultado['codArtigo'] ?? $resultado['id'] ?? 'Registo OK') : 'Sucesso';

            return redirect()->route('artigos.index')
                ->with('success', "Artigo '{$validated['nome']}' registado no ERP e sincronizado localmente! (Código: {$codigo})");
        }

        return back()->with('error', 'Falha ao registar o artigo no ERP. Verifica os logs em storage/logs/laravel.log.');
    }
}