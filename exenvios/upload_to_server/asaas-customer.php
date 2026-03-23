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

    // Asaas normally returns 200 or 201 on success
    if (in_array($createResponse['code'], [200, 201]) && isset($createResponse['response']['id'])) {
        return $createResponse['response']['id'];
    }

    // Improved error message extraction
    $errors = $createResponse['response']['errors'] ?? [];
    if (!empty($errors)) {
        $errorMsg = $errors[0]['description'] ?? 'Erro sem descrição no array de erros';
    } else {
        $errorMsg = $createResponse['response']['description'] ?? ($createResponse['raw'] ?: 'Resposta vazia do Asaas');
    }
    
    $errorCode = $createResponse['code'] ?? 'N/A';
    
    error_log("Asaas Customer Creation Error (Code $errorCode): " . json_encode($createResponse));
    
    // If it's a 400, it might be a specific business rule (invalid CPF, etc)
    throw new Exception("Erro Asaas (HTTP $errorCode) ao criar cliente: " . $errorMsg);
}
?>
