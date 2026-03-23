<?php
/**
 * Migration 001: Dual-Channel Payment System
 * Adds channel/locker support to orders and creates payments table.
 */

require_once __DIR__ . '/../config.php';

try {
    $db = getDB();

    echo "Starting migration...\n";

    // 1. Add columns to orders table
    echo "Updating 'orders' table...\n";
    $db->exec("ALTER TABLE orders 
        ADD COLUMN channel ENUM('WEB', 'LOCKER') DEFAULT 'WEB' AFTER external_ref,
        ADD COLUMN locker_id VARCHAR(50) NULL AFTER channel,
        ADD COLUMN device_id VARCHAR(50) NULL AFTER locker_id,
        ADD COLUMN confirmed_at DATETIME NULL AFTER status");

    // 2. Create payments table
    echo "Creating 'payments' table...\n";
    $db->exec("CREATE TABLE IF NOT EXISTS payments (
        id INT AUTO_INCREMENT PRIMARY KEY,
        order_id INT NOT NULL,
        provider ENUM('ASAAS', 'PAGARME', 'STRIPE', 'STONE', 'CIELO', 'REDE') NOT NULL,
        method ENUM('PIX', 'CARD_ONLINE', 'CARD_PINPAD') NOT NULL,
        provider_reference VARCHAR(255) NULL,
        status ENUM('PENDING', 'AUTHORIZED', 'PAID', 'DECLINED', 'CANCELED', 'ERROR') DEFAULT 'PENDING',
        amount DECIMAL(10, 2) NOT NULL,
        raw_payload JSON NULL,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    // 3. Optional: Migrate existing asaas_id data (if needed)
    // For now, we'll keep asaas_id in orders for backward compatibility during transition,
    // but future payments will go into the 'payments' table.

    echo "Migration completed successfully!\n";

} catch (Exception $e) {
    echo "Migration failed: " . $e->getMessage() . "\n";
    exit(1);
}
