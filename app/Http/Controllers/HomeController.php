<?php

namespace App\Http\Controllers;

use App\Services\ErpService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class HomeController extends Controller
{
    protected $erpService;

    public function __construct(ErpService $erpService)
    {
        $this->erpService = $erpService;
    }

    public function index()
    {
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

        // 2. Filtra estritamente artigos ativos e com nSubFa == 1 para os destaques da Home
        $artigosAtivos = array_filter($todosArtigos, function ($item) {
            if (!is_array($item)) return false;

            $ativo = empty($item['inactivo']) && empty($item['artDesactivado']);
            $isSubFaPublica = isset($item['nSubFa']) && (int)$item['nSubFa'] === 1;

            return $ativo && $isSubFaPublica;
        });

        // 3. Injeta a URL da imagem (proxy) para que os destaques mostrem a imagem corretamente
        $artigosComImagem = array_map(function ($item) {
            $nArtigo = $item['nArtigo'] ?? null;

            if (!empty($nArtigo)) {
                $item['imagem_url'] = route('erp.imagem', $nArtigo);
            } else {
                $item['imagem_url'] = 'https://via.placeholder.com/300x200?text=' . urlencode($item['nome'] ?? 'Sem Imagem');
            }

            return $item;
        }, array_values($artigosAtivos));

        // 4. Pega os primeiros 8 artigos para os destaques
        $destaques = array_slice($artigosComImagem, 0, 8);

        return view('home', compact('destaques'));
    }

    public function sobre()
    {
        return view('sobre');
    }
}