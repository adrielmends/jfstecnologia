<?php
/**
 * Adminer Loader with Modern UI
 * This script downloads Adminer and applies a custom theme for a premium experience.
 */
echo '<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;900&display=swap" rel="stylesheet">';

$filename = 'adminer-4.8.1.php';
$url = 'https://github.com/vrana/adminer/releases/download/v4.8.1/adminer-4.8.1.php';

if (!file_exists($filename)) {
    echo "Baixando interface de gerenciamento (Adminer)... por favor aguarde.";
    $content = file_get_contents($url);
    if ($content) {
        file_put_contents($filename, $content);
        echo "<script>window.location.reload();</script>";
    } else {
        die("Erro ao baixar o Adminer. Verifique a conexão do servidor ou baixe manualmente em adminer.org e salve como $filename");
    }
}

include $filename;
