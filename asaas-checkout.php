<?php
require_once 'asaas-customer.php';

/**
 * Creates a payment in Asaas
 * 
 * @param array $paymentData [customerId, billingType, value, description]
 * @return array [ok => bool, response => array, error => string]
 */
function createAsaasPayment($paymentData) {
    $payload = [
        'customer' => $paymentData['customerId'],
        'billingType' => $paymentData['billingType'], // PIX, CREDIT_CARD, BOLETO
        'value' => (float)$paymentData['value'],
        'dueDate' => date('Y-m-d', strtotime('+3 days')),
        'description' => $paymentData['description'] ?? 'Depósito em conta Ex-Envios',
        'externalReference' => $paymentData['externalReference'] ?? uniqid('DEP_'),
        'remoteIp' => $paymentData['remoteIp'] ?? ($_SERVER['REMOTE_ADDR'] ?? '127.0.0.1')
    ];

    // Handle Credit Card specifics
    if ($paymentData['billingType'] === 'CREDIT_CARD' && isset($paymentData['creditCard'])) {
        $payload['creditCard'] = $paymentData['creditCard'];
        $payload['creditCardHolderInfo'] = [
            'name' => $paymentData['creditCard']['holderName'] ?? ($paymentData['customerName'] ?? ''),
            'email' => $paymentData['customerEmail'] ?? '',
            'cpfCnpj' => $paymentData['customerCpf'] ?? '',
            'postalCode' => '79002010', // More specific Campo Grande CEP
            'addressNumber' => 'SN',
            'phone' => preg_replace('/\D/', '', $paymentData['customerPhone'] ?? '') ?: '67999999999'
        ];
    }

    $response = asaasRequest('/payments', 'POST', $payload);

    if (in_array($response['code'], [200, 201])) {
        $paymentDataResult = $response['response'];
        $paymentId = $paymentDataResult['id'];
        
        // If PIX, get the QR Code
        if ($paymentData['billingType'] === 'PIX') {
            $pixResponse = asaasRequest('/payments/' . $paymentId . '/pixQrCode');
            $paymentDataResult['pix'] = $pixResponse['response'];
        }

        return ['ok' => true, 'data' => $paymentDataResult];
    }

    $errors = $response['response']['errors'] ?? [];
    $errorDesc = !empty($errors) ? $errors[0]['description'] : ($response['response']['description'] ?? 'Erro desconhecido');
    
    // Add debug info to help understand why CVV is "missing"
    $debugInfo = " | Payload Enviado: " . json_encode($payload);
    
    return ['ok' => false, 'error' => $errorDesc . " (HTTP " . $response['code'] . ")" . $debugInfo];
}

// Example AJAX Handler
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'generate_pix') {
    // This is where you would get user data from session
    $userData = [
        'name' => $_POST['userName'],
        'email' => $_POST['userEmail'],
        'cpfCnpj' => $_POST['userCpf'],
        'phone' => $_POST['userPhone']
    ];

    $customerId = getOrCreateAsaasCustomer($userData);

    if ($customerId) {
        $payment = createAsaasPayment([
            'customerId' => $customerId,
            'billingType' => 'PIX',
            'value' => $_POST['amount'],
            'description' => 'Recarga de Saldo - Ex-Envios'
        ]);

        header('Content-Type: application/json');
        echo json_encode($payment);
        exit;
    }

    echo json_encode(['ok' => false, 'error' => 'Falha ao processar cliente no Asaas.']);
    exit;
}
?>
