<?php
ini_set('session.gc_maxlifetime', 28800);
ini_set('session.cookie_lifetime', 28800);

session_start();

include 'conexao.php';

// Proteção de acesso
if (!isset($_SESSION['id_usuario']) || $_SESSION['perfil'] !== 'VENDEDOR') {
    header("Location: login.php");
    exit();
}

// Ativa o lançamento de exceções para erros do MySQL
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

if (!isset($_SESSION['id_usuario'])) {
    die("Acesso negado.");
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_peca = intval($_POST['id_peca']);
    $quantidade = intval($_POST['quantidade']);
    $preco_unitario = floatval($_POST['preco']);
    $desconto_percentual = floatval($_POST['desconto']);
    $forma_pagamento = $_POST['forma_pagamento']; // Captura o novo campo
    
    $id_vendedor = $_SESSION['id_usuario']; 
    $data_venda = date('Y-m-d H:i:s');

    // Cálculos
    $subtotal = $quantidade * $preco_unitario;
    $valor_desconto = $subtotal * ($desconto_percentual / 100);
    $total_final = $subtotal - $valor_desconto;

    // --- INÍCIO DA TRANSAÇÃO ---
    $conn->begin_transaction();

    try {
        // 1. Inserir na tabela VENDAS
        $sql_venda = "INSERT INTO vendas (id_vendedor, data_venda, valor_total, desconto_aplicado, status) 
                      VALUES ('$id_vendedor', '$data_venda', '$total_final', '$valor_desconto', 'FINALIZADA')";
        $conn->query($sql_venda);
        
        $id_venda_gerada = $conn->insert_id; // Este ID será usado nos próximos passos

        // 2. Inserir na tabela ITENS_VENDA 
        $sql_item = "INSERT INTO itens_venda (id_venda, id_peca, quantidade, preco_unitario) 
                     VALUES ('$id_venda_gerada', '$id_peca', '$quantidade', '$preco_unitario')";
        $conn->query($sql_item);

        // 3. Registrar a SAÍDA no estoque
        $sql_mov = "INSERT INTO movimentacao_estoque (id_peca, tipo, quantidade, data_movimentacao) 
                    VALUES ('$id_peca', 'SAIDA', '$quantidade', '$data_venda')";
        $conn->query($sql_mov);

        // 4. INSERIR NA TABELA PAGAMENTOS (A nova parte que você pediu)
        // O id_pagamento é auto_increment e a data_pagamento pode ser a mesma da venda
        $sql_pagamento = "INSERT INTO pagamento (id_venda, forma_pagamento, data_pagamento) 
                          VALUES ('$id_venda_gerada', '$forma_pagamento', '$data_venda')";
        $conn->query($sql_pagamento);

        // Se tudo deu certo, confirma todas as inserções no banco
        $conn->commit();
        echo "<script>alert('Venda #$id_venda_gerada e Pagamento ($forma_pagamento) registrados com sucesso!'); window.location.href='vendedor.php';</script>";

    } catch (mysqli_sql_exception $e) {
        // Se houver qualquer erro em QUALQUER uma das tabelas, desfaz TUDO (Rollback)
        $conn->rollback();
        echo "Erro crítico ao processar venda e pagamento: " . $e->getMessage();
    }
}
?>