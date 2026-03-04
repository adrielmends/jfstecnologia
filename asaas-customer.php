<?php
require_once 'asaas-config.php';

/**
 * Finds or creates a customer in Asaas
 * 
 * @param array $userData [name, email, cpfCnpj, phone]
 * @return string|false Customer ID from Asaas
 */
function getOrCreateAsaasCustomer($userData) {
    // 1. Try to find by CPF/CNPJ
    $cpfCnpj = preg_replace('/\D/', '', $userData['cpfCnpj']);
    $response = asaasRequest('/customers?cpfCnpj=' . $cpfCnpj);
    
    if ($response['code'] === 200 && !empty($response['response']['data'])) {
        return $response['response']['data'][0]['id'];
    }

    // 2. If not found, create new customer
    $newCustomer = [
        'name' => $userData['name'],
        'email' => $userData['email'],
        'cpfCnpj' => $cpfCnpj,
        'mobilePhone' => preg_replace('/\D/', '', $userData['phone'] ?? ''),
        'notificationDisabled' => true // We follow up on the site
    ];

    $createResponse = asaasRequest('/customers', 'POST', $newCustomer);

    if ($createResponse['code'] === 200 && isset($createResponse['response']['id'])) {
        return $createResponse['response']['id'];
    }

    error_log("Asaas Customer Creation Error: " . json_encode($createResponse));
    return false;
}
?>
