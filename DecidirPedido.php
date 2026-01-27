<?php
session_start();
include 'conexao.php';

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