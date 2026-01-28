<?php
   // 1. Configurações de tempo (Sempre antes de tudo)
ini_set('session.gc_maxlifetime', 28800);
ini_set('session.cookie_lifetime', 28800);

// 2. Inicia a sessão com as novas configurações
session_start();

// 3. Inclui a conexão com o banco
include 'conexao.php';

    header('Content-Type: application/json');

    // Verificação silenciosa de sessão
    if (!isset($_SESSION['id_usuario'])) {
    http_response_code(401); // Informa ao navegador que não está autorizado
    echo json_encode(['tem_novo' => false, 'erro' => 'sessao_expirada']);
    exit; // Mata o script aqui
    }

    // Buscamos TODOS os pendentes para atualizar o contador e a lista
    $sql = "SELECT pc.id_pedido, u.nome as solicitante, pec.nome as peca_nome, pc.observacao
            FROM pedido_compra pc
            JOIN usuarios u ON pc.id_responsavel_estoque = u.id_usuario
            JOIN pecas pec ON pc.id_peca = pec.id_peca
            WHERE pc.status = 'PENDENTE' 
            ORDER BY pc.id_pedido DESC";

    $result = $conn->query($sql);
    $pedidos = [];
    while($row = $result->fetch_assoc()) {
        $pedidos[] = $row;
    }

    echo json_encode([
        'tem_novo' => count($pedidos) > 0,
        'total' => count($pedidos),
        'pedidos' => $pedidos
    ]);
