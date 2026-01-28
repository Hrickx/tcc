<?php
// 1. Configurações de tempo (Sempre antes de tudo)
ini_set('session.gc_maxlifetime', 28800);
ini_set('session.cookie_lifetime', 28800);

// 2. Inicia a sessão com as novas configurações
session_start();

// 3. Inclui a conexão com o banco
include 'conexao.php';

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
            <label for="preco_display">Preço Unitário (R$)</label>
            <input type="number" id="preco_display" step="0.01" placeholder="0.00" 
                readonly style="background-color: #f1f5f9; cursor: not-allowed; color: #64748b;">
            
            <input type="hidden" name="preco" id="preco">
            
            <small style="font-size: 10px; color: #94a3b8;">Preço automático via sistema</small>
        </div>
        <div class="form-group">
            <label for="desconto">Desconto (%) - Máx <?php echo $desc_max; ?>%</label>
            <input type="number" name="desconto" id="desconto" value="0" min="0" max="<?php echo $desc_max; ?>">
        </div>
        <div class="form-group">
            <label for="forma_pagamento">Forma de Pagamento</label>
            <select name="forma_pagamento" id="forma_pagamento" required 
                    style="width: 100%; padding: 0.75rem; border: 1px solid #cbd5e1; border-radius: 8px; background: white;">
                <option value="">Selecione...</option>
                <option value="Dinheiro">Dinheiro</option>
                <option value="Cartão de Crédito">Cartão de Crédito</option>
                <option value="Cartão de Débito">Cartão de Débito</option>
                <option value="Pix">Pix</option>
            </select>
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
                    // SQL CORRIGIDO: Agora pegamos o valor_total direto da tabela vendas, 
                    // pois ele já contém o cálculo final com desconto processado no banco.
                    $sql_vendas = "SELECT 
                                        iv.id_venda, 
                                        p.nome, 
                                        iv.quantidade, 
                                        iv.preco_unitario, 
                                        v.valor_total as total_com_desconto,
                                        v.desconto_aplicado
                                    FROM itens_venda iv 
                                    JOIN pecas p ON iv.id_peca = p.id_peca 
                                    JOIN vendas v ON iv.id_venda = v.id_venda
                                    WHERE v.id_vendedor = '$id_vendedor'
                                    ORDER BY v.id_venda DESC LIMIT 10";
                                    
                    $res = $conn->query($sql_vendas);

                    if($res && $res->num_rows > 0) {
                        while($venda = $res->fetch_assoc()): ?>
                            <tr>
                                <td>#<?php echo $venda['id_venda']; ?></td>
                                <td><?php echo $venda['nome']; ?></td>
                                <td><?php echo $venda['quantidade']; ?> un</td>
                                <td>R$ <?php echo number_format($venda['preco_unitario'], 2, ',', '.'); ?></td>
                                
                                <td style="font-weight: bold; color: #059669;">
                                    R$ <?php echo number_format($venda['total_com_desconto'], 2, ',', '.'); ?>
                                    
                                </td>
                    
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
    // 1. Captura dos elementos do DOM
    const inputIdPeca = document.getElementById('id_peca');
    const inputQtd = document.getElementById('quantidade');
    const inputPreco = document.getElementById('preco');
    const inputDesc = document.getElementById('desconto');
    const displayTotal = document.getElementById('valorFinal');
    
    // --- NOVA FUNÇÃO: BUSCAR PREÇO AUTOMÁTICO ---
       inputIdPeca.addEventListener('blur', function() {
    const id = this.value;
    if (id > 0) {
        fetch(`BuscarPeca.php?id_peca=${id}`)
            .then(response => {
                // Se o servidor retornar 401, a sessão caiu
                if (response.status === 401) window.location.href = 'login.php';
                return response.json();
            })
            .then(data => {
                if (data && data.preco) {
                    document.getElementById('preco_display').value = data.preco; // Mostra na tela
                    document.getElementById('preco').value = data.preco;         // Guarda para o PHP
                    atualizarCalculo();
                }
            })
            .catch(err => console.error("Erro ao buscar peça:", err));
    }
});

    // 2. Definição do limite de desconto vindo do PHP
    const limiteDesconto = <?php echo $desc_max; ?>;

    // 3. Função principal de cálculo
    function atualizarCalculo() {
        const qtd = parseFloat(inputQtd.value) || 0;
        const preco = parseFloat(inputPreco.value) || 0;
        let desc = parseFloat(inputDesc.value) || 0;

        // Validação de desconto em tempo real
        if (desc > limiteDesconto) {
            alert("Seu limite de desconto é de " + limiteDesconto + "%");
            inputDesc.value = limiteDesconto;
            desc = limiteDesconto;
        }

        // Cálculo matemático
        const subtotal = qtd * preco;
        const total = subtotal - (subtotal * (desc / 100));

        // Atualização visual na tela com formatação de moeda brasileira
        displayTotal.innerText = `R$ ${total.toLocaleString('pt-BR', { 
            style: 'decimal',
            minimumFractionDigits: 2, 
            maximumFractionDigits: 2 
        })}`;
    }

    // 4. Adiciona os ouvintes de evento (escuta quando o usuário digita)
    [inputQtd, inputPreco, inputDesc].forEach(input => {
        input.addEventListener('input', atualizarCalculo);
    });

    // 5. Manter sessão ativa (Ping) para não deslogar durante o atendimento
    // Mantém a sessão viva "batendo na porta" do servidor a cada 60 segundos
    setInterval(() => {
        fetch('ping.php')
            .then(response => {
                if (response.status === 401) {
                    // Se a sessão cair mesmo assim, avisa antes de deslogar
                    window.location.href = 'login.php?erro=sessao_expirada';
                }
            })
            .catch(err => console.log("Erro ao manter conexão"));
    }, 60000);

    // Impede que o formulário seja enviado se a sessão cair no último segundo
    document.getElementById('vendaForm').addEventListener('submit', function(e) {
        fetch('ping.php').then(res => {
            if (res.status === 401) {
                e.preventDefault();
                alert("Sua sessão expirou. Por favor, faça login em outra aba para não perder os dados desta venda.");
            }
        });
    });
</script>
</body>
</html>