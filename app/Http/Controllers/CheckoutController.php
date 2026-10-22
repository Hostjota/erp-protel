<?php

namespace App\Http\Controllers;

use App\Services\ErpService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use App\Mail\EncomendaEfetuadaMail;
use Illuminate\Support\Facades\Mail;

class CheckoutController extends Controller
{
    protected $erpService;

    protected function client()
    {
        return Http::baseUrl('http://garfosgarfos.ddns.net:9000')
            ->withToken(env('ERP_API_TOKEN'))
            ->acceptJson();
    }

    public function __construct(ErpService $erpService)
    {
        $this->erpService = $erpService;
    }

    /**
     * Exibe o formulário de checkout com os itens do carrinho.
     */
    public function index()
    {
        $carrinho = session()->get('carrinho', []);

        if (empty($carrinho)) {
            return redirect()->route('carrinho.index')->with('error', 'O seu carrinho está vazio.');
        }

        $subtotal = collect($carrinho)->sum(function ($item) {
            return ($item['preco'] ?? 0) * ($item['quantidade'] ?? 0);
        });

        return view('loja.checkout', compact('carrinho', 'subtotal'));
    }

    /**
     * Processa a encomenda e envia para o ERP de forma separada (Cliente -> Encomenda).
     */
    public function store(Request $request)
    {
        $tipoCliente = $request->input('tipo_cliente', 'novo');
        $nContaCliente = null;
        $nif = '';
        $nomeCliente = 'Cliente Loja Online';
        $moradaCliente = 'Morada não especificada';
        $cpCliente = '1000';
        $localidadeCliente = 'LUANDA';

        // Validação comum do carrinho
        $carrinho = session()->get('carrinho', []);
        if (empty($carrinho)) {
            return redirect()->route('carrinho.index')->with('error', 'O seu carrinho está vazio.');
        }

        try {
            // ==========================================
            // PASSO 1: GERIR O CLIENTE (CRIAR OU BUSCAR)
            // ==========================================
            if ($tipoCliente === 'existente') {
                $busca = $request->input('nif_busca');

                if (empty($busca)) {
                    return back()->withInput()->with('error', 'Por favor, indique o NIF ou número de conta existente.');
                }

                // Tenta encontrar o cliente no ERP
                $clienteErp = $this->erpService->getClienteByNif($busca) ?? $this->erpService->getClienteByConta($busca);

                if (!$clienteErp) {
                    return back()->withInput()->with('error', 'Não foi encontrada nenhuma conta com os dados fornecidos no ERP.');
                }

                $nContaCliente = $clienteErp['nConta'] ?? $clienteErp['id'] ?? null;
                $nomeCliente = $clienteErp['nome'] ?? $nomeCliente;
                $moradaCliente = $clienteErp['rua'] ?? $moradaCliente;
                $cpCliente = $clienteErp['codPostal'] ?? $cpCliente;
                $localidadeCliente = $clienteErp['localidade'] ?? $localidadeCliente;
                $nif = $clienteErp['nContribuint'] ?? $busca;

            } else {
                // Validação do formulário de novo cliente
                $request->validate([
                    'nome' => 'required|string|max:255',
                    'email' => 'required|email|max:255',
                    'telefone' => 'required|string|max:50',
                    'morada' => 'required|string|max:500',
                    'codigo_postal' => 'required|string|max:20',
                    'localidade' => 'required|string|max:100',
                    'nif' => 'nullable|string|max:50',
                ]);

                $nomeCliente = $request->input('nome');
                $moradaCliente = $request->input('morada');
                $cpCliente = $request->input('codigo_postal');
                $localidadeCliente = $request->input('localidade');
                $nif = $request->input('nif') ?: '';

                // Registo limpo do novo cliente no ERP
                $dadosClientePayload = [
                    'action' => 1,
                    'nome' => $nomeCliente,
                    'email' => $request->input('email'),
                    'telefone' => $request->input('telefone'),
                    'telemovel' => $request->input('telefone'),
                    'rua' => $moradaCliente,
                    'codPostal' => $cpCliente,
                    'localidade' => $localidadeCliente,
                    'nCondPag' => 1,
                    'codCondComerciais' => 99,
                ];

                Log::info('CRIANDO NOVO CLIENTE NO ERP:', $dadosClientePayload);
                $clienteSalvo = $this->erpService->salvarCliente($dadosClientePayload);


                if (is_array($clienteSalvo)) {
                    $nContaCliente = $clienteSalvo['nConta'] ?? $clienteSalvo['data']['nConta'] ?? $clienteSalvo['id'] ?? null;

                }
                // Se por ventura o método de salvamento não retornou o ID diretamente,
                // fazemos uma rápida busca pelo NIF para garantir que apanhamos o número de conta correto gerado.
                if (empty($nContaCliente) && !empty($nif)) {
                    $clienteRecuperado = $this->erpService->getClienteByNif($nif);
                    if ($clienteRecuperado) {
                        $nContaCliente = $clienteRecuperado['nConta'] ?? $clienteRecuperado['id'] ?? null;
                    }
                }

                if (empty($nContaCliente)) {
                    throw new \Exception('O cliente foi enviado, mas o ERP não retornou o número de conta (nConta) necessário para prosseguir.');
                }
            }

            // ==========================================
            // PASSO 2: CRIAR A ENCOMENDA COM A CONTA JÁ PRONTA
            // ==========================================
            $nDocfa = (int) config('erp.n_doc_fa', 550);
            $nNumer = (int) config('erp.n_numer', 26);

            $payloadEncomenda = [
                'cab' => [
                    'nDocfa' => $nDocfa,
                    'nDocFa' => $nDocfa,
                    'nNumer' => $nNumer,
                    'nConta' => (string) $nContaCliente,
                    'nomeConta' => $nomeCliente,
                    'dataDoc' => now()->toIso8601String(),
                    'observacoes' => 'Encomenda efetuada via Loja Online',
                ],
                'clifo' => [
                    'action' => 0, // Como a conta já existe obrigatoriamente no Passo 1, aqui é 0 (apenas referência)
                    'nConta' => (string) $nContaCliente,
                    'id' => (string) $nContaCliente,
                    'nIdAltern' => 0,
                    'nome' => $nomeCliente,
                    'rua' => $moradaCliente,
                    'codPostal' => $cpCliente,
                    'localidade' => $localidadeCliente,
                    'codPais' => 244,
                    'nomePais' => 'Angola',
                    'nContribuint' => $nif,
                ],
                'moradaCarga' => [],
                'moradaDescarga' => [],
                'transportador' => [
                    'dataGuiaTransporte' => '1899-12-30T12:00:00.000Z'
                ],
                'cargaInfo' => [],
                'meiosPagamento' => [],
                'resumoIva' => [],
                'linhas' => collect($carrinho)->values()->map(function ($item, $index) {
                    return [
                        'nSeq' => $index + 1,
                        'nArtigo' => (string) ($item['codigo'] ?? $item['nArtigo'] ?? ''),
                        'nomeArtigo' => (string) ($item['nome'] ?? ''),
                        'qtd1' => (float) ($item['quantidade'] / 1),
                        'prVenda1' => (float) ($item['preco'] ?? 0),
                        'nArmazem' => 1,
                    ];
                })->toArray()
            ];

            Log::info('ENVIANDO ENCOMENDA COM CONTA VALIDADA:', $payloadEncomenda);
            $respostaErp = $this->erpService->criarEncomenda($payloadEncomenda);

            Log::info('RESPOSTA DE SUCESSO DA ENCOMENDA:', is_array($respostaErp) ? $respostaErp : ['raw' => $respostaErp]);

            if (empty($respostaErp)) {
                throw new \Exception('O ERP retornou uma resposta vazia ao criar o documento comercial.');
            }

            $dadosDocumento = $respostaErp['data'] ?? $respostaErp;
            $numeroEncomenda = $dadosDocumento['nDoc'] ?? $dadosDocumento['numero'] ?? $dadosDocumento['id'] ?? 'ENC-' . rand(1000, 9999);
/*
            $contasBancarias = [
                ['banco' => 'BAI (Banco Angolano de Investimentos)', 'ibans' => 'AO06 0040 0000 1234 5678 1019 2', 'titular' => 'HOSTJOTA TECNOLOGIA, LDA'],
                ['banco' => 'BFA (Banco de Fomento Angola)', 'ibans' => 'AO06 0006 0000 9876 5432 1018 5', 'titular' => 'HOSTJOTA TECNOLOGIA, LDA']
            ];

            $dadosClienteMail = [
                'nome' => $nomeCliente,
                'email' => $request->input('email') ?? ($clienteErp['email'] ?? 'cliente@loja.com')
            ];

            // 1. Enviar e-mail para o Cliente (caso tenha e-mail válido)
            if (!empty($dadosClienteMail['email'])) {
                Mail::to($dadosClienteMail['email'])->send(
                    new EncomendaEfetuadaMail($numeroEncomenda, $carrinho, $dadosClienteMail, $contasBancarias)
                );
            }

            // 2. Enviar e-mail para a Administração / Loja
            $emailLoja = config('mail.from.address', 'geral@hostjota.com');
            Mail::to($emailLoja)->send(
                new EncomendaEfetuadaMail($numeroEncomenda, $carrinho, $dadosClienteMail, $contasBancarias)
            );
*/
            // Limpa o carrinho de compras após o sucesso total
            session()->forget('carrinho');
            return redirect()->route('checkout.sucesso', ['referencia' => $numeroEncomenda]);

        } catch (\Exception $e) {
            $mensagemErroOriginal = $e->getMessage();
            Log::error('Erro no fluxo de Checkout ERP: ' . $mensagemErroOriginal);

            if (str_contains($mensagemErroOriginal, 'não tem Stock suficiente') || str_contains($mensagemErroOriginal, 'Stock')) {
                preg_match('/"([^"]+)"/', $mensagemErroOriginal, $matches);
                $codigoArtigo = $matches[1] ?? 'solicitado';
                $mensagemAmigavel = "Lamentamos, mas o artigo com código [{$codigoArtigo}] não tem stock suficiente no armazém.";
            } else {
                $mensagemAmigavel = "Ocorreu um erro ao processar: " . $mensagemErroOriginal;
            }

            return back()->withInput()->with('error', $mensagemAmigavel);
        }
    }

    /**
     * Página de confirmação de sucesso.
     */
    public function sucesso($referencia)
    {
        // Contas bancárias disponíveis para pagamento por transferência em Kz
        $contasBancarias = [
            ['banco' => 'BAI (Banco Angolano de Investimentos)', 'ibans' => 'AO06 0040 0000 1234 5678 1019 2', 'titular' => 'HOSTJOTA TECNOLOGIA, LDA'],
            ['banco' => 'BFA (Banco de Fomento Angola)', 'ibans' => 'AO06 0006 0000 9876 5432 1018 5', 'titular' => 'HOSTJOTA TECNOLOGIA, LDA']
        ];

        return view('loja.sucesso', compact('referencia', 'contasBancarias'));
    }
}