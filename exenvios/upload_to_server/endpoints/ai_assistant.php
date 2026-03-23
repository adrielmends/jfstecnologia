<?php
session_start();
require_once '../config.php';

if (!isset($_SESSION['user_id'])) {
    jsonResponse(false, null, 'Não autorizado');
}

$input = json_decode(file_get_contents('php://input'), true);
$userMsg = $input['message'] ?? '';

if (empty($userMsg)) {
    jsonResponse(false, null, 'Mensagem vazia');
}

// Load context
$kbPath = '../data/knowledge_base.txt';
$faqPath = '../data/faq.txt';
$kbContent = file_exists($kbPath) ? file_get_contents($kbPath) : 'Informações da empresa Ex-Envios.';
$faqContent = file_exists($faqPath) ? file_get_contents($faqPath) : '';

// Prepare context for the prompt
$systemInstruction = "Você é o 'Robô Ex-Envios', o assistente virtual inteligente oficial da plataforma Ex-Envios.
Seu objetivo é ajudar os clientes com dúvidas sobre fretes, pagamentos e o uso da plataforma.
Utilize o seguinte contexto para basear suas respostas. Se não souber a resposta, peça para o usuário contatar o suporte humano no WhatsApp (disponível no menu Ajuda).
Seja sempre cordial, profissional e direto.

BASE DE CONHECIMENTO:
$kbContent

PERGUNTAS FREQUENTES (FAQ):
$faqContent";

// Updated to use models/gemini-flash-latest (as confirmed in ListModels)
$url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-flash-latest:generateContent?key=" . GEMINI_API_KEY;

$data = [
    "system_instruction" => [
        "parts" => [
            ["text" => $systemInstruction]
        ]
    ],
    "contents" => [
        [
            "role" => "user",
            "parts" => [
                ["text" => $userMsg]
            ]
        ]
    ],
    "generationConfig" => [
        "temperature" => 0.7,
        "maxOutputTokens" => 1000
    ]
];

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlError = curl_error($ch);
curl_close($ch);

if ($curlError) {
    jsonResponse(false, null, 'Erro de conexão: ' . $curlError);
}

if ($httpCode !== 200) {
    $errData = json_decode($response, true);
    $errMsg = $errData['error']['message'] ?? 'Erro desconhecido na API';
    jsonResponse(false, null, 'Erro ' . $httpCode . ': ' . $errMsg);
}

$result = json_decode($response, true);
$aiText = $result['candidates'][0]['content']['parts'][0]['text'] ?? '';

if (empty($aiText)) {
    jsonResponse(false, null, 'IA retornou resposta vazia.');
}

jsonResponse(true, ['response' => trim($aiText)]);
