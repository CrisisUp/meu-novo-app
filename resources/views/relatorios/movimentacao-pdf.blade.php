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
        .section-title { margin-top: 30px; font-size: 14px; font-weight: bold; text-transform: uppercase; border-bottom: 1px solid #ccc; padding-bottom: 5px; }
        .grid { width: 100%; margin-top: 10px; }
        .grid td { border: none; text-align: left; vertical-align: top; padding: 0 10px; }
        .small-table { width: 100%; border-collapse: collapse; }
        .small-table th, .small-table td { border: 1px solid #ccc; padding: 4px; font-size: 9px; }
        .small-table th { background-color: #f9f9f9; }

        .footer { margin-top: 50px; }
        .signatures { margin-top: 80px; width: 100%; }
        .signatures td { border: none; padding: 0 20px; vertical-align: top; width: 50%; }
        .line { border-top: 1px solid #000; width: 200px; margin: 0 auto 5px; }
        
        .info-footer { position: fixed; bottom: 0; width: 100%; text-align: right; font-size: 8px; color: #999; }
        .page-break { page-break-before: always; }
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
                <th colspan="3">60 a 64 ANOS</th>
                <th colspan="3">65 a 69 ANOS</th>
                <th colspan="3">70 a 74 ANOS</th>
                <th colspan="3">75 a 79 ANOS</th>
                <th colspan="3">80 ANOS OU MAIS</th>
                <th rowspan="2">TOTAL GERAL</th>
            </tr>
            <tr>
                <th>M</th>
                <th>F</th>
                <th>O</th>
                <th>M</th>
                <th>F</th>
                <th>O</th>
                <th>M</th>
                <th>F</th>
                <th>O</th>
                <th>M</th>
                <th>F</th>
                <th>O</th>
                <th>M</th>
                <th>F</th>
                <th>O</th>
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
                    $total = array_sum((array)$d);
                @endphp
                <tr class="{{ $index == 3 ? 'total-row' : '' }}">
                    <td class="label-cell">{{ $linha['label'] }}</td>
                    <td>{{ $d->m_60_64 }}</td>
                    <td>{{ $d->f_60_64 }}</td>
                    <td style="color: #999;">{{ $d->o_60_64 }}</td>
                    
                    <td>{{ $d->m_65_69 }}</td>
                    <td>{{ $d->f_65_69 }}</td>
                    <td style="color: #999;">{{ $d->o_65_69 }}</td>
                    
                    <td>{{ $d->m_70_74 }}</td>
                    <td>{{ $d->f_70_74 }}</td>
                    <td style="color: #999;">{{ $d->o_70_74 }}</td>
                    
                    <td>{{ $d->m_75_79 }}</td>
                    <td>{{ $d->f_75_79 }}</td>
                    <td style="color: #999;">{{ $d->o_75_79 }}</td>
                    
                    <td>{{ $d->m_80_mais }}</td>
                    <td>{{ $d->f_80_mais }}</td>
                    <td style="color: #999;">{{ $d->o_80_mais }}</td>
                    
                    <td><strong>{{ $total }}</strong></td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="section-title">Perfil dos Usuários Atendidos (Total: {{ $totalAtendidos }})</div>
    
    <table class="grid">
        <tr>
            <td width="50%">
                <p><strong>Classificação por Sexo</strong></p>
                <table class="small-table">
                    <tr><td>Masculino</td><td align="right">{{ $stats['sexo']['M'] }}</td></tr>
                    <tr><td>Feminino</td><td align="right">{{ $stats['sexo']['F'] }}</td></tr>
                    <tr><td>Outros / Não declarado</td><td align="right">{{ $stats['sexo']['Outros'] }}</td></tr>
                </table>

                <p><strong>Identidade de Gênero</strong></p>
                <table class="small-table">
                    <tr><td>Cisgênero Feminino</td><td align="right">{{ $stats['identidade']['cis_f'] }}</td></tr>
                    <tr><td>Cisgênero Masculino</td><td align="right">{{ $stats['identidade']['cis_m'] }}</td></tr>
                    <tr><td>Transgênero Feminino</td><td align="right">{{ $stats['identidade']['trans_f'] }}</td></tr>
                    <tr><td>Transgênero Masculino</td><td align="right">{{ $stats['identidade']['trans_m'] }}</td></tr>
                    <tr><td>Agênero</td><td align="right">{{ $stats['identidade']['agenero'] }}</td></tr>
                    <tr><td>Não declarado</td><td align="right">{{ $stats['identidade']['nao_declarado'] }}</td></tr>
                </table>
            </td>
            <td width="50%">
                <p><strong>Raça / Cor</strong></p>
                <table class="small-table">
                    <tr><td>Branca</td><td align="right">{{ $stats['raca_cor']['branca'] }}</td></tr>
                    <tr><td>Preta</td><td align="right">{{ $stats['raca_cor']['preta'] }}</td></tr>
                    <tr><td>Parda</td><td align="right">{{ $stats['raca_cor']['parda'] }}</td></tr>
                    <tr><td>Amarela</td><td align="right">{{ $stats['raca_cor']['amarela'] }}</td></tr>
                    <tr><td>Indígena</td><td align="right">{{ $stats['raca_cor']['indigena'] }}</td></tr>
                    <tr><td>Não informado</td><td align="right">{{ $stats['raca_cor']['nao_informado'] }}</td></tr>
                </table>

                <p><strong>Grau de Dependência</strong></p>
                <table class="small-table">
                    <tr><td>Grau I (Independente)</td><td align="right">{{ $stats['grau_dependencia']['I'] }}</td></tr>
                    <tr><td>Grau II (Dependência Leve/Mod.)</td><td align="right">{{ $stats['grau_dependencia']['II'] }}</td></tr>
                    <tr><td>Grau III (Dependência Grave/Total)</td><td align="right">{{ $stats['grau_dependencia']['III'] }}</td></tr>
                </table>
            </td>
        </tr>
    </table>

    <div class="section-title">Tempo de Permanência (Desligamentos do Mês)</div>
    @if(count($stats['saidas_permanencia']) > 0)
        <table class="small-table" style="margin-top: 10px;">
            <thead>
                <tr>
                    <th align="left">Usuário</th>
                    <th>Tempo na Instituição</th>
                    <th align="left">Motivo da Saída</th>
                </tr>
            </thead>
            <tbody>
                @foreach($stats['saidas_permanencia'] as $saida)
                    <tr>
                        <td align="left">{{ $saida['nome'] }}</td>
                        <td>{{ $saida['permanencia'] }}</td>
                        <td align="left">{{ $saida['motivo'] ?? 'Não informado' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <p style="text-align: center; color: #999; margin-top: 10px;">Nenhuma saída registrada neste período.</p>
    @endif

    <div class="page-break"></div>
    <div class="header">
        <h1>Centro de Dia para Idosos - CDI</h1>
        <p>Relatório de Movimentação Mensal - Detalhamento Administrativo</p>
        <p>Competência: {{ $mesNome }} / {{ $ano }}</p>
    </div>

    <div class="section-title">Matriz de Granularidade: Cruzamento Sexo x Raça / Cor</div>
    <table class="small-table" style="margin-top: 10px;">
        <thead>
            <tr style="background-color: #333; color: white;">
                <th align="left">Sexo / Raça-Cor</th>
                <th>Branca</th>
                <th>Preta</th>
                <th>Parda</th>
                <th>Amarela</th>
                <th>Indígena</th>
                <th>Não Inf.</th>
                <th style="background-color: #555;">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach(['M' => 'Masculino', 'F' => 'Feminino', 'Outros' => 'Outros / N.D.'] as $key => $label)
                <tr>
                    <td align="left"><strong>{{ $label }}</strong></td>
                    <td>{{ $stats['sexo_raca'][$key]['branca'] }}</td>
                    <td>{{ $stats['sexo_raca'][$key]['preta'] }}</td>
                    <td>{{ $stats['sexo_raca'][$key]['parda'] }}</td>
                    <td>{{ $stats['sexo_raca'][$key]['amarela'] }}</td>
                    <td>{{ $stats['sexo_raca'][$key]['indigena'] }}</td>
                    <td>{{ $stats['sexo_raca'][$key]['nao_informado'] }}</td>
                    <td style="background-color: #f9f9f9; font-weight: bold;">{{ array_sum($stats['sexo_raca'][$key]) }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr style="background-color: #eee; font-weight: bold;">
                <td align="left">TOTAL POR RAÇA</td>
                <td>{{ $stats['raca_cor']['branca'] }}</td>
                <td>{{ $stats['raca_cor']['preta'] }}</td>
                <td>{{ $stats['raca_cor']['parda'] }}</td>
                <td>{{ $stats['raca_cor']['amarela'] }}</td>
                <td>{{ $stats['raca_cor']['indigena'] }}</td>
                <td>{{ $stats['raca_cor']['nao_informado'] }}</td>
                <td style="background-color: #ddd;">{{ $totalAtendidos }}</td>
            </tr>
        </tfoot>
    </table>

    <div class="footer">
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
