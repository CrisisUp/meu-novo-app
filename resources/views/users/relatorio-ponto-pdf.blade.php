<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Folha de Ponto - {{ $user->name }}</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; color: #333; }
        .header { text-align: center; border-bottom: 2px solid #333; padding-bottom: 10px; margin-bottom: 20px; }
        .header h1 { margin: 0; font-size: 18px; text-transform: uppercase; }
        .info { margin-bottom: 20px; }
        .info table { width: 100%; }
        .info td { padding: 3px 0; }
        .label { font-weight: bold; text-transform: uppercase; font-size: 10px; color: #666; }
        table.ponto { width: 100%; border-collapse: collapse; margin-top: 10px; }
        table.ponto th, table.ponto td { border: 1px solid #ddd; padding: 8px; text-align: center; }
        table.ponto th { background-color: #f5f5f5; font-size: 10px; text-transform: uppercase; }
        .footer { margin-top: 50px; }
        .signature { border-top: 1px solid #333; width: 250px; margin: 0 auto; text-align: center; padding-top: 5px; margin-top: 40px; }
        .signature-label { font-size: 10px; text-transform: uppercase; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Folha de Ponto Mensal</h1>
        <p>Centro de Dia para Idosos (CDI)</p>
    </div>

    <div class="info">
        <table>
            <tr>
                <td width="50%"><span class="label">Funcionário:</span><br><strong>{{ $user->name }}</strong></td>
                <td width="50%"><span class="label">Período:</span><br><strong>{{ $mesNome }} / {{ $ano }}</strong></td>
            </tr>
            <tr>
                <td><span class="label">E-mail:</span><br>{{ $user->email }}</td>
                <td><span class="label">Emissão:</span><br>{{ date('d/m/Y H:i') }}</td>
            </tr>
        </table>
    </div>

    <table class="ponto">
        <thead>
            <tr>
                <th>Data</th>
                <th>Dia</th>
                <th>Entrada</th>
                <th>Saída</th>
                <th>Total Horas</th>
                <th>Assinatura</th>
            </tr>
        </thead>
        <tbody>
            @foreach($pontos as $ponto)
                @php
                    $entrada = \Carbon\Carbon::parse($ponto->entrada);
                    $saida = $ponto->saida ? \Carbon\Carbon::parse($ponto->saida) : null;
                    $horas = $saida ? $entrada->diffInHours($saida) . 'h ' . ($entrada->diffInMinutes($saida) % 60) . 'min' : '-';
                @endphp
                <tr>
                    <td>{{ \Carbon\Carbon::parse($ponto->data)->format('d/m/Y') }}</td>
                    <td style="font-size: 9px;">{{ \Carbon\Carbon::parse($ponto->data)->locale('pt_BR')->dayName }}</td>
                    <td>{{ \Carbon\Carbon::parse($ponto->entrada)->format('H:i') }}</td>
                    <td>{{ $ponto->saida ? \Carbon\Carbon::parse($ponto->saida)->format('H:i') : '--:--' }}</td>
                    <td>{{ $horas }}</td>
                    <td width="100"></td>
                </tr>
            @endforeach
            @if($pontos->isEmpty())
                <tr>
                    <td colspan="6" style="padding: 20px; color: #999;">Nenhum registro encontrado para este mês.</td>
                </tr>
            @endif
        </tbody>
    </table>

    <div class="footer">
        <div style="width: 100%;">
            <div style="float: left; width: 45%;">
                <div class="signature">
                    <span class="signature-label">Assinatura do Funcionário</span>
                </div>
            </div>
            <div style="float: right; width: 45%;">
                <div class="signature">
                    <span class="signature-label">Responsável CDI</span>
                </div>
            </div>
            <div style="clear: both;"></div>
        </div>
    </div>
</body>
</html>
