<?php
session_start();
include 'conexao.php';

// Segurança: Só o gerente pode executar essa ação
if (!isset($_SESSION['id_usuario']) || $_SESSION['perfil'] !== 'GERENTE') {
    die("Acesso negado.");
}

if (isset($_GET['id']) && isset($_GET['novo_status'])) {
    $id = intval($_GET['id']);
    $novo_status = $conn->real_escape_string($_GET['novo_status']);

    // O SQL atualiza o campo status (certifique-se que sua tabela usuários tem essa coluna)
    $sql = "UPDATE usuarios SET status_usuario = '$novo_status' WHERE id_usuario = $id";
    
    if ($conn->query($sql)) {
        header("Location: UsuariosGestao.php?sucesso=1");
    } else {
        echo "Erro ao atualizar status: " . $conn->error;
    }
}
?>