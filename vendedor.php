<?php
session_start();
// Proteção de acesso: só VENDEDOR entra aqui
if (!isset($_SESSION['id_usuario']) || $_SESSION['perfil'] !== 'VENDEDOR') {
    header("Location: login.php");
    exit();
}
include 'conexao.php';
$id_vendedor = $_SESSION['id_usuario'];
$nome_vendedor = $_SESSION['nome'];
// Garante que o desconto tenha um valor padrão se não estiver na sessão
$desc_max = $_SESSION['percentual_desconto_max'] ?? 5; 
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel do Vendedor | AutoPeças Pro</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
   <style>
    /* Configurações Globais e Reset */
    * { 
        margin: 0; 
        padding: 0; 
        box-sizing: border-box; 
        font-family: 'Inter', sans-serif; 
    }

    body { 
        background-color: #f8fafc; 
        color: #1e293b; 
        line-height: 1.6; 
    }

    /* Barra de Navegação (Navbar) */
    .navbar { 
        background-color: #1e293b; 
        color: white; 
        padding: 1rem 2rem; 
        display: flex; 
        justify-content: space-between; 
        align-items: center; 
        box-shadow: 0 2px 4px rgba(0,0,0,0.1); 
    }

    .user-badge { 
        background: #334155; 
        padding: 5px 12px; 
        border-radius: 20px; 
        font-size: 0.85rem; 
    }

    .logout { 
        color: #6a1b9a; 
        background-color: #ffffff; 
        text-decoration: none; 
        font-size: 0.8rem; 
        font-weight: bold; 
        margin-left: 20px; 
        border: 1px solid #ffffff; 
        padding: 4px 10px; 
        border-radius: 4px; 
        transition: 0.3s; 
    }

    .logout:hover { 
        color: white; 
        background-color: transparent; 
        border-color: white; 
    }

    /* Estrutura de Layout e Cards */
    .container { 
        max-width: 1000px; 
        margin: 2rem auto; 
        padding: 0 1rem; 
    }

    .card { 
        background: white; 
        padding: 2rem; 
        border-radius: 12px; 
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); 
        margin-bottom: 2rem; 
    }

    h2 { 
        font-size: 1.25rem; 
        margin-bottom: 1.5rem; 
        color: #334155; 
        display: flex; 
        align-items: center; 
        gap: 10px; 
    }

    /* Formulários e Inputs */
    .grid-form { 
        display: grid; 
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); 
        gap: 1.5rem; 
    }

    .form-group label { 
        display: block; 
        font-size: 0.875rem; 
        font-weight: 600; 
        margin-bottom: 0.5rem; 
        color: #475569; 
    }

    .form-group input { 
        width: 100%; 
        padding: 0.75rem; 
        border: 1px solid #cbd5e1; 
        border-radius: 8px; 
        font-size: 1rem; 
    }

    /* Caixa de Total e Botão Finalizar */
    .total-box { 
        background: #f1f5f9; 
        padding: 1.5rem; 
        border-radius: 8px; 
        margin-top: 1.5rem; 
        display: flex; 
        justify-content: space-between; 
        align-items: center; 
    }

    .total-box span { 
        font-size: 1.1rem; 
        font-weight: 600; 
    }

    .total-box strong { 
        font-size: 1.5rem; 
        color: #059669; 
    }

    .btn-finalizar { 
        background-color: #2563eb; 
        color: white; 
        border: none; 
        padding: 1rem 2rem; 
        border-radius: 8px; 
        font-weight: 600; 
        cursor: pointer; 
        transition: background 0.2s; 
    }

    .btn-finalizar:hover { 
        background-color: #1d4ed8; 
    }

    /* Tabelas e Badges de Status */
    table { 
        width: 100%; 
        border-collapse: collapse; 
        margin-top: 1rem; 
    }

    th { 
        text-align: left; 
        background: #f8fafc; 
        padding: 1rem; 
        font-size: 0.875rem; 
        color: #64748b; 
        border-bottom: 2px solid #e2e8f0; 
    }

    td { 
        padding: 1rem; 
        border-bottom: 1px solid #e2e8f0; 
        font-size: 0.9rem; 
    }

    .badge-status { 
        background: #dcfce7; 
        color: #166534; 
        padding: 4px 10px; 
        border-radius: 12px; 
        font-weight: 600; 
        font-size: 0.75rem; 
    }
