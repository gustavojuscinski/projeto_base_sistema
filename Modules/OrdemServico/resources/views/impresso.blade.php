<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <title>Orçamento da Ordem de Serviço Nº {{ $numero }}</title>
    <style>
        @page { size: 595.5pt 842.25pt; margin: 0; }
        * { box-sizing: border-box; }
        html, body { padding: 0; margin: 0; color: #171717; font-family: DejaVu Sans, sans-serif; }
        .pagina { position: relative; width: 595.5pt; height: 842.25pt; overflow: hidden; page-break-after: always; }
        .pagina:last-child { page-break-after: auto; }
        .fundo { position: absolute; left: 0; top: 0; width: 595.5pt; height: 842.25pt; }
        .campo { position: absolute; font-size: 11pt; line-height: 1.30; white-space: pre-wrap; overflow: hidden; overflow-wrap: break-word; }
        .numero { position: absolute; left: 452pt; top: 132pt; font-size: 16.5pt; font-weight: bold; }
        .valor { position: absolute; text-align: right; width: 95pt; font-size: 11pt; }
        .caixa { position: absolute; width: 11pt; height: 11pt; border: 1.3pt solid black; background: #fff; }
        .caixa.marcada { background: #000; }
        .total { position: absolute; left: 437pt; top: 170pt; width: 85pt; font-size: 13pt; font-weight: bold; text-align: right; }
    </style>
</head>
<body>
    <div class="pagina">
        <img class="fundo" src="{{ $fundo1 }}" alt="">
        <div class="numero">{{ $numero }}</div>
        <div class="campo" style="left:198pt; top:198pt; width:338pt; height:18pt;">{{ $ordem->NOME_CLIENTE }}</div>
        <div class="campo" style="left:210pt; top:225pt; width:329pt; height:22pt; font-size:10.5pt;">{{ $endereco }}</div>
        <div class="campo" style="left:168pt; top:248pt; width:360pt; height:19pt;">{{ $ordem->CEP_CLIENTE }}</div>
        <div class="campo" style="left:209pt; top:272pt; width:325pt; height:19pt;">{{ $ordem->TELEFONE_CLIENTE }}</div>
        <div class="campo" style="left:193pt; top:344pt; width:337pt; height:18pt;">{{ $ordem->MARCA_EQUIPAMENTO }}</div>
        <div class="campo" style="left:202pt; top:368pt; width:327pt; height:18pt;">{{ $ordem->MODELO_EQUIPAMENTO }}</div>
        <div class="campo" style="left:217pt; top:391pt; width:312pt; height:18pt;">{{ $ordem->NUMERO_SERIE }}</div>
        <div class="campo" style="left:178pt; top:414pt; width:351pt; height:18pt;">{{ $ordem->TIPO_EQUIPAMENTO }}</div>
        <div class="campo" style="left:198pt; top:436pt; width:332pt; height:35pt; font-size:10.2pt;">{{ $ordem->DEFEITO }}</div>
        <div class="campo" style="left:59pt; top:522pt; width:478pt; height:59pt;">{{ $ordem->LAUDO_TECNICO }}</div>
        <div class="valor" style="left:437pt; top:644pt;">{{ $moeda($ordem->VALOR_PECAS) }}</div>
        <div class="campo" style="left:59pt; top:661pt; width:472pt; height:37pt; font-size:10.5pt;">{{ $ordem->DESCRICAO_PECAS }}</div>
        <div class="valor" style="left:437pt; top:702pt;">{{ $moeda($ordem->VALOR_SERVICOS) }}</div>
        <div class="campo" style="left:59pt; top:720pt; width:472pt; height:37pt; font-size:10.5pt;">{{ $ordem->DESCRICAO_SERVICOS }}</div>
        <div class="valor" style="left:437pt; top:764pt;">{{ $moeda($ordem->HOUVE_DESLOCAMENTO === 'S' ? $ordem->VALOR_DESLOCAMENTO : 0) }}</div>
        <div class="caixa {{ $ordem->HOUVE_DESLOCAMENTO === 'S' ? 'marcada' : '' }}" style="left:59pt; top:782pt;"></div>
        <div class="caixa {{ $ordem->HOUVE_DESLOCAMENTO === 'S' ? '' : 'marcada' }}" style="left:111pt; top:782pt;"></div>
    </div>
    <div class="pagina">
        <img class="fundo" src="{{ $fundo2 }}" alt="">
        <div class="total">{{ $moeda($ordem->VALOR_TOTAL) }}</div>
    </div>
</body>
</html>
