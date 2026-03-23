<?php
/**
 * Quick Fix for Missing Columns
 * Access this file via browser: your-domain.com/upload_to_server/fix_db.php
 */
require_once 'config.php';

echo "<h2>Migração de Tabelas - Site Ex-Envios</h2>";

try {
    $db = getDB();
    
    $sql = "ALTER TABLE orders 
            ADD COLUMN IF NOT EXISTS sender_cep VARCHAR(10) DEFAULT NULL,
            ADD COLUMN IF NOT EXISTS sender_doc VARCHAR(20) DEFAULT NULL,
            ADD COLUMN IF NOT EXISTS sender_name VARCHAR(255) DEFAULT NULL,
            ADD COLUMN IF NOT EXISTS sender_street VARCHAR(255) DEFAULT NULL,
            ADD COLUMN IF NOT EXISTS sender_number VARCHAR(50) DEFAULT NULL,
            ADD COLUMN IF NOT EXISTS sender_complement VARCHAR(255) DEFAULT NULL,
            ADD COLUMN IF NOT EXISTS sender_neighborhood VARCHAR(100) DEFAULT NULL,
            ADD COLUMN IF NOT EXISTS sender_city_uf VARCHAR(100) DEFAULT NULL,
            ADD COLUMN IF NOT EXISTS receiver_cep VARCHAR(10) DEFAULT NULL,
            ADD COLUMN IF NOT EXISTS receiver_doc VARCHAR(20) DEFAULT NULL,
            ADD COLUMN IF NOT EXISTS receiver_name VARCHAR(255) DEFAULT NULL,
            ADD COLUMN IF NOT EXISTS receiver_contact VARCHAR(255) DEFAULT NULL,
            ADD COLUMN IF NOT EXISTS receiver_street VARCHAR(255) DEFAULT NULL,
            ADD COLUMN IF NOT EXISTS receiver_number VARCHAR(50) DEFAULT NULL,
            ADD COLUMN IF NOT EXISTS receiver_complement VARCHAR(255) DEFAULT NULL,
            ADD COLUMN IF NOT EXISTS receiver_neighborhood VARCHAR(100) DEFAULT NULL,
            ADD COLUMN IF NOT EXISTS receiver_city_uf VARCHAR(100) DEFAULT NULL";

    // Note: IF NOT EXISTS in ALTER TABLE requires MySQL 8.0.19+ or MariaDB 10.0.2+
    // If it fails, we fall back to manual execution.
    
    try {
        $db->exec($sql);
        echo "<p style='color:green;'>Sucesso! As colunas foram adicionadas ou já existiam.</p>";
    } catch (Exception $e) {
        echo "<p style='color:orange;'>Aviso: Comando 'IF NOT EXISTS' pode não ser suportado. Tentando modo compatível...</p>";
        
        $cols = [
            'sender_cep' => 'VARCHAR(10)', 'sender_doc' => 'VARCHAR(20)', 'sender_name' => 'VARCHAR(255)',
            'sender_street' => 'VARCHAR(255)', 'sender_number' => 'VARCHAR(50)', 'sender_complement' => 'VARCHAR(255)',
            'sender_neighborhood' => 'VARCHAR(100)', 'sender_city_uf' => 'VARCHAR(100)',
            'receiver_cep' => 'VARCHAR(10)', 'receiver_doc' => 'VARCHAR(20)', 'receiver_name' => 'VARCHAR(255)',
            'receiver_contact' => 'VARCHAR(255)', 'receiver_street' => 'VARCHAR(255)', 'receiver_number' => 'VARCHAR(50)',
            'receiver_complement' => 'VARCHAR(255)', 'receiver_neighborhood' => 'VARCHAR(100)', 'receiver_city_uf' => 'VARCHAR(100)'
        ];

        foreach ($cols as $col => $type) {
            try {
                $db->exec("ALTER TABLE orders ADD COLUMN $col $type DEFAULT NULL");
                echo "Coluna <b>$col</b> adicionada.<br>";
            } catch (Exception $inner) {
                // Ignore if column exists
            }
        }
        echo "<p style='color:green;'>Migração concluída no modo de compatibilidade.</p>";
    }

} catch (Exception $e) {
    echo "<p style='color:red;'>Erro Fatal: " . $e->getMessage() . "</p>";
    echo "<p>Tente executar o SQL abaixo manualmente no seu phpMyAdmin:</p>";
    echo "<pre>ALTER TABLE orders 
ADD COLUMN sender_cep VARCHAR(10),
ADD COLUMN sender_doc VARCHAR(20),
ADD COLUMN sender_name VARCHAR(255),
ADD COLUMN sender_street VARCHAR(255),
ADD COLUMN sender_number VARCHAR(50),
ADD COLUMN sender_complement VARCHAR(255),
ADD COLUMN sender_neighborhood VARCHAR(100),
ADD COLUMN sender_city_uf VARCHAR(100),
ADD COLUMN receiver_cep VARCHAR(10),
ADD COLUMN receiver_doc VARCHAR(20),
ADD COLUMN receiver_name VARCHAR(255),
ADD COLUMN receiver_contact VARCHAR(255),
ADD COLUMN receiver_street VARCHAR(255),
ADD COLUMN receiver_number VARCHAR(50),
ADD COLUMN receiver_complement VARCHAR(255),
ADD COLUMN receiver_neighborhood VARCHAR(100),
ADD COLUMN receiver_city_uf VARCHAR(100);</pre>";
}
