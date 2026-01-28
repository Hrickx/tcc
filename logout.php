<?php
// 1. Configurações de tempo (Sempre antes de tudo)
ini_set('session.gc_maxlifetime', 28800);
ini_set('session.cookie_lifetime', 28800);

// 2. Inicia a sessão com as novas configurações
session_start();

// 3. Inclui a conexão com o banco
include 'conexao.php';
// 2. Limpa todas as variáveis de sessão
session_unset();

// 3. Destrói a sessão completamente
session_destroy();

// 4. Redireciona o usuário para a tela de login
header("Location: login.php");
exit();
?>