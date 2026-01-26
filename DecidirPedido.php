<?php
session_start();
include 'conexao.php';

// Proteção: Apenas Gerente pode processar essa decisão
if (!isset($_SESSION['id_usuario']) || $_SESSION['perfil'] !== 'GERENTE') {
    die("Acesso negado.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_pedido = intval($_POST['id_pedido']); // Segurança extra com intval
    $acao = $_POST['acao']; // 'APROVAR' ou 'REJEITAR'
    $justificativa = isset($_POST['justificativa']) ? $_POST['justificativa'] : '';
    $data_decisao = date('Y-m-d H:i:s');

    // Sanitização para evitar SQL Injection
    $justificativa = $conn->real_escape_string($justificativa);

    if ($acao === 'APROVADO' || $acao === 'APROVAR') {
        // Status deve ser 'APROVADO' para bater com o que o estoquista.php espera
        $sql = "UPDATE pedido_compra SET 
                status = 'APROVADO', 
                justificativa = NULL,
                data_pedido = '$data_decisao'
                WHERE id_pedido = '$id_pedido'";
        $msg = "Pedido aprovado com sucesso!";
    } else {
        $sql = "UPDATE pedido_compra SET 
                status = 'REJEITADO', 
                justificativa = '$justificativa',
                data_pedido = '$data_decisao'
                WHERE id_pedido = '$id_pedido'";
        $msg = "Pedido rejeitado e justificativa enviada!";
    }

    if ($conn->query($sql) === TRUE) {
        // O gerente volta para a sua tela principal
        echo "<script>alert('$msg'); window.location.href='gerente.php';</script>";
    } else {
        echo "Erro ao processar: " . $conn->error;
    }
}
?>