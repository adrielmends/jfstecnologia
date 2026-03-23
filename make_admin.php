<?php
/**
 * Script utilitário para promover um usuário a ADMIN.
 * Delete este arquivo após o uso por segurança!
 */

require_once 'config.php';

// EDITE O EMAIL ABAIXO:
$emailAdmin = 'seu-email@aqui.com'; 

if ($emailAdmin === 'seu-email@aqui.com') {
    die("Por favor, abra este arquivo (make_admin.php) e coloque o seu email na linha 9.");
}

try {
    $db = getDB();
    $stmt = $db->prepare("UPDATE users SET role = 'admin' WHERE email = ?");
    $stmt->execute([$emailAdmin]);

    if ($stmt->rowCount() > 0) {
        echo "Sucesso! O usuário $emailAdmin agora é um ADMINISTRADOR.<br>";
        echo "Você já pode acessar o admin.php após fazer login.<br>";
        echo "<strong>IMPORTANTE: Delete este arquivo (make_admin.php) do seu servidor agora!</strong>";
    } else {
        echo "Erro: Usuário não encontrado. Você já se cadastrou no site com este email?";
    }
} catch (Exception $e) {
    echo "Erro no banco de dados: " . $e->getMessage();
}
?>
