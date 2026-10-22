<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: Arial, sans-serif; color: #333; line-height: 1.6; }
        .container { width: 100%; max-width: 600px; margin: 0 auto; padding: 20px; background: #f8f9fa; }
        .card { background: #fff; padding: 20px; border-radius: 5px; box-shadow: 0 0 10px rgba(0,0,0,0.05); }
        .table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        .table th, .table td { padding: 8px; border: 1px solid #dee2e6; text-align: left; font-size: 14px; }
        .table th { background: #f1f3f5; }
        .text-primary { color: #007bff; font-weight: bold; }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <h2>Olá, {{ $dadosCliente['nome'] }}!</h2>
            <p>A sua encomenda foi registada com sucesso no nosso sistema.</p>

            <p>Referência da Encomenda: <span class="text-primary">{{ $referencia }}</span></p>

            <h3>Artigos Encomendados:</h3>
            <table class="table">
                <thead>
                    <tr>
                        <th>Artigo</th>
                        <th>Qtd</th>
                        <th>Preço Unit.</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($carrinho as $item)
                    <tr>
                        <td>{{ $item['nome'] ?? 'Artigo' }}</td>
                        <td>{{ $item['quantidade'] }}</td>
                        <td>{{ number_format($item['preco'], 2, ',', '.') }} Kz</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <h3 style="margin-top: 20px;">Coordenadas Bancárias para Pagamento:</h3>
            <p>Por favor, efetue a transferência para uma das contas abaixo e envie o comprovativo:</p>
            <ul style="padding-left: 20px;">
                @foreach($contasBancarias as $conta)
                    <li><strong>{{ $conta['banco'] }}</strong><br>IBAN: <code>{{ $conta['ibans'] }}</code></li>
                @endforeach
            </ul>

            <p style="margin-top: 30px; font-size: 12px; color: #6c757d;">Obrigado por comprar connosco!</p>
        </div>
    </div>
</body>
</html>