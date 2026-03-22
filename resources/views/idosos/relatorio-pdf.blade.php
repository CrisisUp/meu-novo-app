<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Relatório de Frequência Mensal</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; color: #333; }
        .header { text-align: center; border-bottom: 2px solid #10b981; padding-bottom: 10px; margin-bottom: 20px; }
        .header h1 { margin: 0; color: #0f172a; font-size: 20px; }
        .header p { margin: 5px 0 0; color: #64748b; }
        
        .info-section { margin-bottom: 20px; }
        .info-grid { width: 100%; border-collapse: collapse; }
        .info-grid td { padding: 5px 0; }
        .label { font-weight: bold; color: #64748b; width: 120px; }
        
        .table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        .table th { background-color: #f8fafc; border: 1px solid #e2e8f0; padding: 10px; text-align: left; color: #64748b; }
        .table td { border: 1px solid #e2e8f0; padding: 10px; }
        .status-presente { color: #059669; font-weight: bold; }
        .status-ausente { color: #dc2626; }
        
        .footer { position: fixed; bottom: 0; width: 100%; text-align: center; font-size: 10px; color: #94a3b8; border-top: 1px solid #f1f5f9; padding-top: 10px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Gestão CDI - Centro de Dia para Idosos</h1>
        <p>Relatório Mensal de Frequência</p>
    </div>

    <div class="info-section">
        <table class="info-grid">
            <tr>
                <td class="label">Idoso:</td>
                <td><strong>{{ $idoso->nome }}</strong></td>
                <td class="label">Período:</td>
                <td>{{ $mesNome }} / {{ $ano }}</td>
            </tr>
            <tr>
                <td class="label">CPF:</td>
                <td>{{ $idoso->cpf ?? 'Não informado' }}</td>
                <td class="label">Responsável:</td>
                <td>{{ $idoso->contato_emergencia_nome }}</td>
            </tr>
        </table>
    </div>

    <table class="table">
        <thead>
            <tr>
                <th style="width: 80px;">Data</th>
                <th style="width: 100px;">Dia</th>
                <th style="width: 70px;">Status</th>
                <th>Observações / Intercorrências</th>
            </tr>
        </thead>
        <tbody>
            @foreach($frequencias as $freq)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($freq->data)->format('d/m/Y') }}</td>
                    <td style="font-size: 10px; color: #64748b;">{{ ucfirst(\Carbon\Carbon::parse($freq->data)->locale('pt_BR')->dayName) }}</td>
                    <td>
                        <span class="{{ $freq->status == 'presente' ? 'status-presente' : 'status-ausente' }}" style="font-size: 10px; text-transform: uppercase;">
                            {{ $freq->status }}
                        </span>
                    </td>
                    <td>
                        <div style="font-size: 11px;">{{ $freq->observacoes ?? '-' }}</div>
                        @if($freq->profissional && $freq->observacoes)
                            <div style="font-size: 8px; color: #94a3b8; margin-top: 3px; font-style: italic; text-transform: uppercase;">Anotado por: {{ $freq->profissional->name }}</div>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div style="margin-top: 40px;">
        <table style="width: 100%;">
            <tr>
                <td style="width: 50%; text-align: center;">
                    <div style="border-top: 1px solid #333; width: 200px; margin: 0 auto; margin-top: 50px;"></div>
                    <p>Assinatura do Responsável</p>
                </td>
                <td style="width: 50%; text-align: center;">
                    <div style="border-top: 1px solid #333; width: 200px; margin: 0 auto; margin-top: 50px;"></div>
                    <p>Coordenação CDI</p>
                </td>
            </tr>
        </table>
    </div>

    <div class="footer">
        Gerado em {{ date('d/m/Y H:i') }} - Sistema de Gestão CDI
    </div>
</body>
</html>
