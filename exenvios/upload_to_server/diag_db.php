<?php
require_once 'config.php';

header('Content-Type: text/plain');

try {
    $db = getDB();
    echo "Conexão OK\n\n";
    
    echo "Verificando colunas da tabela 'orders':\n";
    $q = $db->query("DESCRIBE orders");
    while($row = $q->fetch(PDO::FETCH_ASSOC)) {
        echo "- " . $row['Field'] . " (" . $row['Type'] . ")\n";
    }
    
} catch (Exception $e) {
    echo "ERRO: " . $e->getMessage();
}
