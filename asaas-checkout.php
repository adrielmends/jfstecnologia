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
        'externalReference' => $paymentData['externalReference'] ?? uniqid('DEP_')
    ];

    $response = asaasRequest('/payments', 'POST', $payload);

    if ($response['code'] === 200) {
        $paymentId = $response['response']['id'];
        
        // If PIX, get the QR Code
        if ($paymentData['billingType'] === 'PIX') {
            $pixResponse = asaasRequest('/payments/' . $paymentId . '/pixQrCode');
            $response['response']['pix'] = $pixResponse['response'];
        }

        return ['ok' => true, 'data' => $response['response']];
    }

    return ['ok' => false, 'error' => $response['response']['errors'][0]['description'] ?? 'Erro desconhecido ao criar pagamento'];
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
