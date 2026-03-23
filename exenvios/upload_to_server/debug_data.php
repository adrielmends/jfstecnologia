<?php
require_once 'config.php';
$db = getDB();

echo "--- USERS ---\n";
$users = $db->query("SELECT id, name, email, cpf FROM users")->fetchAll();
foreach ($users as $u) {
    echo "ID: {$u['id']} | Name: {$u['name']} | Email: {$u['email']} | CPF: {$u['cpf']}\n";
}

echo "\n--- RECENT ORDERS ---\n";
$orders = $db->query("SELECT id, external_ref, customer_name, customer_email, customer_cpf, status FROM orders ORDER BY created_at DESC LIMIT 20")->fetchAll();
foreach ($orders as $o) {
    echo "ID: {$o['id']} | Ref: {$o['external_ref']} | Name: {$o['customer_name']} | Email: {$o['customer_email']} | CPF: {$o['customer_cpf']} | Status: {$o['status']}\n";
}
