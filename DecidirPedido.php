<?php
// Força o PHP a manter a sessão por 8 horas antes de começar
ini_set('session.gc_maxlifetime', 28800);
ini_set('session.cookie_lifetime', 28800);
session_start();

include 'conexao.php';

// Bloqueio de segurança: Se a sessão sumir por um milissegundo, 
// este bloco garante que o erro seja tratado antes de redirecionar sem motivo.
if (!isset($_SESSION['id_usuario'])) {
    if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') {
        // Se for uma requisição AJAX (como o ping ou busca), avisa o JS em vez de dar reload
        header('HTTP/1.1 401 Unauthorized');
        exit;
    } else {
        header("Location: login.php");
        exit();
    }
}

// Definimos que a resposta é JSON logo no início
header('Content-Type: application/json');

// Proteção: Apenas Gerente pode processar
if (!isset($_SESSION['id_usuario']) || $_SESSION['perfil'] !== 'GERENTE') {
    echo json_encode(['success' => false, 'error' => 'Acesso negado.']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_pedido = intval($_POST['id_pedido']);
    $acao = $_POST['acao']; 
    $justificativa = isset($_POST['justificativa']) ? $conn->real_escape_string($_POST['justificativa']) : '';
    $data_decisao = date('Y-m-d H:i:s');

    if ($acao === 'APROVADO') {
        $sql = "UPDATE pedido_compra SET 
                status = 'APROVADO', 
                justificativa = NULL,
                data_pedido = '$data_decisao'
                WHERE id_pedido = $id_pedido";
        $msg = "Pedido aprovado com sucesso!";
    } else {
        $sql = "UPDATE pedido_compra SET 
                status = 'REJEITADO', 
                justificativa = '$justificativa',
                data_pedido = '$data_decisao'
                WHERE id_pedido = $id_pedido";
        $msg = "Pedido rejeitado com sucesso!";
    }

    if ($conn->query($sql) === TRUE) {
        echo json_encode(['success' => true, 'message' => $msg]);
    } else {
        echo json_encode(['success' => false, 'error' => $conn->error]);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Método inválido.']);
}
exit;