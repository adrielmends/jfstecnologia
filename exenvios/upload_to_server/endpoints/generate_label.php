<?php
/**
 * Generate Label Endpoint (Placeholder)
 */

require_once '../config.php';

$id = $_GET['id'] ?? null;

if (!$id) {
    die('ID do pedido não fornecido.');
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>Etiqueta de Envio</title>
    <style>
        body { font-family: sans-serif; display: flex; flex-direction: column; align-items: center; justify-content: center; height: 100vh; background: #f7fafc; }
        .label-sim { border: 2px dashed #cbd5e0; padding: 40px; border-radius: 12px; background: white; text-align: center; max-width: 400px; }
        h1 { color: #002b49; }
        button { background: #FF6600; color: white; border: none; padding: 10px 20px; border-radius: 6px; cursor: pointer; margin-top: 20px; }
    </style>
</head>
<body>
    <div class="label-sim">
        <h1>Simulação de Etiqueta</h1>
        <p>A etiqueta do pedido <strong>#<?php echo htmlspecialchars($id); ?></strong> está sendo processada.</p>
        <p style="color: #718096;">Em um ambiente de produção real, aqui seria gerado o PDF da etiqueta (ZPL/PDF).</p>
        <button onclick="window.close()">Fechar Janela</button>
    </div>
</body>
</html>
