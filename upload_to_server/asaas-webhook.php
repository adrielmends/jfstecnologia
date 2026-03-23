<?php
/**
 * Asaas Webhook Handler
 * 
 * Configure this URL in your Asaas Panel: Settings > Integrations > Webhooks
 */

require_once 'asaas-config.php';
require_once 'config.php';

// 1. Get the payload
$payload = file_get_contents('php://input');
$event = json_decode($payload, true);

if (!$event) {
    http_response_code(400);
    exit('Invalid Payload');
}

// 2. Security Check (Optional: Asaas supports a Secret Token if configured)
// $headers = getallheaders();
// if ($headers['asaas-access-token'] !== 'YOUR_WEBHOOK_SECRET') { exit; }

// 3. Process Events
switch ($event['event']) {
    case 'PAYMENT_RECEIVED':
    case 'PAYMENT_CONFIRMED':
        $payment = $event['payment'];
        $asaasId = $payment['id'];
        $externalReference = $payment['externalReference'];

        try {
            $db = getDB();
            
            // 1. Update Payments table
            $stmt = $db->prepare("UPDATE payments SET status = 'PAID', raw_payload = ? WHERE provider_reference = ?");
            $stmt->execute([json_encode($event), $asaasId]);

            // 2. Update Orders table (Dual check for backward compatibility)
            $stmt = $db->prepare("UPDATE orders SET status = 'paid', confirmed_at = NOW() WHERE asaas_id = ? OR external_ref = ?");
            $stmt->execute([$asaasId, $externalReference]);
            
            error_log("Payment Confirmed: Ref $externalReference, AsaasID $asaasId");
        } catch (Exception $e) {
            error_log("Webhook DB Error: " . $e->getMessage());
        }
        break;

    case 'PAYMENT_OVERDUE':
        // Payment expired
        break;
}

// Always return 200 OK to Asaas
http_response_code(200);
echo "OK";
?>
