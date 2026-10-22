<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\ErpService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Pagination\LengthAwarePaginator;
use Carbon\Carbon;

class EncomendaController extends Controller
{
    protected $erpService;

    public function __construct(ErpService $erpService)
    {
        $this->erpService = $erpService;
    }

    public function index(Request $request)
    {
        $search = trim($request->get('search'));
        $encomendasMapeadas = [];

        // 1. Lê a cache completa e já ordenada criada pelo comando artisan
        if (Storage::exists('encomendas_cache.json')) {
            $encomendasMapeadas = json_decode(Storage::get('encomendas_cache.json'), true) ?? [];
        }

        // 2. Aplica o filtro de pesquisa se houver
        if (!empty($search)) {
            $encomendasMapeadas = array_filter($encomendasMapeadas, function ($item) use ($search) {
                if (is_numeric($search) && $item['id'] == (int)$search) {
                    return true;
                }
                return stripos($item['nDoc'], $search) !== false || stripos($item['nome'], $search) !== false;
            });
            $encomendasMapeadas = array_values($encomendasMapeadas);
        }

        // 3. Paginação eficiente (10 itens por página)
        $perPage = 10;
        $page = $request->input('page', 1);
        $collection = collect($encomendasMapeadas);

        $itensDaPagina = $collection->forPage($page, $perPage);
        // Extrai apenas a chave 'detalhes' para a view (igual à estrutura anterior)
        $encomendasData = $itensDaPagina->pluck('detalhes')->values()->all();

        $encomendas = new LengthAwarePaginator(
            $encomendasData,
            $collection->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view('encomendas.index', compact('encomendas'));
    }

    public function sincronizar(Request $request)
    {
        try {
            Artisan::call('erp:sync-encomendas');

            return redirect()->route('encomendas.index')
                ->with('success', 'Sincronização de encomendas efetuada com sucesso!');
        } catch (\Exception $e) {
            return redirect()->route('encomendas.index')
                ->with('error', 'Erro ao sincronizar: ' . $e->getMessage());
        }
    }

    public function pdf($id)
    {
        $encomenda = $this->erpService->getDocumentoComercialById($id);

        if (!$encomenda) {
            return redirect()->route('encomendas.index')->with('error', 'Encomenda não encontrada.');
        }

        return view('encomendas.pdf', compact('encomenda'));
    }
}