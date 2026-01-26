<?php
include 'conexao.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // 1. Deletar dependências para o banco não travar
    $conn->query("DELETE FROM movimentacao_estoque WHERE id_peca = $id");
    $conn->query("DELETE FROM itens_venda WHERE id_peca = $id");
    $conn->query("DELETE FROM itens_pedido_compra WHERE id_peca = $id");

    // 2. Agora deletar a peça
    $sql = "DELETE FROM pecas WHERE id_peca = $id";

    if ($conn->query($sql)) {
        header("Location: estoquista.php?status=sucesso_excluir");
    } else {
        echo "Erro ao excluir: " . $conn->error;
    }
}
?>