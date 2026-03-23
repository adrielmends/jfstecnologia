<?php
/**
 * Payment Router Component
 * Decides which payment engine to use based on the channel.
 */

require_once __DIR__ . '/asaas-checkout.php';

class PaymentRouter {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    /**
     * Route and initiate payment
     * 
     * @param int $orderId
     * @param array $orderData
     * @return array Result with payment metadata
     */
    public function initiatePayment($orderId, $orderData) {
        $channel = $orderData['channel'] ?? 'WEB';
        $amount = (float)$orderData['total'];

        if ($channel === 'LOCKER') {
            return $this->handleLockerPayment($orderId, $amount, $orderData);
        } else {
            return $this->handleWebPayment($orderId, $amount, $orderData);
        }
    }

    /**
     * Handle Online/Web Payment (Asaas)
     */
    private function handleWebPayment($orderId, $amount, $orderData) {
        // 1. Prepare/Get Asaas Customer
        $customerInfo = [
            'name' => $orderData['name'] ?? 'Cliente WEB',
            'email' => $orderData['email'] ?? 'vendas@exenvios.com.br',
            'cpfCnpj' => $orderData['cpf'] ?? '',
            'phone' => $orderData['phone'] ?? ''
        ];

        $customerId = getOrCreateAsaasCustomer($customerInfo);
        if (!$customerId) {
            throw new Exception('Erro ao sincronizar cliente com gateway de pagamento');
        }

        // 2. Prepare Payment Data
        $billingType = $orderData['billingType'] ?? 'PIX'; // PIX, CREDIT_CARD, BOLETO
        
        $paymentParams = [
            'customerId' => $customerId,
            'billingType' => $billingType,
            'value' => $amount,
            'description' => 'Envio Ex-Envios: ' . ($orderData['service'] ?? 'Serviço'),
            'externalReference' => 'ORD_' . $orderId,
            'customerName' => $customerInfo['name'],
            'customerEmail' => $customerInfo['email'],
            'customerCpf' => $customerInfo['cpfCnpj'],
            'customerPhone' => $customerInfo['phone'],
            'remoteIp' => $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1'
        ];

        if ($billingType === 'CREDIT_CARD' && isset($orderData['creditCard'])) {
            $cc = $orderData['creditCard'];
            $paymentParams['creditCard'] = [
                'number' => $cc['number'] ?? '',
                'holderName' => $cc['holderName'] ?? ($orderData['name'] ?? ''),
                'expiryMonth' => $cc['expiryMonth'] ?? '',
                'expiryYear' => $cc['expiryYear'] ?? '',
                'ccv' => $cc['cvv'] ?? ''
            ];
            $paymentParams['holderName'] = $paymentParams['creditCard']['holderName'];
        }

        // 3. Create Asaas Payment
        $result = createAsaasPayment($paymentParams);

        if (!$result['ok']) {
            throw new Exception('Erro gateway: ' . $result['error']);
        }

        // 4. Register in payments table
        $this->registerPayment($orderId, 'ASAAS', $billingType, $result['data']['id'], 'PENDING', $amount);

        // 5. Update order with asaas_id (legacy support)
        $stmt = $this->db->prepare("UPDATE orders SET asaas_id = ? WHERE id = ?");
        $stmt->execute([$result['data']['id'], $orderId]);

        $responseData = [
            'type' => 'WEB_' . $billingType,
            'paymentId' => $result['data']['id']
        ];

        if ($billingType === 'PIX') {
            $responseData['pix'] = $result['data']['pix'] ?? null;
        } elseif ($billingType === 'BOLETO') {
            $responseData['bankSlipUrl'] = $result['data']['bankSlipUrl'] ?? null;
        }

        return $responseData;
    }

    /**
     * Handle Locker/Physical Payment (PIN pad)
     */
    private function handleLockerPayment($orderId, $amount, $orderData) {
        // For Locker, we don't call a gateway immediately.
        // We register a pending PIN pad payment.
        
        $paymentId = $this->registerPayment(
            $orderId, 
            'STONE', // Default adquirente for PIN pad
            'CARD_PINPAD', 
            null, 
            'PENDING', 
            $amount
        );

        return [
            'type' => 'LOCKER_PINPAD',
            'paymentId' => $paymentId,
            'amount' => $amount
        ];
    }

    /**
     * Record payment attempt
     */
    private function registerPayment($order_id, $provider, $method, $ref, $status, $amount) {
        // Migration: ensure 'method' column is VARCHAR(50) and not a restrictive ENUM
        static $migrated = false;
        if (!$migrated) {
            try {
                $this->db->exec("ALTER TABLE `payments` MODIFY COLUMN `method` VARCHAR(50) DEFAULT NULL");
                $migrated = true;
            } catch (Exception $e) { /* Ignore if already VARCHAR or permission issues */ }
        }

        try {
            $stmt = $this->db->prepare("INSERT INTO payments (
                order_id, provider, method, provider_reference, status, amount
            ) VALUES (?, ?, ?, ?, ?, ?)");
            
            $stmt->execute([$order_id, $provider, $method, $ref, $status, $amount]);
            return $this->db->lastInsertId();
        } catch (PDOException $e) {
            // On failure, grab the exact column definition to report back.
            $debugInfo = "Method: {$method}";
            try {
                $q = $this->db->query("DESCRIBE payments");
                $schema = $q->fetchAll(PDO::FETCH_ASSOC);
                foreach ($schema as $col) {
                    if ($col['Field'] === 'method') {
                        $debugInfo .= " | ColType: " . print_r($col['Type'], true);
                    }
                }
            } catch (Exception $ex) {
                // Ignore schema fetch errors
            }
            throw new Exception("DB Error no registerPayment: " . $e->getMessage() . " | " . $debugInfo);
        }
    }
}
