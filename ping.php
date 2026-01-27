<?php
session_start();
header('Content-Type: application/json');

if (isset($_SESSION['id_usuario'])) {
    echo json_encode(['status' => 'online', 'user' => $_SESSION['nome']]);
} else {
    // Se a sessão caiu, avisamos o JS sem dar reload na página
    http_response_code(401);
    echo json_encode(['status' => 'offline']);
}
?>