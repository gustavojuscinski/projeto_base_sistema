<?php
/*
 * Procura arquivos que enviam texto antes do <!DOCTYPE html>.
 *
 * Como usar:
 *   1. Salve este arquivo na RAIZ do projeto Laravel (onde fica o artisan)
 *   2. Rode no terminal:  php verificar-bom.php
 *   3. Corrija os arquivos listados e apague este script
 */

$pastas = ['app', 'bootstrap', 'config', 'database', 'resources/views', 'routes', 'public/index.php'];
$achados = 0;

function verificar(string $caminho, int &$achados): void
{
    $nome = basename($caminho);

    if (substr($nome, -4) !== '.php') {
        return;
    }

    $conteudo = file_get_contents($caminho);
    $blade = substr($nome, -10) === '.blade.php';
    $problemas = [];

    // 1) BOM (caractere invisível no início do arquivo)
    if (substr($conteudo, 0, 3) === "\xEF\xBB\xBF") {
        $problemas[] = 'BOM no início do arquivo (salvar como UTF-8 sem BOM)';
    }

    // 2) Espaço ou linha em branco no início
    $semBom = substr($conteudo, 0, 3) === "\xEF\xBB\xBF" ? substr($conteudo, 3) : $conteudo;

    if ($semBom !== '' && preg_match('/^\s/', $semBom)) {
        $problemas[] = 'espaço ou linha em branco ANTES do primeiro conteúdo';
    }

    // 3) Arquivos PHP puros: texto depois da tag de fechamento final
    if (!$blade && preg_match('/\?>\s+$/', $conteudo)) {
        $problemas[] = 'espaço/quebra de linha depois do "?>" final (remova o "?>")';
    }

    // 4) Arquivos PHP puros: conteúdo antes da tag de abertura
    if (!$blade && $semBom !== '' && !preg_match('/^\s*<\?php/', $semBom)) {
        $problemas[] = 'conteúdo antes do "<?php" (ou arquivo sem "<?php" no início)';
    }

    if ($problemas) {
        $achados++;
        echo "\n" . $caminho . "\n";
        foreach ($problemas as $p) {
            echo "   - " . $p . "\n";
        }
    }
}

foreach ($pastas as $pasta) {
    if (is_file($pasta)) {
        verificar($pasta, $achados);
        continue;
    }

    if (!is_dir($pasta)) {
        continue;
    }

    $it = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($pasta, FilesystemIterator::SKIP_DOTS)
    );

    foreach ($it as $arquivo) {
        if ($arquivo->isFile()) {
            verificar($arquivo->getPathname(), $achados);
        }
    }
}

echo $achados
    ? "\n{$achados} arquivo(s) com problema.\n"
    : "\nNenhum problema encontrado nessas pastas.\n";