<?php
/**
 * Migration 002: Add Scheduling Columns
 * Adds scheduled_date and scheduled_time to orders.
 */

require_once __DIR__ . '/../config.php';

try {
    $db = getDB();

    echo "Starting migration 002...\n";

    // 1. Add columns to orders table
    echo "Updating 'orders' table...\n";
    $db->exec("ALTER TABLE orders 
        ADD COLUMN scheduled_date DATE NULL AFTER service,
        ADD COLUMN scheduled_time TIME NULL AFTER scheduled_date");

    echo "Migration 002 completed successfully!\n";

} catch (Exception $e) {
    echo "Migration 002 failed: " . $e->getMessage() . "\n";
    exit(1);
}
