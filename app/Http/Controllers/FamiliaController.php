<?php

namespace App\Http\Controllers;

use App\Services\ErpService;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class FamiliaController extends Controller
{
    protected $erpService;

    public function __construct(ErpService $erpService)
    {
        $this->erpService = $erpService;
    }

    public function index(Request $request)
    {
        $search = trim($request->input('search'));
        $familiasArray = [];

        if (!empty($search) && is_numeric($search)) {
            $familia = $this->erpService->getFamiliaById((int) $search);
            if ($familia) {
                $familiasArray = [$familia];
            }
        } else {
            $familiasArray = $this->erpService->getFamilias();

            // Permite filtrar por nome se houver pesquisa de texto na cache
            if (!empty($search)) {
                $familiasArray = array_filter($familiasArray, function ($item) use ($search) {
                    return stripos($item['nome'] ?? '', $search) !== false;
                });
            }
        }

        // Paginação manual de 10 itens
        $perPage = 10;
        $page = $request->input('page', 1);
        $collection = collect($familiasArray);

        $familias = new LengthAwarePaginator(
            $collection->forPage($page, $perPage),
            $collection->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view('familias.index', compact('familias'));
    }
}