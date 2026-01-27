<?php
// Impede que o PHP mostre erros de texto que estraguem o JSON
error_reporting(0); 
header('Content-Type: application/json');

include 'conexao.php';

if (isset($_GET['id_peca'])) {
    $id = intval($_GET['id_peca']);
    
    // Consulta ao banco
    $sql = "SELECT preco, nome FROM pecas WHERE id_peca = $id";
    $res = $conn->query($sql);

    if ($res && $res->num_rows > 0) {
        $dados = $res->fetch_assoc();
        echo json_encode($dados);
    } else {
        echo json_encode(['erro' => 'Peça não encontrada']);
    }
}
exit; // Garante que nada mais seja impresso
?>