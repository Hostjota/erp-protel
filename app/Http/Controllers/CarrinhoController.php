<?php

namespace App\Http\Controllers;

use App\Services\ErpService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class CarrinhoController extends Controller
{
    protected $erpService;

    public function __construct(ErpService $erpService)
    {
        $this->erpService = $erpService;
    }

    /**
     * Exibe a página do carrinho de compras. eliminar até aqui...
     */
    public function index(\App\Services\ErpService $erpService)
    {
        $carrinho = session()->get('carrinho', []);

        $totalComIva = 0;
        foreach ($carrinho as $codigo => $item) {
            $totalComIva += ($item['preco'] ?? 0) * ($item['quantidade'] ?? 0);

            $nArtigo = $item['codigo'] ?? $codigo;
            if (!empty($nArtigo)) {
                $carrinho[$codigo]['imagem_url'] = route('erp.imagem', $nArtigo);

                // Busca o stock fresco ao ERP para definir o limite na tabela do carrinho
                $produtoErp = $erpService->getArtigoByCodigo($nArtigo);
                $carrinho[$codigo]['stock_max'] = $produtoErp['qtdTotal'] ?? $produtoErp['data']['qtdTotal'] ?? 0;
            } else {
                $carrinho[$codigo]['imagem_url'] = 'https://via.placeholder.com/300x200?text=Sem+Imagem';
                $carrinho[$codigo]['stock_max'] = 0;
            }
        }

        $taxaIva = 0.14;
        $subtotalSemIva = $totalComIva / (1 + $taxaIva);
        $valorIva = $totalComIva - $subtotalSemIva;

        return view('loja.carrinho', compact('carrinho', 'subtotalSemIva', 'valorIva', 'totalComIva'));
    }

    /**
     * Adiciona um artigo ao carrinho.
     */
    public function add(Request $request)
    {
        $request->validate([
            'codigo' => 'required|string',
            'quantidade' => 'required|integer|min:1'
        ]);

        $codigo = $request->input('codigo');
        $quantidade = (int) $request->input('quantidade');

        $produto = null;

        // 1. TENTA LER DO CACHE DA LOJA (Instantâneo - 0 segundos)
        // Certifica-te que na tua listagem da loja guardas os artigos em cache (ex: Cache::put('erp_artigos', $artigos, 600))
        $todosArtigos = Cache::get('artigos_cache');

        if (!empty($todosArtigos)) {
            $produto = collect($todosArtigos)->firstWhere('nArtigo', $codigo);
        }

        // 2. Se não estiver no cache da loja, tenta o getArtigos() geral com limite de tempo ou cache próprio
        if (!$produto) {
            $todosArtigos = $this->erpService->getArtigos(); // Lê do ficheiro JSON local sem rebentar o MySQL
            $produto = collect($todosArtigos)->firstWhere('nArtigo', $codigo);
        }

        // 3. Último recurso: busca apenas este artigo específico
        if (!$produto) {
            $produto = $this->erpService->getArtigoByCodigo($codigo);
        }

        if (!$produto) {
            return back()->with('error', 'Produto não encontrado.');
        }

        $carrinho = session()->get('carrinho', []);

        $preco = $produto['precoComIva'] ?? $produto['preco_com_iva'] ?? 0;
        $nome = $produto['nome'] ?? $produto['descricao'] ?? 'Produto sem nome';
        $nArtigo = $produto['nArtigo'] ?? $codigo;

        $imagemUrl = route('erp.imagem', $nArtigo);

        if (isset($carrinho[$codigo])) {
            $carrinho[$codigo]['quantidade'] += $quantidade;
            $carrinho[$codigo]['imagem_url'] = $imagemUrl;
        } else {
            $carrinho[$codigo] = [
                'codigo' => $codigo,
                'nome' => $nome,
                'preco' => (float) $preco,
                'quantidade' => $quantidade,
                'familia' => $produto['nomeFamilia'] ?? '',
                'imagem_url' => $imagemUrl
            ];
        }

        session()->put('carrinho', $carrinho);

        return redirect()->route('carrinho.index');
    }

    /**
     * Atualiza a quantidade de um item no carrinho.
     */
    public function update(Request $request, \App\Services\ErpService $erpService)
{
    $request->validate([
        'codigo' => 'required|string',
        'quantidade' => 'required|integer|min:1'
    ]);

    $codigo = $request->input('codigo');
    $novaQuantidade = (int) $request->input('quantidade');

    // Consulta o ERP em tempo real para obter o stock atual
    $produto = $erpService->getArtigoByCodigo($codigo);

    if (!$produto) {
        return back()->with('error', 'Produto não encontrado no ERP.');
    }

    $stockDisponivel = $produto['qtdTotal'] ?? $produto['data']['qtdTotal'] ?? 0;

    // Validação estrita: impede que a quantidade seja superior ao stock
    if ($novaQuantidade > $stockDisponivel) {
        return back()->with('error', "A quantidade solicitada ({$novaQuantidade}) excede o stock disponível em armazém ({$stockDisponivel}).");
    }

    $carrinho = session()->get('carrinho', []);

    if (isset($carrinho[$codigo])) {
        $carrinho[$codigo]['quantidade'] = $novaQuantidade;
        session()->put('carrinho', $carrinho);
    }

    return redirect()->route('carrinho.index')->with('success', 'Carrinho atualizado com sucesso!');
}

    /**
     * Remove um item do carrinho.
     */
    public function remove(Request $request)
    {
        $request->validate([
            'codigo' => 'required|string'
        ]);

        $carrinho = session()->get('carrinho', []);
        $codigo = $request->input('codigo');

        if (isset($carrinho[$codigo])) {
            unset($carrinho[$codigo]);
            session()->put('carrinho', $carrinho);
            return back()->with('success', 'Produto removido do carrinho.');
        }

        return back()->with('error', 'Item não encontrado.');
    }

    /**
     * Limpa todo o carrinho.
     */
    public function clear()
    {
        session()->forget('carrinho');
        return redirect()->route('carrinho.index')->with('success', 'Carrinho esvaziado.');
    }
}