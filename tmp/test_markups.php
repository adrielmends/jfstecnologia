<?php
// /tmp/test_markups.php
require_once 'c:/HD/Bot/Site Ex-Envios/config.php';

try {
    $db = getDB();
    
    // 1. Manually trigger seeding logic if table exists
    echo "--- Teste de Sincronização e Markups ---\n";
    
    // Mirror seeding logic from get_sync_data.php
    $initialServices = [
        ['03220', 'Frete Correios (SEDEX)', 'fretes', 'markup', 0.00, 'Markup aplicado sobre o custo base.'],
        ['03298', 'Frete Correios (PAC)', 'fretes', 'markup', 0.00, 'Markup aplicado sobre o custo base.'],
        ['TEX', 'Total Express (Expresso)', 'fretes', 'markup', 0.00, 'Markup aplicado sobre o custo base.']
    ];

    foreach ($initialServices as $s) {
        $stmt = $db->prepare("INSERT IGNORE INTO `shipping_services` (`id`, `name`, `category`, `method`, `price`, `description`) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute($s);
        echo "Seed check: " . $s[1] . "\n";
    }

    $db->exec("UPDATE `shipping_services` SET `markup` = 31.00 WHERE `id` = '03220' AND `markup` = 0");
    $db->exec("UPDATE `shipping_services` SET `markup` = 21.00 WHERE `id` = '03298' AND `markup` = 0");
    $db->exec("UPDATE `shipping_services` SET `markup` = 31.00 WHERE `id` = 'TEX' AND `markup` = 0");

    // 2. Fetch and verify
    $stmt = $db->query("SELECT id, name, markup FROM `shipping_services` WHERE id IN ('03220', '03298', 'TEX')");
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo "\nServiços no Banco:\n";
    foreach ($results as $res) {
        echo "- ID: {$res['id']} | Nome: {$res['name']} | Markup: {$res['markup']}%\n";
    }

    // 3. Test calculation logic (simulated from calc_frete_api.php)
    echo "\nSimulação de Cálculo (Custo Base R$ 100,00):\n";
    foreach ($results as $res) {
        $m = 1 + ($res['markup'] / 100);
        $final = 100 * $m;
        echo "- {$res['name']}: R$ {$final} (Markup {$res['markup']}%)\n";
    }

} catch (Exception $e) {
    echo "Erro: " . $e->getMessage();
}