</style>
</head>
<body>

    <nav class="navbar">
        <h1>AutoPeças Pro</h1>
        <div class="user-info">
            <span class="user-badge">Patente: <strong>Vendedor</strong></span>
             <span style="margin-left: 15px;">Olá, <strong><?php echo $nome_vendedor; ?></strong></span>
            <a href="logout.php" class="logout">Sair</a>
        </div>
    </nav>

    <main class="container">
        <section class="card">
            <h2>🛒 Registrar Nova Venda</h2>
            <form action="ProcessarVenda.php" method="POST" id="vendaForm">
                <div class="grid-form">
                    <div class="form-group">
                        <label for="id_peca">ID da Peça</label>
                        <input type="number" name="id_peca" id="id_peca" placeholder="Ex: 1" required>
                    </div>
                    <div class="form-group">
                        <label for="quantidade">Quantidade</label>
                        <input type="number" name="quantidade" id="quantidade" value="1" min="1" required>
                    </div>
                    <div class="form-group">
                        <label for="preco">Preço Unitário (R$)</label>
                        <input type="number" name="preco" id="preco" step="0.01" placeholder="0.00" required>
                    </div>
                    <div class="form-group">
                        <label for="desconto">Desconto (%) - Máx <?php echo $desc_max; ?>%</label>
                        <input type="number" name="desconto" id="desconto" value="0" min="0" max="<?php echo $desc_max; ?>">
                    </div>
                </div>

                <div class="total-box">
                    <span>Valor Final Calculado: <strong id="valorFinal">R$ 0,00</strong></span>
                    <button type="submit" class="btn-finalizar">Finalizar Venda</button>
                </div>
            </form>
        </section>

        <section class="card">
            <h2>📜 Histórico de Vendas Recentes</h2>
            <table>
                <thead>
                    <tr>
                        <th>ID Venda</th>
                        <th>Peça</th>
                        <th>Qtd</th>
                        <th>Preço Unit.</th>
                        <th>Total</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // SQL CORRIGIDO: v.id_vendedor
                    $sql_vendas = "SELECT iv.id_venda, p.nome, iv.quantidade, iv.preco_unitario, (iv.quantidade * iv.preco_unitario) as total_calculado
                                FROM itens_venda iv 
                                JOIN pecas p ON iv.id_peca = p.id_peca 
                                JOIN vendas v ON iv.id_venda = v.id_venda
                                WHERE v.id_vendedor = '$id_vendedor'
                                ORDER BY v.id_venda DESC LIMIT 5";
                    $res = $conn->query($sql_vendas);

                    if($res && $res->num_rows > 0) {
                        while($venda = $res->fetch_assoc()): ?>
                            <tr>
                                <td>#<?php echo $venda['id_venda']; ?></td>
                                <td><?php echo $venda['nome']; ?></td>
                                <td><?php echo $venda['quantidade']; ?></td>
                                <td>R$ <?php echo number_format($venda['preco_unitario'], 2, ',', '.'); ?></td>
                                <td>R$ <?php echo number_format($venda['total_calculado'], 2, ',', '.'); ?></td>
                                <td><span class="badge-status">Finalizada</span></td>
                            </tr>
                        <?php endwhile; 
                    } else {
                        echo "<tr><td colspan='6' style='text-align:center'>Nenhuma venda registrada recentemente.</td></tr>";
                    } ?>
                </tbody>
            </table>
        </section>
    </main>

    <script>
        const inputQtd = document.getElementById('quantidade');
        const inputPreco = document.getElementById('preco');
        const inputDesc = document.getElementById('desconto');
        const displayTotal = document.getElementById('valorFinal');
        const limiteDesconto = <?php echo $desc_max; ?>;

        function atualizarCalculo() {
            const qtd = parseFloat(inputQtd.value) || 0;
            const preco = parseFloat(inputPreco.value) || 0;
            let desc = parseFloat(inputDesc.value) || 0;

            if (desc > limiteDesconto) {
                alert("Seu limite de desconto é de " + limiteDesconto + "%");
                inputDesc.value = limiteDesconto;
                desc = limiteDesconto;
            }

            const subtotal = qtd * preco;
            const total = subtotal - (subtotal * (desc / 100));

            displayTotal.innerText = `R$ ${total.toLocaleString('pt-BR', { minimumFractionDigits: 2 })}`;
        }

        [inputQtd, inputPreco, inputDesc].forEach(input => {
            input.addEventListener('input', atualizarCalculo);
        });
    </script>
</body>
</html>