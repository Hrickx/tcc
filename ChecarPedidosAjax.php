
<?php
session_start();
// checarpedidosajax.php
include 'conexao.php';

// Definimos que este arquivo responde apenas com JSON (importante para o JavaScript)
header('Content-Type: application/json');

// No checarpedidosajax.php, mude o INTERVAL para 30 segundos
$sql = "SELECT pc.id_pedido, u.nome as solicitante, pec.nome as peca_nome 
        FROM pedido_compra pc
        JOIN usuarios u ON pc.id_responsavel_estoque = u.id_usuario
        JOIN pecas pec ON pc.id_peca = pec.id_peca
        WHERE pc.status = 'PENDENTE' 
        AND pc.data_pedido >= NOW() - INTERVAL 30 SECOND 
        LIMIT 1";

try {
    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        $pedido = $result->fetch_assoc();
        echo json_encode([
            'tem_novo' => true, 
            'solicitante' => $pedido['solicitante'], 
            'peca' => $pedido['peca_nome']
        ]);
    } else {
        echo json_encode(['tem_novo' => false]);
    }
} catch (Exception $e) {
    echo json_encode(['tem_novo' => false, 'erro' => $e->getMessage()]);
}
?>