<?php
session_start();
require_once '../config.php';

// Temporary debug code to see why models are giving 404
$url = "https://generativelanguage.googleapis.com/v1beta/models?key=" . GEMINI_API_KEY;

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode !== 200) {
    jsonResponse(false, null, "Debug Error $httpCode: $response");
}

$data = json_decode($response, true);
$modelList = [];
if (isset($data['models'])) {
    foreach ($data['models'] as $m) {
        $modelList[] = $m['name'];
    }
}

jsonResponse(true, ['response' => "Modelos disponíveis para sua chave: " . implode(', ', $modelList)]);
