$ErrorActionPreference = 'Stop'
$raiz = Split-Path -Parent $MyInvocation.MyCommand.Path
$arquivoController = @(
    (Join-Path $raiz 'Modules\OrdemServico\app\Http\Controllers\OrdemServicoController.php'),
    (Join-Path $raiz 'Modules\OrdemServico\Http\Controllers\OrdemServicoController.php')
) | Where-Object { Test-Path $_ } | Select-Object -First 1
if (-not $arquivoController) { throw 'Controller da ordem de servico nao encontrado. Execute o script na raiz do projeto.' }

foreach ($n in 1,2) {
    $imagem = Join-Path $raiz "public\img\begintech-modelo-pagina-$n.jpg"
    if (-not (Test-Path $imagem)) { throw "Imagem ausente: $imagem. Extraia o ZIP na raiz do projeto antes de executar." }
}

$conteudo = [System.IO.File]::ReadAllText($arquivoController)
if (($conteudo | Select-String -Pattern 'function\s+imprimirOrdemServico\s*\(' -AllMatches).Matches.Count -ne 1) { throw 'Esperada exatamente uma declaracao do metodo imprimirOrdemServico. Nada foi alterado.' }
$alterado = $conteudo
foreach ($n in 1,2) {
    $alterado = $alterado.Replace("begintech-modelo-pagina-$n.png", "begintech-modelo-pagina-$n.jpg")
}
$alterado = $alterado.Replace('data:image/png;base64,', 'data:image/jpeg;base64,')
if ($alterado -eq $conteudo) { Write-Host 'O Controller ja referencia os JPEGs (ou possui codigo diferente). Nenhuma alteracao realizada.' }
else {
    Copy-Item $arquivoController "$arquivoController.bak" -Force
    [System.IO.File]::WriteAllText($arquivoController, $alterado, [System.Text.UTF8Encoding]::new($false))
    Write-Host "Controller atualizado. Backup: $arquivoController.bak"
}
Write-Host 'Testando sintaxe PHP...'
& php -l $arquivoController
if ($LASTEXITCODE -ne 0) { throw 'Falha no teste de sintaxe PHP.' }
Write-Host 'Limpando caches Laravel...'
Push-Location $raiz
try { & php artisan optimize:clear } finally { Pop-Location }
Write-Host 'Concluido. Reinicie php artisan serve e abra novamente a impressao.'
