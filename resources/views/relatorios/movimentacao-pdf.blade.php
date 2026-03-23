<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Relatório de Movimentação Mensal - CDI</title>
    <style>
        body { font-family: sans-serif; font-size: 11px; color: #333; margin: 0; padding: 0; }
        .header { text-align: center; border-bottom: 2px solid #444; padding-bottom: 10px; margin-bottom: 20px; }
        .header h1 { font-size: 18px; margin: 0; text-transform: uppercase; }
        .header p { margin: 5px 0 0; font-size: 12px; font-weight: bold; color: #666; }
        
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #999; padding: 8px; text-align: center; }
        th { background-color: #f2f2f2; font-weight: bold; font-size: 10px; }
        .label-cell { text-align: left; font-weight: bold; background-color: #fafafa; }
        
        .total-row { background-color: #eee; font-weight: bold; }
        .footer { margin-top: 50px; }
        .signatures { margin-top: 80px; width: 100%; }
        .signatures td { border: none; padding: 0 20px; vertical-align: top; width: 50%; }
        .line { border-top: 1px solid #000; width: 200px; margin: 0 auto 5px; }
        
        .info-footer { position: fixed; bottom: 0; width: 100%; text-align: right; font-size: 8px; color: #999; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Centro de Dia para Idosos - CDI</h1>
        <p>Relatório de Movimentação Mensal (Controle Social)</p>
        <p>Competência: {{ $mesNome }} / {{ $ano }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th rowspan="2">DISCRIMINAÇÃO</th>
                <th colspan="2">60 a 64 ANOS</th>
                <th colspan="2">65 a 69 ANOS</th>
                <th colspan="2">70 a 74 ANOS</th>
                <th colspan="2">75 ANOS OU MAIS</th>
                <th rowspan="2">TOTAL GERAL</th>
            </tr>
            <tr>
                <th>M</th>
                <th>F</th>
                <th>M</th>
                <th>F</th>
                <th>M</th>
                <th>F</th>
                <th>M</th>
                <th>F</th>
            </tr>
        </thead>
        <tbody>
            @php
                $linhas = [
                    ['label' => 'SALDO ANTERIOR', 'data' => $saldoAnterior],
                    ['label' => 'ENTRADAS (ADMISSÕES)', 'data' => $entradas],
                    ['label' => 'SAÍDAS (DESLIGAMENTOS)', 'data' => $saidas],
                    ['label' => 'SALDO ATUAL', 'data' => $saldoAtual],
                ];
            @endphp

            @foreach ($linhas as $index => $linha)
                @php
                    $d = $linha['data'];
                    $total = $d->m_60_64 + $d->f_60_64 + $d->m_65_69 + $d->f_65_69 + $d->m_70_74 + $d->f_70_74 + $d->m_75_mais + $d->f_75_mais;
                @endphp
                <tr class="{{ $index == 3 ? 'total-row' : '' }}">
                    <td class="label-cell">{{ $linha['label'] }}</td>
                    <td>{{ $d->m_60_64 }}</td>
                    <td>{{ $d->f_60_64 }}</td>
                    <td>{{ $d->m_65_69 }}</td>
                    <td>{{ $d->f_65_69 }}</td>
                    <td>{{ $d->m_70_74 }}</td>
                    <td>{{ $d->f_70_74 }}</td>
                    <td>{{ $d->m_75_mais }}</td>
                    <td>{{ $d->f_75_mais }}</td>
                    <td>{{ $total }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p><strong>Observações:</strong> Relatório gerado automaticamente para fins de controle social e prestação de contas governamentais.</p>
        
        <table class="signatures">
            <tr>
                <td>
                    <div class="line"></div>
                    Responsável pelo Preenchimento
                </td>
                <td>
                    <div class="line"></div>
                    Coordenador(a) do CDI
                </td>
            </tr>
        </table>
    </div>

    <div class="info-footer">
        Gerado em {{ date('d/m/Y H:i:s') }} - Sistema de Gestão CDI
    </div>
</body>
</html>
