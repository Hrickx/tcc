<?php
// 1. Configurações de tempo (Sempre antes de tudo)
ini_set('session.gc_maxlifetime', 28800);
ini_set('session.cookie_lifetime', 28800);

// 2. Inicia a sessão com as novas configurações
session_start();

// 3. Inclui a conexão com o banco
include 'conexao.php';

// Proteção: Apenas Estoquista ou Gerente
if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome = $_POST['nome'];
    $preco = $_POST['preco'];
    $qtd_inicial = $_POST['quantidade'];
    $id_forn = $_POST['id_fornecedor'];

    // 1. Insere a peça na tabela de peças
    $sql_peca = "INSERT INTO pecas (nome, preco_custo, id_fornecedor) VALUES ('$nome', '$preco', '$id_forn')";
    
    if ($conn->query($sql_peca)) {
        $id_nova_peca = $conn->insert_id; // Pega o ID da peça que acabou de ser criada

        // 2. Registra a quantidade inicial na tabela de movimentação para o saldo aparecer
        $sql_mov = "INSERT INTO movimentacao_estoque (id_peca, quantidade, tipo, data_movimentacao) 
                    VALUES ('$id_nova_peca', '$qtd_inicial', 'ENTRADA', NOW())";
        
        $conn->query($sql_mov);
        
        echo "<script>alert('Produto cadastrado com sucesso!'); window.location.href='estoquista.php';</script>";
    } else {
        echo "Erro: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Cadastrar Nova Peça | AutoPeças Pro</title>
   <style>
    /* Importando a fonte Inter para manter o padrão */
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap');

    body { 
        font-family: 'Inter', sans-serif; 
        background-color: #f8fafc; 
        color: #1e293b; 
        padding: 40px 20px; 
    }

    .form-container { 
        max-width: 500px; 
        margin: auto; 
        background: white; 
        padding: 30px; 
        border-radius: 12px; 
        box-shadow: 0 4px 6px rgba(0,0,0,0.05); 
    }

    h2 { 
        font-size: 1.2rem; 
        margin-bottom: 1.5rem; 
        color: #334155; 
        border-bottom: 2px solid #f1f5f9; 
        padding-bottom: 10px; 
    }

    label { 
        display: block; 
        font-size: 0.8rem; 
        font-weight: 600; 
        margin-top: 10px; 
        color: #64748b; 
    }

    input, select { 
        width: 100%; 
        padding: 10px; 
        margin: 8px 0 15px 0; 
        border: 1px solid #cbd5e1; 
        border-radius: 6px; 
        font-size: 0.9rem; 
    }

    input:focus, select:focus { 
        outline: none; 
        border-color: #2563eb; 
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1); 
    }

    button { 
        width: 100%; 
        background: #10b981; 
        color: white; 
        padding: 12px; 
        border: none; 
        border-radius: 6px; 
        cursor: pointer; 
        font-size: 1rem; 
        font-weight: 600; 
        transition: 0.3s; 
        margin-top: 10px;
    }

    button:hover { 
        background: #059669; 
    }

    .voltar { 
        display: inline-block; 
        margin-bottom: 20px; 
        text-decoration: none; 
        color: #64748b; 
        font-size: 0.9rem; 
        font-weight: 500; 
    }

    .voltar:hover { 
        color: #1e293b; 
    }
</style>
</head>
<body>

<div class="form-container">
    <a href="estoquista.php" class="voltar">← Voltar</a>
    <h2>📦 Cadastrar Novo Produto</h2>
    
    <form action="CadastrarPeca.php" method="POST">
        <label>Nome da Peça:</label>
        <input type="text" name="nome" placeholder="Ex: Pastilha de Freio" required>

        <label>Preço de Custo (R$):</label>
        <input type="number" name="preco" step="0.01" placeholder="0.00" required>

        <label>Quantidade Inicial em Estoque:</label>
        <input type="number" name="quantidade" placeholder="Ex: 50" required>

        <label>Fornecedor:</label>
        <select name="id_fornecedor" required>
            <option value="">Selecione o fornecedor</option>
            <?php
            $res = $conn->query("SELECT id_fornecedor, nome FROM fornecedor");
            while($f = $res->fetch_assoc()) {
                echo "<option value='{$f['id_fornecedor']}'>{$f['nome']}</option>";
            }
            ?>
        </select>

        <button type="submit">Finalizar Cadastro</button>
    </form>
</div>

</body>
</html>