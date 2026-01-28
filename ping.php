<?php
// 1. Configurações de tempo (Sempre antes de tudo)
ini_set('session.gc_maxlifetime', 28800);
ini_set('session.cookie_lifetime', 28800);

// 2. Inicia a sessão com as novas configurações
session_start();

// 3. Inclui a conexão com o banco
include 'conexao.php';

header('Content-Type: application/json');

if (isset($_SESSION['id_usuario'])) {
    echo json_encode(['status' => 'online', 'user' => $_SESSION['nome']]);
} else {
    // Se a sessão caiu, avisamos o JS sem dar reload na página
    http_response_code(401);
    echo json_encode(['status' => 'offline']);
}
?>