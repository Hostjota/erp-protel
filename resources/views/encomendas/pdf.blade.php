<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Encomenda - {{ $encomenda['cab']['nDoc'] ?? 'N/D' }}</title>
    <style>
        body { font-family: Arial, sans-serif; color: #333; margin: 0; padding: 20px; font-size: 14px; }
        .invoice-box { max-width: 800px; margin: auto; padding: 30px; border: 1px solid #eee; box-shadow: 0 0 10px rgba(0, 0, 0, 0.15); background: #fff; }
        .header { display: flex; justify-content: space-between; margin-bottom: 40px; }
        .header h1 { margin: 0; color: #007bff; font-size: 24px; }
        .details { margin-bottom: 30px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        table th, table td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
        table th { background-color: #f8f9fa; color: #333; }
        .text-right { text-align: right; }
        .total-section { margin-top: 30px; text-align: right; font-size: 18px; font-weight: bold; }
        .no-print { margin-top: 30px; text-align: center; }
        .btn { padding: 10px 20px; background: #007bff; color: #fff; text-decoration: none; border-radius: 4px; cursor: pointer; }
        @media print { .no-print { display: none; } }
    </style>
</head>
<body>
    <div class="invoice-box">
        @php
            $cab = $encomenda['cab'] ?? $encomenda;
            $linhas = $encomenda['linhas'] ?? [];
        @endphp

        <div class="header">
            <div>
                <h1>NOTA DE ENCOMENDA</h1>
                <p><strong>Nº Documento:</strong> {{ $cab['nDoc'] ?? 'N/D' }}</p>
                <p><strong>Data:</strong> {{ isset($cab['dataDoc']) ? \Carbon\Carbon::parse($cab['dataDoc'])->format('d/m/Y H:i') : '-' }}</p>
            </div>
            <div style="text-align: right;">
                <h3>Loja Online / ERP</h3>
                <p>Documento gerado automaticamente</p>
            </div>
        </div>

        <div class="details">
            <strong>Cliente: </strong> {{ $cab['nomeConta'] ?? $cab['nome'] ?? 'Cliente Desconhecido' }}<br>
            <strong>NIF:     </strong> {{ $cab['nif'] ?? 'Cliente Sem NIF' }}<br>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Artigo / Descrição</th>
                    <th class="text-right">Qtd</th>
                    <th class="text-right">Preço Unit.</th>
                    <th class="text-right">Total</th>
                </tr>
            </thead>
            <tbody>
                @forelse($linhas as $linha)
                    <tr>
                        <td>
                            <strong>{{ $linha['nArtigo'] ?? '' }}</strong><br>
                            <small style="color: #666;">{{ $linha['nomeArtigo'] ?? '' }}</small>
                        </td>
                        <td class="text-right">{{ $linha['qtd1'] ?? 1 }}</td>
                        <td class="text-right">{{ number_format($linha['prVenda1'] ?? 0, 2, ',', '.') }} Kz</td>
                        <td class="text-right">{{ number_format(($linha['qtd1'] ?? 1) * ($linha['prVenda1'] ?? 0), 2, ',', '.') }} Kz</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" style="text-align: center; color: #777;">Sem linhas de artigos registadas.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="total-section">
            Total: {{ number_format($cab['totalDocumento'] ?? 0, 2, ',', '.') }} Kz
        </div>

        <div class="no-print">
            <button onclick="window.print()" class="btn">Imprimir / Guardar PDF</button>
        </div>
    </div>
</body>
</html>