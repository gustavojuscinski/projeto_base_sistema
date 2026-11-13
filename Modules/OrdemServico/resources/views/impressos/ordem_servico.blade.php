<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Orçamento da Ordem de Serviço Nº {{ $numero }}</title>
    <style>
        @page { size: A4 portrait; margin: 0; }
        * { box-sizing: border-box; }
        html, body { margin: 0; padding: 0; color: #171717; font-family: DejaVu Sans, sans-serif; }
        body { font-size: 10pt; }
        .pagina { position: relative; width: 595pt; height: 841pt; overflow: hidden; page-break-after: always; }
        .pagina.ultima { page-break-after: auto; }
        .logo { position: absolute; left: 58pt; top: 50pt; width: 171pt; height: auto; }
        .linha-cabecalho { position: absolute; left: 371pt; top: 0; width: 7pt; height: 110pt; background: #d10b2c; }
        .cabecalho { position: absolute; left: 384pt; top: 58pt; width: 191pt; line-height: 1.27; font-size: 9.1pt; }
        .cabecalho strong { display: block; font-size: 10.3pt; }
        .titulo-principal { position: absolute; left: 60pt; top: 123pt; width: 477pt; font-size: 15pt; font-weight: bold; white-space: nowrap; }
        .linha-titulo { position: absolute; top: 146pt; left: 59pt; width: 476pt; border-top: 1.6pt solid #1a1a1a; }
        .faixa-cinza { position: absolute; top: 164pt; left: 59pt; width: 476pt; height: 292pt; background: #f3f3f3; }
        .bloco-vermelho { position: absolute; top: 176pt; left: 0; width: 128pt; height: 262pt; background: #cb0b29; }
        .dados-cliente { position: absolute; left: 142pt; top: 188pt; width: 384pt; line-height: 1.42; }
        .dados-cliente .linha { margin-bottom: 12pt; min-height: 12pt; }
        .dados-cliente .nome { margin-bottom: 11pt; }
        .rotulo { font-weight: bold; }
        .dados-equipamento { position: absolute; left: 142pt; top: 296pt; width: 382pt; }
        .subtitulo { font-size: 12pt; font-weight: bold; }
        .dados-equipamento .subtitulo { margin: 0 0 11pt; }
        .dados-equipamento .linha { margin: 0 0 5pt; line-height: 1.32; }
        .dados-equipamento .defeito { max-height: 37pt; overflow: hidden; }
        .secao { position: absolute; left: 59pt; width: 477pt; }
        .secao-titulo { font-size: 12.1pt; font-weight: bold; margin: 0; line-height: 1.2; }
        .linha-vermelha { border-top: 2.6pt solid #cd0c30; margin-top: 9pt; }
        .secao-laudo { top: 459pt; }
        .texto-laudo { margin-top: 9pt; font-size: 9.6pt; line-height: 1.30; height: 56pt; overflow: hidden; white-space: pre-line; overflow-wrap: break-word; }
        .secao-valores { top: 575pt; }
        .grupo-valor { position: absolute; width: 477pt; left: 59pt; }
        .grupo-valor .nome-grupo { position: absolute; top: 0; left: 0; font-weight: bold; font-size: 9.6pt; }
        .grupo-valor .preco { position: absolute; top: 0; right: 0; width: 125pt; text-align: right; font-size: 9.6pt; }
        .grupo-valor .descricao { position: absolute; left: 0; top: 19pt; width: 470pt; white-space: pre-line; line-height: 1.27; overflow: hidden; overflow-wrap: break-word; font-size: 9.2pt; }
        .grupo-pecas { top: 613pt; }
        .grupo-pecas .descricao { height: 40pt; }
        .grupo-servicos { top: 668pt; }
        .grupo-servicos .descricao { height: 45pt; }
        .grupo-deslocamento { top: 743pt; }
        .opcoes-deslocamento { position: absolute; left: 59pt; top: 760pt; font-size: 9.4pt; }
        .quadrado { display: inline-block; width: 12pt; height: 12pt; border: 1pt solid #111; vertical-align: middle; margin-right: 2pt; }
        .quadrado.marcado { background: #111; }
        .opcao { display: inline-block; margin-right: 15pt; }
        .total-bloco { position: absolute; left: 59pt; top: 148pt; width: 477pt; height: 40pt; border-top: 2.6pt solid #cd0c30; border-bottom: 2.6pt solid #cd0c30; }
        .total-bloco .palavra { position: absolute; top: 9pt; left: 291pt; font-weight: bold; font-size: 12.5pt; }
        .total-bloco .valor-total { position: absolute; top: 9pt; right: 14pt; width: 125pt; text-align: right; font-weight: bold; font-size: 12.5pt; }
        .validade { position: absolute; top: 199pt; left: 59pt; font-weight: bold; font-size: 9.7pt; }
        .alerta { display: inline-block; position: relative; color: #cd0c30; font-size: 16pt; padding-right: 7pt; vertical-align: middle; }
    </style>
</head>
<body>
    <div class="pagina">
        <img class="logo" src="{{ $logo }}" alt="beginTECH">
        <div class="linha-cabecalho"></div>
        <div class="cabecalho"><strong>BeginTECH</strong>(42) 99151-2459<br>contato.begintech@gmail.com.br<br>www.begintech.com.br</div>
        <div class="titulo-principal">ORÇAMENTO DA ORDEM DE SERVIÇO Nº {{ $numero }}</div>
        <div class="linha-titulo"></div>
        <div class="faixa-cinza"></div><div class="bloco-vermelho"></div>
        <div class="dados-cliente">
            <div class="linha nome"><span class="rotulo">CLIENTE:</span> {{ $ordem->NOME_CLIENTE ?? '' }}</div>
            <div class="linha"><span class="rotulo">ENDEREÇO:</span> {{ $endereco }}</div>
            <div class="linha"><span class="rotulo">CEP:</span> {{ $ordem->CEP_CLIENTE ?? '' }}</div>
            <div class="linha"><span class="rotulo">TELEFONE:</span> {{ $ordem->TELEFONE_CLIENTE ?? '' }}</div>
        </div>
        <div class="dados-equipamento">
            <div class="subtitulo">EQUIPAMENTO</div>
            <div class="linha"><span class="rotulo">MARCA:</span> {{ $ordem->MARCA_EQUIPAMENTO ?? '' }}</div>
            <div class="linha"><span class="rotulo">MODELO:</span> {{ $ordem->MODELO_EQUIPAMENTO ?? '' }}</div>
            <div class="linha"><span class="rotulo">Nº DE SÉRIE:</span> {{ $ordem->NUMERO_SERIE ?? '' }}</div>
            <div class="linha"><span class="rotulo">TIPO:</span> {{ $ordem->TIPO_EQUIPAMENTO ?? '' }}</div>
            <div class="linha defeito"><span class="rotulo">DEFEITO:</span> {{ $ordem->DEFEITO ?? '' }}</div>
        </div>
        <div class="secao secao-laudo">
            <div class="secao-titulo">LAUDO TÉCNICO</div><div class="linha-vermelha"></div>
            <div class="texto-laudo">{{ $ordem->LAUDO_TECNICO ?? '' }}</div>
        </div>
        <div class="secao secao-valores"><div class="secao-titulo">VALORES DO ORÇAMENTO</div><div class="linha-vermelha"></div></div>
        <div class="grupo-valor grupo-pecas">
            <div class="nome-grupo">PEÇAS</div><div class="preco">{{ $moeda($ordem->VALOR_PECAS ?? 0) }}</div>
            <div class="descricao">{{ $ordem->DESCRICAO_PECAS ?? '' }}</div>
        </div>
        <div class="grupo-valor grupo-servicos">
            <div class="nome-grupo">SERVIÇOS</div><div class="preco">{{ $moeda($ordem->VALOR_SERVICOS ?? 0) }}</div>
            <div class="descricao">{{ $ordem->DESCRICAO_SERVICOS ?? '' }}</div>
        </div>
        <div class="grupo-valor grupo-deslocamento">
            <div class="nome-grupo">DESLOCAMENTO</div>
            <div class="preco">{{ $moeda(($ordem->HOUVE_DESLOCAMENTO ?? 'N') === 'S' ? ($ordem->VALOR_DESLOCAMENTO ?? 0) : 0) }}</div>
        </div>
        <div class="opcoes-deslocamento">
            <span class="opcao"><span class="quadrado {{ ($ordem->HOUVE_DESLOCAMENTO ?? 'N') === 'S' ? 'marcado' : '' }}"></span>Sim</span>
            <span class="opcao"><span class="quadrado {{ ($ordem->HOUVE_DESLOCAMENTO ?? 'N') === 'S' ? '' : 'marcado' }}"></span>Não</span>
        </div>
    </div>
    <div class="pagina ultima">
        <img class="logo" src="{{ $logo }}" alt="beginTECH">
        <div class="linha-cabecalho"></div>
        <div class="cabecalho"><strong>BeginTECH</strong>(42) 99151-2459<br>contato.begintech@gmail.com.br<br>www.begintech.com.br</div>
        <div class="total-bloco"><div class="palavra">TOTAL</div><div class="valor-total">{{ $moeda($ordem->VALOR_TOTAL ?? 0) }}</div></div>
        <div class="validade"><span class="alerta">&#9888;</span> Orçamento válido por 05 dias.</div>
    </div>
</body>
</html>
