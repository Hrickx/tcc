<?php
// 1. Configurações de tempo (Sempre antes de tudo)
ini_set('session.gc_maxlifetime', 28800);
ini_set('session.cookie_lifetime', 28800);

// 2. Inicia a sessão com as novas configurações
session_start();

// 3. Inclui a conexão com o banco
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