<?php
include 'conexao.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_venda = intval($_POST['id_venda']);
    $forma_pagamento = $_POST['forma_pagamento'];

    // Inserindo o pagamento
    $sql = "INSERT INTO pagamento (id_venda, forma_pagamento) VALUES ('$id_venda', '$forma_pagamento')";

    if ($conn->query($sql)) {
        // Opcional: Atualizar o status da venda para 'PAGA'
        $conn->query("UPDATE vendas SET status = 'FINALIZADA' WHERE id_venda = '$id_venda'");
        
        echo "<script>alert('Pagamento registrado com sucesso!'); window.location.href='vendedor.php';</script>";
    } else {
        echo "Erro ao registrar pagamento: " . $conn->error;
    }
}
?>