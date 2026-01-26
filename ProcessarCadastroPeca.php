<?php
session_start();
include 'conexao.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitização básica
    $nome = $conn->real_escape_string($_POST['nome']);
    $preco = $_POST['preco'];
    $quantidade_inicial = intval($_POST['quantidade']);
    $id_fornecedor = $_POST['id_fornecedor'];
    $data_atual = date('Y-m-d H:i:s');

    // Inicia uma transação para garantir que ou grava tudo ou não grava nada
    $conn->begin_transaction();

    try {
        // 1. Insere a peça na tabela principal
        $sql_peca = "INSERT INTO pecas (nome, preco, quantidade, id_fornecedor) 
                     VALUES ('$nome', '$preco', '$quantidade_inicial', '$id_fornecedor')";
        $conn->query($sql_peca);
        
        // Pega o ID da peça que acabou de ser criada
        $id_nova_peca = $conn->insert_id;

        // 2. Registra a entrada no histórico de movimentação
        $sql_mov = "INSERT INTO movimentacao_estoque (id_peca, quantidade, tipo, data_movimentacao) 
                    VALUES ('$id_nova_peca', '$quantidade_inicial', 'ENTRADA', '$data_atual')";
        $conn->query($sql_mov);

        // Se chegou aqui, confirma as duas gravações no banco
        $conn->commit();
        echo "<script>alert('Produto cadastrado e estoque inicial registrado!'); window.location.href='estoquista.php';</script>";

    } catch (Exception $e) {
        // Se algo der errado (ex: erro de chave estrangeira), cancela tudo
        $conn->rollback();
        echo "Erro ao processar cadastro: " . $conn->error;
    }
}
?>