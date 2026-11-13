$ErrorActionPreference = 'Stop'
$raiz = Split-Path -Parent $MyInvocation.MyCommand.Path
$destino = Join-Path $raiz 'Modules\OrdemServico\app\Http\Controllers\OrdemServicoController.php'
$novo = Join-Path $raiz 'REFERENCIA\OrdemServicoController.php'
$viewOrigem = Join-Path $raiz 'Modules\OrdemServico\resources\views\impressos\ordem_servico.blade.php'
if (!(Test-Path $destino) -or !(Test-Path $novo) -or !(Test-Path $viewOrigem)) { throw 'Arquivos esperados nao encontrados. Extraia o ZIP na raiz do projeto.' }
# O Controller incluído no pacote é referência completa; não sobrescrever o arquivo atual.
# Substitui apenas o método, preservando todos os demais métodos e mudanças locais.
$conteudo = [System.IO.File]::ReadAllText($destino)
$fonte = [System.IO.File]::ReadAllText($novo)
$padrao = '(?ms)^    public function imprimirOrdemServico\(int \$idCodigo\)\s*\{.*?^    \}'
$antigoMatch = [regex]::Matches($conteudo, $padrao)
$novoMatch = [regex]::Match($fonte, $padrao)
if ($antigoMatch.Count -ne 1 -or !$novoMatch.Success) { throw "Esperada uma declaracao de imprimirOrdemServico (encontradas $($antigoMatch.Count)). Nenhuma alteracao feita." }
$backup = "$destino.bak-$(Get-Date -Format yyyyMMdd-HHmmss)"
Copy-Item $destino $backup
$alterado = [regex]::Replace($conteudo, $padrao, [System.Text.RegularExpressions.MatchEvaluator]{ param($m) $novoMatch.Value }, 1)
[System.IO.File]::WriteAllText($destino, $alterado, (New-Object System.Text.UTF8Encoding($false)))
Write-Host "Metodo atualizado. Backup: $backup"
php -l $destino
php artisan optimize:clear
