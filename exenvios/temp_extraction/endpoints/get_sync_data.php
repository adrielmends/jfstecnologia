<?php
/**
 * Get Sync Data Endpoint
 * Fetches all dynamic app data (locations, services, lockers)
 */

require_once '../config.php';

try {
    $db = getDB();
    $isAdmin = isset($_GET['admin']) && $_GET['admin'] == '1';
    
    // Ensure settings table exists
    $db->exec("CREATE TABLE IF NOT EXISTS `settings` (
        `key` VARCHAR(100) PRIMARY KEY,
        `value` TEXT,
        `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

    $count = $db->query("SELECT COUNT(*) FROM `settings`")->fetchColumn();
    if ($count == 0) {
        $db->exec("INSERT IGNORE INTO `settings` (`key`, `value`) VALUES 
            ('work_days', '1,2,3,4,5'),
            ('open_time', '08:00'),
            ('close_time', '18:00')");
    }
    
    // Auto-create Tables
    $db->exec("CREATE TABLE IF NOT EXISTS `locations` (
        `id` VARCHAR(50) PRIMARY KEY,
        `name` VARCHAR(255) NOT NULL,
        `address` TEXT,
        `sched_info` VARCHAR(255),
        `express_info` VARCHAR(255),
        `type` VARCHAR(50) DEFAULT 'Unidade',
        `totens` VARCHAR(50) DEFAULT '01',
        `occupancy` VARCHAR(50) DEFAULT '5/10',
        `status` VARCHAR(20) DEFAULT 'online',
        `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

    $db->exec("CREATE TABLE IF NOT EXISTS `shipping_services` (
        `id` VARCHAR(50) PRIMARY KEY,
        `name` VARCHAR(255) NOT NULL,
        `size` VARCHAR(100) DEFAULT NULL,
        `category` ENUM('empacotamento', 'coleta', 'fretes') NOT NULL,
        `method` ENUM('fixo', 'markup', 'faixa') NOT NULL,
        `price` DECIMAL(10,2) DEFAULT 0.00,
        `base_price` DECIMAL(10,2) DEFAULT 0.00,
        `markup` DECIMAL(10,2) DEFAULT 0.00,
        `description` TEXT,
        `status` VARCHAR(20) DEFAULT 'active',
        `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

    $db->exec("CREATE TABLE IF NOT EXISTS `lockers` (
        `id` VARCHAR(50) PRIMARY KEY,
        `label` VARCHAR(100) NOT NULL,
        `location_id` VARCHAR(50),
        `hardware_ref` VARCHAR(100),
        `type` VARCHAR(50) DEFAULT 'Hibrido',
        `status` ENUM('online', 'offline', 'manutencao') DEFAULT 'online',
        `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (`location_id`) REFERENCES `locations`(`id`) ON DELETE SET NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

    // Seed initial services if missing
    $svcCount = $db->query("SELECT COUNT(*) FROM `shipping_services`")->fetchColumn();
    if ($svcCount == 0) {
        $initialServices = [
            ['col-01', 'Coleta Programada', null, 'coleta', 'fixo', 5.00, 'Defina dia e horário.'],
            ['col-02', 'Coleta Express', null, 'coleta', 'fixo', 12.00, 'Receba em minutos.'],
            ['col-03', 'Locker Inteligente', null, 'coleta', 'fixo', 0.00, 'Deixe no box seguro.'],
            ['emb-01', 'Caixa Pequena', '20x15x10cm', 'empacotamento', 'fixo', 6.90, 'Caixa padrão para envios pequenos.'],
            ['emb-02', 'Sacola com Bolha', null, 'empacotamento', 'fixo', 4.50, 'Proteção extra para itens frágeis.'],
            ['emb-04', 'CAIXA 1', '30x20x15cm', 'empacotamento', 'fixo', 0.00, 'Caixa de tamanho médio.'],
            ['emb-05', 'CAIXA 2', '40x30x20cm', 'empacotamento', 'fixo', 0.00, 'Caixa de tamanho grande.'],
            ['emb-06', 'CAIXA 3', '50x40x30cm', 'empacotamento', 'fixo', 0.00, 'Caixa extra grande.'],
            ['03220', 'Frete Correios (SEDEX)', null, 'fretes', 'markup', 0.00, 'Markup aplicado sobre o custo base.'],
            ['03298', 'Frete Correios (PAC)', null, 'fretes', 'markup', 0.00, 'Markup aplicado sobre o custo base.'],
            ['TEX', 'Total Express (Expresso)', null, 'fretes', 'markup', 0.00, 'Markup aplicado sobre o custo base.']
        ];

        foreach ($initialServices as $s) {
            $stmt = $db->prepare("INSERT IGNORE INTO `shipping_services` (`id`, `name`, `size`, `category`, `method`, `price`, `description`) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute($s);
        }
        
        // Ensure markups are set for those that need it
        $db->exec("UPDATE `shipping_services` SET `markup` = 30.00 WHERE `id` = '03220'");
        $db->exec("UPDATE `shipping_services` SET `markup` = 20.00 WHERE `id` = '03298'");
        $db->exec("UPDATE `shipping_services` SET `markup` = 30.00 WHERE `id` = 'TEX'");
    }

    // Seed initial locations if empty
    $locCount = $db->query("SELECT COUNT(*) FROM `locations`")->fetchColumn();
    if ($locCount == 0) {
        $db->exec("INSERT INTO `locations` (`id`, `name`, `address`, `sched_info`, `express_info`) VALUES
            ('loc-01', 'EX-ENVIOS MATRIZ', 'Av. Afonso Pena, 1234', 'Seg a Sex às 18h', 'Em até 30 min'),
            ('loc-02', 'EX-ENVIOS SHOPPING', 'Rua Amazonas, 567', 'Seg a Sáb às 20h', 'Em até 60 min')");
            
        // Seed initial lockers for locations
        $db->exec("INSERT IGNORE INTO `lockers` (`id`, `label`, `location_id`, `hardware_ref`, `type`, `status`) VALUES
            ('t-01', 'TOTEM 01', 'loc-01', 'HW-9912', 'Hibrido', 'online'),
            ('t-02', 'TOTEM 02', 'loc-02', 'HW-9915', 'Hibrido', 'online')");
    }


    // Fetch Services
    if ($isAdmin) {
        $servicesStmt = $db->query("SELECT * FROM `shipping_services` ORDER BY `category`, `name` ");
    } else {
        $servicesStmt = $db->query("SELECT * FROM `shipping_services` WHERE LOWER(`status`) IN ('active', 'ativo')");
    }
    $services = $servicesStmt->fetchAll();

    // Fetch Locations
    if ($isAdmin) {
        $locationsStmt = $db->query("SELECT * FROM `locations` ORDER BY `name` ");
    } else {
        $locationsStmt = $db->query("SELECT * FROM `locations` WHERE `status` != 'inactive'");
    }
    $locations = $locationsStmt->fetchAll();

    // Fetch Lockers
    $sqlLockers = "SELECT l.*, loc.name as location_name 
                   FROM `lockers` l 
                   LEFT JOIN `locations` loc ON l.location_id = loc.id ";
    if (!$isAdmin) {
        $sqlLockers .= " WHERE l.status != 'offline' ";
    }
    $lockersStmt = $db->query($sqlLockers);
    $lockers = $lockersStmt->fetchAll();

    // Fetch Settings
    $settingsStmt = $db->query("SELECT `key`, `value` FROM `settings` ");
    $settings = [];
    while ($row = $settingsStmt->fetch()) {
        $settings[$row['key']] = $row['value'];
    }

    jsonResponse(true, [
        'services' => $services,
        'locations' => $locations,
        'lockers' => $lockers,
        'settings' => $settings
    ]);

} catch (Exception $e) {
    error_log("Sync Data Error: " . $e->getMessage());
    jsonResponse(false, null, 'Erro ao sincronizar dados: ' . $e->getMessage());
}
