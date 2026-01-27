<?php
session_start();
include 'conexao.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // 1. Captura os dados enviados pelo formulário (incluindo os novos campos)
    $id_peca = $_POST['id_peca'];
    $id_fornecedor = $_POST['id_fornecedor'];
    $quantidade = $_POST['quantidade'];
    
    // CAPTURANDO OS NOVOS VALORES QUE VIERAM DO FORMULÁRIO
    $valor_unitario = $_POST['valor_unitario']; 
    $valor_total_compra = $_POST['valor_total']; 

    $id_resp = $_SESSION['id_usuario'];
    $data_hoje = date('Y-m-d H:i:s');

    // 2. O INSERT agora inclui as novas colunas
    // Certifique-se de que os nomes (valor_unitario, valor_total_compra) são iguais aos do seu Banco de Dados
    $sql = "INSERT INTO pedido_compra 
            (id_responsavel_estoque, id_peca, id_fornecedor, quantidade, valor_unitario, valor_total_compra, data_pedido, status) 
            VALUES 
            ('$id_resp', '$id_peca', '$id_fornecedor', '$quantidade', '$valor_unitario', '$valor_total_compra', '$data_hoje', 'PENDENTE')";

    if ($conn->query($sql)) {
        echo "<script>alert('Pedido de R$ " . number_format($valor_total_compra, 2, ',', '.') . " enviado para análise!'); window.location.href='estoquista.php';</script>";
    } else {
        echo "Erro ao processar pedido: " . $conn->error;
    }
}
?>