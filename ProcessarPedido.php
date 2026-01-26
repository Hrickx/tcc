<?php
session_start();
include 'conexao.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // 1. Captura os dados do formulário do estoquista
    $id_peca = $_POST['id_peca'];
    $id_fornecedor = $_POST['id_fornecedor'];
    $quantidade = $_POST['quantidade'];
    $tempo_entrega = $_POST['tempo_entrega'];
    $id_resp = $_SESSION['id_usuario']; // Padronizado conforme login
    $data_hoje = date('Y-m-d H:i:s');

    // 2. Busca o preço de custo da peça para calcular o valor total do pedido
    $sql_preco = "SELECT preco_custo FROM pecas WHERE id_peca = '$id_peca'";
    $res_preco = $conn->query($sql_preco);
    $peca_info = $res_preco->fetch_assoc();
    
    $preco_unitario = $peca_info['preco_custo'] ?? 0;
    $valor_total_compra = $preco_unitario * $quantidade;

    // 3. Insere o pedido com status 'PENDENTE'
    // Note que incluí as colunas que o Gerente vai precisar ver depois
   // O INSERT agora usa apenas o que é essencial
$sql = "INSERT INTO pedido_compra 
        (id_responsavel_estoque, id_peca, id_fornecedor, quantidade, valor_total_compra, data_pedido, status) 
        VALUES 
        ('$id_resp', '$id_peca', '$id_fornecedor', '$quantidade', '$valor_total_compra', '$data_hoje', 'PENDENTE')";

    if ($conn->query($sql)) {
        echo "<script>alert('Pedido enviado para análise do gerente!'); window.location.href='estoquista.php';</script>";
    } else {
        echo "Erro ao processar pedido: " . $conn->error;
    }
}
?>