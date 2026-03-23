<?php
session_start();
// Basic security check: Admin only
if (!isset($_SESSION['user_id']) || ($_SESSION['user_role'] ?? '') !== 'admin') {
    http_response_code(403);
    echo json_encode(['error' => 'Acesso negado.']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Método não permitido.']);
    exit;
}

$kbContent = $_POST['kb_content'] ?? null;
$faqContent = $_POST['faq_content'] ?? null;

if ($kbContent === null && $faqContent === null) {
    echo json_encode(['error' => 'Nenhum conteúdo enviado.']);
    exit;
}

$dataDir = __DIR__ . '/../data';
$kbPath = $dataDir . '/knowledge_base.txt';
$faqPath = $dataDir . '/faq.txt';

// Ensure the directory exists
if (!is_dir($dataDir)) {
    mkdir($dataDir, 0755, true);
}

$success = true;
$errors = [];

// Save KB
if ($kbContent !== null) {
    if (file_put_contents($kbPath, $kbContent) === false) {
        $success = false;
        $errors[] = "Erro ao salvar Base de Conhecimento.";
    }
}

// Save FAQ
if ($faqContent !== null) {
    if (file_put_contents($faqPath, $faqContent) === false) {
        $success = false;
        $errors[] = "Erro ao salvar FAQ.";
    }
}

if ($success) {
    echo json_encode(['success' => 'Configurações de IA e FAQ atualizadas com sucesso!']);
} else {
    http_response_code(500);
    echo json_encode(['error' => implode(' | ', $errors)]);
}
?>
