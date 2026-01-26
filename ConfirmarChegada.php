<?php
session_start();
include 'conexao.php';

// Ativa exceções para o MySQLi
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

if (!isset($_SESSION['id_usuario'])) {
    die("Acesso negado.");
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_pedido = intval($_POST['id_pedido']);
    $data_hoje = date('Y-m-d H:i:s');

    // 1. Busca os dados e verifica se o pedido realmente está pronto para ser recebido
    $sql_busca = "SELECT id_peca, quantidade, status FROM pedido_compra WHERE id_pedido = '$id_pedido'";
    $res = $conn->query($sql_busca);
    $dados = $res->fetch_assoc();

    if ($dados) {
        // Só permite processar se o pedido estiver APROVADO. 
        // Se já estiver CONCLUÍDO, não faz nada (evita estoque duplicado).
        if ($dados['status'] !== 'APROVADO') {
            echo "<script>alert('Este pedido não pode ser recebido (Status: " . $dados['status'] . ")'); window.location.href='estoquista.php';</script>";
            exit();
        }

        $id_peca = $dados['id_peca'];
        $quantidade = $dados['quantidade'];

        $conn->begin_transaction();

        try {
            // 2. Registra a ENTRADA no estoque
            $sql_mov = "INSERT INTO movimentacao_estoque (id_peca, tipo, quantidade, data_movimentacao) 
                        VALUES ('$id_peca', 'ENTRADA', '$quantidade', '$data_hoje')";
            $conn->query($sql_mov);

            // 3. Muda o status para 'CONCLUÍDO'
            $sql_status = "UPDATE pedido_compra SET status = 'ENTREGUE' WHERE id_pedido = '$id_pedido'";
            $conn->query($sql_status);

            $conn->commit();
            echo "<script>alert('Sucesso! $quantidade unidades entraram no estoque.'); window.location.href='estoquista.php';</script>";

        } catch (mysqli_sql_exception $e) {
            $conn->rollback();
            echo "Erro ao processar entrada no banco: " . $e->getMessage();
        }
    } else {
        echo "Pedido #$id_pedido não encontrado.";
    }
}
?>