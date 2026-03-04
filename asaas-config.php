<?php
/**
 * Asaas API Configuration
 * 
 * Replace YOUR_API_KEY with your actual Asaas API Key.
 * Use Sandbox for testing and Production for live payments.
 */

// Configuration
define('ASAAS_ENVIRONMENT', 'sandbox'); // 'sandbox' or 'production'
define('ASAAS_API_KEY', '$aact_hmlg_000MzkwODA2MWY2OGM3MWRlMDU2NWM3MzJlNzZmNGZhZGY6OjdjZjFlOGZkLTFjNmYtNGUxNC05N2VlLWJlOTBlMTY5Yzc3Mjo6JGFhY2hfMTg3ZDUxOTAtNTdlMS00NTU5LWFmMjYtNDBiZTBhZjM2ZWFh'); // Updated with your key

// API URLs
if (ASAAS_ENVIRONMENT === 'production') {
    define('ASAAS_API_URL', 'https://www.asaas.com/api/v3');
} else {
    define('ASAAS_API_URL', 'https://sandbox.asaas.com/api/v3');
}

/**
 * Utility function to make Asaas API requests
 */
function asaasRequest($endpoint, $method = 'GET', $data = null) {
    $url = ASAAS_API_URL . $endpoint;
    $ch = curl_init();

    $headers = [
        'Content-Type: application/json',
        'access_token: ' . ASAAS_API_KEY
    ];

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    if ($data && ($method === 'POST' || $method === 'PUT')) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    }

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    return [
        'code' => $httpCode,
        'response' => json_decode($response, true)
    ];
}
?>
